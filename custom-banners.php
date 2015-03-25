<?php
/*
Plugin Name: Custom Banners
Plugin Script: custom-banners.php
Plugin URI: http://goldplugins.com/our-plugins/custom-banners/
Description: Allows you to create custom banners, which consist of an image, text, a link, and a call to action.  Custom banners are easily output via shortcodes. Each visitor to the website is then shown a random custom banner.
Version: 1.4.1
Author: GoldPlugins
Author URI: http://goldplugins.com/

*/

require_once('gold-framework/plugin-base.php');
require_once('lib/lib.php');
require_once('lib/custom_banners_options.php');

class CustomBannersPlugin extends GoldPlugin
{
	function __construct()
	{
		$this->add_hooks();
		$this->create_post_types();
		$this->register_taxonomies();
		$this->add_shortcodes();
		$this->add_stylesheets_and_scripts();
		
		//register sidebar widgets
		add_action( 'widgets_init', array($this, 'custom_banners_register_widgets' ));
				
		add_filter('manage_banner_posts_columns', array($this, 'custom_banners_column_head'), 10);  
		add_action('manage_banner_posts_custom_column', array($this, 'custom_banners_columns_content'), 10, 2); 
			
		add_filter('manage_edit-banner_groups_columns', array($this, 'custom_banners_cat_column_head'), 10);  
		add_action('manage_banner_groups_custom_column', array($this, 'custom_banners_cat_columns_content'), 10, 3); 
			
		$custom_banners_options = new customBannersOptions();
		
		//add Custom CSS
		add_action( 'wp_head', array($this, 'cb_setup_custom_css'));		

		//add our custom links for Settings and Support to various places on the Plugins page
		$plugin = plugin_basename(__FILE__);
		add_filter( "plugin_action_links_{$plugin}", array($this, 'add_settings_link_to_plugin_action_links') );
		add_filter( 'plugin_row_meta', array($this, 'add_custom_links_to_plugin_description'), 10, 2 );	
		
		//add single shortcode metabox to banner add/edit screen
		add_action( 'admin_menu', array($this,'add_meta_boxes')); // add our custom meta boxes
		
		parent::__construct();
	}

	//add Custom CSS
	function cb_setup_custom_css() 
	{
		echo '<style type="text/css" media="screen">' . get_option('custom_banners_custom_css') . "</style>";
	}
	
	function add_hooks()
	{
		parent::add_hooks();
	}
	
	function create_post_types()
	{
		$postType = array('name' => 'Banner', 'plural' => 'Banners', 'slug' => 'banners');
		$customFields = array();
		$customFields[] = array('name' => 'target_url', 'title' => 'Target URL', 'description' => 'Where a user should be sent when they click on the banner or the call to action button', 'type' => 'text');	
		$customFields[] = array('name' => 'cta_text', 'title' => 'Call To Action Text', 'description' => 'The "Call To Action" (text) of the button. Leave this field blank to hide the call to action button.', 'type' => 'text');
		$customFields[] = array('name' => 'css_class', 'title' => 'CSS Class', 'description' => 'Any extra CSS classes that you would like applied to this banner.', 'type' => 'text');
		$this->add_custom_post_type($postType, $customFields);

		//load list of current posts that have featured images	
		$supportedTypes = get_theme_support( 'post-thumbnails' );
		
		//none set, add them just to our type
		if( $supportedTypes === false ){
			add_theme_support( 'post-thumbnails', array( 'banner' ) );       
			//for the banner images    
		}
		//specifics set, add our to the array
		elseif( is_array( $supportedTypes ) ){
			$supportedTypes[0][] = 'banner';
			add_theme_support( 'post-thumbnails', $supportedTypes[0] );
			//for the banner images
		}
	
		//move featured image box to main column
		add_action('add_meta_boxes', array($this,'custom_banner_edit_screen'));		
		
		//remove unused meta boxes
		add_action( 'admin_init', array($this,'custom_banners_unused_meta'));

		// move the post editor under the other metaboxes
		add_action( 'add_meta_boxes', array($this, 'reposition_editor_metabox'), 0 );
		
		// enforce correct order of metaboxes
		add_action('admin_init', array($this, 'set_metabox_order'));
	}
	
	function reposition_editor_metabox() {
		global $_wp_post_type_features;
		if (isset($_wp_post_type_features['banner']['editor']) && $_wp_post_type_features['banner']['editor']) {
			unset($_wp_post_type_features['banner']['editor']);
			add_meta_box(
			'banner_caption',
			__('Banner Caption'),
			array($this, 'output_banner_caption_metabox'),
			'banner', 'normal', 'low'
			);
		}
	}
	
	function output_banner_caption_metabox( $post ) {
		echo '<div class="wp-editor-wrap">';
		wp_editor($post->post_content, 'content', array('dfw' => true, 'tabindex' => 1) );
		echo '</div>';
	}	
	
	function set_metabox_order() {
		global $wpdb, $user_ID, $posts_widgets_order_hash;
		
		// check to see if its already set correctly
		$check_val = get_user_option('_custom_banners_meta_box_order', $user_ID);
		$correct_val = "metaboxes_v1";
		
		// if the metabox order is incorrect or not set, reset it now
		if ($check_val !== $correct_val)
		{
			$metabox_order = get_user_option('meta-box-order_banner', $user_ID);
			if (empty($metabox_order)) {
				$metabox_order = array();
			}
			$banner_info_metabox_id = $this->customPostTypes['banners']->get_metabox_id();
			$metabox_order['normal'] = 'postimagediv,banner_caption,'.$banner_info_metabox_id.'';
			update_user_option($user_ID, 'meta-box-order_banner', $metabox_order, true);
			update_user_option($user_ID, '_custom_banners_meta_box_order', $correct_val, true);
		}
	}
	
	//remove unused meta boxes
	function custom_banners_unused_meta() {
		remove_post_type_support( 'banner', 'excerpt' );
		remove_post_type_support( 'banner', 'custom-fields' );
		remove_post_type_support( 'banner', 'comments' );
		remove_post_type_support( 'banner', 'author' );
	}

	//remove featured image from the sidebar and add it to the main column
	function custom_banner_edit_screen() {
		// remove the Featured Image metabox, and replace it with our own (slightly modified) version, now residing in the main column
		remove_meta_box( 'postimagediv', 'banner', 'side' );
		add_meta_box('postimagediv', __('Banner Image'), array($this, 'custom_banners_post_thumbnail_html'), 'banner', 'normal', 'high');
	}
	
	//custom banner image html callback
	function custom_banners_post_thumbnail_html( $post ) {
		$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );

        $upload_iframe_src = esc_url( get_upload_iframe_src('image', $post->ID ) );
        $set_thumbnail_link = '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set featured image' ) . '" href="%s" id="set-post-thumbnail" class="thickbox">%s</a></p>';
        $content = sprintf( $set_thumbnail_link, $upload_iframe_src, esc_html__( 'Set featured image' ) );

        if ( $thumbnail_id && get_post( $thumbnail_id ) ) {
                $thumbnail_html = wp_get_attachment_image( $thumbnail_id, "full" );
						
                if ( !empty( $thumbnail_html ) ) {
                        $ajax_nonce = wp_create_nonce( 'set_post_thumbnail-' . $post->ID );
                        $content = sprintf( $set_thumbnail_link, $upload_iframe_src, $thumbnail_html );
                        $content .= '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail" onclick="WPRemoveThumbnail(\'' . $ajax_nonce . '\');return false;">' . esc_html__( 'Remove featured image' ) . '</a></p>';
                }           
        }
		
		// output the finalized HTML
        echo apply_filters( 'admin_post_thumbnail_html', $content, $post->ID );
	}
	
	function register_taxonomies()
	{
		$this->add_taxonomy('banner_groups', 'banner', 'Banner Group', 'Banner Groups');
	}
	
	function add_shortcodes()
	{
		add_shortcode('banner', array($this, 'banner_shortcode'));
	}
	
	function banner_shortcode($atts, $content = '')
	{
		// load the shortcodes attributes and merge with our defaults
		$defaults = array(	'id' => '',
							'group' => '',
							'caption_position' => 'bottom',
							'transition' => 'none',
							'pager' => false,
							'count' => 1,
							'timer' => 4000,
							'use_image_tag' => false,
							'show_pager_icons' => false,
							'hide' => false);
							
		$atts = shortcode_atts($defaults, $atts);
		$banner_id = intval($atts['id']);
		
		$html = '';
		
		// load the banner's data
		if($banner_id == ''){
			$banners = get_posts(array('posts_per_page' => $atts['count'], 'orderby' => 'rand', 'post_type'=> 'banner', 'banner_groups' => $atts['group']));
		
			if(isValidCBKey() && (in_array($atts['transition'], array('fadeIn','fadeOut','scrollHorz','scrollVert','shuffle','carousel','flipHorz','flipVert','tileSlide')))){
				$html .= '<div class="cycle-slideshow" data-cycle-fx="' . $atts['transition'] . '" data-cycle-timeout="' . $atts['timer'] . '" data-cycle-slides="> div.banner_wrapper" >';
			}
		
			$first = true;
		
			foreach($banners as $banner){
				
				//hide all but the first banner
				if($first){
					$atts['hide'] = false;
					$first = false;
				} else {
					$atts['hide'] = true;
				}
				
				$html .= $this->buildBannerHTML($banner, $banner_id, $atts);
			}
			
			if(isValidCBKey() && (in_array($atts['transition'], array('fadeIn','fadeOut','scrollHorz','scrollVert','shuffle','carousel','flipHorz','flipVert','tileSlide')))){
				//add pager to bottom of slideshow, if option set
				if($atts['pager'] || $atts['show_pager_icons'] ){
					$html .= '<div class="cycle-pager"></div>';
				}
				
				$html .= '</div><!-- end slideshow -->';
			}
		} else {
			$banner = get_post($banner_id);
			
			$html .= $this->buildBannerHTML($banner, $banner_id, $atts);
		}
		
		// return the generated HTML
		return $html;
	}
	
	function buildBannerHTML($banner, $banner_id, $atts){
		if($banner_id == ''){			
			$banner_id = $banner->ID;		
		}
	
		$post_thumbnail_id = get_post_thumbnail_id( $banner_id );
		$cta = $this->get_option_value($banner_id, 'cta_text', '');
		$target_url = $this->get_option_value($banner_id, 'target_url', '#');
		$css_class = $this->get_option_value($banner_id, 'css_class', '');	
		$use_big_link = get_option('custom_banners_use_big_link');
		$open_in_window = get_option('custom_banners_open_link_in_new_window', 0);
		
		// placeholder variables
		$html = '';
		$img_html = '';
		$banner_style = '';
		
		// add any extra CSS classes to the banner
		$extra_classes = array($css_class, 'banner-' . $banner_id);
		if (strlen($cta) > 0) {
			$extra_classes[] = 'has_cta';
		}
		if ($atts['caption_position'] == 'left') {
			$extra_classes[] = 'left';
			$extra_classes[] = 'horiz';
		}
		else if ($atts['caption_position'] == 'right') {
			$extra_classes[] = 'right';
			$extra_classes[] = 'horiz';
		}
		else if ($atts['caption_position'] == 'top') {
			$extra_classes[] = 'top';
			$extra_classes[] = 'vert';
		}
		else if ($atts['caption_position'] == 'bottom') {
			$extra_classes[] = 'bottom';
			$extra_classes[] = 'vert';
		}
		$extra_classes_str = implode(' ', $extra_classes);
		
		// we can use either a background image on the banner div, or an <img> tag inside the banner div instead
		$option_use_image_tag = isset($atts['use_image_tag']) ? $atts['use_image_tag'] : false;
		
		// load the featured image, of one was specified
		if ($post_thumbnail_id !== '' && $post_thumbnail_id > 0)
		{
			if (!$option_use_image_tag) 
			{
				$img_src = wp_get_attachment_image_src($post_thumbnail_id, 'full');
				$banner_style = "background-image: url('" . $img_src[0] . "');";
				$img_html = '';
			}
			else {
				$img_html = wp_get_attachment_image($post_thumbnail_id, 'full');
			}			
		}		
		
		if($atts['hide']){
			$banner_display = 'style="display:none;"';
		} else {
			$banner_display = '';
		}
		
		if($open_in_window){
			$link_target = ' target="_blank" ';
		} else {
			$link_target = '';
		}
		
		// generate the html now
		$html .= '<div class="banner_wrapper" '. $banner_display .'>';
			$html .= '<div class="banner ' . $extra_classes_str . '" style="' . $banner_style . '">';
				if($use_big_link){
					$html .= '<a class="custom_banners_big_link" ' . $link_target . ' href="' . $target_url . '"></a>';
				}
				$html .= $img_html;
				$caption = $banner->post_content;
				if (strlen($caption) > 0 || strlen($cta) > 0)
				{
					$html .= '<div class="banner_caption">';
						$html .= $caption;
						if (strlen($cta) > 0)
						{				
							$html .= '<div class="banner_call_to_action">';
								$html .= '<a href="' . $target_url . '" ' . $link_target . ' class="banner_btn_cta">' . htmlspecialchars($cta) . '</a>';
							$html .= '</div>'; //<!--.banner_call_to_action-->
						}
					$html .= '</div>'; //<!--.banner_caption-->
				}
			$html .= '</div>'; //<!--.banner -->
		$html .= '</div>'; //<!--.banner_wrapper-->
		
		return $html;
	}
	
	function add_stylesheets_and_scripts()
	{
		$cssUrl = plugins_url( 'assets/css/wp-banners.css' , __FILE__ );
		$this->add_stylesheet('wp-banners-css',  $cssUrl);
		
		if(isValidCBKey()){  
			//need to include cycle2 this way, for compatibility with our other plugins
			$jsUrl = plugins_url( 'assets/js/jquery.cycle2.min.js' , __FILE__ );
			$this->add_script('cycle2',  $jsUrl, array( 'jquery' ),
			false,
			true);		
			
			$jsUrl = plugins_url( 'assets/js/wp-banners.js' , __FILE__ );
			$this->add_script('wp-banners-js',  $jsUrl, array( 'jquery' ),
			false,
			true);			
		}
	}
 
	//this is the heading of the new column we're adding to the banner posts list
	function custom_banners_column_head($defaults) {  
		$defaults = array_slice($defaults, 0, 2, true) +
		array("single_shortcode" => "Shortcode") +
		array_slice($defaults, 2, count($defaults)-2, true);
		return $defaults;  
	}  

	//this content is displayed in the banner post list
	function custom_banners_columns_content($column_name, $post_ID) {  
		if ($column_name == 'single_shortcode') {  
			echo "<code>[banner id={$post_ID}]</code>";
		}  
	} 

	//this is the heading of the new column we're adding to the banner category list
	function custom_banners_cat_column_head($defaults) {  
		$defaults = array_slice($defaults, 0, 2, true) +
		array("single_shortcode" => "Shortcode") +
		array_slice($defaults, 2, count($defaults)-2, true);
		return $defaults;  
	}  

	//this content is displayed in the banner category list
	function custom_banners_cat_columns_content($value, $column_name, $tax_id) {  

		$category = get_term_by('id', $tax_id, 'banner_groups');
		
		return "<code>[banner group='{$category->slug}']</code>"; 
	}

	//register any widgets here
	function custom_banners_register_widgets() {
		include('lib/widgets/single_banner_widget.php');
		include('lib/widgets/rotating_banner_widget.php');
		
		register_widget( 'singleBannerWidget' );
		register_widget( 'rotatingBannerWidget' );
	}
	
	//add an inline link to the settings page, before the "deactivate" link
	function add_settings_link_to_plugin_action_links($links) { 
	  $settings_link = '<a href="admin.php?page=custom-banners-settings">Settings</a>';
	  array_unshift($links, $settings_link); 
	  return $links; 
	}

	// add inline links to our plugin's description area on the Plugins page
	function add_custom_links_to_plugin_description($links, $file) { 

		/** Get the plugin file name for reference */
		$plugin_file = plugin_basename( __FILE__ );
	 
		/** Check if $plugin_file matches the passed $file name */
		if ( $file == $plugin_file )
		{		
			$new_links['settings_link'] = '<a href="admin.php?page=custom-banners-settings">Settings</a>';
			$new_links['support_link'] = '<a href="http://goldplugins.com/contact/?utm-source=plugin_menu&utm_campaign=support" target="_blank">Get Support</a>';
				
			if(!isValidCBKey()){
				$new_links['upgrade_to_pro'] = '<a href="http://goldplugins.com/our-plugins/custom-banners/upgrade-to-custom-banners-pro/?utm_source=plugin_menu&utm_campaign=upgrade" target="_blank">Upgrade to Pro</a>';
			}
			
			$links = array_merge( $links, $new_links);
		}
		return $links; 
	}
	
		
	/* Displays a meta box with the shortcodes to display the current banner */
	function display_shortcodes_meta_box() {
		global $post;
		echo "Add this shortcode to any page where you'd like to <strong>display</strong> this Banner:<br />";
		echo '<pre>[banner id="' . $post->ID . '"]</pre>';
	}

	function add_meta_boxes(){
		add_meta_box( 'banner_shortcodes', 'Shortcodes', array($this, 'display_shortcodes_meta_box'), 'banner', 'side', 'default' );
	}
}
$ebp = new CustomBannersPlugin();
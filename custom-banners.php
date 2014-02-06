
<?php
/*
Plugin Name: Custom Banners
Plugin Script: custom-banners.php
Plugin URI: http://goldplugins.com/our-plugins/custom-banners/
Description: Allows you to create custom banners, which consist of an image, text, a link, and a call to action.  Custom banners are easily output via shortcodes. Each visitor to the website is then shown a random custom banner.
Version: 1.1
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
				
		add_filter('manage_banner_posts_columns', array($this, 'custom_banners_column_head'), 10);  
		add_action('manage_banner_posts_custom_column', array($this, 'custom_banners_columns_content'), 10, 2); 
			
		add_filter('manage_edit-banner_groups_columns', array($this, 'custom_banners_cat_column_head'), 10);  
		add_action('manage_banner_groups_custom_column', array($this, 'custom_banners_cat_columns_content'), 10, 3); 
		
		$custom_banners_options = new customBannersOptions();
		
		parent::__construct();
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
							'count' => 1,
							'timer' => 4000);
		$atts = shortcode_atts($defaults, $atts);
		$banner_id = intval($atts['id']);
		
		$html = '';
		
		// load the banner's data
		if($banner_id == ''){
			$banners = get_posts(array('posts_per_page' => $atts['count'], 'orderby' => 'rand', 'post_type'=> 'banner', 'banner_groups' => $atts['group']));
		
			if(isValidCBKey() && ($atts['transition'] == 'fadeIn' || $atts['transition'] == 'scrollHorz')){
				$html .= '<div class="cycle-slideshow" data-cycle-fx="' . $atts['transition'] . '" data-cycle-timeout="' . $atts['timer'] . '" data-cycle-slides="> div" data-cycle-auto-height="container" >';
			}
		
			foreach($banners as $banner){
				$html .= $this->buildBannerHTML($banner, $banner_id, $atts);
			}
			
			if(isValidCBKey() && ($atts['transition'] == 'fadeIn' || $atts['transition'] == 'scrollHorz')){
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
		$option_use_bg_image = true; // todo: this is supported below; we just need to wire up an option on the user's side to toggle
		
		// load the featured image, of one was specified
		if ($post_thumbnail_id !== '' && $post_thumbnail_id > 0)
		{
			if ($option_use_bg_image) 
			{
				$img_src = wp_get_attachment_image_src($post_thumbnail_id, 'full');
				$banner_style = "background-image: url('" . $img_src[0] . "');";
				$img_html = '';
			}
			else {
				$img_html = wp_get_attachment_image($post_thumbnail_id, 'full');
			}			
		}		
		
		// generate the html now
		$html .= '<div class="banner_wrapper">';
			$html .= '<div class="banner ' . $extra_classes_str . '" style="' . $banner_style . '">';
				$html .= $img_html;
				$html .= '<div class="banner_caption">';
					$html .= $banner->post_content;
					if (strlen($cta) > 0)
					{				
						$html .= '<div class="banner_call_to_action">';
							$html .= '<a href="' . $target_url . '" class="banner_btn_cta">' . htmlspecialchars($cta) . '</a>';
						$html .= '</div>'; //<!--.banner_call_to_action-->
					}
				$html .= '</div>'; //<!--.banner_caption-->
			$html .= '</div>'; //<!--.banner -->
		$html .= '</div>'; //<!--.banner_wrapper-->
		
		return $html;
	}
	
	function add_stylesheets_and_scripts()
	{
		$cssUrl = plugins_url( 'assets/css/wp-banners.css' , __FILE__ );
		$this->add_stylesheet('wp-banners-css',  $cssUrl);
		
		if(isValidCBKey()){  
			$jsUrl = plugins_url( 'assets/js/wp-banners.js' , __FILE__ );
			$this->add_script('wp-banners-js',  $jsUrl, array( 'jquery' ));		
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
}
$ebp = new CustomBannersPlugin();
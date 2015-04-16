<?php
/*
This file is part of Custom Banners.

Custom Banners is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Custom Banners is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with The Custom Banners.  If not, see <http://www.gnu.org/licenses/>.
*/

class customBannersOptions
{
	var $textdomain = '';
	
	function __construct(){
		//may be running in non WP mode (for example from a notification)
		if(function_exists('add_action')){
			//add a menu item
			add_action( 'admin_menu', array($this, 'add_admin_menu_item') );	
			add_action( 'admin_init', array( $this, 'admin_scripts' ) );
			add_action( 'admin_head', array($this, 'admin_css') );
		}
		$this->shed = new GoldPlugins_BikeShed();
	}
	
	function add_admin_menu_item(){
		$title = "Custom Banners Settings";
		$page_title = "Custom Banners Settings";
		$top_level_slug = "custom-banners-settings";
		$style_menu_label = isValidCBKey() ? __('Style Options', $this->textdomain) : __('Style Options (Pro)', $this->textdomain);
		
		//create new top-level menu
		add_menu_page($page_title, $title, 'administrator', $top_level_slug, array($this, 'basic_settings_page'));
		add_submenu_page($top_level_slug , 'Basic Options', 'Basic Options', 'administrator', $top_level_slug, array($this, 'basic_settings_page'));
		add_submenu_page($top_level_slug , 'Style Options', $style_menu_label, 'administrator', 'custom-banners-style-settings', array($this, 'style_settings_page'));
		add_submenu_page($top_level_slug , 'Help & Instructions', 'Help & Instructions', 'administrator', 'custom-banners-help', array($this, 'help_settings_page'));

		//call register settings function
		add_action( 'admin_init', array($this, 'register_settings'));	
	}


	function register_settings(){
		//register our settings
		register_setting( 'custom-banners-settings-group', 'custom_banners_custom_css' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_use_big_link' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_open_link_in_new_window' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_never_show_captions' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_never_show_cta_buttons' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_default_width' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_default_height' );
		
		register_setting( 'custom-banners-settings-group', 'custom_banners_registered_name' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_registered_url' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_registered_key' );

		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_background_color' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_background_opacity' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_background_color' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_border_color' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_border_radius' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_button_font_size' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_button_font_style' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_button_font_family' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_cta_button_font_color' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_font_size' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_font_style' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_font_family' );
		register_setting( 'custom-banners-style-settings-group', 'custom_banners_caption_font_color' );
	}
	
	//function to produce tabs on admin screen
	function admin_tabs($current = 'homepage' ) {
		$style_label = isValidCBKey() ? __('Style Options', $this->textdomain) : __('Style Options (Requires Pro)', $this->textdomain);
		$tabs = array( 	'custom-banners-settings' => __('Basic Options', $this->textdomain), 
						'custom-banners-style-settings' => $style_label,
						'custom-banners-help' => __('Help &amp; Instructions', $this->textdomain)
				);
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
			foreach( $tabs as $tab => $name ){
				$class = ( $tab == $current ) ? ' nav-tab-active' : '';
				echo "<a class='nav-tab$class' href='?page=$tab'>$name</a>";
			}
		echo '</h2>';
	}
	
	function admin_scripts()
	{
		wp_enqueue_script(
			'gp-admin',
			plugins_url('../assets/js/gp-admin.js', __FILE__),
			array( 'jquery' ),
			false,
			true
		);	
	}
		
	function admin_css()
	{
		if(is_admin()) {
			$admin_css_url = plugins_url( '../assets/css/admin_style.css' , __FILE__ );
			wp_register_style('custom-banners-admin', $admin_css_url);
			wp_enqueue_style('custom-banners-admin');
		}	
	}

	function settings_page_top(){
		$title = "Custom Banners Settings";
		$message = "Custom Banners Settings Updated.";
		
		global $pagenow;
	?>
	<div class="wrap gold_plugins_settings <?php if(isValidCBKey()): ?>is_pro<?php endif; ?>">
		<h2><?php echo $title; ?></h2>
		
		<p class="cb_need_help">Need Help? <a href="http://goldplugins.com/documentation/custom-banners-documentation/" target="_blank">Click here</a> to read instructions, see examples, and find more information on how to add, edit, update, and output your custom banners.</p>
		
		<?php if(!isValidCBKey()): ?>		
			<?php $this->output_mailing_list_form(); ?>
		<?php endif; ?>
		
		<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') : ?>
		<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
		<?php endif;
		
		$this->get_and_output_current_tab($pagenow);
	}
	
	function output_mailing_list_form()
	{
		global $current_user;
?>
		<script type="text/javascript">
			jQuery(function () {
				if (typeof(gold_plugins_init_mailchimp_form) == 'function') {
				gold_plugins_init_mailchimp_form();
				}
			});
		</script>
		<!-- Begin MailChimp Signup Form -->		
		<div id="signup_wrapper">
			<div class="topper">
				<h3>Save 20% on Custom Banners Pro!</h3>
				<p class="pitch">Submit your name and email and we’ll send you a coupon for 20% off your upgrade to the Pro version.</p>
			</div>
			<div id="mc_embed_signup">
				<form action="http://illuminatikarate.us2.list-manage.com/subscribe/post?u=403e206455845b3b4bd0c08dc&amp;id=27d2c9ee87" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
					<label for="mce-NAME">Your Name:</label>
					<input type="text" value="<?php echo (!empty($current_user->display_name) ? $current_user->display_name : ''); ?>" name="NAME" class="name" id="mce-NAME" placeholder="Your Name">
					<label for="mce-EMAIL">Your Email:</label>
					<input type="email" value="<?php echo (!empty($current_user->user_email) ? $current_user->user_email : ''); ?>" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
					<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
					<div style="position: absolute; left: -5000px;"><input type="text" name="b_403e206455845b3b4bd0c08dc_6ad78db648" tabindex="-1" value=""></div>
					<div class="clear"><input type="submit" value="Send Me The Coupon Now" name="subscribe" id="mc-embedded-subscribe" class="smallBlueButton"></div>
						<p class="secure"><img src="<?php echo plugins_url( '../assets/img/lock.png', __FILE__ ); ?>" alt="Lock" width="16px" height="16px" />We respect your privacy.</p>
						<input type="hidden" id="mc-upgrade-plugin-name" value="Custom Banners Pro" />
						<input type="hidden" id="mc-upgrade-link-per" value="http://goldplugins.com/purchase/custom-banners-pro/single?promo=newsub20" />
						<input type="hidden" id="mc-upgrade-link-biz" value="http://goldplugins.com/purchase/custom-banners-pro/business?promo=newsub20" />
						<input type="hidden" id="mc-upgrade-link-dev" value="http://goldplugins.com/purchase/custom-banners-pro/developer?promo=newsub20" />
						<input type="hidden" id="gold_plugins_already_subscribed" name="gold_plugins_already_subscribed" value="<?php echo get_user_setting ('_c_b_ml_has_subscribed', '0'); ?>" />
				</form>
				<div class="features">
					<strong>When you upgrade, you'll instantly gain access to:</strong>
					<ul>
						<li>Fading Banner Widget</li>
						<li>Advanced Transitions</li>
						<li>Outstanding support</li>
						<li>Remove all banners from the admin area</li>
						<li>And more!</li>
					</ul>
					<a href="http://goldplugins.com/our-plugins/custom-banners?utm_source=cpn_box&utm_campaign=upgrade&utm_banner=learn_more" title="Learn More">Learn More About Custom Banners Pro &raquo;</a>
				</div>
			</div>
			<p class="u_to_p"><a href="http://goldplugins.com/our-plugins/custom-banners/upgrade-to-custom-banners-pro/?utm_source=plugin&utm_campaign=small_text_signup">Upgrade to Custom Banners Pro now</a> to remove banners like this one.</p>
		</div>
		<!--End mc_embed_signup-->
<?php	
	}
	
	function get_and_output_current_tab($pagenow){
		$tab = $_GET['page'];
		
		$this->admin_tabs($tab); 
				
		return $tab;
	}
	
	function basic_settings_page(){	
		$this->settings_page_top();
		
		?><form method="post" action="options.php">
			<?php settings_fields( 'custom-banners-settings-group' ); ?>			
			
			<h3>Basic Options</h3>
			
			<p>Use the below options to control various bits of output.</p>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_custom_css">Custom CSS</a></th>
					<td><textarea name="custom_banners_custom_css" id="custom_banners_custom_css" style="width: 250px; height: 250px;"><?php echo get_option('custom_banners_custom_css'); ?></textarea>
					<p class="description">Input any Custom CSS you want to use here.  The plugin will work without you placing anything here - this is useful in case you need to edit any styles for it to work with your theme, though.<br/> For a list of available classes, click <a href="http://goldplugins.com/documentation/custom-banners-documentation/html-css-information-for-custom-banners/" target="_blank">here</a>.</p></td>
				</tr>
			</table>
			
			<table class="form-table">
			<?php
				$this->text_input('custom_banners_default_width', 'Default Banner Width', 'Enter a default width for your banners, in pixels. If not specified, the banner will try to fit its container.', '');
				$this->text_input('custom_banners_default_height', 'Default Banner Height', 'Enter a default height for your banners, in pixels. If not specified, the banner will try to fit its container.', '');
			?>
			</table>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_use_big_link">Link Entire Banner</label></th>
					<td><input type="checkbox" name="custom_banners_use_big_link" id="custom_banners_use_big_link" value="1" <?php if(get_option('custom_banners_use_big_link')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description">If checked, the entire banner will be linked to the Target URL - not just the CTA.</p>
					</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_open_link_in_new_window">Open Link in New Window</label></th>
					<td><input type="checkbox" name="custom_banners_open_link_in_new_window" id="custom_banners_open_link_in_new_window" value="1" <?php if(get_option('custom_banners_open_link_in_new_window')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description">If checked, the Banner Link / CTA will open in a New Window.</p>
					</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_never_show_captions">Never Show Captions</label></th>
					<td><input type="checkbox" name="custom_banners_never_show_captions" id="custom_banners_never_show_captions" value="1" <?php if(get_option('custom_banners_never_show_captions')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description">If checked, your banners will not show their captions, even if you enter one.</p>
					</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="custom_banners_never_show_cta_buttons">Never Show CTA Buttons</label></th>
					<td><input type="checkbox" name="custom_banners_never_show_cta_buttons" id="custom_banners_never_show_cta_buttons" value="1" <?php if(get_option('custom_banners_never_show_cta_buttons')){ ?> checked="CHECKED" <?php } ?>/>
					<p class="description">If checked, your banners will not show their buttons, even if you have enter a call to action.</p>
					</td>
				</tr>
			</table>
				
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>

			<?php include('registration_options.php'); ?>
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
		</div><?php 
	} // end basic_settings_page function	
	
	function style_settings_page()
	{
		$this->settings_page_top();
		$disabled = !(isValidCBKey());
		?><form method="post" action="options.php" class="gp_settings_form">
			<?php if (!isValidCBKey()): ?>
			<div class="custom_banners_not_registered">
				<h3>Custom Banners Pro Required To Use These Features</h3>
				<p>Custom Banners Pro is required to use these features. The options below will become instantly available once you have registered and activated your plugin. <br /><br /><a class="button" href="http://goldplugins.com/our-plugins/custom-banners/upgrade-to-custom-banners-pro/?utm_campaign=registration&utm_source=custom_banners_settings" target="_blank">Click Here To Upgrade To Pro</a> <br /> <br /><em>You'll receive your API keys as soon as you complete your payment, instantly unlocking these features and more!</em></p>
			</div>
			<?php endif; ?>
			<?php settings_fields( 'custom-banners-style-settings-group' ); ?>
			<h3>Style Options</h3>
			<p>Use these options to adjust the style of your caption and your call to action buttons.</p>
			<fieldset>
				<legend>Caption Background</legend>				
				<table class="form-table">
					<?php 
						$this->color_input('custom_banners_caption_background_color', 'Background Color', '#000000', $disabled);
						$this->text_input('custom_banners_caption_background_opacity', 'Background Opacity (percentage)', '', '70', $disabled);
					?>
				</table>
			</fieldset>
			
			<fieldset>
				<legend>Caption Text</legend>
				<table class="form-table">
					<?php
						$this->typography_input('custom_banners_caption_*', 'Caption Font', 'Please note: these settings can be overridden for each banner by using the Visual Editor.', '', '', '', '#ffffff', $disabled );
					?>
				</table>
			</fieldset>
						
			<fieldset>
				<legend>Call To Action (CTA) Button</legend>
				<table class="form-table">
					<?php
						$this->typography_input('custom_banners_cta_button_*', 'Font', '', '', '', '', '', $disabled);
						$this->color_input('custom_banners_cta_background_color', 'Background Color', '#ffa500', $disabled);
						$this->color_input('custom_banners_cta_border_color', 'Border Color', '#ff8c00', $disabled);
						$this->text_input('custom_banners_cta_border_radius', 'Border Radius', '', '5', $disabled);
					?>
				</table>
			</fieldset>
	
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
		</div><?php 
	}
	
	function text_input($name, $label, $description = '', $default_val = '', $disabled = false)
	{
		$val = get_option($name, $default_val);
		if (empty($val)) {
			$val = $default_val;
		}
		$this->shed->text(
			array(
				'name' => $name,
				'label' => $label,
				'value' => $val,
				'description' => $description,
				'disabled' => $disabled
			)
		);
	}
	
	function color_input($name, $label, $default_color = '#000000', $disabled = false)
	{		
		$val = get_option($name, $default_color);
		if (empty($val)) {
			$val = $default_color;
		}
		$this->shed->color(
			array(
				'name' => $name,
				'label' => $label,
				'default_color' => $default_color,
				'value' => $val,
				'disabled' => $disabled
			)
		);
	}
	
	function typography_input($name, $label, $description = '', $default_font_family = '', $default_font_size = '', $default_font_style = '', $default_font_color = '#000080', $disabled = false)
	{
		$options = array(
			'name' => $name,
			'label' => $label,
			'default_color' => $default_font_color,
			'description' => $description,
			'google_fonts' => true,
			'values' => array(),
			'disabled' => $disabled			
		);
		$fields = array(
			'font_size' => $default_font_size,
			'font_family' => $default_font_family,
			'font_color' => $default_font_color,
			'font_style' => $default_font_style
		);
		foreach ($fields as $key => $default_value)
		{
			list($field_name, $field_id) = $this->shed->get_name_and_id($options, $key);
			$val = get_option($field_name, $default_value);
			if (empty($val)) {
				$val = $default_value;
			}			
			$options['values'][$key] = $val;
		}
		$this->shed->typography($options);
	}
	
	function help_settings_page(){
		$this->settings_page_top();
		include('pages/help.html');
	}	
} // end class
?>
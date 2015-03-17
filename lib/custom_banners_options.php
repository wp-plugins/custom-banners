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
	}
	
	function add_admin_menu_item(){
		$title = "Custom Banners Settings";
		$page_title = "Custom Banners Settings";
		$top_level_slug = "custom-banners-settings";
		
		//create new top-level menu
		add_menu_page($page_title, $title, 'administrator', $top_level_slug, array($this, 'basic_settings_page'));
		add_submenu_page($top_level_slug , 'Basic Options', 'Basic Options', 'administrator', $top_level_slug, array($this, 'basic_settings_page'));
		add_submenu_page($top_level_slug , 'Help & Instructions', 'Help & Instructions', 'administrator', 'custom-banners-help', array($this, 'help_settings_page'));

		//call register settings function
		add_action( 'admin_init', array($this, 'register_settings'));	
	}


	function register_settings(){
		//register our settings
		register_setting( 'custom-banners-settings-group', 'custom_banners_custom_css' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_use_big_link' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_open_link_in_new_window' );
		
		register_setting( 'custom-banners-settings-group', 'custom_banners_registered_name' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_registered_url' );
		register_setting( 'custom-banners-settings-group', 'custom_banners_registered_key' );
	}
	
	//function to produce tabs on admin screen
	function admin_tabs($current = 'homepage' ) {	
		$tabs = array( 'custom-banners-settings' => __('Basic Options', $this->textdomain), 'custom-banners-help' => __('Help & Instructions', $this->textdomain));
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
			
			<?php include('registration_options.php'); ?>
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
		</div><?php 
	} // end basic_settings_page function	
	
	function help_settings_page(){
		$this->settings_page_top();
		include('pages/help.html');
	}	
} // end class
?>
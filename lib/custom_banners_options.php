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
			add_action('admin_menu', array($this, 'add_admin_menu_item'));		
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

	function settings_page_top(){
		$title = "Custom Banners Settings";
		$message = "Custom Banners Settings Updated.";
		
		global $pagenow;
	?>
	<div class="wrap">
		<h2><?php echo $title; ?></h2>
		
		<p class="cb_need_help">Need Help? <a href="http://goldplugins.com/documentation/custom-banners-documentation/" target="_blank">Click here</a> to read instructions, see examples, and find more information on how to add, edit, update, and output your custom banners.</p>
		
		<?php if(!isValidCBKey()): ?>			
			<!-- Begin MailChimp Signup Form -->
			<style type="text/css">
				/* MailChimp Form Embed Code - Slim - 08/17/2011 */
				#mc_embed_signup form {display:block; position:relative; text-align:left; padding:10px 0 10px 3%}
				#mc_embed_signup h2 {font-weight:bold; padding:0; margin:15px 0; font-size:1.4em;}
				#mc_embed_signup input {border:1px solid #999; -webkit-appearance:none;}
				#mc_embed_signup input[type=checkbox]{-webkit-appearance:checkbox;}
				#mc_embed_signup input[type=radio]{-webkit-appearance:radio;}
				#mc_embed_signup input:focus {border-color:#333;}
				#mc_embed_signup .button {clear:both; background-color: #aaa; border: 0 none; border-radius:4px; color: #FFFFFF; cursor: pointer; display: inline-block; font-size:15px; font-weight: bold; height: 32px; line-height: 32px; margin: 0 5px 10px 0; padding:0; text-align: center; text-decoration: none; vertical-align: top; white-space: nowrap; width: auto;}
				#mc_embed_signup .button:hover {background-color:#777;}
				#mc_embed_signup .small-meta {font-size: 11px;}
				#mc_embed_signup .nowrap {white-space:nowrap;}     
				#mc_embed_signup .clear {clear:none; display:inline;}

				#mc_embed_signup h3 { color: #008000; display:block; font-size:19px; padding-bottom:10px; font-weight:bold; margin: 0 0 10px;}
				#mc_embed_signup .explain {
					color: #808080;
					width: 600px;
				}
				#mc_embed_signup label {
					color: #000000;
					display: block;
					font-size: 15px;
					font-weight: bold;
					padding-bottom: 10px;
				}
				#mc_embed_signup input.email {display:block; padding:8px 0; margin:0 4% 10px 0; text-indent:5px; width:58%; min-width:130px;}

				#mc_embed_signup div#mce-responses {float:left; top:-1.4em; padding:0em .5em 0em .5em; overflow:hidden; width:90%;margin: 0 5%; clear: both;}
				#mc_embed_signup div.response {margin:1em 0; padding:1em .5em .5em 0; font-weight:bold; float:left; top:-1.5em; z-index:1; width:80%;}
				#mc_embed_signup #mce-error-response {display:none;}
				#mc_embed_signup #mce-success-response {color:#529214; display:none;}
				#mc_embed_signup label.error {display:block; float:none; width:auto; margin-left:1.05em; text-align:left; padding:.5em 0;}		
				#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
					#mc_embed_signup{    
							background-color: white;
							border: 1px solid #DCDCDC;
							clear: left;
							color: #008000;
							font: 14px Helvetica,Arial,sans-serif;
							margin-top: 10px;
							margin-bottom: 0px;
							max-width: 800px;
							padding: 5px 12px 0px;
				}
				#mc_embed_signup form{padding: 10px}

				#mc_embed_signup .special-offer {
					color: #808080;
					margin: 0;
					padding: 0 0 3px;
					text-transform: uppercase;
				}
				#mc_embed_signup .button {
				  background: #5dd934;
				  background-image: -webkit-linear-gradient(top, #5dd934, #549e18);
				  background-image: -moz-linear-gradient(top, #5dd934, #549e18);
				  background-image: -ms-linear-gradient(top, #5dd934, #549e18);
				  background-image: -o-linear-gradient(top, #5dd934, #549e18);
				  background-image: linear-gradient(to bottom, #5dd934, #549e18);
				  -webkit-border-radius: 5;
				  -moz-border-radius: 5;
				  border-radius: 5px;
				  font-family: Arial;
				  color: #ffffff;
				  font-size: 20px;
				  padding: 10px 20px 10px 20px;
				  line-height: 1.5;
				  height: auto;
				  margin-top: 7px;
				  text-decoration: none;
				}

				#mc_embed_signup .button:hover {
				  background: #65e831;
				  background-image: -webkit-linear-gradient(top, #65e831, #5dd934);
				  background-image: -moz-linear-gradient(top, #65e831, #5dd934);
				  background-image: -ms-linear-gradient(top, #65e831, #5dd934);
				  background-image: -o-linear-gradient(top, #65e831, #5dd934);
				  background-image: linear-gradient(to bottom, #65e831, #5dd934);
				  text-decoration: none;
				}
				#signup_wrapper {
					max-width: 800px;
					margin-bottom: 20px;
				}
				#signup_wrapper .u_to_p
				{
					font-size: 10px;
					margin: 0;
					padding: 2px 0 0 3px;				
				]
			</style>
			<div id="signup_wrapper">
				<div id="mc_embed_signup">
					<form action="http://illuminatikarate.us2.list-manage.com/subscribe/post?u=403e206455845b3b4bd0c08dc&amp;id=27d2c9ee87" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						<p class="special-offer">Special Offer:</p>
						<h3>Subscribe to our newsletter now, and we'll give you a discount on Custom Banners Pro!</h3>
						<label for="mce-EMAIL">Your Email:</label>
						<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
						<!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
						<div style="position: absolute; left: -5000px;"><input type="text" name="b_403e206455845b3b4bd0c08dc_6ad78db648" tabindex="-1" value=""></div>
						<div class="clear"><input type="submit" value="Subscribe Now" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
						<p class="explain"><strong>What To Expect:</strong> <br/> As soon as you've confirmed your subscription, you'll receive a coupon code for a big discount on Custom Banners Pro. After that, you'll receive about one email from us each month, jam-packed with special offers and tips for getting the most out of WordPress. Of course, you can unsubscribe at any time.</p>
					</form>
				</div>
				<p class="u_to_p"><a href="http://goldplugins.com/our-plugins/custom-banners/">Upgrade to Custom Banners Pro now</a> to remove banners like this one.</p>
			</div>
			<!--End mc_embed_signup-->
		<?php endif; ?>
		
		<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') : ?>
		<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
		<?php endif;
		
		$this->get_and_output_current_tab($pagenow);
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
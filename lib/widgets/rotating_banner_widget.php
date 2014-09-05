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
along with Custom Banners.  If not, see <http://www.gnu.org/licenses/>.

Shout out to http://www.makeuseof.com/tag/how-to-create-wordpress-widgets/ for the help
*/

class rotatingBannerWidget extends WP_Widget
{
	function rotatingBannerWidget(){
		$widget_ops = array('classname' => 'rotatingBannerWidget', 'description' => 'Displays a rotating banner.' );
		$this->WP_Widget('rotatingBannerWidget', 'Rotating Banner Widget', $widget_ops);
	}

	function form($instance){
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'num_banners' => null, 'timer' => '4000', 'caption_position' => 'bottom', 'use_image_tag' => false, 'transition' => 'none', 'group' => '', 'show_pager_icons' => false) );
		$title = $instance['title'];
		$num_banners = $instance['num_banners'];
		$transition = $instance['transition'];
		$group = $instance['group'];
		$caption_position = $instance['caption_position'];
		$use_image_tag = $instance['use_image_tag'];
		$show_pager_icons = $instance['show_pager_icons'];
		$timer = $instance['timer'];
		
		if(!isValidCBKey()){
			echo '<p><a href="http://goldplugins.com/our-plugins/custom-banners/" target="_blank">Upgrade</a> to Custom Banners Pro today to unlock this widget and more cool features!</p>';
		}		
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Widget Title:</label><input <?php if(!isValidCBKey()):?> disabled="DISABLED" <?php endif;?> class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
			
			<p><label for="<?php echo $this->get_field_id('num_banners'); ?>">Number of Banners to Use:</label><input <?php if(!isValidCBKey()):?> disabled="DISABLED" <?php endif;?> class="widefat" id="<?php echo $this->get_field_id('num_banners'); ?>" name="<?php echo $this->get_field_name('num_banners'); ?>" type="text" value="<?php echo esc_attr($num_banners); ?>" /></p>
			
			<p><label for="<?php echo $this->get_field_id('group'); ?>">Banner Group:</label>
			<select <?php if(!isValidCBKey()):?> disabled="DISABLED" <?php endif;?> id="<?php echo $this->get_field_id('group'); ?>" name="<?php echo $this->get_field_name('group'); ?>">			
				<?php
				$categories = get_terms('banner_groups'); 
				foreach($categories as $category):				
				?>
				<option value="<?php echo $category->slug; ?>" <?php if($group == $category->slug): ?> selected="SELECTED" <?php endif; ?>><?php echo $category->name; ?></option>
				<?php endforeach; ?>
				<option value="" <?php if($group == ""): ?> selected="SELECTED" <?php endif; ?>>All Categories</option>
			</select></p>
						
			<p><label for="<?php echo $this->get_field_id('transition'); ?>">Transition:</label>
			<select <?php if(!isValidCBKey()):?> disabled="DISABLED" <?php endif;?> id="<?php echo $this->get_field_id('transition'); ?>" name="<?php echo $this->get_field_name('transition'); ?>">
				<option value="fadeIn"  <?php if($transition == "fadeIn"): ?> selected="SELECTED" <?php endif; ?>>Fade In</option>
				<option value="fadeOut"  <?php if($transition == "fadeOut"): ?> selected="SELECTED" <?php endif; ?>>Fade Out</option>
				<option value="scrollHorz"  <?php if($transition == "scrollHorz"): ?> selected="SELECTED" <?php endif; ?>>Horizontal Scroll</option>
				<option value="scrollVert"  <?php if($transition == "scrollVert"): ?> selected="SELECTED" <?php endif; ?>>Vertical Scroll</option>
				<option value="shuffle"  <?php if($transition == "shuffle"): ?> selected="SELECTED" <?php endif; ?>>Shuffle</option>
				<option value="carousel"  <?php if($transition == "carousel"): ?> selected="SELECTED" <?php endif; ?>>Carousel</option>
				<option value="flipHorz"  <?php if($transition == "flipHorz"): ?> selected="SELECTED" <?php endif; ?>>Horizontal Flip</option>
				<option value="flipVert"  <?php if($transition == "flipVert"): ?> selected="SELECTED" <?php endif; ?>>Vertical Flip</option>
				<option value="tileSlide"  <?php if($transition == "tileSlide"): ?> selected="SELECTED" <?php endif; ?>>Tile Slide</option>
			</select></p>
						
			<p><label for="<?php echo $this->get_field_id('timer'); ?>">Time Between Transitions:</label>
			<select <?php if(!isValidCBKey()):?> disabled="DISABLED" <?php endif;?> id="<?php echo $this->get_field_id('timer'); ?>" name="<?php echo $this->get_field_name('timer'); ?>">
				<option value="1000"  <?php if($timer == "1000"): ?> selected="SELECTED" <?php endif; ?>>1 second</option>
				<option value="2000"  <?php if($timer == "2000"): ?> selected="SELECTED" <?php endif; ?>>2 seconds</option>
				<option value="3000"  <?php if($timer == "3000"): ?> selected="SELECTED" <?php endif; ?>>3 seconds</option>
				<option value="4000"  <?php if($timer == "4000"): ?> selected="SELECTED" <?php endif; ?>>4 seconds</option>
				<option value="5000"  <?php if($timer == "5000"): ?> selected="SELECTED" <?php endif; ?>>5 seconds</option>
				<option value="6000"  <?php if($timer == "6000"): ?> selected="SELECTED" <?php endif; ?>>6 seconds</option>
				<option value="7000"  <?php if($timer == "7000"): ?> selected="SELECTED" <?php endif; ?>>7 seconds</option>
				<option value="8000"  <?php if($timer == "8000"): ?> selected="SELECTED" <?php endif; ?>>8 seconds</option>
				<option value="9000"  <?php if($timer == "9000"): ?> selected="SELECTED" <?php endif; ?>>9 seconds</option>
			</select></p>
			
			<p><label for="<?php echo $this->get_field_id('caption_position'); ?>">Caption Position:</label>
			<select <?php if(!isValidCBKey()):?> disabled="DISABLED" <?php endif;?> id="<?php echo $this->get_field_id('caption_position'); ?>" name="<?php echo $this->get_field_name('caption_position'); ?>">
				<option value="left"  <?php if($caption_position == "left"): ?> selected="SELECTED" <?php endif; ?>>Left</option>
				<option value="right"  <?php if($caption_position == "right"): ?> selected="SELECTED" <?php endif; ?>>Right</option>
				<option value="top"  <?php if($caption_position == "top"): ?> selected="SELECTED" <?php endif; ?>>Top</option>
				<option value="bottom"  <?php if($caption_position == "bottom"): ?> selected="SELECTED" <?php endif; ?>>Bottom</option>
			</select></p>
			
			<p><label for="<?php echo $this->get_field_id('use_image_tag'); ?>">Use Image Tag Instead of Background Image: </label><input <?php if(!isValidCBKey()):?> disabled="DISABLED" <?php endif;?> class="widefat" id="<?php echo $this->get_field_id('use_image_tag'); ?>" name="<?php echo $this->get_field_name('use_image_tag'); ?>" type="checkbox" value="1" <?php if($use_image_tag){ ?>checked="CHECKED"<?php } ?>/></p>
			
			<p><label for="<?php echo $this->get_field_id('show_pager_icons'); ?>">Show Pager Icons: </label><input <?php if(!isValidCBKey()):?> disabled="DISABLED" <?php endif;?> class="widefat" id="<?php echo $this->get_field_id('show_pager_icons'); ?>" name="<?php echo $this->get_field_name('show_pager_icons'); ?>" type="checkbox" value="1" <?php if($show_pager_icons){ ?>checked="CHECKED"<?php } ?>/></p>
		<?php
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['num_banners'] = $new_instance['num_banners'];
		$instance['caption_position'] = $new_instance['caption_position'];
		$instance['use_image_tag'] = $new_instance['use_image_tag'];
		$instance['show_pager_icons'] = $new_instance['show_pager_icons'];
		$instance['transition'] = $new_instance['transition'];
		$instance['group'] = $new_instance['group'];
		$instance['timer'] = $new_instance['timer'];
		return $instance;
	}

	function widget($args, $instance){
		if(isValidCBKey()){
			global $ebp;
			
			//defaults
			$atts = array(	'id' => '',
							'group' => '',
							'caption_position' => 'bottom',
							'transition' => 'none',
							'count' => 1,
							'timer' => 4000,
							'use_image_tag' => false,
							'hide' => false,
							'show_pager_icons' => false);
			
			extract($args, EXTR_SKIP);

			echo $before_widget;
			
			$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
			$atts['caption_position'] = empty($instance['caption_position']) ? null : $instance['caption_position'];
			$atts['use_image_tag'] = empty($instance['use_image_tag']) ? null : $instance['use_image_tag'];
			$atts['show_pager_icons'] = empty($instance['show_pager_icons']) ? null : $instance['show_pager_icons'];
			$atts['count'] = empty($instance['num_banners']) ? '1' : $instance['num_banners'];
			$atts['transition'] = empty($instance['transition']) ? '1' : $instance['transition'];
			$atts['group'] = empty($instance['group']) ? '' : $instance['group'];
			$atts['timer'] = empty($instance['timer']) ? '4000' : $instance['timer'];
			
			if (!empty($title)){
				echo $before_title . $title . $after_title;;
			}
				
			echo $ebp->banner_shortcode($atts);

			echo $after_widget;
		}
	}
}
?>
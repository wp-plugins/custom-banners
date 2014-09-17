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

class singleBannerWidget extends WP_Widget
{
	function singleBannerWidget(){
		$widget_ops = array('classname' => 'singleBannerWidget', 'description' => 'Displays a specified banner.' );
		$this->WP_Widget('singleBannerWidget', 'Custom Banner Widget', $widget_ops);
	}

	function form($instance){
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'bannerid' => null, 'caption_position' => 'bottom', 'use_image_tag' => false) );
		$title = $instance['title'];
		$bannerid = $instance['bannerid'];
		$caption_position = $instance['caption_position'];
		$use_image_tag = $instance['use_image_tag'];
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>">Widget Title:</label><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
			<?php
				$args = array( 'post_type' => 'banner', 'posts_per_page' => -1);
				$banners = get_posts($args);
			?>
				<p><label for="<?php echo $this->get_field_id('bannerid'); ?>">Banner to Display: </label>
				<select id="<?php echo $this->get_field_id('bannerid'); ?>" name="<?php echo $this->get_field_name('bannerid'); ?>">
				<?php if($banners) : foreach ( $banners as $banner  ) : ?>
					<option value="<?php echo $banner->ID; ?>"  <?php if($bannerid == $banner->ID): ?> selected="SELECTED" <?php endif; ?>><?php echo $banner->post_title; ?></option>
				<?php endforeach; endif;?>
				 </select></p>
				 <p><label for="<?php echo $this->get_field_id('caption_position'); ?>">Caption Position: </label>
				 <select id="<?php echo $this->get_field_id('caption_position'); ?>" name="<?php echo $this->get_field_name('caption_position'); ?>">
					<option value="left"  <?php if($caption_position == "left"): ?> selected="SELECTED" <?php endif; ?>>Left</option>
					<option value="right"  <?php if($caption_position == "right"): ?> selected="SELECTED" <?php endif; ?>>Right</option>
					<option value="top"  <?php if($caption_position == "top"): ?> selected="SELECTED" <?php endif; ?>>Top</option>
					<option value="bottom"  <?php if($caption_position == "bottom"): ?> selected="SELECTED" <?php endif; ?>>Bottom</option>
				 </select></p>
				<p><label for="<?php echo $this->get_field_id('use_image_tag'); ?>">Use Image Tag: </label><input class="widefat" id="<?php echo $this->get_field_id('use_image_tag'); ?>" name="<?php echo $this->get_field_name('use_image_tag'); ?>" type="checkbox" value="1" <?php if($use_image_tag){ ?>checked="CHECKED"<?php } ?>/></p>
			<?php
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['bannerid'] = $new_instance['bannerid'];
		$instance['caption_position'] = $new_instance['caption_position'];
		$instance['use_image_tag'] = $new_instance['use_image_tag'];
		return $instance;
	}

	function widget($args, $instance){
		global $ebp;
		
		//defaults
		$atts = array(	'id' => '',
						'group' => '',
						'caption_position' => 'bottom',
						'transition' => 'none',
						'count' => 1,
						'timer' => 4000,
						'use_image_tag' => false,
						'hide' => false);

		
		extract($args, EXTR_SKIP);

		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$bannerid = empty($instance['bannerid']) ? null : $instance['bannerid'];
		$atts['caption_position'] = empty($instance['caption_position']) ? null : $instance['caption_position'];
		$atts['use_image_tag'] = empty($instance['use_image_tag']) ? null : $instance['use_image_tag'];
		if (!empty($title)){
			echo $before_title . $title . $after_title;;
		}
			
		$banner = get_post($bannerid);
		echo $ebp->buildBannerHTML($banner, $bannerid, $atts);

		echo $after_widget;
	} 
}
?>
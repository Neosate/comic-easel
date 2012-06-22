<?php
/*
Widget Name: Comic Blog Post Widget
Widget URI: http://comiceasel.com
Description: Display's the comic's blog post.
Author: Philip M. Hofer (Frumph)
Author URI: http://frumph.net/
Version: 1.00
*/

function ceo_display_comic_small_blog_post($instance) {
	global $post;
	if ($instance['showtitle']) { echo "<h3 class=\"comic-post-widget-title\">".get_the_title()."</h3>\r\n"; }
	if ($instance['showdate']) { echo "<div class=\"comic-post-widget-date\">".get_the_time(get_option('time_format'))."</div>\r\n"; }
	the_content();
	if ($instance['showcommentlink'] && ($post->comment_status == 'open') && !is_singular()) { ?>
		<div class="comment-link">
		<?php comments_popup_link('<span class="comment-balloon comment-balloon-empty">&nbsp;</span>'.__('Comment&nbsp;','easel'), '<span class="comment-balloon">1</span> '.__('Comment ','easel'), '<span class="comment-balloon">%</span> '.__('Comments ','easel')); ?>
		</div>
		<?php
	}
}

class ceo_comic_blog_post_widget extends WP_Widget {
	
	function ceo_comic_blog_post_widget($skip_widget_init = false) {
		if (!$skip_widget_init) {
			$widget_ops = array('classname' => __CLASS__, 'description' => __('Displays the comic blog post for the day, best used when the comic blog post is disabled and you want to put this into a sidebar.','comiceasel') );
			$this->WP_Widget(__CLASS__, __('Comic Easel - Blog Post','comiceasel'), $widget_ops);
		}
	}
	
	function widget($args, $instance) {
		global $post, $wp_query;
		if (!is_home() && $instance['onlyhome']) return;
		if (is_page() || is_archive() || is_search()) return;
		extract($args, EXTR_SKIP);
		if (is_home()) {
			ceo_Protect();
			$args = array(
					'showposts' => 1,
					'posts_per_page' => 1,
					'post_type' => 'comic'
					);
			$posts = get_posts($args);
			foreach ($posts as $post) {
				setup_postdata($post);
				if (!($instance['hidecontent'] && empty($post->post_content))) {
					echo $before_widget;
					$temp_query = $wp_query->is_single;
					$wp_query->is_single = true;
					$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
					ceo_display_comic_small_blog_post($instance);
					$wp_query->is_single = $temp_query;
					echo $after_widget;
				}
			}
			ceo_UnProtect();
		} elseif ($post->post_type == 'comic') {
			setup_postdata($post);
			if ( !( $instance['hidecontent'] && empty($post->post_content) ) && ($post->post_type == 'comic') ) {
				echo $before_widget;
				$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
				if (!empty($title)) { echo "<div class=\"comic-post-widget-header\">".$title."</div>\r\n"; }
				ceo_display_comic_small_blog_post($instance);
				echo $after_widget;
			}
		}
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['onlyhome'] = (bool)( $new_instance['onlyhome'] == 1 ? true : false );
		$instance['showtitle'] = (bool)( $new_instance['showtitle'] == 1 ? true : false );		
		$instance['showdate'] = (bool)( $new_instance['showdate'] == 1 ? true : false );
		$instance['showcommentlink'] = (bool)( $new_instance['showcommentlink'] == 1 ? true : false );
		$instance['hidecontent'] = (bool)( $new_instance['hidecontent'] == 1 ? true : false );
		return $instance;
	}
	
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'onlyhome' => false, 'showtitle' => false, 'showdate' => false, 'showcommentlink' => false, 'hidecontent' => false  ) );
		$title = strip_tags($instance['title']);
		$onlyhome = $instance['onlyhome'];
		$showtitle = $instance['showtitle'];
		$showdate = $instance['showdate'];
		$showcommentlink = $instance['showcommentlink'];
		$hidecontent = $instance['hidecontent'];
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">Heading:<br /><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('onlyhome'); ?>"><input id="<?php echo $this->get_field_id('onlyhome'); ?>" name="<?php echo $this->get_field_name('onlyhome'); ?>" type="checkbox" value="1" <?php checked(true, $onlyhome); ?> /> Display only on the home page?</label></p>
		<p><label for="<?php echo $this->get_field_id('showtitle'); ?>"><input id="<?php echo $this->get_field_id('showtitle'); ?>" name="<?php echo $this->get_field_name('showtitle'); ?>" type="checkbox" value="1" <?php checked(true, $showtitle); ?> /> Show the title of the post?</label></p>
		<p><label for="<?php echo $this->get_field_id('showdate'); ?>"><input id="<?php echo $this->get_field_id('showdate'); ?>" name="<?php echo $this->get_field_name('showdate'); ?>" type="checkbox" value="1" <?php checked(true, $showdate); ?> /> Show the date of the post?</label></p>			
		<p><label for="<?php echo $this->get_field_id('showcommentlink'); ?>"><input id="<?php echo $this->get_field_id('showcommentlink'); ?>" name="<?php echo $this->get_field_name('showcommentlink'); ?>" type="checkbox" value="1" <?php checked(true, $showcommentlink); ?> /> Show the comment link to the post?</label></p>			
		<p><label for="<?php echo $this->get_field_id('hidecontent'); ?>"><input id="<?php echo $this->get_field_id('hidecontent'); ?>" name="<?php echo $this->get_field_name('hidecontent'); ?>" type="checkbox" value="1" <?php checked(true, $hidecontent); ?> /> Hide the display of the widget if there's no content?</label></p>			
		
		<?php
	}
}

add_action( 'widgets_init', create_function('', 'return register_widget("ceo_comic_blog_post_widget");') );
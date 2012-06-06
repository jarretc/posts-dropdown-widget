<?php
/*
Plugin Name: Posts Dropdown Widget
Description: Displays a list of your posts in a dropdown list.
Author: Jarret Cade
Author URI: http://jarretcade.com
Version: 1.0
License: GPLv2 or later

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

add_action( 'widgets_init', 'pdw_widget_register' );

function pdw_widget_register() {
	register_widget( 'PDW_Widget' );
}

class PDW_Widget extends WP_Widget {

	function PDW_Widget() {
		$widget_ops = array(
			'classname' => 'pdw_widget_class',
			'description' => 'Display your posts in a dropdown list.'
			);
		$this->WP_Widget( 'pdw_widget_class', 'Posts Dropdown', $widget_ops );
	}

	function form( $instance ) {
		$defaults = array( 'title' => 'Posts Dropdown', 'number_posts' => 10 );
		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = $instance['title'];
		$postsnum = $instance['number_posts'];
		$sortby = $instance['sortby'];
		?>
			<p>Title: <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
			<p>Number of posts: <input size="1" name="<?php echo $this->get_field_name( 'number_posts' ); ?>" type="text" value="<?php echo $postsnum; ?>" /> -1 for all posts</p>
			<p>Sort by: <select name="<?php echo $this->get_field_name( 'sortby' ); ?>">
							<option value="ASC" <?php selected( $sortby, 'ASC' ); ?>>Ascending</option>
							<option value="DESC" <?php selected( $sortby, 'DESC' ); ?>>Descending</option>
						</select></p>
	<?php }

	function update( $new, $old ) {
		$settings = $old;
		$settings['title'] = strip_tags( $new['title'] );
		$settings['number_posts'] = (int) $new['number_posts'];
		$settings['sortby'] = $new['sortby'];
		return $settings;
	}

	function widget( $args, $instance ) {
		extract( $args );

		echo $before_widget;
		$title = apply_filters( 'widget_title', $instance['title'] );
		$postsnum = $instance['number_posts'];
		$sortby = $instance['sortby'];

		if ( !empty( $title) ) {
			echo $before_title . $title . $after_title;
		};

		global $post;
		$args = array( 'numberposts' => (int) $postsnum, 'order' => $sortby );
		$posts = get_posts( $args );
		echo '<select onchange="document.location.href=this.options[this.selectedIndex].value">';
		$num = 1;
		foreach ( $posts as $post ) : setup_postdata($post);
			echo '<option value="' . get_permalink() . '">';
			echo ' - ' . $num . the_title();
			echo '</option>';
			$num++;
		endforeach;
		echo '</select>';
		echo $after_widget;

		wp_reset_postdata();
	}
	
}
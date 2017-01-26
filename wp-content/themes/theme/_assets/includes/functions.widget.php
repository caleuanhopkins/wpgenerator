<?php

class themeInitials_AuthorInitials_post_widgets extends WP_Widget {

	public function __construct() {
		parent::__construct('themeInitials_AuthorInitials_post_widgets', __('Posts in Sidebar', 'text_domain'), 
			array( 'description' => __( 'Select which type of posts to appear on the sidebar', 'text_domain' ), ) 
		);
	}

	public function widget( $args, $instance ) {

		apply_filters( 'widget_title', $instance['title'] );

		if($instance['limit']){
			$limit = $instance['limit'];
		}else{
			$limit = 4;
		}

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => $limit,
			'order'          => 'DESC',
			'orderby'        => 'meta_value_num'
	    );

		if($instance['posttype'] == 'standard'){
			$post_type = $instance['posttype'];
			$post_formats = get_theme_support( 'post-formats' );
			$args['tax_query'] = array(
				array(
			        'taxonomy' => 'post_format',
			        'field'    => 'slug',
			        'terms'    => array(),
			        'operator' => 'NOT IN'
				)
			);
			foreach($post_formats[0] as $vals):
				$args['tax_query'][0]['terms'][] = 'post-format-'.$vals;
			endforeach;
		}else if($instance['posttype']){
			$post_type = $instance['posttype'];
			$args['tax_query'] = array(
				array(
			        'taxonomy' => 'post_format',
			        'field'    => 'slug',
			        'terms'    => array( 'post-format-'.$post_type )										
				)
			);
		}

		$posts = new WP_Query($args);

		include('templates/front/sidebar-posts.php');

	   	wp_reset_postdata();
	    wp_reset_query();	
	}
		
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'New title', 'text_domain' );
		}

		if ( isset( $instance[ 'posttype' ] ) ) {
			$selectedType = $instance[ 'posttype' ];
		}	

		if ( isset( $instance[ 'limit' ] ) ) {
			$limit = $instance[ 'limit' ];
		}else{
			$limit = 4;
		}

		$post_formats = get_theme_support( 'post-formats' );

		include('templates/admin/post_admin_widget.php');
	}
			
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['posttype'] = ( ! empty( $new_instance['posttype'] ) ) ? strip_tags( $new_instance['posttype'] ) : '';
		$instance['limit'] = ( ! empty( $new_instance['limit'] ) ) ? strip_tags( $new_instance['limit'] ) : '';

		return $instance;
	}
}
?>
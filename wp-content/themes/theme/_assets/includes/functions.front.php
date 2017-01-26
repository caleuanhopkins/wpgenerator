<?php

class Functions_front extends Functions{

	private $bitlytoken;
	private $sliderIndex;
	private $popular_count_key;
	
	public function __construct(){
		parent::__construct();
		$this->themeInitials_AuthorInitials_front_init();
		$this->sliderIndex=0;
		$this->popular_count_key = 'themeInitials_AuthorInitials_post_count';
	}
	
	private function themeInitials_AuthorInitials_front_init(){
		add_filter('excerpt_length', array($this, $this->class_prefix.'excerpt_length'), 999 );
		add_action('wp_head', array($this, $this->class_prefix.'add_ajaxUrl'));
	}

	public function themeInitials_AuthorInitials_add_ajaxUrl(){
		echo "<script type='text/javascript'>var ajaxurl = '".admin_url('admin-ajax.php')."';</script>";
	}

	public function themeInitials_AuthorInitials_excerpt_length( $length ) {
		return 8;
	}

	public function themeInitials_AuthorInitials_time_ago( $type = 'post' ) {
		$d = 'comment' == $type ? 'get_comment_time' : 'get_post_time';
		return human_time_diff($d('U'), current_time('timestamp')) . " " . __('ago');
	}

	public function themeInitials_AuthorInitials_get_post_by_category($category, $limit){
		$posts = new WP_Query(array(
			'post_type'      => 'post',
			'category_name'  => $category,
			'posts_per_page' => $limit,
			'order'          => 'DESC',
			'orderby'        => 'date'
	    ));
	    return $posts;
	   	wp_reset_postdata();
	    wp_reset_query();
	}

}

$themeInitials_AuthorInitials_functions = new Functions_front;

?>
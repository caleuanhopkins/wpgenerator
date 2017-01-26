<?php

class Functions {

	public $class_prefix = '';
	private $rel_plugin_url;
	private $rel_assets_url;
	private $home_url;
	private $get_theme_name;
	private $theme_path;

	public function __construct(){
		$this->class_prefix = 'themeInitials_AuthorInitials_';
		$this->fileName = basename(__FILE__);
		$this->init();
	}
	
	private function init(){
		if(!empty($_GET['export'])){
			$this->themeInitials_AuthorInitials_crms_export();
			die();
		}
		add_action( 'after_switch_theme', array($this, $this->class_prefix.'firstRunRequirements' ));
		//$dbCheck = get_option('themeInitials_AuthorInitials_tiers_installed');
		//$dbCheck = true;
		if(!$dbCheck){
			$this->themeInitials_AuthorInitials_firstRunRequirements();
		}
		$this->themeInitials_AuthorInitials_theme_actions();
		$this->themeInitials_AuthorInitials_setup_rewrites();
		$this->themeInitials_AuthorInitials_tidy_wp();
		$this->themeInitials_AuthorInitials_hide_wp();
		$this->themeInitials_AuthorInitials_ajaxActions();
	}
	
	private function themeInitials_AuthorInitials_theme_actions(){
		add_action('init', array($this, $this->class_prefix.'global_functions' ));
		add_action('after_setup_theme', array($this, $this->class_prefix.'setup_theme' ));
		add_action('admin_init', array($this, $this->class_prefix.'flush_rewrites'));
		add_filter('excerpt_length', array($this, $this->class_prefix.'excerpt_length'), 999 );
		add_action('rss2_item', array($this, $this->class_prefix.'featured_img_rss'));
		add_action('widgets_init', array($this, $this->class_prefix.'widgets_init'));
		add_action('admin_menu',array($this, $this->class_prefix.'admin_panels'));
		add_shortcode( 'insert_gallery', array($this, $this->class_prefix.'gallery_shortcode' ));
		//add_action('rss2_item', array($this, $this->class_prefix.'excerpt_rss'));
	}

	public function themeInitials_AuthorInitials_firstRunRequirements(){
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}custom_theme_table` (
		  id mediumint(40) NOT NULL AUTO_INCREMENT,
		  name longtext NOT NULL,
		  UNIQUE KEY id (id)
		);";
		dbDelta( $sql );
		update_option( "themeInitials_AuthorInitials_tiers_installed", '1');
	}

	public function themeInitials_AuthorInitials_featured_img_rss(){
		global $post;
		if ( has_post_thumbnail( $post->ID ) ){
			echo '<enclosure url="'.esc_url( home_url( '/' ) ).wp_get_attachment_url( get_post_thumbnail_id($post->ID) ).'" length="0" type="'.get_post_mime_type( get_post_thumbnail_id($post->ID) ).'" />
			';
		}
		//return $content;
	}

	public function themeInitials_AuthorInitials_admin_panels(){
		global $submenu;
		$page = add_menu_page('Example Menu Panel', 'Example Menu Panel', 'administrator' ,$this->fileName.'-menu-panel', array($this, 'themeInitials_AuthorInitials_example_manu_panel'));
		$subpage = add_submenu_page( $this->fileName.'-menu-panel', 'Example Sub-menu Panel', 'Example Sub-menu Panel', 'administrator', $this->fileName.'-example-sub-menu-panel', array($this, 'themeInitials_AuthorInitials_example_sub_menu_panel'));
	}

	public function themeInitials_AuthorInitials_excerpt_rss(){
		global $post;
		if ( get_the_excerpt() ){
			echo '<EXCERPT>'.get_the_excerpt().'</EXCERPT>
			';
		}
		//return $content;
	}


	public function themeInitials_AuthorInitials_excerpt_length( $length ) {
		return 1;
	}


	public function themeInitials_AuthorInitials_global_functions(){
		add_theme_support( 'menus' );
		register_nav_menus( array(
			'main_menu' => 'Main Menu'
		) );
	}

	public function themeInitials_AuthorInitials_setup_theme(){
		if ( function_exists( 'add_theme_support' ) ) {
			add_theme_support( 'post-thumbnails' );
			add_image_size('post-thumbnail', 'auto', 120, array( 'center', 'center' ));

			add_theme_support( 'post-formats', array(
				'video',
				'gallery',
			) );
		}
	}

	public function themeInitials_AuthorInitials_widgets_init(){
		if ( function_exists( 'register_sidebar' )){
			register_sidebar(array(
				'name'          => "Homepage Sidebar",
				'id'            => "homepagesidebar",
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			));
		}

		if ( function_exists( 'register_widget' )){
			register_widget( $this->class_prefix.'post_widgets' );
		}
	}

	private function themeInitials_AuthorInitials_setup_rewrites(){
		$theme_name = explode('/themes/', get_template_directory());
		$this->theme_name = next($theme_name);
		$this->home_url = array(home_url('/', 'http'), home_url('/', 'https'));
		$this->rel_plugin_url = $this->themeInitials_AuthorInitials_root_relative_url(str_replace($this->home_url, '', plugins_url()));
		$this->rel_assets_url = $this->themeInitials_AuthorInitials_root_relative_url(str_replace($this->home_url, '', content_url()));
		$this->theme_path = $this->rel_assets_url.'/themes/'.$this->theme_name.'/';
	}
	
	private function themeInitials_AuthorInitials_tidy_wp(){
		remove_action('wp_head', 'feed_links', 2);
		remove_action('wp_head', 'feed_links_extra', 3);
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'index_rel_link');
		remove_action('wp_head', 'parent_post_rel_link', 10, 0);
		remove_action('wp_head', 'start_post_rel_link', 10, 0);
		remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
		add_action('wp_head', array($this, $this->class_prefix.'remove_recent_comments_style'), 1);
		add_filter('gallery_style', array($this,$this->class_prefix.'gallery_style'));
	}
	
	public function themeInitials_AuthorInitials_remove_recent_comments_style() {
	  	global $wp_widget_factory;
	  	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
			remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
	  	}
	}
	
	public function themeInitials_AuthorInitials_gallery_style($css) {
  		return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
	}
	
	public function themeInitials_AuthorInitials_add_rewrites($content) {
	  	global $wp_rewrite;
	  	$themeInitials_AuthorInitials_new_non_wp_rules = array(
			'css/(.*)'      => 'wp-content/themes/'. $this->theme_name . '/_assets/css/$1',
			'js/(.*)'       => 'wp-content/themes/'. $this->theme_name . '/_assets/js/$1',
			'imgs/(.*)'      => 'wp-content/themes/'. $this->theme_name . '/_assets/imgs/$1',
			'plugins/(.*)'  => 'wp-content/plugins/$1'
	  	);
	  	$wp_rewrite->non_wp_rules += $themeInitials_AuthorInitials_new_non_wp_rules;
	}

	public function themeInitials_AuthorInitials_clean_urls($content){
		$content = $this->themeInitials_AuthorInitials_root_relative_url($content).'/';
		if (strpos($content, $this->rel_plugin_url) !== false) {
			return str_replace($this->rel_plugin_url, '/plugins', $content);
		} else {
			return str_replace($this->theme_path, '/', $content);
		}
	}
	
	public function themeInitials_AuthorInitials_wp_nav_menu($text) {
		$replace = array(
		  'current-menu-item'     => 'active',
		  'current-menu-parent'   => 'active',
		  'current-menu-ancestor' => 'active',
		  'current_page_item'     => 'active',
		  'current_page_parent'   => 'active',
		  'current_page_ancestor' => 'active',
		);
	
	  	$text = str_replace(array_keys($replace), $replace, $text);
	  	return $text;
	}
	
	public function themeInitials_AuthorInitials_root_relative_url($input) {
		preg_match('|https?://([^/]+)(/.*)|i', $input, $matches);
		if (!isset($matches[1]) || !isset($matches[2])) {
			return $input;
		} elseif (($matches[1] === $_SERVER['SERVER_NAME']) || $matches[1] === $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']) {
			return wp_make_link_relative($input);
		} else {
			return $input;
		}
	}
	
	public function themeInitials_AuthorInitials_fix_duplicate_subfolder_urls($input) {
	  	$output = $this->themeInitials_AuthorInitials_root_relative_url($input);
	  	preg_match_all('!([^/]+)/([^/]+)!', $output, $matches);
	
	  	if (isset($matches[1]) && isset($matches[2])) {
			if ($matches[1][0] === $matches[2][0]) {
		  		$output = substr($output, strlen($matches[1][0]) + 1);
			}
	  	}
	
	  	return $output;
	}
	
	private function themeInitials_AuthorInitials_hide_wp(){

		add_action('generate_rewrite_rules', array($this, $this->class_prefix.'add_rewrites'));
		add_filter('wp_nav_menu', array($this,$this->class_prefix.'wp_nav_menu'));

		if (!is_admin()) {
			$function_titles = array(
				'script_loader_src',
				'style_loader_src' 
			);
			$this->themeInitials_AuthorInitials_add_filter($function_titles, 'fix_duplicate_subfolder_urls');
			$function_titles = array(
				'plugins_url',
				'bloginfo', 
				'stylesheet_directory_uri',
				'template_directory_uri',
				'script_loader_src',
				'style_loader_src',
			);
			$this->themeInitials_AuthorInitials_add_filter($function_titles, 'clean_urls');
			if (!in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
				$function_titles = array(
					'script_loader_src',
					'style_loader_src' 
				);
				$this->themeInitials_AuthorInitials_add_filter($function_titles, 'fix_duplicate_subfolder_urls');
				$function_titles = array(
					'bloginfo_url', 
					'theme_root_uri', 
					'stylesheet_directory_uri', 
					'template_directory_uri', 
					'plugins_url',
					'get_the_permalink',
					'wp_get_attachment_url',
					'the_permalink', 
					'wp_list_pages', 
					'wp_list_categories', 
					'wp_nav_menu', 
					'the_content_more_link', 
					'the_tags', 
					'get_pagenum_link', 
					'get_comment_link', 
					'month_link', 
					'day_link', 
					'year_link', 
					'tag_link', 
					'the_author_posts_link'
				);
				$this->themeInitials_AuthorInitials_add_action($function_titles, 'root_relative_url');
			}
		}	
	}

	private function themeInitials_AuthorInitials_add_action($titles,$function){
		foreach($titles as $title) {
			$full_func = $this->class_prefix.$function;
			add_action($title, array($this, $full_func));
		}
	}

	private function themeInitials_AuthorInitials_add_filter($titles,$function){
		foreach($titles as $title) {
			$full_func = $this->class_prefix.$function;
			add_filter($title, array($this, $full_func));
		}
	}

	public function themeInitials_AuthorInitials_flush_rewrites() {
	 	global $wp_rewrite;
	  	$wp_rewrite->flush_rules();
	}

	private function themeInitials_AuthorInitials_ajaxActions(){
		if(method_exists($this,$this->class_prefix.'')){
			add_action( 'wp_ajax_themeInitials_AuthorInitials_example_admin_ajax_method', array($this ,$this->class_prefix.'example_admin_ajax_method') ); 
			add_action( 'wp_ajax_nopriv_themeInitials_AuthorInitials_example_admin_ajax_method', array($this ,$this->class_prefix.'example_admin_ajax_method') ); 
		}
		if(method_exists($this,$this->class_prefix.'')){
			add_action( 'wp_ajax_themeInitials_AuthorInitials_example_front_ajax_method', array($this ,$this->class_prefix.'example_front_ajax_method') ); 
			add_action( 'wp_ajax_nopriv_themeInitials_AuthorInitials_example_front_ajax_method', array($this ,$this->class_prefix.'example_front_ajax_method') ); 
		}
	}

}

if(!is_admin()){
	require_once('_assets/includes/functions.front.php');
	require_once('_assets/includes/functions.walker.php');
}else{
	require_once('_assets/includes/functions.admin.php');
	require_once('_assets/includes/functions.admin.walker.php');
	require_once('_assets/includes/functions.customTable.php');
}

require_once('_assets/includes/functions.widget.php');

?>
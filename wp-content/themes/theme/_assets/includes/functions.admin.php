<?php

class Functions_admin extends Functions{
	
	public function __construct(){
		parent::__construct();
		$this->themeInitials_AuthorInitials_admin_init();
	}
	
	private function themeInitials_AuthorInitials_admin_init(){
		$this->themeInitials_AuthorInitials_admin_actions();
		$this->themeInitials_AuthorInitials_admin_filters();
	}

	private function themeInitials_AuthorInitials_admin_actions(){
		//add_action( 'save_post', array($this, $this->class_prefix.'save_post_meta'));
		add_action( 'admin_head', array($this, $this->class_prefix.'admin_setup'));
		//add_action( 'init', array(  $this,  $this->class_prefix.'tinymce_video' ) );
		add_action( 'add_meta_boxes_post', array($this, $this->class_prefix.'admin_post_metaboxes'));
		add_action( 'admin_enqueue_scripts', array($this, $this->class_prefix.'load_admin_script' ));
	}

	public function themeInitials_AuthorInitials_tinymce_video(){	
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		if ( get_user_option( 'rich_editing' ) !== 'true' ) {
			return;
		}
	}

	public function themeInitials_AuthorInitials_admin_setup(){
		$post = get_post();
		echo '<script> var ajax_nonce = "'.wp_create_nonce( "example_nonce" ).'"; var post_id = '.(!empty($post->ID)?$post->ID: '0').'</script>';
	}

	public function themeInitials_AuthorInitials_save_post_meta(){
		global $post;
		global $wpdb;
	    /*if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	    if(!empty($_POST['example_field'])) {
	      update_post_meta($post->ID, 'example_field', $_POST['example_field']);
	    } else {
	      delete_post_meta($post->ID, 'example_field');
	    }*/
	}

	public function themeInitials_AuthorInitials_admin_post_metaboxes($post){
		add_meta_box('themeInitials_AuthorInitials_example_metabox', 'Example Metabox Title', array($this, $this->class_prefix.'example_metabox'), 'post', 'normal', 'default');
	}

	public function themeInitials_AuthorInitials_example_metabox(){
		global $post;
		global $wpdb;
		include_once('templates/admin/example_metabox.php');		
	}

	private function themeInitials_AuthorInitials_admin_filters(){
		//add_filter( 'wp_terms_checklist_args', array( $this, $this->class_prefix.'term_radio_checklist' ));
	}

	public function themeInitials_AuthorInitials_term_radio_checklist($args){
		if ( ! empty( $args['taxonomy'] ) && $args['taxonomy'] === 'category' && empty( $args['walker'] ) || is_a( $args['walker'], 'Walker' ) ) {
			if (class_exists( 'Functions_Walker_Category_Radio_Checklist' ) ) {
				$args['walker'] = new Functions_Walker_Category_Radio_Checklist();
			}
		}
		return $args;
	}


	public function themeInitials_AuthorInitials_load_admin_script() {
       	wp_enqueue_script( 'admin-js', get_template_directory_uri() . '/_assets/js/admin/admin.js');
		wp_enqueue_style('admin-styles', get_template_directory_uri().'/_assets/css/admin/admin.css');
	}

	public function themeInitials_AuthorInitials_example_manu_panel(){
		global $wpdb;

		$tableData = array(array('id' => 1, 'one'=> 'one', 'two' => 'a'), array('id' => 2, 'one' =>'two', 'two' => 'b'));
		$theCols = array_keys((array)$tableData[0]);

		$columns = array($theCols[1] => 'Column 1', $theCols[2] => 'Column 2');

		$catCols = array_keys((array)$tableData);

		$crmTable = new Functions_customTable(time());
		$crmTable->itemDelete = false;
		$crmTable->prepare_items($tableData,$columns, NULL, $catCols[1]);

		include_once('templates/admin/example_menu_panel.php');
	}

	public function themeInitials_AuthorInitials_example_sub_menu_panel(){
		global $wpdb;
		include_once('templates/admin/example_sub_menu_panel.php');
	}

	public function themeInitials_AuthorInitials_example_admin_ajax_method(){
		global $wpdb;
		echo json_encode(array('success'=>(bool)true, 'data' => 'hello world'));
		wp_die();
	}

}

$themeInitials_AuthorInitials_functions = new Functions_admin;

?>
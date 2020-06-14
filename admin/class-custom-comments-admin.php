<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       /
 * @since      1.0.0
 *
 * @package    Custom_Comments
 * @subpackage Custom_Comments/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Custom_Comments
 * @subpackage Custom_Comments/admin
 * @author     Amelya <amelya@syneforge.com>
 */
class Custom_Comments_Admin {

	public $comments_object;
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		global $wpdb;
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		register_setting( $this->plugin_name, 'custom_comments' );
		$this->options = get_option( 'custom_comments' );
		$this->wpdb    = $wpdb;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( $this->plugin_name == $_GET['page'] || $_GET['page'] == 'custom_comments_list' ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/custom-comments-admin.css', array(), $this->version, 'all' );

		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( 'custom_comments_list' == $_GET['page'] || $this->plugin_name == $_GET['page'] ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/custom-comments-admin.js', array( 'jquery' ), $this->version, false );

		}
	}

	/**
	 * Add page to admin menu
	 *
	 * @since    1.0.0
	 *
	 * add_action( 'admin_menu', 'add_menu' );
	 */
	public function add_menu() {

		global $admin_page_hooks;
		$parent_menu_name = 'Custom Plugins';
		$parent_menu_slug = 'customplugins-top-slug';

		if ( empty( $admin_page_hooks[ $parent_menu_slug ] ) ) {
			add_menu_page(
				$parent_menu_name,
				$parent_menu_name,
				'manage_options',
				$parent_menu_slug,
				array( $this, 'render_plugin_page' )
			);
		}
		add_submenu_page(
			$parent_menu_slug,
			'Custom Comments',
			'Custom Comments',
			'manage_options',
			$this->plugin_name,
			array( $this, 'custom_comments_page_options' )
		);

		$notification_count = $this->get_unapproved_comments_count();
		$hook               = add_menu_page(
			'Comments',
			$notification_count ? sprintf( 'Comments <span class="awaiting-mod">%d</span>', $notification_count ) : 'Comments',
			'manage_options',
			'custom_comments_list',
			[ $this, 'plugin_settings_page' ],
			'dashicons-admin-comments',
			'24'
		);

		add_action( "load-$hook", [ $this, 'screen_option' ] );

	}


	public function get_unapproved_comments_count() {
		$sql = 'SELECT(SELECT COUNT(*) FROM ' . $this->wpdb->prefix . 'comments where ' . $this->wpdb->prefix . 'comments.comment_approved ="0")+(SELECT COUNT(*) from ' . $this->wpdb->prefix . 'term_comments where ' . $this->wpdb->prefix . 'term_comments.comment_approved = "0" )AS count';

		return $this->wpdb->get_var( $sql );
	}

	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		if ( $_GET['custom_comment'] && $_GET['action'] == 'edit' ) {
			require plugin_dir_path( __FILE__ ) . 'partials/custom-comments-edit-comment-template.php';
		} elseif ( $_GET['custom_comment'] && $_GET['action'] == 'delete' ) {
			// $this->update_comment($data);
			$id = $_GET['custom_comment'] . '_' . $_GET['type'];
			$this->delete_comment( $id );
		} elseif ( $_GET['custom_comment'] && $_GET['action'] == 'approve' ) {
			$data = array( 'comment_approved' => '1' );
			$this->update_comment( $data );
		} elseif ( $_GET['custom_comment'] && $_GET['action'] == 'unapprove' ) {
			$data = array( 'comment_approved' => '0' );
			$this->update_comment( $data );
		} elseif ( $_POST['action'] == 'edited_custom_comment' ) {
			$this->update_comment( $_POST );
		} elseif ( $_GET['custom_comment'] && $_GET['action'] == 'spam' ) {
			$data = array( 'comment_approved' => 'spam' );
			$this->update_comment( $data );
		} elseif ( $_GET['custom_comment'] && $_GET['action'] == 'trash' ) {
			$data = array( 'comment_approved' => 'trash' );
			$this->update_comment( $data );
		} elseif ( $_GET['action'] == 'move_to_trash' ) {
			$data = array( 'comment_approved' => 'trash' );
			$this->update_comment( $data );
			wp_redirect( admin_url( '/' ) . 'admin.php?page=custom_comments_list' );
		} else {
			require plugin_dir_path( __FILE__ ) . 'partials/custom-comments-admin-panel-template.php';
		}
	}

	/**
	 * Delete a comment record.
	 *
	 * @param int $id customer ID
	 *
	 * @return bool
	 */
	public function delete_comment( $id ) {
		$parse_data  = explode( '_', $id );
		$comment_id  = $parse_data[0];
		$type        = $parse_data[1];
		$definitions = array(
			'table'    => $type == 'post' ? 'comments' : 'term_comments',
			'ID_field' => 'comment_ID',
		);
		$this->wpdb->delete(
			"{$this->wpdb->prefix}" . $definitions['table'],
			[ "{$definitions['ID_field']}" => $comment_id ],
			[ '%d' ]
		);

		return wp_redirect( wp_get_referer() );
	}

	public function update_comment( $data = null ) {
		if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
			if ( $_GET['type'] == 'term' ) {
				$table = $this->wpdb->prefix . 'term_comments';
			} else {
				$table = $this->wpdb->prefix . 'comments';
			}
			$where = array( 'comment_ID' => $_GET['custom_comment'] );
		} else {
			$table = $_REQUEST['table'];
			$data  = array(
				'comment_author'       => $_REQUEST['comment_author'],
				'comment_author_email' => $_REQUEST['comment_author_email'],
				'comment_content'      => $_REQUEST['content'],
				'comment_approved'     => $_REQUEST['comment_status'],
			);
			$where = array( 'comment_ID' => $_POST['comment_ID'] );
		}
		$this->wpdb->update( $table, $data, $where );

		return wp_redirect( wp_get_referer() );
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Custom Comments',
			'default' => 5,
			'option'  => 'comments_per_page',
		];

		add_screen_option( $option, $args );

		$this->comments_object = new Comments_List();
	}

	public function custom_comments_page_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		require_once plugin_dir_path( __FILE__ ) . 'partials/custom-comments-admin-display.php';
	}

	public function remove_default_comment_bar() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu( 'comments' );
	}

	public function remove_admin_menu_default_comments() {
		remove_menu_page( 'edit-comments.php' );
	}

	/**
	 * Quick edit for custom comment in admin area
	 */
	public function quick_edit() {
		$id      = $_POST['params']['ID'];
		$type    = $_POST['params']['type'];
		$content = $_POST['params']['content'];
		$author  = $_POST['params']['author'];
		$email   = $_POST['params']['email'];

		$table = $type == 'post' ? $this->wpdb->prefix . 'comments' : $this->wpdb->prefix . 'term_comments';

		$data   = array(
			'comment_author'       => $author,
			'comment_author_email' => $email,
			'comment_content'      => $content,
			'comment_parent'       => 1,
		);
		$where  = array( 'comment_ID' => $id );
		$update = $this->wpdb->update( $table, $data, $where );

		if ( false === $update ) {
			$response = array( 'status' => 'error' );
		} else {
			$response = array( 'status' => 'Fields updated' );
		}
		echo json_encode( $response );
		wp_die();
	}
}



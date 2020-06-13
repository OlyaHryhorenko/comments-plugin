<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Comments_List extends WP_List_Table {

	public $wpdb;
	public $plugin_name;

	public function __construct() {

		parent::__construct( [
			'singular' => 'Comment',
			'plural'   => 'Comments',
			'ajax'     => false
		] );
		global $wpdb;
		$this->wpdb        = $wpdb;
		$this->plugin_name = 'custom_comment';
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No comments avaliable.', $this->plugin_name );
	}

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		// todo change date format
		switch ( $column_name ) {
			case 'comment_author':
				return $this->comment_author_display( $item );
			case 'obj_id':
				return $this->linked_obj_display( $item );
			case 'comment_approved':
				return $this->comment_status( $item[ $column_name ] );
			default:
				return $item[ $column_name ];
		}
	}

	protected function comment_author_display( $item ) {
		require plugin_dir_path( __FILE__ ) . 'partials/user-avatar-template.php';
	}

	protected function linked_obj_display( $item ) {
		require plugin_dir_path( __FILE__ ) . 'partials/response-link-template.php';
	}

	protected function comment_status( $item ) {
		switch ( $item ) {
			case '1':
				$item = 'Approved';
				break;
			case '0':
				$item = 'Unapproved';
				break;
			default:
				return $item;
		}

		return $item;
	}

	function column_comment_content( $item ) {

		$actions = array(
			'approve'    => sprintf( '<a href="?page=%s&action=%s&custom_comment=%s&type=%s">Approve</a>', $_REQUEST['page'], 'approve', $item['ID'], $item['type'] ),
			'unapprove'  => sprintf( '<a href="?page=%s&action=%s&custom_comment=%s&type=%s">Unapprove</a>', $_REQUEST['page'], 'unapprove', $item['ID'], $item['type'] ),
			'edit'       => sprintf( '<a href="?page=%s&action=%s&custom_comment=%s&type=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['ID'], $item['type'] ),
			'quick-edit' => sprintf( '<a href="?page=%s&action=%s&custom_comment=%s&type=%s">Quick Edit</a>', $_REQUEST['page'], 'quick-edit', $item['ID'], $item['type'] ),
			'delete'     => sprintf( '<a href="?page=%s&action=%s&custom_comment=%s&type=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['ID'], $item['type'] ),
			'trash'      => sprintf( '<a href="?page=%s&action=%s&custom_comment=%s&type=%s">Trash</a>', $_REQUEST['page'], 'trash', $item['ID'], $item['type'] ),
		);

		return sprintf( '%1$s %2$s', '<span class="c_comment_content">' . $item['comment_content'] . '</span>', $this->row_actions( $actions ) );
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-check[]" value="%s" />', $item['ID'] . '_' . $item['type']
		);
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'                   => '<input type="checkbox" />',
			'comment_author'       => __( 'Author', 'sp' ),
			'comment_content'      => __( 'Comment', 'sp' ),
			'comment_author_email' => __( 'Email', 'sp' ),
			'type'                 => __( 'Type', 'sp' ),
			'obj_id'               => __( 'Response On', 'sp' ),
			'comment_approved'     => __( 'Status', 'sp' ),
			'comment_date'         => __( 'Submitted On', 'sp' ),
		];

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'comment_author'       => array( 'comment_author', true ),
			'comment_author_email' => array( 'comment_author_email', false ),
			'type'                 => array( 'type', false ),
			'comment_date'         => array( 'comment_date', false )
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-unapprove' => 'Unapprove',
			'bulk-approve'   => 'Approve',
			'bulk-delete'    => 'Delete',
		];

		return $actions;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
		$current_page = $this->get_pagenum();
		if ( $_GET['filter'] == 'comments' ) {
			$total_items = $this->record_count( 'where comment_approved = "' . $_GET['comment_status'] . '"' );
		} else {
			$total_items = $this->record_count();
		}

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );

		$this->items = $this->get_comments( $per_page, $current_page );
	}

	/**
	 *
	 * esc_url_raw() is used to prevent converting ampersand in url to "#038;"
	 */
	public function process_bulk_action() {

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) ) {

			$delete_ids = esc_sql( $_POST['bulk-check'] );

			foreach ( $delete_ids as $id ) {
				$this->delete_comment( $id );

			}

			wp_redirect( esc_url_raw( add_query_arg() ) );
			exit;
		}


		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-approve' ) ) {
			$approve_ids = esc_sql( $_POST['bulk-check'] );

			foreach ( $approve_ids as $id ) {
				$this->set_status( $id, '1' );

			}
			wp_redirect( esc_url_raw( add_query_arg() ) );
			exit;
		}

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-unapprove' ) ) {
			$approve_ids = esc_sql( $_POST['bulk-check'] );

			foreach ( $approve_ids as $id ) {
				$this->set_status( $id, '0' );
			}
			wp_redirect( esc_url_raw( add_query_arg() ) );
			exit;
		}
	}

	/**
	 * Delete a comment record.
	 *
	 * @param int $id customer ID
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
	}

	public function set_status( $id, $status ) {
		$parse_data  = explode( '_', $id );
		$comment_id  = $parse_data[0];
		$type        = $parse_data[1];
		$definitions = array(
			'table' => $type == 'post' ? 'comments' : 'term_comments',
		);
		$data        = array( 'comment_approved' => $status );
		$where       = array( 'comment_ID' => $comment_id );
		$this->wpdb->update( "{$this->wpdb->prefix}" . $definitions['table'], $data, $where );
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public function record_count( $params = null ) {
		if ( empty( $params ) ) {
			$where = 'where comment_approved = "1" or comment_approved = "0"';
		} else {
			$where = $params;
		}
		$sql = "SELECT(SELECT COUNT(*) FROM {$this->wpdb->prefix}term_comments {$where})+(SELECT COUNT(*) from {$this->wpdb->prefix}comments {$where})AS count";

		return $this->wpdb->get_var( $sql );
	}

	/**
	 * Retrieve custom comments data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */

	public function get_comments( $per_page = 5, $page_number = 1 ) {

		$sql = "SELECT comment_post_ID  COLLATE utf8mb4_general_ci as obj_id, comment_ID AS ID, comment_date  as comment_date, 
            comment_approved COLLATE utf8mb4_general_ci as comment_approved, 
            comment_content COLLATE utf8mb4_general_ci as comment_content,
            comment_post_ID as id, comment_author_email COLLATE utf8mb4_general_ci as comment_author_email,
            comment_author COLLATE utf8mb4_general_ci as comment_author,
            comment_agent COLLATE utf8mb4_general_ci as comment_agent, user_id as user_id, 'post' as type FROM " . $this->wpdb->prefix . "comments";
		if ( $_GET['filter'] == 'comments' ) {
			$sql .= ' where comment_approved=' . '"' . $_GET['comment_status'] . '"';
		} else {
			$sql .= ' where comment_approved="0" or comment_approved ="1"';
		}
		$sql .= " UNION ALL";
		$sql .= " SELECT  comment_post_ID  COLLATE utf8mb4_general_ci as obj_id, comment_ID AS ID, comment_date as comment_date,
        comment_approved COLLATE utf8mb4_general_ci as comment_approved,
        comment_content COLLATE utf8mb4_general_ci as  comment_content,
        comment_post_ID  as id, comment_author_email COLLATE utf8mb4_general_ci as comment_author_email,
         comment_author COLLATE utf8mb4_general_ci as comment_author,
         comment_agent COLLATE utf8mb4_general_ci as comment_agent,user_id as user_id, 'term' as type FROM " . $this->wpdb->prefix . "term_comments";
		if ( $_GET['filter'] == 'comments' ) {
			$sql .= ' where comment_approved=' . '"' . $_GET['comment_status'] . '"';
		} else {
			$sql .= ' where comment_approved="0" or comment_approved ="1"';
		}
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
		} else {
			$sql .= ' ORDER BY comment_date desc';
		}

		$sql    .= " LIMIT $per_page";
		$sql    .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		$result = $this->wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
	}

	public function get_comment( $type, $ID ) {
		$defaults = array(
			'table'    => $type == 'post' ? 'comments' : 'term_comments',
			'ID_field' => $type == 'comment_ID',
			'obj_id'   => $type == 'comment_post_ID'
		);
		$sql      = "SELECT * from {$this->wpdb->prefix}{$defaults['table']} where comment_ID={$ID}";

		return $this->wpdb->get_results( $sql )[0];
	}

	/**
	 * @return array
	 * tab menu om top of the table
	 */

	protected function get_views() {

		$status_links = array(
			"all"      => sprintf( "<a href='?page=custom_comments_list' %s>All (%s)</a>", ! isset( $_GET['comment_status'] ) ? 'class="current"' : '', $this->record_count( '' ) ),
			"pending"  => sprintf( "<a href='?page=custom_comments_list&filter=comments&comment_status=0' %s>Pending (%s)</a>", $_GET['comment_status'] == "0" ? 'class="current"' : '', $this->record_count( 'where comment_approved = "0"' ) ),
			"approved" => sprintf( "<a href='?page=custom_comments_list&filter=comments&comment_status=1' %s>Approved (%s)</a>", $_GET['comment_status'] == 1 ? 'class="current"' : '', $this->record_count( 'where comment_approved = "1"' ) ),
			"spam"     => sprintf( "<a href='?page=custom_comments_list&filter=comments&comment_status=spam' %s>Spam (%s)</a>", $_GET['comment_status'] == 'spam' ? 'class="current"' : '', $this->record_count( 'where comment_approved = "spam"' ) ),
			"trash"    => sprintf( "<a href='?page=custom_comments_list&filter=comments&comment_status=trash' %s>Trash (%s)</a>", $_GET['comment_status'] == 'trash' ? 'class="current"' : '', $this->record_count( 'where comment_approved = "trash"' ) )
		);

		return $status_links;
	}

}


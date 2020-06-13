<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       /
 * @since      1.0.0
 *
 * @package    Custom_Comments
 * @subpackage Custom_Comments/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Custom_Comments
 * @subpackage Custom_Comments/public
 * @author     Amelya <amelya@syneforge.com>
 */
class Custom_Comments_Public {

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
	private $wpdb;
	private $table;


	public function __construct( $plugin_name, $version ) {
		global $wpdb;
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->options     = get_option( 'custom_comments' );
		$this->wpdb        = $wpdb;
		$this->table       = $wpdb->prefix . 'term_comments';

	}


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
        if (empty( $this->options['disable_js'] ) ) {
	        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/custom-comments-public.js', '',
		        $this->version, true );
        }

	}

	public function comments_form() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/comment-form-template.php';
	}

	/**
	 * @return bool
	 * Add comment controller, check if captcha v3 exists and validates it
	 */
	public function add_comment() {
		$response = json_encode( array( 'status' => 'success' ) );
		if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) && ! empty( get_option( 'custom-recaptcha-settings' )['custom-captcha-secret-key'] ) ) {
			$secret         = get_option( 'custom-recaptcha-settings' )['custom-captcha-secret-key'];
			$verifyResponse = file_get_contents( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response'] );
			$responseData   = json_decode( $verifyResponse );
			if ( $responseData->success ) {
				$response = json_encode( array( 'status' => 'success' ) );
			} else {
				$response = json_encode( array( 'status' => 'error' ) );

				return false;
			}
		}

		if ( $_POST['type'] == 'post' ) {
			$this->add_post_comment( $_POST );
		} elseif ( $_POST['type'] == 'term' ) {
			$this->add_term_comment( $_POST );
		} else {
			$response = json_encode( array( 'status' => 'error' ) );

			return false;
		}

		wp_die( $response );
	}

	/**
	 * @param $request
	 * Adds post comment with its meta fields is possible
	 *
	 * @return false|int
	 */
	private function add_post_comment( $request ) {
		$data = array(
			'comment_post_ID'      => $request['id'],
			'comment_author'       => $request['name'] ?: 'guest',
			'comment_author_email' => $request['email'],
			'comment_content'      => $request['text'],
			'comment_date'         => date( "Y-m-d H:i:s" ),
			'comment_approved'     => 0,
			'comment_parent'       => $request['comment_parent'],
			'user_id'              => get_current_user_id()
		);

		$this->custom_comment_meta_fields( $data, $request );

		return wp_insert_comment( wp_slash( $data ) );
	}

	private function custom_comment_meta_fields( $data, $request ) {
		if ( $this->options['additional_fields'] ) {
			foreach ( $this->options['additional_fields'] as $item ) {
				$data[ $item['slug'] ] = $request[ $item['slug'] ];
			}
		}

		return $data;
	}

	/**
	 * @param $request
	 * Adds term comment with its meta fields is possible
	 *
	 * @return false|int
	 */

	private function add_term_comment( $request ) {
		$data = array(
			'comment_author'       => $request['name'] ?: 'guest',
			'comment_author_email' => $request['email'],
			'comment_content'      => $request['text'],
			'comment_post_ID'      => $request['id'],
			'comment_date'         => date( "Y-m-d H:i:s" ),
			'comment_approved'     => 0,
			'comment_parent'       => 0,
			'user_id'              => get_current_user_id()
		);

		$this->custom_comment_meta_fields( $data, $request );
		error_log(json_encode($data));
		return $this->wpdb->insert( $this->table, $data );
	}

	public function get_custom_comments( $args ) {
		if ( is_category() || is_tax() ) {
			$this->wpdb->comments    = $this->wpdb->prefix . 'term_comments';
			$this->wpdb->commentmeta = $this->wpdb->prefix . 'term_commentmeta';
		} else {
			$this->wpdb->comments    = $this->wpdb->prefix . 'comments';
			$this->wpdb->commentmeta = $this->wpdb->prefix . 'commentmeta';
		}
		$comments = get_comments( $args );

		return $comments;
	}

	public function custom_comments_front_option() {
		?>
        <script>
            var custom_comments = {
                "url": "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                "error_name": "<?php echo isset( $this->options['error_name'] ) ? $this->options['error_name'] : ''; ?>",
                "error_email": "<?php echo isset( $this->options['error_email'] ) ? $this->options['error_email'] : ''; ?>",
                "error_text": "<?php echo isset( $this->options['error_text'] ) ? $this->options['error_text'] : ''; ?>",
                "success_text": "<?php echo isset( $this->options['success_text'] ) ? $this->options['success_text'] : ''; ?>",
            };
        </script>
		<?php
	}
}

function custom_comments() {
	$comment_form = new Custom_Comments_Public( CUSTOM_COMMENTS_NAME, CUSTOM_COMMENTS_VERSION );
	$comment_form->comments_form();
}


function get_custom_comments( $args = null ) {
	$comment_form = new Custom_Comments_Public( CUSTOM_COMMENTS_NAME, CUSTOM_COMMENTS_VERSION );

	return $comment_form->get_custom_comments( $args );
}
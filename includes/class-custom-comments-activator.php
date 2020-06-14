<?php

/**
 * Fired during plugin activation
 *
 * @link       /
 * @since      1.0.0
 *
 * @package    Custom_Comments
 * @subpackage Custom_Comments/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Custom_Comments
 * @subpackage Custom_Comments/includes
 * @author     Amelya <amelya@syneforge.com>
 */
class Custom_Comments_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */

	public $table;
	public $table_comment_meta;

	public static function activate() {

		self::activate_custom_term_comments();
	}

	public function activate_custom_term_comments() {
		global $wpdb;
		$table              = $wpdb->prefix . 'term_comments';
		$table_comment_meta = $wpdb->prefix . 'term_commentmeta';
		$charset_collate    = $wpdb->get_charset_collate();

		$create_sql_comment = "CREATE TABLE  {$table} (" .
							  'comment_ID INT(11) unsigned NOT NULL auto_increment,' .
							  "comment_post_ID bigint(20) unsigned NOT NULL default '0' ," .
							  'comment_author tinytext NOT NULL,' .
							  "comment_author_email varchar(200) NOT NULL default ''," .
							  "comment_author_url varchar(200) NOT NULL default ''," .
							  "comment_author_IP varchar(100) NOT NULL default ''," .
							  "comment_date datetime NOT NULL default '0000-00-00 00:00:00'," .
							  "comment_date_gmt datetime NOT NULL default '0000-00-00 00:00:00'," .
							  'comment_content text NOT NULL,' .
							  "comment_karma INT(11) NOT NULL default '0'," .
							  "comment_approved varchar(20) NOT NULL default '1'," .
							  "comment_agent varchar(20) NOT NULL default '1'," .
							  "comment_type varchar(20) NOT NULL default ''," .
							  "comment_parent bigint(20) unsigned NOT NULL default '0'," .
							  "user_id bigint(20) unsigned NOT NULL default '0'," .
							  'PRIMARY KEY  (comment_ID)' .
							  ") $charset_collate;";

		$create_sql_commentmeta = "CREATE TABLE {$table_comment_meta} (" .
								  'meta_id bigint(20) unsigned NOT NULL auto_increment,' .
								  "comment_id bigint(20) unsigned NOT NULL default '0'," .
								  'meta_key varchar(255) default NULL,' .
								  'meta_value longtext,' .
								  'PRIMARY KEY  (meta_id)' .
								  ") $charset_collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $create_sql_comment );
		dbDelta( $create_sql_commentmeta );
	}

}



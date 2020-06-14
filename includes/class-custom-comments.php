<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       /
 * @since      1.0.0
 *
 * @package    Custom_Comments
 * @subpackage Custom_Comments/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Custom_Comments
 * @subpackage Custom_Comments/includes
 * @author     Amelya <amelya@syneforge.com>
 */
class Custom_Comments {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Custom_Comments_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;
    protected $options;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'CUSTOM_COMMENTS_VERSION' ) ) {
            $this->version = CUSTOM_COMMENTS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'custom-comments';
        $this->options     = get_option( 'custom_comments' );
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Custom_Comments_Loader. Orchestrates the hooks of the plugin.
     * - Custom_Comments_i18n. Defines internationalization functionality.
     * - Custom_Comments_Admin. Defines all hooks for the admin area.
     * - Custom_Comments_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-custom-comments-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-custom-comments-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-custom-comments-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-custom-comments-admin-table-list.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-custom-comments-public.php';

        $this->loader = new Custom_Comments_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Custom_Comments_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Custom_Comments_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Custom_Comments_Admin( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu' );
        $this->loader->add_action( 'admin_post_update_comment', $plugin_admin, 'update_comment' );
        $this->loader->add_action( 'wp_before_admin_bar_render', $plugin_admin, 'remove_default_comment_bar' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'remove_admin_menu_default_comments' );
        $this->loader->add_action( 'wp_ajax_quick_edit', $plugin_admin, 'quick_edit' );
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Custom_Comments_Public( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'wp_ajax_add_comment', $plugin_public, 'add_comment' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'wp_ajax_nopriv_add_comment', $plugin_public, 'add_comment' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'custom_comments_front_option' );


    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Custom_Comments_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader() {
        return $this->loader;
    }

}

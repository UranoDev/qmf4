<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://codigoti.tech
 * @since      1.0.0
 *
 * @package    Qmf
 * @subpackage Qmf/includes
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
 * @package    Qmf
 * @subpackage Qmf/includes
 * @author     Dario Morales <tlatlauki@hotmail.com>
 */
class Qmf4 {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Qmf4_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

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
		if ( defined( 'QMF4_VERSION' ) ) {
			$this->version = QMF4_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'qmf';

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
	 * - Qmf4_Loader. Orchestrates the hooks of the plugin.
	 * - Qmf4_i18n. Defines internationalization functionality.
	 * - Qmf4_Admin. Defines all hooks for the admin area.
	 * - Qmf4_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-qmf4-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-qmf4-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-qmf4-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-qmf4-public.php';

		$this->loader = new Qmf4_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Qmf4_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Qmf4_i18n();

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

		$plugin_admin = new Qmf4_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'qmf4_agrega_pagina_opciones' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'qmf4_registro_opciones' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'qmf4_campos_woocommerce' );
		$this->loader->add_action( 'woocommerce_admin_order_data_after_billing_address', $plugin_admin, 'qmf4_agrega_datos_orden');
		$this->loader->add_action( 'woocommerce_admin_order_data_after_order_details', $plugin_admin, 'qmf4_agrega_datos_factura_orden');
		$this->loader->add_action( 'woocommerce_update_product', $plugin_admin, 'qmf4_sync_al_guardar_producto', 10, 1 );
		$this->loader->add_action( 'woocommerce_order_status_changed', $plugin_admin, 'qmf4_cambio_status_orden', 10, 4 ); 
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_admin, 'qmf4_liga_factura_thankyou' );
		$this->loader->add_filter( 'manage_edit-product_columns', $plugin_admin, 'qmf4_agrega_columna_wc_productos' );
		$this->loader->add_filter( 'manage_posts_custom_column', $plugin_admin, 'qmf4_llena_columna_wc_productos');
		//Para agregar action a la lista desplegable
		$this->loader->add_filter( 'bulk_actions-edit-shop_order', $plugin_admin, 'qmf4_add_action_batch_orders' );
		//Para actualizar status en QUIEROMIFACTURA 
		$this->loader->add_filter( 'handle_bulk_actions-edit-shop_order', $plugin_admin, 'qmf4_bulk_action_handler', 10, 3 );
		//Muestra las actualizaciones
		$this->loader->add_action('admin_notices', $plugin_admin, 'qmf4_bulk_action_admin_notice');
		//Muestra datos de facturación en Profile
		$this->loader->add_action('show_user_profile', $plugin_admin, 'qmf4_show_data_profile', 10);
		$this->loader->add_action('edit_user_profile', $plugin_admin, 'qmf4_show_data_profile', 11);
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Qmf4_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_filter( 'woocommerce_get_query_vars', $plugin_public, 'qmf4_query_vars', 0 );
		$this->loader->add_filter( 'woocommerce_account_menu_items', $plugin_public, 'qmf4_mi_cuenta_menu_datos_facturacion', 0 );
		$this->loader->add_filter( 'woocommerce_account_datos-de-facturacion_endpoint', $plugin_public, 'qmf4_mi_cuenta_menu_datos_facturacion_campos', 0 );
		

		if( get_option( 'qmf_habilitar_facturacion' ) == '1' ) {
			$this->loader->add_action( 'woocommerce_after_checkout_billing_form', $plugin_public, 'qmf4_agrega_campos_checkout' );
			$this->loader->add_action( 'woocommerce_checkout_process', $plugin_public, 'qmf4_valida_campos_checkout');
			$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'qmf4_guarda_campos_checkout' );
			//$this->loader->add_filter( 'query_vars', $plugin_public, 'qmf4_query_vars', 0 );
			
			$this->loader->add_filter( 'the_title', $plugin_public, 'qmf4_mi_cuenta_menu_datos_facturacion_titulo' );

		}
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
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Qmf4_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

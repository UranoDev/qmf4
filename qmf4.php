<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://urano.dev
 * @since             1.0.0
 * @package           Qmf4
 *
 * @wordpress-plugin
 * Plugin Name:       Quiero mi Factura V4
 * Plugin URI:        https://urano.dev
 * Description:       Integración de Woocommerce con los servicios de facturación en línea de Quiero mi Factura (<a href='https://quieromifactura.mx'>quieromifactura.mx</a>).
 * Version:           1.0.1
 * Author:            Urano González
 * Author URI:        https://urano.dev/qmf
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       qmf4
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * WC tested up to 	  5.9
 * WC requires at least 5.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'QMF4_VERSION', '0.1.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-qmf4-activator.php
 */
function activate_qmf4() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-qmf4-activator.php';
	if ( is_plugin_active( 'qmf/qmf.php') ) {
		die('Plugin NO activado. Necesita desintalar el plugin QMF que sirve para la versión 3 de CFDI');
	}
	Qmf4_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-qmf4-deactivator.php
 */
function deactivate_qmf4() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-qmf4-deactivator.php';
	Qmf4_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_qmf4' );
register_deactivation_hook( __FILE__, 'deactivate_qmf4' );

require_once plugin_dir_path(  __FILE__  ) . 'helpers/plugin-update-checker-4.10/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://codigonube.com/p/update/?action=get_metadata&slug=qmf4',
	__FILE__, //Full path to the main plugin file or functions.php.
	'qmf4'
);

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-qmf4.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_qmf4() {

	/**
	 * Validar si WooCommerce está activo
	 */
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$plugin = new Qmf4();
		$plugin->run();
	} else { 
		add_action( 'admin_notices', 'qmf4_woocommerce_no_activado' );
	}

	/**
	 * Verifica si se han registrado los datos generales del plugin
	 */
	$qmf_rfc_emisor = get_option( 'qmf_rfc_emisor' );
	$qmf_cp_sucursal = get_option( 'qmf_cp_sucursal' );
	$qmf_sucursal = get_option( 'qmf_sucursal' );
	$qmf_sandbox = get_option( 'qmf_sandbox' );

	if(!$qmf_rfc_emisor || !$qmf_cp_sucursal || !$qmf_sucursal || empty($qmf_rfc_emisor) || empty($qmf_cp_sucursal) || empty($qmf_sucursal)) {
		add_action( 'admin_notices', 'qmf4_datos_generales_incompletos' );
	}
	
	if($qmf_sandbox == '1') {
		add_action( 'admin_notices', 'qmf4_sandbox' );
	}
}

/**
 * Despliega un mensaje de error si WooCommerce no esta activado
 *
 * @since     1.0.0
 */
function qmf4_woocommerce_no_activado() {
	?>
	<div class="error notice">
		<p><?php _e( '¡Quiero mi Factura requiere de WooCommerce para funcionar!', 'qmf' ); ?></p>
	</div>
	<?php
}

/**
 * Despliega un mensaje de error si los datos generales del plugin están incompletos
 *
 * @since     1.0.0
 */
function qmf4_datos_generales_incompletos() {
	?>
	<div class="error notice">
		<p><?php _e( '¡Los datos generales del plugin están incompletos por lo que no podrá emitir facturas!', 'qmf' ); ?></p>
	</div>
	<?php
}

/**
 * Despliega un mensaje de advertencia si el plugin se encuentra trabajando en modo sandbox
 *
 * @since     1.0.0
 */
function qmf4_sandbox() {
	?>
	<div class="notice-warning notice">
		<p><?php _e( 'Quiero mi factura se encuentra activado en modo Sandbox por lo que no se generarán facturas reales', 'qmf' ); ?></p>
	</div>
	<?php
}

run_qmf4();

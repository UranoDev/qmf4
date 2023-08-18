<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://codigoti.tech
 * @since      1.0.0
 *
 * @package    Qmf
 * @subpackage Qmf/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Qmf
 * @subpackage Qmf/includes
 * @author     Dario Morales <tlatlauki@hotmail.com>
 */
class Qmf4_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		Qmf4_Deactivator::qmf4_elimina_datos_facturacion_endpoint();
		//Qmf4_Deactivator::qmf4_elimina_tablas();
	}

	/**
	 * Elimina el endpoint que se necesita para adicionar la pestaña de Datos de Facturación
	 * de la página Mi Cuenta y guarda los enlaces permanentes para que sea reconocida
	 *
	 * @since    1.0.0
	 */
	function qmf4_elimina_datos_facturacion_endpoint() {
		/* add_rewrite_endpoint( 'datos-de-facturacion', EP_ROOT | EP_PAGES );
		flush_rewrite_rules(); */
	}

	/**
	 * Elimina las tablas del plugin
	 *
	 * @since    1.0.0
	 */
	function qmf4_elimina_tablas() {
		global $wpdb;

		$qmf_tabla_unidades = $wpdb->prefix . "qmf_unidades"; 
		$qmf_tabla_productos_servicios = $wpdb->prefix . "qmf_productos_servicios"; 
		$qmf_tabla_uso_CFDI = $wpdb->prefix . "qmf_uso_cfdi"; 

		$wpdb->query("DROP TABLE IF EXISTS `$qmf_tabla_unidades`");
		$wpdb->query("DROP TABLE IF EXISTS `$qmf_tabla_productos_servicios`");
		$wpdb->query("DROP TABLE IF EXISTS `$qmf_tabla_uso_CFDI`");
		$wpdb->query("DROP TABLE IF EXISTS $wpdb->prefix"."qmf_forma_de_pago");
		$wpdb->query("DROP TABLE IF EXISTS $wpdb->prefix"."qmf_regimen_fiscal");
	}
}
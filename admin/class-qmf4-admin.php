<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://urano.dev
 * @since      1.0.0
 *
 * @package    Qmf
 * @subpackage Qmf/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Qmf
 * @subpackage Qmf/admin
 * @author     Urano Dev  <u@urano.dev>
 */
class Qmf4_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $WebserviceProduccion = "https://quieromifactura.mx/PROD/web_services/servidorMarket.php?wsdl";
	private $WebserviceSandbox = "https://quieromifactura.mx/QA2/web_services/servidorMarket.php?wsdl";
	private $RFCGenerico = "XAXX010101000";

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$qmf_path_sandbox_op = get_option ('qmf_path_sandbox');
		if (!$qmf_path_sandbox_op){
			$qmf_path_sandbox_op = "https://quieromifactura.mx/QA2/web_services/servidorMarket.php?wsdl";
		}
		$this->WebserviceSandbox = $qmf_path_sandbox_op;

		$qmf_path_prod_op = get_option ('qmf_path_prod');
		if (!$qmf_path_prod_op){
			$qmf_path_prod_op = "https://quieromifactura.mx/PROD/web_services/servidorMarket.php?wsdl";
		}
		$this->WebserviceProduccion = $qmf_path_prod_op;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/qmf4-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/qmf4-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Agrega página de Quiero mi factura a las opciones del menú de administrador de Wordpress
	 *
	 * @since  1.0.0
	 */
	public function qmf4_agrega_pagina_opciones() {
	
		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Quiero mi factura CFDI4', 'qmf' ),
			__( 'Quiero mi factura CFDI4', 'qmf' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'qmf4_despliega_pagina_opciones' ),
			'',
			26
		);

	}

	/**
	 * Despliega la página de opciones del plugin
	 *
	 * @since  1.0.0
	 */
	public function qmf4_despliega_pagina_opciones() {
		include_once 'partials/qmf4-admin-display.php';
	}

	/**
	 * Registro de los diferentes campos dentro de la página de opciones
	 *
	 * @since  1.0.0
	 */
	public function qmf4_registro_opciones() {
		add_settings_section(
			'qmf_general',
			__( 'General', 'qmf' ),
			array( $this, 'qmf4_seccion_general' ),
			$this->plugin_name
		);
		add_settings_field(
			'qmf_usuario',
			__( 'Usuario de Quiero mi Factura', 'qmf' ),
			array( $this, 'qmf4_usuario' ),
			$this->plugin_name,
			'qmf_general',
			array( 'label_for' => 'qmf_usuario' )
		);
		add_settings_field(
			'qmf_rfc_emisor',
			__( 'RFC del Emisor', 'qmf' ),
			array( $this, 'qmf4_rfc_emisor' ),
			$this->plugin_name,
			'qmf_general',
			array( 'label_for' => 'qmf_rfc_emisor' )
		);
		add_settings_field(
			'qmf_cp_sucursal',
			__( 'Código Postal de la Sucursal', 'qmf' ),
			array( $this, 'qmf4_cp_sucursal' ),
			$this->plugin_name,
			'qmf_general',
			array( 'label_for' => 'qmf_cp_sucursal' )
		);
		add_settings_field(
			'qmf_sucursal',
			__( 'Sucursal', 'qmf' ),
			array( $this, 'qmf4_sucursal' ),
			$this->plugin_name,
			'qmf_general',
			array( 'label_for' => 'qmf_sucursal' )
		);
		add_settings_field(
			'qmf_admin_uso_cfdi',
			__( 'Uso CFDI default', 'qmf' ),
			array($this, 'qmf4_admin_uso_cfdi'),
			$this->plugin_name,
			'qmf_general',
			array( 'label_for' => 'qmf_admin_uso_cfdi' )
		);
 		add_settings_field(
			'qmf_sandbox',
			__( 'Sandbox', 'qmf' ),
			array( $this, 'qmf4_sandbox' ),
			$this->plugin_name,
			'qmf_general',
			array( 'label_for' => 'qmf_sandbox' )
		);
		add_settings_field(
			'qmf_habilitar_facturacion',
			__( 'El comprador puede solicitar timbrado de factura desde la tienda?', 'qmf' ),
			array( $this, 'qmf4_habilitar_facturacion' ),
			$this->plugin_name,
			'qmf_general',
			array( 'label_for' => 'qmf_habilitar_facturacion' )
		);
		add_settings_field(
			'qmf_mensaje_thankyou',
			__( 'Mensaje en la página de Pedido Recibido', 'qmf' ),
			array( $this, 'qmf4_mensaje_thankyou' ),
			$this->plugin_name,
			'qmf_general',
			array( 'label_for' => 'qmf_mensaje_thankyou' )
		);
		add_settings_field(
			'qmf_path_sandbox',
			__( 'URL para sandbox', 'qmf' ),
			array( $this, 'qmf4_path_sandbox' ),
			$this->plugin_name,
			'qmf_general',
			array( 'label_for' => 'qmf_path_sandbox' )
		);
		add_settings_field(
			'qmf_path_prod',
			__( 'URL para PROD', 'qmf' ),
			array( $this, 'qmf4_path_prod' ),
			$this->plugin_name,
			'qmf_general',
			array( 'label_for' => 'qmf_path_prod' )
		);
		add_settings_section(
			'qmf_debug',
			__( 'DEBUG', 'qmf' ),
			array( $this, 'qmf4_seccion_debug' ),
			$this->plugin_name
		);
		add_settings_field('qmf_DEBUG',
			__("Debug de QMF"),
			array($this, "qmf4_DEBUG"),
			$this->plugin_name,
			'qmf_debug',
			array( 'label_for' => 'qmf_path_prod' )
		);
		add_settings_field('qmf_descargar',
			__("Descargar Log file"),
			array($this, "qmf4_boton_descargar"),
			$this->plugin_name,
			'qmf_debug'
		);

		add_settings_field('qmf_test_conexion',
			__("Test conexión"),
			array($this, "qmf4_boton_conexion"),
			$this->plugin_name,
			'qmf_debug'
		);
		

		register_setting( $this->plugin_name, 'qmf_usuario', array( $this, 'qmf4_valida_usuario' ) );
		register_setting( $this->plugin_name, 'qmf_rfc_emisor', array( $this, 'qmf4_valida_rfc' ) );
		register_setting( $this->plugin_name, 'qmf_cp_sucursal', array( $this, 'qmf4_valida_cp_sucursal' ) );
		register_setting( $this->plugin_name, 'qmf_sucursal', array( $this, 'qmf4_valida_sucursal' ) );
		register_setting( $this->plugin_name, 'qmf_admin_uso_cfdi', array( $this, 'qmf4_valida_admin_uso_cfdi' ) );
		register_setting( $this->plugin_name, 'qmf_sandbox', array( $this, 'qmf4_valida_sandbox' ) );
		register_setting( $this->plugin_name, 'qmf_habilitar_facturacion', array( $this, 'qmf4_valida_habilitar_facturacion' ) );
		register_setting( $this->plugin_name, 'qmf_mensaje_thankyou', array( $this, 'qmf4_valida_mensaje_thankyou' ) );
		register_setting( $this->plugin_name, 'qmf_path_sandbox', array( $this, 'qmf4_valida_path_sandbox' ) );
		register_setting( $this->plugin_name, 'qmf_path_prod', array( $this, 'qmf4_valida_path_prod' ) );
		register_setting( $this->plugin_name, 'qmf_DEBUG', array( $this, 'qmf4_valida_DEBUG' ) );
		register_setting( $this->plugin_name, 'qmf_descargar', array( $this, 'qmf4_valida_descarga' ) );
		register_setting( $this->plugin_name, 'qmf_test_conexion', array( $this, 'qmf4_valida_test_conexion' ) );
	}

	/**
	 * Despliega el texto de la sección en la páfina de opciones
	 *
	 * @since  1.0.0
	 */
	public function qmf4_seccion_general() {
		echo '<p>' . __( 'Configuración de los datos generales.', 'qmf' ) . '</p>';
	}	

	public function qmf4_seccion_debug() {
		echo '<p>' . __( 'Configuración de la sección DEBUG', 'qmf' ) . '</p>';
	}	

	/**
	 * Carga el valor y despliega el campo usuario
	 *
	 * @since  1.0.0
	 */
	public function qmf4_usuario() {
		$qmf_usuario = get_option( 'qmf_usuario' );
		echo '<input type="text" name="qmf_usuario" id="qmf_usuario" value="' . $qmf_usuario . '">';
	}

	/**
	 * Carga el valor y despliega el campo RFC del emisor
	 *
	 * @since  1.0.0
	 */
	public function qmf4_rfc_emisor() {
		$qmf_rfc_emisor = get_option( 'qmf_rfc_emisor' );
		echo '<input type="text" name="qmf_rfc_emisor" id="qmf_rfc_emisor" value="' . $qmf_rfc_emisor . '">';
	}

	/**
	 * Carga el valor y despliega el campo Código Postal de la Sucursal
	 *
	 * @since  1.0.0
	 */
	public function qmf4_cp_sucursal() {
		$qmf_cp_sucursal = get_option( 'qmf_cp_sucursal' );
		echo '<input type="text" name="qmf_cp_sucursal" id="qmf_cp_sucursal" value="' . $qmf_cp_sucursal . '">';
	}

	/**
	 * Carga el valor y despliega el campo Sucursal
	 *
	 * @since  1.0.0
	 */
	public function qmf4_sucursal() {
		$qmf_sucursal = get_option( 'qmf_sucursal' );
		echo '<input type="text" name="qmf_sucursal" id="qmf_sucursal" value="' . $qmf_sucursal . '">';
	}

	
/**
	 * Carga el valor y despliega el campo Uso CFDI
	 *
	 * @since  1.0.0
	 */
	public function qmf4_admin_uso_cfdi() {
		global $wpdb;
		$qmf_admin_uso_cfdi = get_option('qmf_admin_uso_cfdi');

		$qmf_tabla_uso_CFDI = $wpdb->prefix . "qmf_uso_cfdi"; 
		$qmf_uso_CFDI_results = $wpdb->get_results("SELECT * from $qmf_tabla_uso_CFDI order by id");
		$qmf_uso_CFDI_opciones = array();

		echo "<select id='qmf_admin_uso_cfdi' name='qmf_admin_uso_cfdi'>";
		foreach($qmf_uso_CFDI_results as $qmf_uso_CFDI_row) {
			$qmf_uso_CFDI_opciones[$qmf_uso_CFDI_row->id] = "($qmf_uso_CFDI_row->id) $qmf_uso_CFDI_row->descripcion";
		}

		foreach($qmf_uso_CFDI_results as $qmf_uso_CFDI_row) {
			$selected = ($qmf_admin_uso_cfdi==$qmf_uso_CFDI_row->id) ? 'selected="selected"' : '';
			echo "<option value='$qmf_uso_CFDI_row->id' $selected>($qmf_uso_CFDI_row->id) $qmf_uso_CFDI_row->descripcion</option>";
		}
		echo "</select>";
	}


	/**
	 * Carga el valor y despliega el campo Sandbox
	 *
	 * @since  1.0.0
	 */
	public function qmf4_sandbox() {
		$qmf_sandbox = get_option( 'qmf_sandbox' );
		echo '<input type="checkbox" name="qmf_sandbox" id="qmf_sandbox" value="1" ' . checked(1, $qmf_sandbox, false) . '>';
        echo '<br>(IP server: ' . $_SERVER['REMOTE_ADDR'] . ')<br>';
        echo '<br>Version Plugin: ' . QMF4_VERSION . '<br>';
	}

	/**
	 * Carga el valor y despliega el campo Habilitar Facturación
	 *
	 * @since  1.0.0
	 */
	public function qmf4_habilitar_facturacion() {
		$qmf_habilitar_facturacion = get_option( 'qmf_habilitar_facturacion' );
		echo '<input type="checkbox" name="qmf_habilitar_facturacion" id="qmf_habilitar_facturacion" value="1" ' . checked(1, $qmf_habilitar_facturacion, false) . '>';
	}

	/**
	 * Carga el valor y despliega el campo Habilitar Facturación
	 *
	 * @since  1.0.0
	 */
	public function qmf4_mensaje_thankyou() {
		$qmf_habilitar_facturacion = get_option( 'qmf_mensaje_thankyou' );
		echo '<textarea name="qmf_mensaje_thankyou" id="qmf_mensaje_thankyou" rows="20" cols="100">' . $qmf_habilitar_facturacion . '</textarea>';
	}

	public function qmf4_path_sandbox(){
		$qmf_path_sandbox_op = get_option ('qmf_path_sandbox');
		if (!$qmf_path_sandbox_op){
			$qmf_path_sandbox_op = "http://quieromifactura.mx/QA2/web_services/servidorMarket.php?wsdl";
		}
		echo '<input type="text" name="qmf_path_sandbox" id="qmf_path_sandbox" size="98" value="' . $qmf_path_sandbox_op . '">';
	}

	public function qmf4_path_prod(){
		$qmf_path_prod_op = get_option ('qmf_path_prod');
		if (!$qmf_path_prod_op){
			$qmf_path_prod_op = "https://quieromifactura.mx/PROD/web_services/servidorMarket.php?wsdl";
		}
		echo '<input type="text" name="qmf_path_prod" id="qmf_path_prod" size="98" value="' . $qmf_path_prod_op . '">';
	}

	public function qmf4_DEBUG() {
		$qmf_DEBUG = get_option('qmf_DEBUG');
		echo '<input type="checkbox" name="qmf_DEBUG" id="qmf_DEBUG" value="1" ' . checked(1, $qmf_DEBUG, false) . '>';
		$s = wp_upload_dir(null, true, true);
		echo "<br>Path de archivo log:  " . print_r($s['path'],true) .  "<br>";
	}

	public function qmf4_boton_descargar() {
		echo '<input type="submit" name="descargar" class="button button-primary" value="Descargar" id="descargar">';
		}
	

	public function qmf4_boton_conexion() {
		echo '<input type="submit" name="test_conexion" class="button button-primary" value="test_conexion" id="test_conexion">';
	}
	

	/**
	 * Valida y sanitiza el usuario
	 *
	 * @since  1.0.0
	 */
	public function qmf4_valida_usuario($usuario) {
		$usuario_anterior = get_option( 'qmf_usuario' );
		$rfc = $_POST['qmf_rfc_emisor'];
		$usuario = strtoupper ( $usuario );
		$rfc = strtoupper ( $rfc );
		//$this->my_debug ("cambiando user antes ($usuario_anterior), nuevo ($usuario)");
		if(($usuario != "DUTI_INT1040702T34") && (!strpos($usuario, $rfc))) {
			add_settings_error(
				$this->plugin_name,
				'qmf_usuario_incorrecto',
				'Usuario Incorrecto, no coincide con RFC',
				'error'
			);
			$usuario = $usuario_anterior;
		}
		return $usuario;
	}

	/**
	 * Valida y sanitiza el RFC
	 *
	 * @since  1.0.0
	 */
	public function qmf4_valida_rfc($rfc) {
		$rfc_anterior = get_option( 'qmf_rfc_emisor' );
		$patron = '/^([A-Z,Ñ,&]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[A-Z|\d]{3})$/';
		$rfc = strtoupper ( $rfc );
		if(!preg_match($patron, $rfc)) {
			add_settings_error(
				$this->plugin_name,
				'qmf_rfc_incorrecto',
				'RFC Incorrecto',
				'error'
			);
			$rfc = $rfc_anterior;
		}
		return $rfc;
	}

	/**
	 * Valida y sanitiza el Código Postal de la Sucursal
	 *
	 * @since  1.0.0
	 */
	public function qmf4_valida_cp_sucursal($cp) {
		$cp_anterior = get_option( 'qmf_cp_sucursal' );
		$patron = '/^([0-9]{5})$/';
		if(!preg_match($patron, $cp)) {
			add_settings_error(
				$this->plugin_name,
				'qmf_cp_sucursal_incorrecto',
				'El Código Postal de la sucursal es incorrecto',
				'error'
			);
			$cp = $cp_anterior;
		}
		return $cp;
	}

	/**
	 * Valida y sanitiza la Sucursal
	 *
	 * @since  1.0.0
	 */
	public function qmf4_valida_sucursal($sucursal) {
		sanitize_text_field( $sucursal );
		return $sucursal;
	}

	/**
	 * Carga el valor y despliega el campo uso de CFDI
	 *
	 * @since  1.0.0
	 */
	function qmf4_valida_admin_uso_cfdi($uso_cfdi){
	return $uso_cfdi;
	}

	/**
	 * Valida y sanitiza en campo Sandbox
	 *
	 * @since  1.0.0
	 */
	public function qmf4_valida_sandbox($sandbox) {
		sanitize_text_field( $sandbox );
		return $sandbox;
	}

	/**
	 * Valida y sanitiza en campo Habilitar Facturacion
	 *
	 * @since  1.0.0
	 */
	public function qmf4_valida_habilitar_facturacion($habilitar_facturacion) {
		sanitize_text_field( $habilitar_facturacion );
		return $habilitar_facturacion;
	}

	public function qmf4_valida_test_conexion(){
		if (isset($_REQUEST['test_conexion'])){
			$this->test_conexion();
		}
	}

	/**
	 * Valida y sanitiza la Sucursal
	 *
	 * @since  1.0.0
	 */
	public function qmf4_valida_mensaje_thankyou($mensaje_thankyou) {
		sanitize_text_field( $mensaje_thankyou );
		return $mensaje_thankyou;
	}

	public function qmf4_valida_path_sandbox($url_sandbox){
		$url_sandbox = esc_url_raw($url_sandbox);
		return $url_sandbox;
	}

	public function qmf4_valida_path_prod($url_prod){
		$url_sandbox = esc_url_raw($url_prod);
		return $url_prod;
	}

	public function qmf4_valida_DEBUG ($habilitar_debug){
		sanitize_text_field($habilitar_debug);
		return $habilitar_debug;
	}
	
	public function qmf4_valida_descarga(){
		if (isset($_REQUEST['descargar'])){
			$this->debug_file_download();
		}
	}
	/**
	 * Gestión de los campos personalizados de productos de WooCommerce
	 *
	 * @since  1.0.0
	 */
	public function qmf4_campos_woocommerce() {
		add_action( 'woocommerce_product_data_tabs', array( $this, 'qmf4_agrega_tab_ajustes_producto_woocommerce' ) ); 
		add_action( 'woocommerce_product_data_panels', array( $this, 'qmf4_panel_producto' ) );
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'qmf4_agrega_campos_producto_variaciones' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'qmf4_guarda_campos_producto_variaciones' ), 10, 2 );
		add_action( 'woocommerce_process_product_meta', array( $this, 'qmf4_guarda_campos_producto_woocommerce' ) );
	}

	/**
	 * Agrega tab a los ajustes de producto de WooCommerce
	 *
	 * @since  1.0.0
	 */
	public function qmf4_agrega_tab_ajustes_producto_woocommerce($tabs) {
		$tabs['qmf'] = array(
			'label'    => 'Quiero mi factura',
			'target'   => 'qmf_datos',
			'priority' => 99,
		);
		return $tabs;
	}

	/**
	 * Agrega los campos personalizados a los productos de WooCommerce
	 *
	 * @since  1.0.0
	 */
	function qmf4_panel_producto(){
		global $woocommerce, $post;

		echo '<div id="qmf_datos" class="panel woocommerce_options_panel hidden">';
		woocommerce_wp_text_input(
			array(
			  'id'          => 'qmf_unidad_medida',
			  'label'       => __( 'Unidad de medida', 'qmf' ),
			  'placeholder' => 'Ingrese la unidad de medida',
			  'desc_tip'    => 'true',
			  'description' => __( 'La unidad de medida pueden ser piezas, kilos, etc.', 'qmf' )
			)
		);
		woocommerce_wp_text_input(
			array(
			  'id'          => 'qmf_clave_unidad_sat',
			  'label'       => __( 'Clave de unidad SAT', 'qmf' ),
			  'placeholder' => 'Ingrese la clave de unidad de medida válida del SAT',
			  'description' => __( 'Puede consultar el catálogo en el siguiente enlace: <a href="http://pys.sat.gob.mx/PyS/catUnidades.aspx" target="_blank">Catálogo</a>', 'qmf' )
			)
		);
		woocommerce_wp_text_input(
			array(
			  'id'          => 'qmf_clave_producto_servicio_sat',
			  'label'       => __( 'Clave de producto/servicio SAT', 'qmf' ),
			  'placeholder' => 'Ingrese la clave de prosucto/servicio válida del SAT',
			  'description' => __( 'Puede consultar el catálogo en el siguiente enlace: <a href="http://pys.sat.gob.mx/PyS/catPyS.aspx" target="_blank">Catálogo</a>', 'qmf' )
			)
		);
		echo '</div>';
	}

	/**
	 * Agrega los campos personalizados a las variaciones de productos de WooCommerce
	 *
	 * @since  1.0.0
	 */
	function qmf4_agrega_campos_producto_variaciones( $loop, $variation_data, $variation ) {
		woocommerce_wp_text_input(
			array(
			  'id'          => "qmf_unidad_medida_{$loop}",
			  'name'        => "qmf_unidad_medida[{$loop}]",
			  'value'         => get_post_meta( $variation->ID, 'qmf_unidad_medida', true ),
			  'label'       => __( 'Unidad de medida', 'qmf' ),
			  'placeholder' => 'Ingrese la unidad de medida',
			  'desc_tip'    => 'true',
			  'description' => __( 'La unidad de medida pueden ser piezas, kilos, etc.', 'qmf' ),
			  'wrapper_class' => 'form-row form-row-full'
			)
		);
		woocommerce_wp_text_input(
			array(
			  'id'          => "qmf_clave_unidad_sat_{$loop}",
			  'name'        => "qmf_clave_unidad_sat[{$loop}]",
			  'value'       => get_post_meta( $variation->ID, 'qmf_clave_unidad_sat', true ),
			  'label'       => __( 'Calve de unidad SAT', 'qmf' ),
			  'placeholder' => 'Ingrese la clave de unidad de medida válida del SAT',
			  'desc_tip'    => 'true',
			  'description' => __( 'Puede consultar el catálogo en el siguiente enlace: <a href="http://pys.sat.gob.mx/PyS/catUnidades.aspx" target="_blank">Catálogo</a>', 'qmf' ),
			  'wrapper_class' => 'form-row form-row-full'
			)
		);
		woocommerce_wp_text_input(
			array(
			  'id'          => "qmf_clave_producto_servicio_sat_{$loop}",
			  'name'        => "qmf_clave_producto_servicio_sat[{$loop}]",
			  'value'       => get_post_meta( $variation->ID, 'qmf_clave_producto_servicio_sat', true ),
			  'label'       => __( 'Calve de producto/servicio SAT', 'qmf' ),
			  'placeholder' => 'Ingrese la clave de prosucto/servicio válida del SAT',
			  'desc_tip'    => 'true',
			  'description' => __( 'Puede consultar el catálogo en el siguiente enlace: <a href="http://pys.sat.gob.mx/PyS/catPyS.aspx" target="_blank">Catálogo</a>', 'qmf' ),
			  'wrapper_class' => 'form-row form-row-full'
			)
		);
	}

	/**
	 * Guarda los campos personalizados de producto de WooCommerce
	 *
	 * @since  1.0.0
	 */
	public function qmf4_guarda_campos_producto_woocommerce($post_id) {

		$qmf_unidad_medida = $_POST['qmf_unidad_medida'];
		$qmf_clave_unidad_sat = $_POST['qmf_clave_unidad_sat'];
		$qmf_clave_producto_servicio_sat = $_POST['qmf_clave_producto_servicio_sat'];
		if( !empty( $qmf_unidad_medida ) )
			update_post_meta( $post_id, 'qmf_unidad_medida', sanitize_text_field( $qmf_unidad_medida ) );
		if( !empty( $qmf_clave_unidad_sat ) )
			update_post_meta( $post_id, 'qmf_clave_unidad_sat', sanitize_text_field( $qmf_clave_unidad_sat ) );
		if( !empty( $qmf_clave_producto_servicio_sat ) )
			update_post_meta( $post_id, 'qmf_clave_producto_servicio_sat', sanitize_text_field( $qmf_clave_producto_servicio_sat ) );
	}

	/**
	 * Guarda los campos personalizados de las variaciones de productos de WooCommerce
	 *
	 * @since  1.0.0
	 */
	function qmf4_guarda_campos_producto_variaciones( $post_id, $loop ) {
		$qmf_unidad_medida = $_POST['qmf_unidad_medida'][ $loop ];
		$qmf_clave_unidad_sat = $_POST['qmf_clave_unidad_sat'][ $loop ];
		$qmf_clave_producto_servicio_sat = $_POST['qmf_clave_producto_servicio_sat'][ $loop ];
	
		if ( ! empty( $qmf_unidad_medida ) )
			update_post_meta( $post_id, 'qmf_unidad_medida', esc_attr( $qmf_unidad_medida ));
		if ( ! empty( $qmf_clave_unidad_sat ) )
			update_post_meta( $post_id, 'qmf_clave_unidad_sat', esc_attr( $qmf_clave_unidad_sat ));
		if ( ! empty( $qmf_clave_producto_servicio_sat ) )
			update_post_meta( $post_id, 'qmf_clave_producto_servicio_sat', esc_attr( $qmf_clave_producto_servicio_sat ));
	}

	/**
	 * Muestra los datos de facturación en el administrador del pedido
	 *
	 * @since  1.0.0
	 */
	function qmf4_agrega_datos_orden($order){
		$qmf_facturar_ahora = get_post_meta( $order->get_id(), 'qmf_facturar_ahora', true );
		$qmf_rfc_comprador = get_post_meta( $order->get_id(), 'qmf_rfc_comprador', true );
		$qmf_nombre_receptor = get_post_meta( $order->get_id(), 'qmf_nombre_receptor', true );
		$qmf_uso_cfdi = get_post_meta( $order->get_id(), 'qmf_uso_cfdi', true );
		$qmf_cp_receptor = get_post_meta( $order->get_id(), 'qmf_cp_receptor', true );

		$qmf_facturar_ahora = empty($qmf_facturar_ahora) ? 'No' : 'Si';
		
		echo '<h3>Datos de facturación</h3>';
		echo '<p>';
		echo '	<strong>'.__('¿Solictó factura al realizar el pedido?').':</strong> ' . $qmf_facturar_ahora . '<br><br>';
		echo '	<strong>'.__('RFC del comprador').':</strong> ' . $qmf_rfc_comprador . ' ' . $qmf_nombre_receptor . ' ' . $qmf_cp_receptor . '</br><br>';
		echo '	<strong>'.__('Uso CFDI').':</strong> ' . $qmf_uso_cfdi . '</br><br>';
		echo '</p>';
	}

	/**
	 * Muestra los daenlaces de descarga de facturas en el administrador del pedido
	 *
	 * @since  1.0.0
	 */
	function qmf4_agrega_datos_factura_orden($order){
		$qmf_link_factura = get_post_meta( $order->get_id(), 'qmf_link_factura', true );
		$qmf_link_factura_PDF = get_post_meta( $order->get_id(), 'qmf_link_factura_PDF', true );

		if(!empty($qmf_link_factura)) {
			echo '<p class="form-field form-field-wide">'.__('Factura').':</p>';
			echo '<p class="form-field form-field-wide">';
			echo "	<a href='$qmf_link_factura'>" . __('Descargar documentos', 'qmf') . "</a><br>";
			echo "	<a href='$qmf_link_factura_PDF' target='_blank'>" . __('Consultar PDF', 'qmf') . "</a>";
			echo "</p>";
		}
	}

	function qmf4_sync_al_guardar_producto( $product_id ) {
		$qmf_producto = wc_get_product( $product_id );
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/nusoap/nusoap.php';
		if(get_option( 'qmf_sandbox' ) == '1')
			$wsdl = $this->WebserviceSandbox;
		else
			$wsdl = $this->WebserviceProduccion;
		$client = new nusoap_client($wsdl);
		$err = $client->getError();
		if ($err) {
			$this->my_debug('<h2>Constructor nusoap error</h2><pre>' . $err . '</pre>');
		}
		$qmf_USUARIO = get_option( 'qmf_usuario' );
		if(empty($qmf_RFC)) $qmf_RFC = $this->RFCGenerico;
		$qmf_CodProducto = $qmf_CodProductoMarketPlace = $qmf_producto->get_id();
		$qmf_NombreProducto = $this->qmf4_convert_special_chars(addslashes(strip_tags($qmf_producto->get_name())));
		$qmf_sku = $qmf_producto->get_sku();
		if ($qmf_sku == ''){$qmf_sku = $qmf_CodProducto;}
		$qmf_Unidad = $qmf_producto->get_meta('qmf_unidad_medida');
		$qmf_ClaveProdServ = $qmf_producto->get_meta('qmf_clave_producto_servicio_sat');
		$qmf_ClaveUnidad = $qmf_producto->get_meta('qmf_clave_unidad_sat');
		$xml = "<!-- ?xml version='1.0' encoding='UTF-8'? Generated by QMF4 V " . QMF4_VERSION . " -->";
		$xml .="	<CodProducto>$qmf_CodProducto</CodProducto>
				<NombreProducto>$qmf_NombreProducto</NombreProducto>
				<ClaveProdServ>$qmf_ClaveProdServ</ClaveProdServ>
				<Unidad>$qmf_Unidad</Unidad>
				<CodProductoMarketPlace>$qmf_CodProductoMarketPlace</CodProductoMarketPlace>
				<ClaveUnidad>$qmf_ClaveUnidad</ClaveUnidad>
				<RFC>$qmf_USUARIO</RFC>
				<valuename></valuename>
				<sku>$qmf_sku</sku>";
		$producto = $client->call('PRODUCTOS',$xml);
		$this->my_debug("Guardar Producto: $product_id");
		$this->my_debug("-------------------------------------------------------------------------");
		$this->my_debug("URL: $wsdl ");
		$this->my_debug("XML: $xml ");
		$this->my_debug("Respuesta Webservice: \n" . print_r($producto, TRUE));
	}

	function qmf4_cambio_status_orden( $order_id, $status_from, $status_to, $instance ) {
		if($status_to == 'Pending payment' || $status_to == 'processing' || $status_to == 'completed' || $status_to == 'cancelled' || $status_to == 'refunded')
			$this->qmf4_genera_factura($order_id, $status_to);
	}

	function qmf4_convert_special_chars(string $s){
		$a = array('á','é','í','ó','ú','Á','É','Í','Ó','Ú','ñ','Ñ','ü','Ü','´','&');
		$b = array('@a12345','@e12345','@i12345','@o12345','@u12345','@A12345','@E12345','@I12345','@O12345','@U12345','@n12345','@N12345','@u678','@U678','@COM','@ii12345');
		$input = str_replace($a, $b, $s);
		return $input;
	}
	
	function qmf4_genera_factura( $order_id, $status ) { 
		$qmf_estatus_orden = '';
		switch($status) {
			case 'Pending payment':
				$qmf_estatus_orden = 'Pending';
				break;
			case 'processing':
				$qmf_estatus_orden = 'Paid';
				break;
			case 'completed':
				$qmf_estatus_orden = 'Completed';
				break;
			case 'refunded':
			case 'cancelled':
				$qmf_estatus_orden = 'Cancelled';
				break;
		}
        if ($qmf_estatus_orden === '') return;

		$qmf_orden = wc_get_order( $order_id );
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/nusoap/nusoap.php';
		if(get_option( 'qmf_sandbox' ) == '1'){
			$wsdl = $this->WebserviceSandbox;}
		else{
			$wsdl = $this->WebserviceProduccion;}
		$client = new nusoap_client($wsdl);
		$err = $client->getError();
		if ($err) {
			$this->my_debug ('<h2>Constructor error</h2><pre>' . $err . '</pre>');
		}
		$qmf_USUARIO = get_option( 'qmf_usuario' );
		if(empty($qmf_USUARIO)) $qmf_USUARIO = $this->RFCGenerico;
		$qmf_STATUS = $qmf_orden->get_meta('qmf_facturar_ahora') == '1' ? 'FACTURAR' : 'ALMACENAR';
		$qmf_RFC = $qmf_orden->get_meta('qmf_rfc_comprador');
		if(empty($qmf_RFC)) $qmf_RFC = $this->RFCGenerico;
		$qmf_SellerOrderId = $qmf_CodProductoMarketPlace = $qmf_orden->get_order_number();
		$qmf_PurchaseDate = $qmf_orden->get_date_created()->date('Y-m-d');
		$qmf_OrderTotal_CurrencyCode = $qmf_orden->get_currency();
		$qmf_OrderTotal_Amount = $qmf_orden->get_total();
		$qmf_items = $qmf_orden->get_items();
		$qmf_NumberOfItemsShipped = 0;
		$qmf_BuyerName = htmlspecialchars ($qmf_orden->get_billing_first_name() . " " . $qmf_orden->get_billing_last_name());
		$qmf_BuyerEmail = $qmf_orden->get_billing_email();
		$qmf_UsoCFDI = $qmf_orden->get_meta('qmf_uso_cfdi');
		if(empty($qmf_UsoCFDI)) $qmf_UsoCFDI = get_option('qmf_admin_uso_cfdi');
		$qmf_nombre_receptor = $qmf_orden->get_meta('qmf_nombre_receptor');
		$qmf_cp_receptor = $qmf_orden->get_meta('qmf_cp_receptor');
		$qmf_regimen_fiscal_receptor = $qmf_orden->get_meta('qmf_regimen_fiscal_receptor');
		$qmf_forma_de_pago = $qmf_orden->get_meta('qmf_forma_de_pago');
		if ((is_null($qmf_forma_de_pago) || $qmf_forma_de_pago === '')) $qmf_forma_de_pago = '31';

		//Descuentos de cupones
		$suma_cupones = 0;
		
		//$suma_cupones = round ($qmf_orden->get_total_discount(false), 6);

		$fees_data = $qmf_orden->get_fees(); //get fee data
		$fees_amount_discount = 0;
		foreach ($fees_data as $fee) {
			$f = $fee->get_total();
			if ($f<0){ //descuentos son negativos
				$fees_amount_discount = $fees_amount_discount + ($f*-1);
			}
			$f_name = $fee->get_name();
			$f_type = $fee->get_type();
			$f_data = $fee->get_data();
			$f_total = $fee->get_total();
			$this->my_debug	("Fee Item : $f_name - $f_type, $f_total.\n" . print_r($f_data, true));
			
		}
		$tot_sin_desc = 0;
		foreach ($qmf_items as $item_id => $qmf_item) { //recorremos cada item para obtener el total
			$qmf_Title = $this->qmf4_convert_special_chars(addslashes(strip_tags($qmf_item->get_name())));
			$qmf_QuantityShipped = $qmf_item->get_quantity();
			$item_total = $qmf_orden->get_item_subtotal($qmf_item, true, true) * $qmf_QuantityShipped;
			$tot_sin_desc = $tot_sin_desc + $item_total;
			$this->my_debug	("Item : $qmf_Title, (total- $item_total)");
		}
		
		$this->my_debug("total de fees:  $fees_amount_discount, total orden sin desc $tot_sin_desc");
		$diferencia = $tot_sin_desc - $qmf_OrderTotal_Amount;
		foreach( $qmf_orden->get_coupon_codes() as $coupon_code ) {
			// Get the WC_Coupon object
			$coupon = new WC_Coupon($coupon_code);
			$discount_type = $coupon->get_discount_type(); // Get coupon discount type
			$coupon_amount = $coupon->get_amount(); // Get coupon amount
			$this->my_debug("Cupón por orden $discount_type -> $coupon_amount");
		}
		$this->my_debug("buscando cupones por item");
		$order_items = $qmf_orden->get_items('coupon');
		foreach( $order_items as $item_id => $item_coupon ){
			// Retrieving the coupon ID reference
			$coupon_post_obj = get_page_by_title( $item_coupon->get_name(), OBJECT, 'shop_coupon' );
			$coupon_id = $coupon_post_obj->ID;
		
			// Get an instance of WC_Coupon object (necessary to use WC_Coupon methods)
			$coupon = new WC_Coupon($coupon_id);
		
			// Get the Coupon discount amounts in the order
			$coupon_amount = wc_get_order_item_meta( $item_id, 'discount_amount', true );
			$order_discount_tax_amount = wc_get_order_item_meta( $item_id, 'discount_amount_tax', true );

			## Or get the coupon amount object
			$coupons_amount = $coupon->get_amount();
		
			$this->my_debug("Cupón por item $coupons_amount, $order_discount_tax_amount : " . print_r($coupon,true));
		}
		

		$taxes_enabled = wc_tax_enabled();
		$soap_action = 'RequestXMLCFDIimpuestos';

		$this->my_debug ("Están habilitados los impuestos: $taxes_enabled, servicio $soap_action");
		foreach($qmf_items as $qmf_item) {
			$qmf_NumberOfItemsShipped += (int)$qmf_item->get_quantity();
		}
		$xml = "<!-- ?xml version='1.0' encoding='UTF-8'? Generated by QMF4 V " . QMF4_VERSION . " -->";
		$xml .="	<USUARIO>$qmf_USUARIO</USUARIO>
				<STATUS>$qmf_STATUS</STATUS>
				<Order>
					<RFC>$qmf_RFC</RFC>
					<NombreReceptor>$qmf_nombre_receptor</NombreReceptor>
					<RegimenFiscalReceptor>$qmf_regimen_fiscal_receptor</RegimenFiscalReceptor>
					<codigoPostal>$qmf_cp_receptor</codigoPostal>
					<SellerOrderId>$qmf_SellerOrderId</SellerOrderId>
					<PurchaseDate>$qmf_PurchaseDate</PurchaseDate>
					<OrderStatus>$qmf_estatus_orden</OrderStatus>
					<SalesChannel>WooCommerce</SalesChannel>
					<OrderTotal>
						<CurrencyCode>$qmf_OrderTotal_CurrencyCode</CurrencyCode>
						<Amount>$qmf_OrderTotal_Amount</Amount>
					</OrderTotal>
					<NumberOfItemsShipped>$qmf_NumberOfItemsShipped</NumberOfItemsShipped>
					<BuyerName>$qmf_BuyerName</BuyerName>
					<BuyerEmail>$qmf_BuyerEmail</BuyerEmail>
					<UsoCFDI>$qmf_UsoCFDI</UsoCFDI>
					<FormaPago>$qmf_forma_de_pago</FormaPago>
				</Order>
				<ListOrderItemsResponse>
					<ListOrderItemsResult>
						<OrderItems>";
		$first_item = true;
		$index = 0;
		if ($taxes_enabled){
			$tipo_factor = "Tasa";
			$item_taxes = $qmf_orden->get_items('tax');
			$this->my_debug("Estructura TAXES: ". print_r($item_taxes,true));
			foreach( $qmf_orden->get_items('tax') as $item ){
				$this->my_debug("ITEM tax " . print_r($item, true));
				$item_id = $item->get_rate_id();
				$item_taxes[$item_id]['name']        = $this->qmf4_convert_special_chars(addslashes(strip_tags($item->get_name()))); // Get rate code name (item title)
				$item_taxes[$item_id]['rate_code']   = $item->get_rate_code(); // Get rate code
				$item_taxes[$item_id]['rate_label']  = $item->get_label(); // Get label
				$item_taxes[$item_id]['tax_total']   = $item->get_tax_total(); // Get tax total amount (for this rate)
				$item_taxes[$item_id]['ship_total']  = $item->get_shipping_tax_total(); // Get shipping tax total amount (for this rate)
				$item_taxes[$item_id]['is_compound'] = $item->is_compound(); // check if is compound (conditional)
				$item_taxes[$item_id]['compound']    = $item->get_compound(); // Get compound
				$item_taxes[$item_id]['rate_percent'] = $item->get_rate_percent(); // Get rate percent
				if (strtoupper($item_taxes[$item_id]['rate_label']) == "IVA 16"){
					$item_taxes[$item_id]['tipo_factor'] = "Tasa";
				}
				if (strtoupper($item_taxes[$item_id]['rate_label']) == "IVA 0"){
					$item_taxes[$item_id]['tipo_factor'] = "Tasa";
				}
				if (strtoupper($item_taxes[$item_id]['rate_label']) == "IVA EXENTO"){
					$item_taxes[$item_id]['tipo_factor'] = "Exento";
				}
				$this->my_debug("TAX ITEM: (item id: $item_id) ". print_r($item_taxes[$item_id],true));
			}
		}
		$index = 0;
		$suma_items = 0;
		$num_items = count($qmf_items);
		foreach($qmf_items as $item_id => $qmf_item) {
			$s = round($qmf_item->get_subtotal(),2);
			$t = round($qmf_item->get_total(),2);
            $tax = round($qmf_item->get_subtotal_tax(),2);
			$x = ($qmf_item->get_subtotal() - $qmf_item->get_total()) *1.16;
			/*if (($tax > 0) && (false === wc_prices_include_tax())){
                $this->my_debug("No tax in price included, subtracting tax from discount ($x - $tax)");
                $x = round($x - $qmf_item->get_subtotal_tax(), 2);
			}*/
			/*$taxes = $qmf_item->get_taxes();
			foreach ( $taxes as $rate_id => $tax_item) {
                $this->my_debug("ID: $rate_id, taxes: " . print_r($tax_item, true));
            }*/
			/*if (isset($taxes['total'][1])){ //TODO Review this calculation. Without it OK, if taxes not included
				$this->my_debug("Recalculate discount, before : $x");
				$x = $x + $taxes['total'][1];
                $this->my_debug("Recalculate discount, after : $x");
			}*/
			$qmf_Title = $this->qmf4_convert_special_chars(addslashes(strip_tags($qmf_item->get_name())));
			$qmf_QuantityShipped = $qmf_item->get_quantity();
			$this->my_debug("prod: $qmf_Title, sub: $s, tot $t descuento: $x");
			//$product_id = $qmf_item->get_product_id();
			$qmf_ASIN = $qmf_item->get_product_id();
			//$qmf_ItemPrice = round (wc_get_product( $qmf_item->get_product_id() )->get_price(),6);
			$qmf_ItemPrice = $qmf_orden->get_item_subtotal($qmf_item, true, true);
			//$amount_subtotal = $qmf_ItemPrice * $qmf_QuantityShipped; //Esto está mal
            $amount_subtotal = $qmf_item->get_total() + $qmf_item->get_total_tax();
			//El costo del envío se coloca en el primer item
			if ($first_item){
				$amount_shipping = $qmf_orden->get_shipping_total();
				//$amount_shipping = $qmf_orden->get_shipping_total() + $qmf_orden->get_shipping_tax();
				$first_item = false;
				$diferencia = $diferencia + $amount_shipping;
			} else{
				$amount_shipping = 0;
				$suma_cupones = 0;
			}
			if ($fees_amount_discount > 0){
				$suma_cupones = $suma_cupones + round(($amount_subtotal/$tot_sin_desc)*$fees_amount_discount, 2);
			}
			$suma_cupones = $suma_cupones + $x;
			if ((($index+1) == $num_items) ){
				$suma_cupones = $diferencia;
			}
			$diferencia = $diferencia - $suma_cupones;
			$this->my_debug ("Item Detail, Index $index, Desc $suma_cupones, Suma $suma_items, total $qmf_OrderTotal_Amount");
			$suma_items = $suma_items + $suma_cupones;
			$xml .= "
							<OrderItem>
								<ASIN>$qmf_ASIN</ASIN>
								<Title>$qmf_Title</Title>
								<QuantityShipped>$qmf_QuantityShipped</QuantityShipped>
								<ItemPrice>
									<Amount>$amount_subtotal</Amount>
								</ItemPrice>
								<ShippingPrice>
									<Amount></Amount>
								</ShippingPrice>
								<PromotionDiscount>
									<Amount>$x</Amount>
								</PromotionDiscount>";
			$this->my_debug("Items ASIN=$qmf_ASIN, #=$qmf_QuantityShipped, ship=$amount_shipping, descuento=$suma_cupones");
			if ($taxes_enabled){
				$xml .= "<!--Taxes enabled-->\n";
				$xml .= "<Impuestos>
				<Traslados>";
				$taxes = $qmf_item->get_taxes();
				$num_taxes = count($taxes['total']);
				$this->my_debug("Num de impuestos: $num_taxes");
				$this->my_debug ("TAXES per item: ". print_r($taxes,true));
                if ($num_taxes == 0){
	                $xml .= '	  <Traslado Impuesto="002" TipoFactor="Exento" TasaOCuota="0.0"/>' . "\n";
                }
				foreach($taxes['total'] as $rate_id => $tax){
					$this->my_debug ("tax per item: Value $($tax) index: $rate_id " . print_r($item_taxes[$rate_id], true));
					$tipo_factor = $item_taxes[$rate_id]['tipo_factor'] ?? "";
					$rate_percent = $item_taxes[$rate_id]['rate_percent'];
					if (is_numeric($tax) && is_numeric($item->get_shipping_tax_total())){
						$tax = $tax + $item->get_shipping_tax_total();
						//$xml .= "	  <Traslado Impuesto=\"002\" TipoFactor=\"$tipo_factor\" TasaOCuota=\"0.$rate_percent\" Importe=\"$tax\"/>
						$xml .= "	  <Traslado Impuesto=\"002\" TipoFactor=\"$tipo_factor\" TasaOCuota=\"0.$rate_percent\"/>\n";
					}
				}
				$xml .= "</Traslados>
			  </Impuestos>";
			} else {
				$xml .= "<!--Taxes NOT enabled-->\n";
				$tipo_factor = "Tasa";
				$rate_percent = 16;
				$tax = $amount_subtotal - round(($amount_subtotal / 1.16),2); //TODO: revisar redondeo y esto ya lo tiene Woo calculado
				if ($amount_shipping > 0){
					//$tax = $tax + $qmf_orden->get_shipping_tax();
					$tax = $tax + $amount_shipping-round(($amount_shipping/1.16),2);
					$this->my_debug("Shipping $amount_shipping, " . print_r($qmf_orden->get_shipping_tax(),true));
				}
				if ($fees_amount_discount > 0){
					$tax_discount = $fees_amount_discount - round($fees_amount_discount / 1.16,2);
					$this->my_debug("descuentos fees: $fees_amount_discount, descuento iva: $tax_discount, tax = $tax");
					$tax = $tax - $tax_discount;
				}
				$xml .= "<Impuestos>
				<Traslados>";
				//$xml .= "	  <Traslado Impuesto=\"002\" TipoFactor=\"$tipo_factor\" TasaOCuota=\"0.$rate_percent\" Importe=\"$tax\"/>";
				$xml .= "	  <Traslado Impuesto=\"002\" TipoFactor=\"$tipo_factor\" TasaOCuota=\"0.$rate_percent\"/>\n";
				$xml .= "</Traslados>
			  </Impuestos>";
			  $this->my_debug("Usando default 16%: $tax");
			}
			$xml .= "</OrderItem>";
			$index++;
			$this->my_debug("valores tax: TipoFactor=$tipo_factor TasaOCuota=0.$rate_percent Importe=$tax");
		}

		//Agregar shippment como un item, dejarlo al final
		$amount_shipping = $qmf_orden->get_shipping_total() + $qmf_orden->get_shipping_tax();
		if ($amount_shipping){
			$xml .= "
			<OrderItem>
				<ASIN>01Ship</ASIN>
				<Title>Gastos de Envío</Title>
				<QuantityShipped>1</QuantityShipped>
				<ItemPrice>
					<Amount>$amount_shipping</Amount>
				</ItemPrice>
				<ShippingPrice>
					<Amount></Amount>
				</ShippingPrice>
				<PromotionDiscount>
					<Amount></Amount>
				</PromotionDiscount>";
			$xml .= "<Impuestos>
			<Traslados>";
			//$xml .= "	  <Traslado Impuesto=\"002\" TipoFactor=\"$tipo_factor\" TasaOCuota=\"0.$rate_percent\" Importe=\"$tax\"/>";
			$xml .= "	  <Traslado Impuesto=\"002\" TipoFactor=\"$tipo_factor\" TasaOCuota=\"0.$rate_percent\"/></Traslados>
			</Impuestos>";
			$xml .= "</OrderItem>";
		}
		$xml .= "
				</OrderItems>
					</ListOrderItemsResult>
				</ListOrderItemsResponse>";
		
		$orden = $client->call($soap_action,$xml);
		if(isset($orden['DOCUMENTOS'])) {
			update_post_meta($order_id, 'qmf_link_factura', $orden['DOCUMENTOS']);
			update_post_meta($order_id, 'qmf_link_factura_PDF', $orden['DOCUMENTOPDF']);
		}
		$this->my_debug("QMF4 Ver " . QMF4_VERSION . "Envío de Orden: $order_id con servicio $soap_action, impuestos: $taxes_enabled");
		$this->my_debug("-------------------------------------------------------------------------");
		$this->my_debug("URL: $wsdl ");
		$this->my_debug("XML: $xml ");
		if ($orden === ""){
			$this->my_debug("Respuesta Webservice ERROR no responde server: \n");
		}else{
			$this->my_debug("Respuesta Webservice: \n" . print_r($orden, TRUE));
		}
	}

	function qmf4_liga_factura_thankyou($order_id) {
		$qmf_orden = wc_get_order( $order_id );
		//$qmf_documentos = $qmf_orden->get_meta('qmf_link_factura');
		//$qmf_documentos_pdf = $qmf_orden->get_meta('qmf_link_factura_PDF');
		$qmf_mensaje = nl2br( get_option( 'qmf_mensaje_thankyou' ) ) . '<br /><br />';
		/*
		if(!empty($qmf_documentos)) {
			$qmf_mensaje = str_replace('[liga_documentos]', "<a href='$qmf_documentos'>Descargar (PDF y XML)</a>", $qmf_mensaje);
			$qmf_mensaje =str_replace('[liga_pdf]', "<a href='$qmf_documentos_pdf' target='_blank'>Consultar PDF</a>", $qmf_mensaje);
			echo $qmf_mensaje;
		} else {
			$qmf_mensaje = str_replace('[liga_documentos]', "", $qmf_mensaje);
			$qmf_mensaje =str_replace('[liga_pdf]', "", $qmf_mensaje);
			echo $qmf_mensaje;
		}*/
		echo $qmf_mensaje;
	}

	/**
	 * Despliega un mensaje de error si no existe la clave de unidad
	 *
	 * @since     1.0.0
	 */
	function qmf4_no_existe_clave_unidad() {
		?>
		<div class="error notice">
			<p><?php _e( '¡La clave de unidad del SAT no existe!', 'qmf' ); ?></p>
		</div>
		<?php
	}

	function qmf4_agrega_columna_wc_productos( $columns ){
		return $columns + array('qmf_facturable' => '¿Facturable?');
	}

	function qmf4_llena_columna_wc_productos( $column_name ) {
		global $wpdb, $post;
		if( $column_name == 'qmf_facturable' ) {
			$qmf_tabla_unidades = $wpdb->prefix . "qmf_unidades"; 
			$qmf_tabla_productos_servicios = $wpdb->prefix . "qmf_productos_servicios"; 
			$qmf_producto = wc_get_product( get_the_id() );
			$qmf_unidad_medida = $qmf_producto->get_meta('qmf_unidad_medida');
			$qmf_clave_unidad_sat = $qmf_producto->get_meta('qmf_clave_unidad_sat');
			$qmf_clave_producto_servicio_sat = $qmf_producto->get_meta('qmf_clave_producto_servicio_sat');
			if(empty($qmf_unidad_medida)) {
				echo "<span class='qmf_icon qmf_no'></span>";
				return;
			}
			$existe = (int)$wpdb->get_var("select count(*) from $qmf_tabla_unidades where id = '" . sanitize_text_field( $qmf_clave_unidad_sat ) . "'");
			if(!$existe) {
				echo "<span class='qmf_icon qmf_no'></span>";
				return;
			}
			$existe = (int)$wpdb->get_var("select count(*) from $qmf_tabla_productos_servicios where id = '" . sanitize_text_field( $qmf_clave_producto_servicio_sat ) . "'");
			if(!$existe) {
				echo "<span class='qmf_icon qmf_no'></span>";
				return;
			}
			echo "<span class='qmf_icon qmf_si'></span>";
		}
	}

	function qmf4_add_action_batch_orders ($bulk_actions){
        //insert our custom action at beginning
		return array( "refresh_qmf" => __( "Refresh Status at QMF", "qmf" ) ) + $bulk_actions;
	}

	function qmf4_bulk_action_handler ( $redirect_to, $doaction, $post_ids ){
		$this->my_debug ("action handler con action = $doaction");
		if ( $doaction !== "refresh_qmf" ) {
			return $redirect_to;
		}
		$this->my_debug("Inicio loop");
		foreach ( $post_ids as $post_id ) {
			$this->my_debug("Loop $post_id");
			// Perform action for each post.
			$order = new WC_Order($post_id);
			$status_to = $order->get_status();
			$this->qmf4_genera_factura($post_id, $status_to);
		}
		return add_query_arg( 'refreshed_orders', count( $post_ids ), $redirect_to );
	}

	function qmf4_bulk_action_admin_notice(){
		if ( ! empty( $_REQUEST['refreshed_orders'] ) ) {
			$refreshed_count = intval( $_REQUEST['refreshed_orders'] );
			printf( '<div id="message" class="updated fade">' .
			  _n( '%s órdenes actualizadas.',
				'%s órdenes actualizadas',
				$refreshed_count,
				'qmf_update_order'
			  ) . '</div>', $refreshed_count);
		  }
	}

	function qmf4_show_data_profile (WP_User $user){
		$qmf_facturar_ahora = get_user_meta( $user->ID, 'qmf_facturar_ahora', true );
		$qmf_rfc_comprador = get_user_meta( $user->ID, 'qmf_rfc_comprador', true );
		$qmf_nombre_receptor = get_user_meta( $user->ID, 'qmf_nombre_receptor', true );
		$qmf_uso_cfdi = get_user_meta( $user->ID, 'qmf_uso_cfdi', true );
		$qmf_cp_receptor = get_user_meta( $user->ID, 'qmf_cp_receptor', true );

		$qmf_facturar_ahora = empty($qmf_facturar_ahora) ? 'No' : 'Si';
		
		echo '<h3>Datos de facturación</h3>';
		echo '<p>';
		echo '	<strong>'.__('¿Solictó factura al realizar el pedido?').':</strong> ' . $qmf_facturar_ahora . '<br><br>';
		echo '	<strong>'.__('RFC, nombre, CP del comprador').':</strong> ' . $qmf_rfc_comprador . ' ' . $qmf_nombre_receptor . ' ' . $qmf_cp_receptor . '</br><br>';
		echo '	<strong>'.__('Uso CFDI').':</strong> ' . $qmf_uso_cfdi . '</br><br>';
		echo '</p>';
	}

	function my_debug($msj){
		if((WP_DEBUG == true) || (get_option('qmf_DEBUG') == 1)) {
			date_default_timezone_set(get_option('timezone_string'));
			$usuario = get_option( 'qmf_usuario' );
			$s = wp_upload_dir(null, true, true);
			$log = fopen($s['path'] . "/Qmf4_$usuario.log", "a+");
			fputs($log, date('Y-m-d h:i:s A') . " $msj\n");
			fclose($log);
		}
	}

	function debug_file_read($filePath) {
		if (file_exists($filePath)) {
			$file = fopen($filePath, "r");
			$responce = '';
			fseek($file, -1048576, SEEK_END);
			while (!feof($file)) {
				$responce .= fgets($file);
			}
			fclose($file);
			return $responce;
		}
		return false;
	}
	
	function debug_file_download() {
		$usuario = get_option( 'qmf_usuario' );
		$s = wp_upload_dir(null, true, true);
		$path = $s['path'] . "/Qmf4_$usuario.log";
		$content = $this->debug_file_read($path);
		header('Content-type: application/octet-stream', true);
		header('Content-Disposition: attachment; filename="' . basename($path) . '"', true);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $content;
		exit();
	}

	function test_conexion(){
		header('Content-Type: application/xml');
		$handle = curl_init();
		// Set the url
		$url = "https://quieromifactura.mx/QA2/web_services/servidorMarket.php?wsdl";
		curl_setopt($handle, CURLOPT_URL, $url);

		// Set the result output to be a string.
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($handle);
	    print_r($output);
		exit();
	}
}
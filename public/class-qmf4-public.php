<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://urano.dev
 * @since      1.0.0
 *
 * @package    Qmf4
 * @subpackage Qmf4/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Qmf4
 * @subpackage Qmf4/public
 * @author     Urano Dev <u@urano.dev>
 */
class Qmf4_Public {

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Qmf4_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Qmf4_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/qmf4-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Qmf4_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Qmf4_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/qmf4-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Agrega los campos de facturación al checkout
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	function qmf4_agrega_campos_checkout( $checkout ) {
		global $wpdb;

		$qmf_tabla_uso_CFDI = $wpdb->prefix . "qmf_uso_cfdi"; 
		$qmf_uso_CFDI_results = $wpdb->get_results("SELECT * from $qmf_tabla_uso_CFDI order by id");
		$qmf_uso_CFDI_opciones = array();
		foreach($qmf_uso_CFDI_results as $qmf_uso_CFDI_row) {
			$qmf_uso_CFDI_opciones[$qmf_uso_CFDI_row->id] = "($qmf_uso_CFDI_row->id) $qmf_uso_CFDI_row->descripcion";
		}

		//select para régimen fiscal
		$qmf_tabla_regimen_fiscal = $wpdb->prefix . "qmf_regimen_fiscal"; 
		$qmf_regimen_fiscal_results = $wpdb->get_results("SELECT * from $qmf_tabla_regimen_fiscal order by c_RegimenFiscal");
		$qmf_regimen_fiscal_opciones = array();
		foreach($qmf_regimen_fiscal_results as $qmf_regimen_fiscal_row) {
			$qmf_regimen_fiscal_opciones[$qmf_regimen_fiscal_row->c_RegimenFiscal] =  "($qmf_regimen_fiscal_row->c_RegimenFiscal) $qmf_regimen_fiscal_row->descripcion";
		}

		//select para forma de pagos
		$qmf_tabla_forma_de_pago = $wpdb->prefix . "qmf_forma_de_pago";
		$qmf_forma_de_pago_results = $wpdb->get_results ("SELECT * from $qmf_tabla_forma_de_pago");
		$qmf_forma_de_pago_opciones = array();
		foreach ($qmf_forma_de_pago_results as $qmf_forma_de_pago_row) {
			$qmf_forma_de_pago_opciones[$qmf_forma_de_pago_row->id] = "($qmf_forma_de_pago_row->id) $qmf_forma_de_pago_row->descripcion";
		}

		echo '<div id="qmf_campos_checkout">';

		$checked = $checkout->get_value( 'qmf_facturar_ahora' ) ? $checkout->get_value( 'qmf_facturar_ahora' ) : 1;
		
		woocommerce_form_field( 'qmf_facturar_ahora', array( 
			'type' => 'checkbox', 
			'class' => array('input-checkbox'), 
			'label' => __('¿Emitir factura?'), 
			'required' => false,
		), $checkout->get_value( 'qmf_facturar_ahora' ) );

		echo "<p style='border: 1px solid orange;color:orange;padding:15px'>Si no solicita su factura en este momento puede hacerlo posteriormente</p>";

		echo '<div id="qmf_campos_checkout_detalle">';

		woocommerce_form_field( 'qmf_rfc_comprador', array(
			'type'          => 'text',
			'class'         => array('my-field-class form-row-wide'),
			'label'         => __('RFC'),
			'placeholder'   => __('Ingrese su RFC (Sin espacios ni guiones)'),
			'required' 		=> false,
			), $checkout->get_value( 'qmf_rfc_comprador' ));

		woocommerce_form_field( 'qmf_nombre_receptor', array(
			'type'          => 'text',
			'class'         => array('my-field-class form-row-wide'),
			'label'         => __('Razón Social'),
			'placeholder'   => __('Ingrese su Razón Social'),
			'required' 		=> false,
			), $checkout->get_value( 'qmf_nombre_receptor' ));

		woocommerce_form_field( 'qmf_regimen_fiscal_receptor', array( 
			'type' => 'select', 
			'options' => $qmf_regimen_fiscal_opciones,
			'label' => __('Régimen Fiscal'), 
			'required' => false,
		), $checkout->get_value( 'qmf_regimen_fiscal_receptor' ) );

		woocommerce_form_field( 'qmf_uso_cfdi', array( 
			'type' => 'select', 
			'options' => $qmf_uso_CFDI_opciones,
			'label' => __('Uso CFDI'), 
			'required' => false,
		), $checkout->get_value( 'qmf_uso_cfdi' ) );

		woocommerce_form_field( 'qmf_forma_de_pago', array(
			'type' => 'select',
			'options' => $qmf_forma_de_pago_opciones,
			'label' => __('Forma de pago de esta compra'),
			'requeired' => false,
		), $checkout->get_value('qmf_forma_de_pago'));
		

		woocommerce_form_field( 'qmf_cp_receptor', array(
			'type'          => 'text',
			'class'         => array('my-field-class form-row-wide'),
			'label'         => __('Código Postal'),
			'placeholder'   => __('Ingrese su Código Postal'),
			'required' 		=> false,
			), $checkout->get_value( 'qmf_cp_receptor' ));
			

		
		echo '</div></div>';
	}	

	/**
	 * Valida los campos de facturación del checkout antes de guardarlos
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	function qmf4_valida_campos_checkout() {
		// Check if set, if its not set add an error.
		if ( isset($_POST['qmf_facturar_ahora']) ) {
			if ( empty($_POST['qmf_rfc_comprador']) )
				wc_add_notice( __( 'Si desea facturar ahora se requiere su RFC' ), 'error' );
			$patron = '/^([A-Z,Ñ,&]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[A-Z|\d]{3})$/';
			$_POST['qmf_rfc_comprador'] = strtoupper ( $_POST['qmf_rfc_comprador'] );
			if(!preg_match($patron, $_POST['qmf_rfc_comprador']))
				wc_add_notice( __( 'Hay un error en su RFC' ), 'error' );
		}
	}

	/**
	 * Guarda los campos de facturación que se registraron en el checkout
	 *
	 * @since    1.0.0
	 */
	function qmf4_guarda_campos_checkout( $order_id ) {
		global $current_user;
		wp_get_current_user();

		if ( ! empty( $_POST['qmf_facturar_ahora'] ) )
			update_post_meta( $order_id, 'qmf_facturar_ahora', sanitize_text_field( $_POST['qmf_facturar_ahora'] ) );
		if ( ! empty( $_POST['qmf_rfc_comprador'] ) )
			update_post_meta( $order_id, 'qmf_rfc_comprador', sanitize_text_field( $_POST['qmf_rfc_comprador'] ) );
		if ( ! empty( $_POST['qmf_nombre_receptor'] ) )
		update_post_meta( $order_id, 'qmf_nombre_receptor', sanitize_text_field( $_POST['qmf_nombre_receptor'] ) );
		if ( ! empty( $_POST['qmf_regimen_fiscal_receptor'] ) )
		update_post_meta( $order_id, 'qmf_regimen_fiscal_receptor', sanitize_text_field( $_POST['qmf_regimen_fiscal_receptor'] ) );
		if ( ! empty( $_POST['qmf_uso_cfdi'] ) )
			update_post_meta( $order_id, 'qmf_uso_cfdi', sanitize_text_field( $_POST['qmf_uso_cfdi'] ) );
		if ( ! empty( $_POST['qmf_cp_receptor'] ) )
			update_post_meta( $order_id, 'qmf_cp_receptor', sanitize_text_field( $_POST['qmf_cp_receptor'] ) );
		if ( ! empty( $_POST['qmf_forma_de_pago'] ) )
			update_post_meta( $order_id, 'qmf_forma_de_pago', sanitize_text_field( $_POST['qmf_forma_de_pago'] ) );

		if ( $current_user ) {
			if ( ! empty( $_POST['qmf_facturar_ahora'] ) )
				update_user_meta( $current_user->ID, 'qmf_facturar_ahora', sanitize_text_field( $_POST['qmf_facturar_ahora'] ) );
			if ( ! empty( $_POST['qmf_rfc_comprador'] ) )
				update_user_meta( $current_user->ID, 'qmf_rfc_comprador', sanitize_text_field( $_POST['qmf_rfc_comprador'] ) );
			if ( ! empty( $_POST['qmf_nombre_receptor'] ) )
				update_user_meta( $current_user->ID, 'qmf_nombre_receptor', sanitize_text_field( $_POST['qmf_nombre_receptor'] ) );
			if ( ! empty( $_POST['qmf_regimen_fiscal_receptor'] ) )
				update_user_meta( $current_user->ID, 'qmf_regimen_fiscal_receptor', sanitize_text_field( $_POST['qmf_regimen_fiscal_receptor'] ) );
			if ( ! empty( $_POST['qmf_uso_cfdi'] ) )
				update_user_meta( $current_user->ID, 'qmf_uso_cfdi', sanitize_text_field( $_POST['qmf_uso_cfdi'] ) );
			if ( ! empty( $_POST['qmf_cp_receptor'] ) )
				update_user_meta( $current_user->ID, 'qmf_cp_receptor', sanitize_text_field( $_POST['qmf_cp_receptor'] ) );
			if ( ! empty( $_POST['qmf_forma_de_pago'] ) )
				update_user_meta( $current_user->ID, 'qmf_forma_de_pago', sanitize_text_field( $_POST['qmf_forma_de_pago'] ) );
		}
	}
	

	/**
	 * Actualización de los query_vars para agregar la pestaña de datos
	 * de facturación a la página de Mi Cuenta
	 *
	 * @since    1.0.0
	 */
	function qmf4_query_vars( $vars ) {
		$vars[] = 'datos-de-facturacion';
		return $vars;
	}

	/**
	 * Agrega la pestaña de Datos de Facturación a la página de Mi Cuenta
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	function qmf4_mi_cuenta_menu_datos_facturacion( $items ) {
		// Remove the logout menu item.
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );
	
		// Insert your custom endpoint.
		$items['datos-de-facturacion'] = __( 'Datos de facturacion', 'qmf4' );
	
		// Insert back the logout item.
		$items['customer-logout'] = $logout;
	
		return $items;
	}

	/**
	 * Cambia el título de la página Mi Cuenta cuando se selecciona la pestaña
	 * de Datos de Facturación
	 *
	 * @since    1.0.0
	 */
	function qmf4_mi_cuenta_menu_datos_facturacion_titulo( $title ) {
		global $wp_query;
	
		$is_endpoint = isset( $wp_query->query_vars['datos-de-facturacion'] );
		if (is_account_page()) {
			$this->my_debug("titulo de admin page " . print_r($wp_query->query_vars . " y facturacion habilitada: " . get_option( 'qmf_habilitar_facturacion' ) ,true));
		}
	
		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			$title = __( 'Datos de Facturación', 'qmf4' );
	
			remove_filter( 'the_title', 'qmf_mi_cuenta_menu_datos_facturacion_titulo' );
		}
	
		return $title;
	}
	
	/**
	 * Despliega y guarda los datos de la pestaña de Datos de Facturación en la página
	 * de Mi Cuenta
	 *
	 * @since    1.0.0
	 */
	function qmf4_mi_cuenta_menu_datos_facturacion_campos() {
		global $current_user, $wpdb;

		wp_get_current_user();
		

		$qmf_tabla_uso_CFDI = $wpdb->prefix . "qmf_uso_cfdi"; 
		$qmf_uso_CFDI_results = $wpdb->get_results("SELECT * from $qmf_tabla_uso_CFDI order by id");

		$qmf_tabla_regimen_fiscal = $wpdb->prefix . "qmf_regimen_fiscal"; 
		$qmf_regimen_fiscal_receptor_results = $wpdb->get_results("SELECT * from $qmf_tabla_regimen_fiscal order by c_RegimenFiscal");

		$qmf_tabla_forma_de_pago = $wpdb->prefix . "qmf_forma_de_pago";
		$qmf_forma_de_pago_results = $wpdb->get_results ("SELECT * from $qmf_tabla_forma_de_pago");

		if ( $current_user ) {
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$hay_error = false;
				$qmf_facturar_ahora = $_POST['qmf_facturar_ahora'];
				$qmf_rfc_comprador = $_POST['qmf_rfc_comprador'];
				$qmf_nombre_receptor = $_POST['qmf_nombre_receptor'];
				$qmf_regimen_fiscal_receptor = $_POST['qmf_regimen_fiscal_receptor'];
				$qmf_uso_cfdi = $_POST['qmf_uso_cfdi'];
				$qmf_forma_de_pago = $_POST['qmf_forma_de_pago'];
				$qmf_cp_receptor = $_POST['qmf_cp_receptor'];
				if(empty($qmf_facturar_ahora)) 
					$qmf_facturar_ahora = '0';

				if ( $qmf_facturar_ahora ) {
					if ( empty($qmf_rfc_comprador) ) {
						echo "<div class='woocommerce'>";
						echo "		<ul class='woocommerce-error' role='alert'>";
						echo "			<li>";
						echo "				" . __('Si desea emitir su factura al momento de realizar la compra es necesario que proporcione su RFC', 'qmf4');
						echo "			</li>";
						echo "		</ul>";
						echo "</div>";
						$hay_error = true;
					} else {
						$patron = '/^([A-Z,Ñ,&]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])[A-Z|\d]{3})$/';
						$qmf_rfc_comprador = strtoupper ( $qmf_rfc_comprador );
						if(!preg_match($patron, $qmf_rfc_comprador)) {
								echo "<div class='woocommerce'>";
								echo "		<ul class='woocommerce-error' role='alert'>";
								echo "			<li>";
								echo "				" . __('Hay un error en su RFC', 'qmf4');
								echo "			</li>";
								echo "		</ul>";
								echo "</div>";
								$hay_error = true;
						}
					}
				}
				if(!$hay_error) {
					update_user_meta( $current_user->ID, 'qmf_facturar_ahora', sanitize_text_field( $_POST['qmf_facturar_ahora'] ) );
					update_user_meta( $current_user->ID, 'qmf_rfc_comprador', sanitize_text_field( $_POST['qmf_rfc_comprador'] ) );
					update_user_meta( $current_user->ID, 'qmf_nombre_receptor', sanitize_text_field($_POST['qmf_nombre_receptor']) );
					update_user_meta( $current_user->ID, 'qmf_regimen_fiscal_receptor', sanitize_text_field( $_POST['qmf_regimen_fiscal_receptor'] ) );
					update_user_meta( $current_user->ID, 'qmf_uso_cfdi', sanitize_text_field( $_POST['qmf_uso_cfdi'] ) );
					update_user_meta( $current_user->ID, 'qmf_forma_de_pago', sanitize_text_field( $_POST['qmf_forma_de_pago']));
					update_user_meta( $current_user->ID, 'qmf_cp_receptor', sanitize_text_field( $_POST['qmf_cp_receptor'] ) );

					echo "<div class='woocommerce'>";
					echo "		<ul class='woocommerce-message' role='alert'>";
					echo "			<li>";
					echo "				" . __('Sus datos de facturación se han actualizado correctamente', 'qmf4');
					echo "			</li>";
					echo "		</ul>";
					echo "</div>";
				}
			}

			$qmf_facturar_ahora = get_user_meta( $current_user->ID, 'qmf_facturar_ahora' , true );
			$qmf_rfc_comprador = get_user_meta( $current_user->ID, 'qmf_rfc_comprador' , true );
			$qmf_nombre_receptor = get_user_meta( $current_user->ID, 'qmf_nombre_receptor', true);			
			$qmf_regimen_fiscal_receptor = get_user_meta( $current_user->ID, 'qmf_regimen_fiscal_receptor' , true );
			$qmf_uso_cfdi = get_user_meta( $current_user->ID, 'qmf_uso_cfdi', true );
			$qmf_forma_de_pago = get_user_meta( $current_user->ID, 'qmf_forma_de_pago', true );
			$qmf_cp_receptor = get_user_meta( $current_user->ID, 'qmf_cp_receptor' , true );
		}

		echo "<form class='woocommerce-EditDatosFacturacionForm edit-account' action='' method='post'>";
		echo "		<p class='form-row input-checkbox' id='qmf_facturar_ahora_field' data-priority=''>";
		echo "			<span class='woocommerce-input-wrapper'>";
		echo "				<label class='checkbox '>";
		echo "					<input type='checkbox' class='input-checkbox ' name='qmf_facturar_ahora' id='qmf_facturar_ahora' value='1' " . checked( $qmf_facturar_ahora, true, false ) . "> " . __( '¿Deseo que se emita mi factura al momento de realizar la compra?', 'qmf4' );
		echo "				</label>";
		echo "			</span>";
		echo "		</p>";
		echo "		<p class='form-row form-row-wide' id='qmf_rfc_comprador_field'>";
		echo "			<label for='qmf_rfc_comprador'>" . __( 'RFC', 'qmf4' ) . "</label>";
		echo "			<span class='woocommerce-input-wrapper'>";
		echo "				<input type='text' class='input-text' name='qmf_rfc_comprador' id='qmf_rfc_comprador' placeholder='" . __( 'Ingrese su RFC (Sin espacios ni guiones)', 'qmf4' ) . "' value='" . $qmf_rfc_comprador . "'>";
		echo "			</span>";
		echo "		</p>";
		echo "		<p class='form-row form-row-wide' id='qmf_nombre_receptor'>";
		echo "			<label for='qmf_rfc_comprador'>" . __( 'Nombre Receptor', 'qmf4' ) . "</label>";
		echo "			<span class='woocommerce-input-wrapper'>";
		echo "				<input type='text' class='input-text' name='qmf_nombre_receptor' id='qmf_nombre_receptor' placeholder='" . __( 'Ingrese su Nombre o Razón Social', 'qmf4' ) . "' value='" . $qmf_nombre_receptor . "'>";
		echo "			</span>";
		echo "		</p>";
		echo "		<p class='form-row' id='qmf_regimen_fiscal_receptor' data-priority=''>";
		echo "			<span class='woocommerce-input-wrapper'>";
		echo "				<label for='qmf_regimen_fiscal_receptor'>" . __( 'Régimen Fiscal', 'qmf4' ) . "</label>";
		echo "				<span class='woocommerce-input-wrapper'>";
		echo "					<select name='qmf_regimen_fiscal_receptor' id='qmf_regimen_fiscal_receptor' value='$qmf_regimen_fiscal_receptor'>";
		foreach($qmf_regimen_fiscal_receptor_results as $qmf_regimen_fiscal_receptor_row) {
			$qmf_selected = "";
			if($qmf_regimen_fiscal_receptor == $qmf_regimen_fiscal_receptor_row->c_RegimenFiscal) $qmf_selected = 'selected';
			echo "					<option value='$qmf_regimen_fiscal_receptor_row->c_RegimenFiscal' $qmf_selected>($qmf_regimen_fiscal_receptor_row->c_RegimenFiscal) $qmf_regimen_fiscal_receptor_row->descripcion</option>";
		}
		echo "					</select>";
		echo "				</span>";
		echo "			</span>";
		echo "		</p>";
		echo "		<p class='form-row' id='qmf_uso_cfdi' data-priority=''>";
		echo "			<span class='woocommerce-input-wrapper'>";
		echo "				<label for='qmf_uso_cfdi'>" . __( 'Uso del CFDI', 'qmf4' ) . "</label>";
		echo "				<span class='woocommerce-input-wrapper'>";
		echo "					<select name='qmf_uso_cfdi' id='qmf_uso_cfdi' value='$qmf_uso_cfdi'>";
		foreach($qmf_uso_CFDI_results as $qmf_uso_CFDI_row) {
			$qmf_selected = "";
			if($qmf_uso_cfdi == $qmf_uso_CFDI_row->id) $qmf_selected = 'selected';
			echo "					<option value='$qmf_uso_CFDI_row->id' $qmf_selected>($qmf_uso_CFDI_row->id) $qmf_uso_CFDI_row->descripcion</option>";
		}
		echo "					</select>";
		echo "				</span>";
		echo "			</span>";
		echo "		</p>";
		echo "		</p>";
		echo "		<p class='form-row' id='qmf_forma_de_pago' data-priority=''>";
		echo "			<span class='woocommerce-input-wrapper'>";
		echo "				<label for='qmf_forma_de_pago'>" . __( 'Forma de Pago preferida', 'qmf4' ) . "</label>";
		echo "				<span class='woocommerce-input-wrapper'>";
		echo "					<select name='qmf_forma_de_pago' id='qmf_forma_de_pago' value='$qmf_forma_de_pago'>";
		foreach($qmf_forma_de_pago_results as $qmf_forma_de_pago_results_row) {
			$qmf_selected = "";
			if($qmf_forma_de_pago == $qmf_forma_de_pago_results_row->id) $qmf_selected = 'selected';
			echo "					<option value='$qmf_forma_de_pago_results_row->id' $qmf_selected>($qmf_forma_de_pago_results_row->id) $qmf_forma_de_pago_results_row->descripcion</option>";
		}
		echo "					</select>";
		echo "				</span>";
		echo "			</span>";
		echo "		</p>";
		echo "		<p class='form-row form-row-wide' id='qmf_cp_receptor'>";
		echo "			<label for='qmf_cp_receptor'>" . __( 'Código postal', 'qmf4' ) . "</label>";
		echo "			<span class='woocommerce-input-wrapper'>";
		echo "				<input type='text' class='input-text' name='qmf_cp_receptor' id='qmf_cp_receptor' placeholder='" . __( 'Ingrese el código postal de su domicilio fiscal', 'qmf4' ) . "' value='" . $qmf_cp_receptor . "'>";
		echo "			</span>";
		echo "		</p>";
		echo "		<button id='submit' type='submit' value='" . __( 'Guardar los Cambios', 'qmf4') . "'>" . __( 'Guardar los Cambios', 'qmf4') . "</button>";
		echo "</form>";
	}

	function my_debug($msj){
		$s = get_option('qmf_DEBUG');
		if((WP_DEBUG == true) || (get_option('qmf_DEBUG') == 1)) {
			date_default_timezone_set(wp_timezone_string());
			$usuario = get_option( 'qmf_usuario' );
			$log = fopen(plugin_dir_path( dirname( __FILE__ ) ) . "Qmf4_$usuario.log", "a+");
			fputs($log, date('Y-m-d h:i:s A') . " $msj\n");
			fclose($log);
		}
	}
}

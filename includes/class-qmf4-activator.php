<?php

/**
 * Fired during plugin activation
 *
 * @link       https://codigoti.tech
 * @since      1.0.0
 *
 * @package    Qmf
 * @subpackage Qmf/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Qmf
 * @subpackage Qmf/includes
 * @author     Dario Morales <tlatlauki@hotmail.com>
 */
class Qmf4_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		Qmf4_Activator::qmf4_agrega_datos_facturacion_endpoint();
		Qmf4_Activator::qmf4_crea_tablas();
	}

	/**
	 * Agrega el endpoint que se necesita para adicionar la pestaña de Datos de Facturación
	 * de la página Mi Cuenta y guarda los enlaces permanentes para que sea reconocida
	 *
	 * @since    1.0.0
	 */
	static function qmf4_agrega_datos_facturacion_endpoint() {
		add_rewrite_endpoint( 'datos-de-facturacion', EP_PERMALINK | EP_PAGES );
		flush_rewrite_rules();
	}

	/**
	 * Agrega las tablas necesarias con el contenido de los catálogos del SAT
	 *
	 * @since    1.0.0
	 */
	static function qmf4_crea_tablas() {
		global $wpdb;

		$qmf_tabla_unidades = $wpdb->prefix . "qmf_unidades"; 
		$qmf_tabla_productos_servicios = $wpdb->prefix . "qmf_productos_servicios"; 
		$qmf_tabla_uso_cfdi = $wpdb->prefix . "qmf_uso_cfdi";
		$qmf_tabla_forma_de_pago = $wpdb->prefix . "qmf_forma_de_pago";
		$qmf_tabla_regimen_fiscal = $wpdb->prefix . "qmf_regimen_fiscal";

		$wpdb->query("DROP TABLE IF EXISTS `$qmf_tabla_unidades`");
		$wpdb->query("DROP TABLE IF EXISTS `$qmf_tabla_productos_servicios`");
		$wpdb->query("DROP TABLE IF EXISTS `$qmf_tabla_uso_cfdi`");
		$wpdb->query("DROP TABLE IF EXISTS `$qmf_tabla_forma_de_pago`");
		$wpdb->query("DROP TABLE IF EXISTS `$qmf_tabla_regimen_fiscal`");
	

		$sql = "CREATE TABLE $qmf_tabla_unidades (
					id varchar(255) NOT NULL,
					nombre varchar(255),
					descripcion varchar(255),
					PRIMARY KEY  (id),
					INDEX (nombre)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

				CREATE TABLE $qmf_tabla_productos_servicios (
					id varchar(255) NOT NULL,
					descripcion varchar(255),
					PRIMARY KEY  (id),
					INDEX (descripcion)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

				CREATE TABLE $qmf_tabla_forma_de_pago (
				`id` varchar(4) NOT NULL,
				`descripcion` varchar(48) NOT NULL,
				UNIQUE KEY `c_FormaPago` (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

				CREATE TABLE $qmf_tabla_regimen_fiscal (
				`c_RegimenFiscal` int(11) NOT NULL,
				`descripcion` varchar(120) NOT NULL,
				PRIMARY KEY (`c_RegimenFiscal`),
				UNIQUE KEY `c_RegimenFiscal` (`c_RegimenFiscal`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

				CREATE TABLE $qmf_tabla_uso_cfdi (
				`id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
				`descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `descripcion` (`descripcion`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$error = dbDelta( $sql );

		$qmf_data = array();
		if (($gestor = fopen(plugin_dir_path( dirname( __FILE__ ) ) . "data/qmf_unidades.csv", "r")) !== FALSE) {
			while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
				array_push($qmf_data, array( 
					'id' => $datos[0], 
					'nombre' => $datos[1], 
					'descripcion' =>  $datos[2], 
				));
			}
			Qmf4_Activator::qmf4_wpdb_bulk_insert($qmf_tabla_unidades, $qmf_data);
			fclose($gestor);
		}
		

		$qmf_data = [];
		if (($gestor = fopen(plugin_dir_path( dirname( __FILE__ ) ) . "data/qmf_productos_servicios.csv", "r")) !== FALSE) {
			while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
				array_push($qmf_data, array( 
					'id' => $datos[0], 
					'descripcion' =>  $datos[1], 
				));
			}
			Qmf4_Activator::qmf4_wpdb_bulk_insert($qmf_tabla_productos_servicios, $qmf_data);
			fclose($gestor);
		}

		$qmf_data = [];
		if (($gestor = fopen(plugin_dir_path( dirname( __FILE__ ) ) . "data/qmf_UsoCFDI.csv", "r")) !== FALSE) {
			while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
				array_push($qmf_data,
					array( 
						'id' => $datos[0], 
						'descripcion' =>  $datos[1], 
					) 
				);			
			}
			Qmf4_Activator::qmf4_wpdb_bulk_insert($qmf_tabla_uso_cfdi, $qmf_data);
			fclose($gestor);
		}
		
		$qmf_data = [];
		if (($gestor = fopen(plugin_dir_path( dirname( __FILE__ ) ) . "data/qmf_FormaPago.csv", "r")) !== FALSE) {
			while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
				array_push($qmf_data, array( 
					'id' => $datos[0], 
					'descripcion' =>  $datos[1], 
				));
			}
			Qmf4_Activator::qmf4_wpdb_bulk_insert($qmf_tabla_forma_de_pago, $qmf_data);
			fclose($gestor);
		}

		$qmf_data = [];
		if (($gestor = fopen(plugin_dir_path( dirname( __FILE__ ) ) . "data/qmf_RegimenFiscal.csv", "r")) !== FALSE) {
			while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
				array_push($qmf_data, array( 
					'c_RegimenFiscal' => $datos[0], 
					'descripcion' =>  $datos[1], 
				));
			}
			Qmf4_Activator::qmf4_wpdb_bulk_insert($qmf_tabla_regimen_fiscal, $qmf_data);
			fclose($gestor);
		}
	}

	static function qmf4_wpdb_bulk_insert($table, $rows) {
		global $wpdb;
		
		// Extract column list from first row of data
		$columns = array_keys($rows[0]);
		asort($columns);
		$columnList = '`' . implode('`, `', $columns) . '`';
	
		// Start building SQL, initialise data and placeholder arrays
		$sql = "INSERT INTO `$table` ($columnList) VALUES\n";
		$placeholders = array();
		$data = array();
	
		// Build placeholders for each row, and add values to data array
		foreach ($rows as $row) {
			ksort($row);
			$rowPlaceholders = array();
	
			foreach ($row as $key => $value) {
				$data[] = $value;
				$rowPlaceholders[] = '%s';
			}
	
			$placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
		}
	
		// Stitch all rows together
		$sql .= implode(",\n", $placeholders);
	
		// Run the query.  Returns number of affected rows.
		return $wpdb->query($wpdb->prepare($sql, $data));
	}

}
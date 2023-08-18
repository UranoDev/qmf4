<?php
$path = preg_replace( '/wp-content.*$/', '', __DIR__ );
require_once( $path . 'wp-load.php' );

    echo "Reading Google Sheet with Descriptions for symbols".PHP_EOL;
    //wp get remote http
	//https://docs.google.com/spreadsheets/d/1clBUotlu4ZrQsYL37WiaecG_e1cxQURAhkzWtpqLBFQ/edit?usp=sharing
    $apy_google_key = 'AIzaSyDcoWzeKNQMlbFzk5-KtkN2EKvHZ6b9VS0';
    $google_sheet_id = '1clBUotlu4ZrQsYL37WiaecG_e1cxQURAhkzWtpqLBFQ';
    $google_sheet_range = 'c_ClaveUnidad!A1:c2419';
    $url = "https://sheets.googleapis.com/v4/spreadsheets/$google_sheet_id/values/$google_sheet_range";
    $response = wp_remote_get( $url, array(
      'headers' => array('X-goog-api-key' => $apy_google_key,),
    ) );
    if ( is_wp_error( $response ) ) {
      $error_message = $response->get_error_message();
      echo "Can't get custom descriptions: $error_message";
    } else {
      echo "Got custom descriptions".PHP_EOL;
      $i = 0;
      $response = json_decode($response['body'], true);
	  
      echo "Got ".count($response['values'])." descriptions".PHP_EOL;
      $headers = $response['values'][0];
      
      $headers = implode(',', $headers);
      echo "Headers: $headers".PHP_EOL;
      echo "----------------------------------------".PHP_EOL;
      $bbm_symbols = array();
	  $i = 1;
    //write text file
    $file = fopen("clave_unidad.txt","w");
	  foreach ($response['values'] as $row) {
      if ($i < 50) {
        echo "($i) $id,\"$nombre\",\"$descripcion\"". PHP_EOL;
      }
      $id = isset($response['values'][$i][0])?$response['values'][$i][0]:'';
      $nombre = isset($response['values'][$i][1])?str_replace("\n", ' ',$response['values'][$i][1]):'';
      $descripcion = isset($response['values'][$i][2])?str_replace("\n", ' ',$response['values'][$i][2]):'';
      //write text 
      if ($id != '') {
        fwrite($file, "$id,\"$nombre\",\"$descripcion\"\n");
        $i++;
      }
	  }
    fclose($file);
	}

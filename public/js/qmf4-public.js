(function( $ ) {
	'use strict';

	$( window ).load(function() {

		if($('#qmf_facturar_ahora').is(':checked')) {
			$('#qmf_campos_checkout_detalle').css('display', 'block');
		} else {
			$('#qmf_campos_checkout_detalle').css('display', 'none');
		}

		$('#qmf_facturar_ahora').click(function() {
			if($(this).is(':checked')) {
				$('#qmf_campos_checkout_detalle').css('display', 'block');
			} else {
				$('#qmf_campos_checkout_detalle').css('display', 'none');
			}
		})

		$('#qmf_copiar_direccion').click(function( event ) {
			event.preventDefault();
			$('#qmf_calle').val($('#billing_address_1').val());
			$('#qmf_colonia').val($('#billing_address_2').val());
			$('#qmf_estado').val($('#billing_state option:selected').text());
			$('#qmf_pais').val($('#billing_country option:selected').text());
			$('#qmf_cp_receptor').val($('#billing_postcode').val());
		})

	});

})( jQuery );

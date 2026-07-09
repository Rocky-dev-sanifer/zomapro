/**
 * Prestashop module : OpartDevis
 *
 * @author Olivier CLEMENCE <manit4c@gmail.com>
 * @copyright  Op'art
 * @license Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 */

$(document).ready(function() { 

	$(".switch").on("change",function(){
		if($('#OPARTDEVIS_RELANCE_on').is( ":checked" )){
			$('.form-group:nth-child(21)').show();
			$('.form-group:nth-child(22)').show();
			$('.form-group:nth-child(23)').show();
			
		}
		else{
			$('.form-group:nth-child(21)').hide();
			$('.form-group:nth-child(22)').hide();
			$('.form-group:nth-child(23)').hide();
		}
	});

	if($('#OPARTDEVIS_RELANCE_on').is( ":checked" )){
			$('.form-group:nth-child(21)').show();
			$('.form-group:nth-child(22)').show();
			$('.form-group:nth-child(23)').show();
			
		}
	else{
			$('.form-group:nth-child(21)').hide();
			$('.form-group:nth-child(22)').hide();
			$('.form-group:nth-child(23)').hide();
	}



		$(".switch").on("change",function(){
		if($('#OPARTDEVIS_CAPTCHA_on').is( ":checked" )){
			$('.form-group:nth-child(25)').show();
			$('.form-group:nth-child(26)').show();
			
		}
		else{
			$('.form-group:nth-child(25)').hide();
			$('.form-group:nth-child(26)').hide();
		}
	});

	if($('#OPARTDEVIS_CAPTCHA_on').is( ":checked" )){
			$('.form-group:nth-child(25)').show();
			$('.form-group:nth-child(26)').show();
			
		}
	else{
			$('.form-group:nth-child(25)').hide();
			$('.form-group:nth-child(26)').hide();
	}
	

});

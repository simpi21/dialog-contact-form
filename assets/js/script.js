(function($){
	"use strict";

	$('#dialog_contact_form').submit( function( event ){
		event.preventDefault();
		$('#dcf_error_status').hide('slow').empty();
		$('#dcf_success_status').hide('slow').empty();
		$('.dcf-ajax-loader').show('fast');

		$.ajax({
			type: "POST",
			url: DialogContactForm.ajaxurl,
			data: {
				action: 'dialog_contact_form',
				nonce: DialogContactForm.nonce,
				formData: $("#dialog_contact_form").serialize()
			},
			success: function( response ) {
				$('<span></span>').text( response.data ).appendTo('#dcf_success_status');
				$('#dcf_success_status').show('slow');
				$("#dialog_contact_form").trigger("reset").hide();
				$('.dcf-ajax-loader').hide('fast');
				$('#dialogContactForm').delay(3000).fadeOut('slow');
			},
			error: function( response ) {
				var errorMsg = response.responseJSON.data;
				$(errorMsg).each(function(index, item ){
					$('<span></span>').text( item ).appendTo('#dcf_error_status');
					$('#dcf_error_status').show('slow');
					$('.dcf-ajax-loader').hide('fast');
				});
			}
		});
	});

	// Update captcha image
	$(".dcf-svg-loader").click(function() {
		$(this).addClass('dcf-spin');
		$.ajax({
			type: "POST",
			url: DialogContactForm.ajaxurl,
			data: {
				action: 'dialog_contact_form_captcha',
				nonce: DialogContactForm.nonce
			},
		    success: function( result ) {
			    $("#dcf-captcha-img").attr("src", 'data:image/jpeg;base64,' + result );
			    $(".dcf-svg-loader").removeClass('dcf-spin');
		    }
		});
	});

	// Set captcha image
	$( document ).ready( function(){
		$.ajax({
			type: "POST",
			url: DialogContactForm.ajaxurl,
			data: {
				action: 'dialog_contact_form_captcha',
				nonce: DialogContactForm.nonce
			},
		    success: function(result) {
			    $("#dcf-captcha-img").attr("src", 'data:image/jpeg;base64,' + result );
		    }
		});
	});

})(jQuery);
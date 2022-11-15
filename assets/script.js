var GFFDI_VERSION = '2.0.0';
function gffdi_first_time_init( path ){
	// console.log ("GFFDI_VERSION",GFFDI_VERSION);
	var head = document.getElementsByTagName('head')[0];
	var style = document.createElement('link');
	style.href = path+'style.css';
	style.type = 'text/css';
	style.rel = 'stylesheet';
	head.append(style);
}

jQuery(document).ready(function($){
	$(document).bind("gform_load_field_settings", function(event, field, form){
		// console.log(field);
		var has_image = false;
		image_id = field["display_image_id"];
		image_size = field["display_image_size"];
		image_alt = field["display_image_alt"];
		image_url = field["display_image_url"];
		image_dimension = field["display_image_dimension"];
		
		has_image = ( image_id != '' && image_id > 0 ) ? true : has_image;
		if ( typeof image_url !== 'undefined' && image_url != ''  ){
			has_image = true;
			// make sure to show custom sizes
			$('#display_image_size').val('custom').prop('disabled', 'disabled').trigger('change');
		} else if( image_size == '' ){
			$('#display_image_size').val('').hide();
		} else {
			$('#display_image_size').val(image_size).show();
		}
		if( image_dimension != '' ){
			const dimensions = image_dimension.split(':');
			$("#display_image_size_custom").show();
			$("#display_image_size_c_width").val(dimensions[0]);
			$("#display_image_size_c_height").val(dimensions[1]);
		}
		$('#display_image_alt').val(image_alt);
		$("#url_image_input").val(image_url);
		if( has_image ){
			gffdi_toggle_placeholder( true, field["id"] );
		} else {
			gffdi_toggle_placeholder( false, field["id"] );
		}
		// console.log('gform_load_field_settings',field);
	});
	$('#display_image_alt').change(function( event ){
		const field_id = $('#sidebar_field_label').data('fieldid');
		const field_data = $(this).val();
		SetFieldProperty( 'display_image_alt', field_data );
		$('#input_' + field_id ).attr('alt', field_data );
	});
	
	$('#display_image_size').change(function( event ){
		const field_id = $('#sidebar_field_label').data('fieldid');
		const input_field = $('#input_' + field_id );
		const image_id = input_field.data('imgid');
		const image_size = $(this).val();
		
		if( image_id > 0 ){
			wp.media.attachment(image_id).fetch().then(function (data) {
				var image_size_tmp =  image_size == 'custom' ? 'full' : image_size;
		  		// preloading finished
		  		input_field.attr( 'src',wp.media.attachment(image_id).attributes.sizes[image_size_tmp].url ).show();
			});
		} else if( image_size != "custom" ){
			const image_ratio = {
				"thumbnail": "150",
				"medium": "300",
				"large": "1024",
				"custom": "auto",
				"full": "auto" // do not set
			}
			if( input_field.height() == input_field.width() ){
				// image is square
				input_field.attr('width',image_ratio[image_size]).attr('height', image_ratio[image_size]);
			} else if ( input_field.height() > input_field.width() ){
				// image is portrait
				input_field.attr('width',image_ratio[image_size]).attr('height', 'auto');
			} else if ( input_field.height() < input_field.width() ){
				// image is landscape
				input_field.attr('width','auto').attr('height', image_ratio[image_size]);
			}
			// console.log(input_field, input_field.height(), input_field.width());
		}
		
		if( image_size == 'custom' ){
			$('#display_image_size_custom').show();
		} else {
			$('#display_image_size_custom').hide();
		}
		
		SetFieldProperty( 'display_image_size', image_size );
		input_field.data('imgsize', image_size );
		console.log(image_size);
		

	});
	
	$("#display_image_size_c_width,#display_image_size_c_height").change(function( event ){
		console.log('changing dimensions', $(this).val(), $(this).data('type') );
		const field_id = $('#sidebar_field_label').data('fieldid');
		const input_field = $('#input_' + field_id );
		var image_dimensions = '';
		if( $(this).data('type') == 'width' ){
			input_field.attr('width',$(this).val()).attr('height', $("#display_image_size_c_height").val());
			image_dimensions = $(this).val()+':'+$("#display_image_size_c_height").val();
		} else if( $(this).data('type') == 'height') {
			input_field.attr('width',$("#display_image_size_c_width").val()).attr('height', $(this).val());
			image_dimensions = $("#display_image_size_c_width").val()+':'+$(this).val();
		}
		SetFieldProperty( 'display_image_dimension', image_dimensions );
		input_field.data('dimension', image_dimensions );
	});
	
	// on upload button click
	$('.GFFDI_action').click(function( event ){
		event.preventDefault(); // prevent default link click and page refresh

		const button = $(this);
		const field_id = $('#sidebar_field_label').data('fieldid');
		const input_field = $('#input_' + field_id );
		const image_id = input_field.data('imgid');

		// determine course of action
		switch( button.data('action') ){
			case 'open-url':
				$('#url_image_input_wrapper').show();
				break;
			case 'set-url':
				image_url = $("#url_image_input").val();
				console.log("set the url",image_url);
				// TODO add image sideload to media library
				// https://stackoverflow.com/questions/26451022/upload-media-file-from-external-url-to-wordpress-media-library
				SetFieldProperty('display_image_url', image_url );
				input_field.attr( 'src', image_url ).show()
				if( image_url == '') {
					$('#display_image_size').val('').prop('disabled', false).trigger('change');
				} else {
					$('#display_image_size').val('custom').prop('disabled', 'disabled').trigger('change');
				}
				gffdi_toggle_placeholder( true, field_id );
				break;
			case 'upload':
			case 'library':
			
				image_size = input_field.data('imgsize');
				
				$('#url_image_input_wrapper').hide();
				$('#display_image_size').val('').prop('disabled', false).trigger('change');
				$('#display_image_size_custom').hide();
				
				var frame = wp.media({
					title: 'Select image', // modal window title
					library : {
						type : 'image'
					},
					button: {
						text: 'Use this image' // button label text
					},
					multiple: false
				});
				// frame.on('ready',function(e){
				// 	if( button.data('action') == 'upload' && e == 'ready'){
				// 		$('.media-router #menu-item-upload').trigger('click');
				// 	} else if( button.data('action') == 'library' && e == 'ready'){
				// 		$('.media-router #menu-item-browse').trigger('click');
				// 	}
				frame.on( 'select', function() { // it also has "open" and "close" events
					const attachment = frame.state().get( 'selection' ).first().toJSON();
					SetFieldProperty('display_image_id', attachment.id );
					// clear out the image url input
					$("#url_image_input").val('');
					SetFieldProperty('display_image_url', '' );
					
					
					input_field.data('imgid',attachment.id);
					
					if( image_size == 'undefined' || image_size == '' ){
						image_size = 'full';
						SetFieldProperty( 'display_image_size', image_size );
						jQuery('#display_image_size').val( image_size );	
					}
					
					
					input_field.attr( 'src', attachment.sizes[image_size].url ).show();
					gffdi_toggle_placeholder( true, field_id );
					
				});
				
				// already selected images
				frame.on( 'open', function() {
					if( image_id ) {
				  	const selection = frame.state().get( 'selection' )
				  	attachment = wp.media.attachment( image_id );
				  	attachment.fetch();
				  	selection.add( attachment ? [attachment] : [] );
					}
					
				});
				
				frame.open();
				break;
			case 'remove': // clear image
				SetFieldProperty('display_image_id', '' );
				$('#display_image_alt').val('').trigger('change');
				input_field.removeAttr( 'src' ).data('imgid','').data('imgsize','').hide();				
				gffdi_toggle_placeholder( false, field_id );
				break;
		}
		
	
	});
});

function gffdi_toggle_placeholder( has_image, field_id ){
	if( has_image ){
		// console.log('has image');
		jQuery("#field_" + field_id + " .ginput_container_display_image").addClass('has-image').removeClass('has-placeholder');
		jQuery("#display_image_size,label[for='display_image_size'],.gf-display-image-remove,#display_image_alt,label[for='display_image_alt'],p[for='display_image_alt']").show();
		jQuery(".gf-display-image-add,label[for='display_image_id']").hide();
	} else {
		// console.log('has placeholder');
		jQuery("#field_" + field_id + " .ginput_container_display_image").addClass('has-placeholder').removeClass('has-image');
		jQuery("#display_image_size,label[for='display_image_size'],.gf-display-image-remove,#display_image_alt,label[for='display_image_alt'],p[for='display_image_alt'],#display_image_size_custom").hide();
		jQuery("#display_image_size_c_width,#display_image_size_c_height").val('').trigger('change');
		jQuery(".gf-display-image-add,label[for='display_image_id']").show();
	}
	jQuery("#url_image_input_wrapper").hide();
}
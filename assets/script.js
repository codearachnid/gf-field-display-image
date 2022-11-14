var GFFDI_VERSION = '1.1.0';
function gffdi_first_time_init( path ){
	console.log ("GFFDI_VERSION",GFFDI_VERSION);
	var head = document.getElementsByTagName('head')[0];
	var style = document.createElement('link');
	style.href = path+'style.css';
	style.type = 'text/css';
	style.rel = 'stylesheet';
	head.append(style);
}
jQuery(document).bind("gform_load_field_settings", function(event, field, form){
	console.log(field);
	image_id = field["display_image_id"];
	image_size = field["display_image_size"];
	if( image_size == '' ){
		jQuery('#display_image_size').val('').hide();
	} else {
		jQuery('#display_image_size').val(image_size).show();
	}
	if( image_id != '' && image_id > 0 ){
		gffdi_toggle_placeholder( true, field["id"] );
	} else {
		gffdi_toggle_placeholder( false, field["id"] );
	}
	// console.log('gform_load_field_settings',field);
});
jQuery(document).ready(function($){
	
	$('.gf-display-image-size').change(function( event ){
		const field_id = $('#sidebar_field_label').data('fieldid');
		const image_id = $('#input_' + field_id ).data('imgid');
		const image_size = $(this).val();
		wp.media.attachment(image_id).fetch().then(function (data) {
		  // preloading finished
		  $("#input_" + field_id).attr( 'src',wp.media.attachment(image_id).attributes.sizes[image_size].url ).show();
		});
		
		SetFieldProperty( 'display_image_size', image_size );
		$('#input_' + field_id ).data('imgsize', image_size );
	});
	
	// on upload button click
	$('.GFFDI_action').click(function( event ){
		event.preventDefault(); // prevent default link click and page refresh

		const button = $(this);
		const field_id = $('#sidebar_field_label').data('fieldid');
		const image_id = $('#input_' + field_id ).data('imgid');
		const field_elem = $('#input_' + field_id );

		// determine course of action
		switch( button.data('action') ){
			case 'upload':
			case 'library':
			
				image_size = field_elem.data('imgsize');
				
				const customUploader = wp.media({
					title: 'Select image', // modal window title
					library : {
						type : 'image'
					},
					button: {
						text: 'Use this image' // button label text
					},
					multiple: false
				}).on( 'select', function() { // it also has "open" and "close" events
					const attachment = customUploader.state().get( 'selection' ).first().toJSON();
					SetFieldProperty('display_image_id', attachment.id );
					
					
					field_elem.data('imgid',attachment.id);
					
					if( image_size == 'undefined' || image_size == '' ){
						image_size = 'full';
						SetFieldProperty( 'display_image_size', image_size );
						jQuery('.gf-display-image-size').val( image_size );
					}
					
					
					field_elem.attr( 'src', attachment.sizes[image_size].url ).show();
					gffdi_toggle_placeholder( true, field_id );
					
				});
				
				// already selected images
				customUploader.on( 'open', function() {
					if( image_id ) {
				  	const selection = customUploader.state().get( 'selection' )
				  	attachment = wp.media.attachment( image_id );
				  	attachment.fetch();
				  	selection.add( attachment ? [attachment] : [] );
					}
					
				});
				
				customUploader.open();
				break;
			case 'remove': // clear image
				SetFieldProperty('display_image_id', '' );
				field_elem.removeAttr( 'src' ).data('imgid','').data('imgsize','').hide();				
				gffdi_toggle_placeholder( false, field_id );
				break;
		}
		
	
	});
});

function gffdi_toggle_placeholder( has_image, field_id ){
	if( has_image ){
		// console.log('has image');
		jQuery("#field_" + field_id + " .ginput_container_display_image").addClass('has-image').removeClass('has-placeholder');
		jQuery('.gf-display-image-size').show();
		jQuery('.gf-display-image-remove').show();
		jQuery('.gf-display-image-add').hide();
	} else {
		// console.log('has placeholder');
		jQuery("#field_" + field_id + " .ginput_container_display_image").addClass('has-placeholder').removeClass('has-image');
		jQuery('.gf-display-image-size').hide();
		jQuery('.gf-display-image-remove').hide();
		jQuery('.gf-display-image-add').show();
	}
	
}
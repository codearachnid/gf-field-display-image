jQuery(document).bind("gform_load_field_settings", function(event, field, form){
	image_id = field["display_image_id"];
	image_size = field["display_image_size"];
	if( image_size == '' ){
		jQuery('#display_image_size').hide();
	} else {
		jQuery('#display_image_size').val(image_size).show();
	}
	if( image_id != '' && image_id > 0 ){
		jQuery('.gf-display-image-upload').hide();
		jQuery('.gf-display-image-remove').show();
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
		  jQuery("#input_" + field_id).attr( 'src',wp.media.attachment(image_id).attributes.sizes[image_size].url ).show();
		});
		
		SetFieldProperty( 'display_image_size', image_size );
		$('#input_' + field_id ).data('imgsize', image_size );
	});
	
	// on upload button click
	$('.gf-display-image-upload').click(function( event ){
		event.preventDefault(); // prevent default link click and page refresh
		
		const button = $(this)
		const field_id = $('#sidebar_field_label').data('fieldid');
		const image_id = $('#input_' + field_id ).data('imgid');
		image_size = $('#input_' + field_id ).data('imgsize');

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
			$('#input_' + field_id ).data('imgid',attachment.id);
			
			if( image_size == 'undefined' || image_size == '' ){
				image_size = 'full';
				SetFieldProperty( 'display_image_size', image_size );
				jQuery('.gf-display-image-size').val( image_size );
			}
			
			// console.log('gf-display-image-upload','selected image', field_id, image_size, attachment.id, attachment);
			
			jQuery("#input_" + field_id).attr( 'src', attachment.sizes[image_size].url ).show();
			jQuery('.gf-display-image-upload').hide();
			jQuery('.gf-display-image-remove').show();
			jQuery('.gf-display-image-size').show();
			
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
	
	});
	// on remove button click
	$( '.gf-display-image-remove' ).click(function( event ){
		event.preventDefault();
		const button = $(this);
		const field_id = $('#sidebar_field_label').data('fieldid');
		SetFieldProperty('display_image_id', '' );
		jQuery("#input_" + field_id).removeAttr( 'src' ).data('imgid','').data('imgsize','').hide();
		jQuery('.gf-display-image-upload').show();
		jQuery('.gf-display-image-remove').hide();
		jQuery('.gf-display-image-size').hide();
	});
});
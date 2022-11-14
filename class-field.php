<?php

class Display_Image_GF_Field extends GF_Field {
	public $type = 'display_image';
	
	public function get_form_editor_field_title() {
		return esc_attr__( 'Display Image', 'gf_field_display_image' );
	}
	
	/**
	 * Returns the field's form editor icon.
	 *
	 * This could be an icon url or a gform-icon class.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return 'gform-icon--post-image';
	}
	
	/**
	 * Assign the field button to the Advanced Fields group.
	 *
	 * @return array
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon'
		);
	}
	
	/**
	 * The settings which should be available on the field in the form editor.
	 *
	 * @return array
	 */
	function get_form_editor_field_settings() {
		return array(
			'display_image_id',
			'display_image_size',
			'css_class_setting',
			'admin_label_setting',
			'visibility_setting',
			'conditional_logic_field_setting',
		);
	}
	
	/**
	 * Enable this field for use with conditional logic.
	 *
	 * @return bool
	 */
	public function is_conditional_logic_supported() {
		return true;
	}


	
	/**
	 * Define the fields inner markup.
	 *
	 * @param array $form The Form Object currently being processed.
	 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param null|array $entry Null or the Entry Object currently being edited.
	 *
	 * @return string
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {
		$id              = absint( $this->id );
		$form_id         = absint( $form['id'] );
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
	
		// Prepare the value of the input ID attribute.
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_{$id}" : "input_{$form_id}_{$id}";
		// $field_id = "input_{$id}";
		$form_id  = ( $is_entry_detail || $is_form_editor ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;
		
		$image_size = !empty($this->display_image_size) ? $this->display_image_size : 'full';
		$image_to_display = wp_get_attachment_image_src( $this->display_image_id, $image_size );
	
		
		
		// set up placeholder in admin - will be leveraged if no image is set
		if( $is_form_editor ){
			// use the same generated placeholder as WP Image Block
			$placeholder = '<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60" preserveAspectRatio="none" class="placeholder__illustration" aria-hidden="true" focusable="false"><path vector-effect="non-scaling-stroke" d="M60 60 0 0"></path></svg>';
			
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

			$load_script = sprintf('<script type="text/javascript">if( typeof GFFDI_VERSION === "undefined" ){jQuery.getScript( "%s", function() { gffdi_first_time_init("%s"); });}</script>',
				plugins_url( "/assets/script{$min}.js?ver=" . GFFDI_VERSION, __FILE__ ),
				plugins_url( "/assets/", __FILE__ )
				);
			
			return sprintf('<div class="ginput_container ginput_container_%s %s"><img %s id="%s" class="%s" data-imgsize="%s" data-imgid="%s" />%s%s</div>',
				$this->type, 
				empty($image_to_display) ? 'has-placeholder' : 'has-image',
				!empty( $image_to_display ) ? 'src="' . $image_to_display[0] . '"' : '',
				$field_id,
				!empty( $this->display_image_id) ? '' : ' hidden ',
				$this->display_image_size,
				$this->display_image_id,
				$placeholder, 
				$load_script
			);

		} else {
			// return the raw image on the frontend
			return sprintf('<img %s id="%s" class="%s" data-imgsize="%s" data-imgid="%s" />',
				!empty( $image_to_display ) ? 'src="' . $image_to_display[0] . '"' : '',
				$field_id,
				!empty( $this->display_image_id) ? '' : ' hidden ',
				$this->display_image_size,
				$this->display_image_id,
			);
			
		}
	
		
	}
	
	public function get_field_content( $value, $force_frontend_label, $form ) {
		$form_id         = $form['id'];
		$admin_buttons   = $this->get_admin_buttons();
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_admin        = $is_entry_detail || $is_form_editor;
		$field_label     = '';//$this->get_field_label( $force_frontend_label, $value );
		$field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
		$field_content   = ! $is_admin ? '{FIELD}' : $field_content = sprintf( "%s{FIELD}", $admin_buttons );
	 
		return $field_content;
	}
}

GF_Fields::register( new Display_Image_GF_Field() );

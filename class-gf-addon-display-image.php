<?php

GFForms::include_addon_framework();

class GFAddonDisplayImage extends GFAddOn {

	protected $_version = '1.0.0.72';
	protected $_min_gravityforms_version = '2.0';

	protected $_slug = 'gf-field-display-image';
	protected $_path = 'gf-field-display-image/gf-field-display-image.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Forms Image Choices';
	protected $_short_title = 'Display Image';
	protected $_url = 'https://github.com/codearachnid/gf-field-display-image/';

	// protected $_supported_field_types = ['radio', 'checkbox', 'survey', 'poll', 'quiz', 'post_custom_field', 'product', 'option'];
	// protected $_supported_input_types = ['radio', 'checkbox'];
	// protected $_standard_merge_tags = ['all_fields', 'pricing_fields']; //'all_quiz_results'

	/**
	 * Members plugin integration
	 */
	protected $_capabilities = array( 'gravityforms_edit_forms', 'gravityforms_edit_settings' );

	/**
	 * Permissions
	 */
	protected $_capabilities_settings_page = 'gravityforms_edit_settings';
	protected $_capabilities_form_settings = 'gravityforms_edit_forms';
	protected $_capabilities_uninstall = 'gravityforms_uninstall';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFFieldDisplayImage
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	
	// public function plugin_page() {
	// 	echo 'This page appears in the Forms menu';
	// }
	// 
	// public function plugin_settings_fields() {
	// 	return array(
	// 		array(
	// 			'title'  => esc_html__( 'Simple Add-On Settings', 'simpleaddon' ),
	// 			'fields' => array(
	// 				array(
	// 					'name'              => 'mytextbox',
	// 					'tooltip'           => esc_html__( 'This is the tooltip', 'simpleaddon' ),
	// 					'label'             => esc_html__( 'This is the label', 'simpleaddon' ),
	// 					'type'              => 'text',
	// 					'class'             => 'small',
	// 					'feedback_callback' => array( $this, 'is_valid_setting' ),
	// 				)
	// 			)
	// 		)
	// 	);
	// }
	
	public function pre_init() {
		parent::pre_init();
	 
		if ( $this->is_gravityforms_supported() && class_exists( 'GF_Field' ) ) {
			require_once( 'class-gf-field-display-image.php' );
		}
	}
	
	public function init_admin() {
		parent::init_admin();
	 
		add_filter( 'gform_tooltips', array( $this, 'tooltips' ) );
		add_action( 'gform_field_standard_settings', array( $this, 'field_appearance_settings' ), 10, 2 );
	}
	
	public function styles() {
		$min = ''; //defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
		$styles = [[
			'handle'  => 'gf_field_display_image',
			'src'     => $this->get_base_url() . "/assets/style{$min}.css",
			'version' => $this->_version,
			'enqueue' => [[
				'field_types' => ['display_image'],
			]],
		]];
		return array_merge( parent::styles(), $styles );
	}
	
	public function scripts() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
		wp_enqueue_media();
		$scripts = [[
			'handle'  => 'gf_field_display_image',
			'src'     => $this->get_base_url() . '/assets/script.js',
			'version' => $this->_version,
			'deps'    => ['jquery'],
			'enqueue' => [[
				'field_types' => ['display_image'],
			]],
		]];
		return array_merge( parent::scripts(), $scripts );
	}
	
	public function tooltips( $tooltips ) {
		$insert_tooltips = array(
			'display_image_id' => sprintf( '<h6>%s</h6>%s', esc_html__( 'Select Image', 'gf_field_display_image' ), esc_html__( '', 'gf_field_display_image' ) ),
			'label_setting' => sprintf( '<h6>%s</h6>%s', esc_html__( 'Field Label', 'gf_field_display_image' ), esc_html__('Enter the label for this Image block. It will help you identify your Image blocks in the form editor, but it will not be displayed on the form.', 'gf_field_display_image' )),
		);
	 
		return array_merge( $tooltips, $insert_tooltips );
	}
	
	public function field_appearance_settings( $position, $form_id ) {
		if ( $position == 10 ) {
			?>
			<li class="display_image_id field_setting">
				<label for="display_image_id">
					<?php esc_html_e( 'Upload an image file, pick one from your media library, or add one with a URL.', 'gf_field_display_image' ); ?>
					<?php gform_tooltip( 'display_image_id' ) ?>
				</label>
				<input id="upload_image_button" type="button" class="button components-button gf-display-image-upload is-primary" data-action="upload" value="<?php _e( 'Upload' ); ?>" />
				<input id="library_image_button" type="button" class="button gf-display-image-upload" data-action="library" value="<?php _e( 'Media Library' ); ?>" />
				<input id="url_image_button" type="button" class="button gf-display-image-upload" data-action="url" value="<?php _e( 'Insert from URL' ); ?>" />
				<input id="remove_image_button" type="button" class="button gf-display-image-remove hidden" data-action="remove" value="<?php _e( 'Remove image' ); ?>" />
				<select id="display_image_size" class="gf-display-image-size hidden">
					<option>Image Size</option>
					<option value="thumbnail">Thumbnail</option>
					<option value="medium">Medium</option>
					<option value="full">Full Size</option>
					<!-- <option value="custom">Custom</option> -->
				</select>
			</li>
	 
			<?php
		}
	}

	public function debug_output($data = '', $background='black', $color='white') {
		echo '<pre style="padding:20px; background:'.$background.'; color:'.$color.';">';
		print_r($data);
		echo '</pre>';
	}


} // end class

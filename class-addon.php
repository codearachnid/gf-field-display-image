<?php

GFForms::include_addon_framework();

class GFAddonDisplayImage extends GFAddOn {
	
	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0
	 * @access private
	 * @var    object $_instance If available, contains an instance of this class.
	 */
	private static $_instance = null;
	
	/**
	 * Defines the version of the Advanced Post Creation Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_version Contains the version, defined from advancedpostcreation.php
	 */
	protected $_version = GFFDI_VERSION;
	
	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = '2.4.5';
	
	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gf-field-display-image';
	
	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gf-field-display-image/gf-field.php';
	
	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;
	
	/**
	 * Defines the URL where this Add-On can be found.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'https://github.com/codearachnid/gf-field-display-image/';
	
	/**
	 * Defines the title of this Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_title The title of the Add-On.
	 */
	protected $_title = 'Gravity Forms Image Display Field';
	
	/**
	 * Defines the short title of the Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = 'Display Image';
	
	/**
	 * Defines if Add-On should use Gravity Forms servers for update data.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    bool
	 */
	protected $_enable_rg_autoupgrade = true;
	
	/**
	 * Defines if Add-On should allow users to configure what order feeds are executed in.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    bool
	 */
	protected $_supports_feed_ordering = true;
	
	/**
	 * Defines the capability needed to access the Add-On settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_settings_page The capability needed to access the Add-On settings page.
	 */
	protected $_capabilities_settings_page = '';
	
	/**
	 * Defines the capability needed to access the Add-On form settings page.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_form_settings The capability needed to access the Add-On form settings page.
	 */
	protected $_capabilities_form_settings = '';
	
	/**
	 * Defines the capability needed to uninstall the Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_capabilities_uninstall The capability needed to uninstall the Add-On.
	 */
	protected $_capabilities_uninstall = '';
	
	/**
	 * Defines the capabilities needed for the Advanced Post Creation Add-On
	 *
	 * @since  1.0
	 * @access protected
	 * @var    array $_capabilities The capabilities needed for the Add-On
	 */
	protected $_capabilities = ['gravityforms_edit_forms'];
	
	/**
	 * Get instance of this class.
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 *
	 * @return GF_Advanced_Post_Creation
	 */
	public static function get_instance() {
	
		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}
	
		return self::$_instance;
	
	}
	
	public function pre_init() {
		parent::pre_init();
	 
		if ( $this->is_gravityforms_supported() && class_exists( 'GF_Field' ) ) {
			require_once( 'class-field.php' );
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

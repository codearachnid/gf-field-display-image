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
	 
		add_filter( 'gform_tooltips', [ $this, 'tooltips' ] );
		add_action( 'gform_field_standard_settings', [ $this, 'field_appearance_settings' ], 10, 2 );
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
			'display_image_alt' => sprintf( '<h6>%s</h6>', esc_html__('Alternative Text', 'gf_field_display_image' )),
		);
	 
		return array_merge( $tooltips, $insert_tooltips );
	}
	
	public function get_image_sizes($size = '' ) {
		$wp_additional_image_sizes = wp_get_additional_image_sizes();
	
		$sizes = array();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();
	
		// Create the full array with sizes and crop info
		foreach( $get_intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array( 
					'width' => $wp_additional_image_sizes[ $_size ]['width'],
					'height' => $wp_additional_image_sizes[ $_size ]['height'],
					'crop' =>  $wp_additional_image_sizes[ $_size ]['crop']
				);
			}
		}
	
		// Get only 1 size if found
		if ( $size ) {
			if( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
			} else {
				return false;
			}
		}
		return $sizes;
	}
	
	public function field_appearance_settings( $position, $form_id ) {
		if ( $position == 10 ) {
			?>
			<li class="display_image_id field_setting">
				<label for="display_image_id">
					<?php esc_html_e( 'Upload an image file, pick one from your media library, or add one with a URL.', 'gf_field_display_image' ); ?>
					<?php gform_tooltip( 'display_image_id' ) ?>
				</label>
				<p>
				<input id="upload_image_button" type="button" class="button GFFDI_action components-button gf-display-image-add is-primary" data-action="upload" value="<?php _e( 'Upload' ); ?>" />
				<input id="library_image_button" type="button" class="button GFFDI_action gf-display-image-add" data-action="library" value="<?php _e( 'Media Library' ); ?>" />
				<input id="url_image_button" type="button" class="button GFFDI_action gf-display-image-add" data-action="open-url" value="<?php _e( 'Insert from URL' ); ?>" />
				<div id="url_image_input_wrapper" for="url_image_button">
					<input id="url_image_input" name="url_image_input" class="url_image_input" type="text" aria-label="URL" placeholder="Paste or type URL" value="">
					<button type="button" class="GFFDI_action components-button submit has-icon" data-action="set-url" aria-label="Apply">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 24 24" width="24" height="24" aria-hidden="true" focusable="false">
							<path d="M6.734 16.106l2.176-2.38-1.093-1.028-3.846 4.158 3.846 4.157 1.093-1.027-2.176-2.38h2.811c1.125 0 2.25.03 3.374 0 1.428-.001 3.362-.25 4.963-1.277 1.66-1.065 2.868-2.906 2.868-5.859 0-2.479-1.327-4.896-3.65-5.93-1.82-.813-3.044-.8-4.806-.788l-.567.002v1.5c.184 0 .368 0 .553-.002 1.82-.007 2.704-.014 4.21.657 1.854.827 2.76 2.657 2.76 4.561 0 2.472-.973 3.824-2.178 4.596-1.258.807-2.864 1.04-4.163 1.04h-.02c-1.115.03-2.229 0-3.344 0H6.734z"></path>
						</svg>
					</button>
				</div>
				<input id="remove_image_button" type="button" class="button GFFDI_action gf-display-image-remove hidden" data-action="remove" value="<?php _e( 'Remove image' ); ?>" />
				</p>
				<label for="display_image_alt">
					<?php esc_html_e( 'Alt Text', 'gf_field_display_image' ); ?>
					<?php gform_tooltip( 'display_image_alt' ) ?>
				</label>
				<textarea id="display_image_alt" name="display_image_alt" class="" rows="2" ></textarea>
				<p for="display_image_alt"><a href="https://www.w3.org/WAI/tutorials/images/decision-tree" target="_blank" rel="external noreferrer noopener">Describe the purpose of the image <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="" aria-hidden="true" focusable="false"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg></a> Leave empty if the image is purely decorative.</p>
				<label for="display_image_size">
					<?php esc_html_e( 'Image Size', 'gf_field_display_image' ); ?>
				</label>
				<select id="display_image_size" name="display_image_size" class="gf-display-image-size hidden">
					<option>Image Size</option>
					<option value="thumbnail">Thumbnail</option>
					<option value="medium">Medium</option>
					<option value="full">Full Size</option>
					<option value="custom">Custom</option>
				</select>
				<p id="display_image_size_custom" >
					<label for="display_image_size_c_width">Width</label>
					<input id="display_image_size_c_width" name="display_image_size_c_width" data-type="width" />
					<label for="display_image_size_c_height">Height</label>
					<input id="display_image_size_c_height" name="display_image_size_c_height" data-type="height" />
				</p>
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

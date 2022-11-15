<?php
/*
Plugin Name: Gravity Forms Image Display Field
Plugin URI: https://github.com/codearachnid/gravityforms-field-display-image
Description: Easily add images as fields in your form. Native integration with Gravity Forms and WordPress block style settings. Gives you full control over sizing and styling your images with precision. Upload an image file, pick one from your media library, or add one with a URL to display in the form as a field.
Author: Timothy Wood (@codearachnid)
Version: 2.0.0
Requires at least: 4.0
Requires PHP: 5.6
Tested up to: 6.1.1
Author URI: https://codearachnid.com
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: gf_field_display_image
*/

/*
	Copyright 2022 Timothy Wood

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('GFFDI_VERSION', '2.0.0');

add_action( 'gform_loaded', 'GF_Field_Display_Image_Bootstrap' );
function GF_Field_Display_Image_Bootstrap() {

	if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
		return;
	}

	// are we on GF 2.5+
	//define( 'GFFDI_GF_MIN_2_5', version_compare( GFCommon::$version, '2.5-dev-1', '>=' ) );

	require_once( 'class-addon.php' );
	GFAddOn::register( 'GFAddonDisplayImage' );

}
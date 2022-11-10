<?php
/*
Plugin Name: Gravity Forms Field Image Display
Plugin URI: 
Description: Easily add images to display in the form as a field
Author: Timothy Wood (@codearachnid)
Version: 1.1.0
Requires at 3.5
Tested up to: 6.0
Author URI: https://codearachnid.com
License: GPL2
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

define('GFFDI_VERSION', '1.0.3');

add_action( 'gform_loaded', 'GF_Field_Display_Image_Bootstrap' );
function GF_Field_Display_Image_Bootstrap() {

	if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
		return;
	}

	// are we on GF 2.5+
	//define( 'GFFDI_GF_MIN_2_5', version_compare( GFCommon::$version, '2.5-dev-1', '>=' ) );

	require_once( 'class-gf-addon-display-image.php' );
	GFAddOn::register( 'GFAddonDisplayImage' );

}

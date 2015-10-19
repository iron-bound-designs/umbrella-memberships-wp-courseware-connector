<?php
/*
 * Plugin Name: Umbrella Memberships WP Courseware Connector
 * Version: 1.0
 * Plugin URI: https://ironbounddesigns.com/product/umbrella-memberships/
 * Description: Integrate WP Courseware with Umbrella Memberships for iThemes Exchange
 * Author: Iron Bound Designs
 * Author URI: https://ironbounddesigns.com
 * License: GPLv2 or later
 */

add_action( 'init', function () {

	if ( ! class_exists( 'WPCW_Exchange' ) || ! defined( 'WPCW_PLUGIN_VERSION' ) || ! class_exists( '\ITEGMS\Plugin' ) ) {
		add_action( 'all_admin_notices', function () {
			?>

			<div class="notice notice-error">
				<p>
					Umbrella Memberships for WP Courseware requires: Umbrella Memberships, WP Courseware and the WP Courseware Exchange Connector Plugin.
				</p>
			</div>
			<?php
		} );
	} else {

		require_once __DIR__ . '/class.wpcw-exchange-umbrella.php';

		remove_action( 'init', 'WPCW_Exchange_init', 1 );

		$item = new WPCW_Exchange_Umbrella();

		// Check for WP Courseware
		if ( ! $item->found_wpcourseware() ) {
			$item->attach_showWPCWNotDetectedMessage();

			return;
		}

		// Not found the membership tool
		if ( ! $item->found_membershipTool() ) {
			$item->attach_showToolNotDetectedMessage();

			return;
		}

		// Found the tool and WP Coursewar, attach.
		$item->attachToTools();
	}

}, 0 );
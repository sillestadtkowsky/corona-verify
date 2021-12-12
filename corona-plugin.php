<?php
ob_start();

/**
 *
 * Plugin Name:       Corona-Test-Verify
 * Plugin URI:        https://plugins-wordpress-osowsky-webdesign.info
 * Description:       Dieses Plugin erlaubt jedem Mitarbeiter das digitale Vorzeigen eines gültigen 3G-Status, nach dem dieser zentral im Betrieb erfasst wurde. Für den Gegencheck wird zusätzlich ein QR-Code erzeugt, der eine zeitlich beschränkte Gültigkeit hat.
 * Version:           1.6.0
 * Requires at least: 5.8.2
 * Requires PHP:      7.2
 * Author:            Silvio Osowsky
 * Author URI:        https://osowsky-webdesign.de
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       osowsky-design-plugin
 */

define( 'CUSER_PAGE', __FILE__ );
define( 'CORONA_TEST_VERIFY_PLUGIN', __FILE__ );
define( 'CORONA_TEST_VERIFY_PLUGIN_BASENAME', plugin_basename( CORONA_TEST_VERIFY_PLUGIN ) );
define( 'CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR', untrailingslashit( dirname( CORONA_TEST_VERIFY_PLUGIN ) ) );
define( 'CORONA_TEST_VERIFY_PLUGIN_PLUGIN_PATH', plugin_dir_url( __FILE__ ) );

/*
* Load required classes
*/

require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/inc/wp-enqueue.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/main/shortcode.php';

require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/class/excel.class.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/class/updater.class.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/class/db.class.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/class/utils.class.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/class/secure.class.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/class/qr.class.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/class/option.class.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/class/initdb.class.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/class/employee.table.class.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/class/testview.table.class.php';

require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/admin/corona-admin.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/admin/menus/tools.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/admin/menus/options.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/admin/menus/employee.php';
require_once CORONA_TEST_VERIFY_PLUGIN_PLUGIN_DIR . '/admin/menus/corona-test.php';

/**
 * Plugin Uninstall DB
 */
register_activation_hook( __FILE__, array ( 'CV_INITDB', 'installDB') );
register_deactivation_hook( __FILE__, array ( 'CV_INITDB', 'deInstallDB') );
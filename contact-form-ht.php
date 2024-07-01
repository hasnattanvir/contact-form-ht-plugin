<?php
/**
 * Plugin Name: Contact Form HT
 * Description: A custom contact form plugin with admin dashboard capabilities.
 * Version: 1.0
 * Author: A H Tanvir
 * Requires at least:5.6
 * Requires PHP:8.0
 * Author:A H Tanvir
 * Author URI: https://github.com/hasnattanvir
 * License: GPL V2 or later
 * License URI: http://www.gnu.org/licenses/lgpl.html
 * Update URI: https://github.com/hasnattanvir/contact-form-ht-plugin
 * Company Name: linuxbangla
 * Text Domain:cfht
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include required files
include_once plugin_dir_path(__FILE__) . 'includes/class-contact-form.php';
include_once plugin_dir_path(__FILE__) . 'includes/class-contact-form-admin.php';

// Initialize the plugin
function cf_plugin_init() {
    $contact_form = new Contact_Form();
    $contact_form_admin = new Contact_Form_Admin();
}
add_action('plugins_loaded', 'cf_plugin_init');

// Function to create database table on plugin activation
function cf_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form_entries';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        email text NOT NULL,
        phone text NOT NULL,
        photo_url text NOT NULL,
        message text NOT NULL,
        date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Register the activation hook
register_activation_hook(__FILE__, 'cf_create_table');
?>


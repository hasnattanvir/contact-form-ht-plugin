<?php
if (!class_exists('Contact_Form_Admin')) {
    class Contact_Form_Admin {

        public function __construct() {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_post_delete_contact_form_entry', array($this, 'delete_contact_form_entry'));
            add_action('admin_post_edit_contact_form_entry', array($this, 'edit_contact_form_entry'));
            add_action('admin_post_update_contact_form_entry', array($this, 'update_contact_form_entry'));

            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }

        public function add_admin_menu() {
            // page ttitle
            // menu title
            // required capability
            // menu slug
            // this argument is a callback function
            // -this refers to the current class
            // -admin-page (custom method display content menu page)
            // icon
            // menu position
            add_menu_page(
                'Contact Form Submissions',
                'Contact Form',
                'manage_options',
                'contact-form-submissions',
                array($this, 'admin_page'),
                'dashicons-email-alt',
                26
            );
            // parent slug
            // page title
            // menu title
            // capability
            // menu slug
            // callback function
            add_submenu_page(
                'contact-form-submissions',
                'Info',
                'Info',
                'manage_options',
                'contact-form-info',
                array($this, 'info_page')
            );
            add_submenu_page(
                null,
                'Edit Contact Form Entry',
                'Edit Contact Form Entry',
                'manage_options',
                'edit-contact-form-entry',
                array($this, 'edit_contact_form_entry')
            );
        }

        public function admin_page() {
            global $wpdb;
            $table_name = $wpdb->prefix . 'contact_form_entries';
            $results = $wpdb->get_results("SELECT * FROM $table_name");

            echo '<div class="wrap">';
            echo '<h1>Contact Form Submissions</h1>';
            echo '<table class="widefat fixed" cellspacing="0">';
            echo '<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Message</th><th>Photo</th><th>Date</th><th>Actions</th></tr></thead>';
            echo '<tbody>';
            foreach ($results as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row->name) . '</td>';
                echo '<td>' . esc_html($row->email) . '</td>';
                echo '<td>' . esc_html($row->phone) . '</td>';
                echo '<td>' . esc_html($row->message) . '</td>';
                echo '<td><img width="100px" src="' . esc_url($row->photo_url) . '" alt="photo"> <br/> <a href="' . esc_url($row->photo_url) . '" target="_blank">View Photo</a></td>';
                echo '<td>' . esc_html($row->date) . '</td>';
                echo '<td>';
                echo '<a href="' . wp_nonce_url(admin_url('admin-post.php?action=delete_contact_form_entry&id=' . $row->id), 'delete_contact_form_entry_' . $row->id) . '">Delete</a> | ';
                echo '<a href="' . admin_url('admin.php?page=edit-contact-form-entry&id=' . $row->id) . '">Edit</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }

        public function delete_contact_form_entry() {
            if (!isset($_GET['id']) || !wp_verify_nonce($_GET['_wpnonce'], 'delete_contact_form_entry_' . $_GET['id'])) {
                wp_die('Invalid request.');
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'contact_form_entries';
            $wpdb->delete($table_name, array('id' => intval($_GET['id'])));

            wp_redirect(admin_url('admin.php?page=contact-form-submissions'));
            exit;
        }

        public function edit_contact_form_entry() {
            if (!isset($_GET['id'])) {
                wp_die('Invalid request.');
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'contact_form_entries';
            $entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", intval($_GET['id'])));

            if (!$entry) {
                wp_die('Entry not found.');
            }

            include plugin_dir_path(__FILE__) . '../templates/edit-contact-form-entry.php';
        }

        public function update_contact_form_entry() {
            if (!isset($_POST['id'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['message'])) {
                wp_die('Missing required fields.');
            }

            if (!check_admin_referer('update_contact_form_entry_' . $_POST['id'])) {
                wp_die('Invalid request.');
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'contact_form_entries';

            $data = array(
                'name' => sanitize_text_field($_POST['name']),
                'email' => sanitize_email($_POST['email']),
                'phone' => sanitize_text_field($_POST['phone']),
                'message' => sanitize_textarea_field($_POST['message'])
            );

            if (!empty($_FILES['photo']['name'])) {
                $allowed_file_types = array('image/jpeg', 'image/png', 'image/jpg');
                $file_type = wp_check_filetype_and_ext($_FILES['photo']['tmp_name'], $_FILES['photo']['name']);

                if (!in_array($file_type['type'], $allowed_file_types)) {
                    wp_die('Only JPG, JPEG, and PNG files are allowed.');
                }

                $uploaded = media_handle_upload('photo', 0);

                if (is_wp_error($uploaded)) {
                    wp_die('Image upload failed.');
                }

                $data['photo_url'] = wp_get_attachment_url($uploaded);
            }

            $wpdb->update($table_name, $data, array('id' => intval($_POST['id'])));

            wp_redirect(admin_url('admin.php?page=contact-form-submissions'));
            exit;
        }

        public function info_page() {
            include plugin_dir_path(__FILE__) . '../templates/info-page.php';
        }
    }
}
?>

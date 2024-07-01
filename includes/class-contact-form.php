<?php
if (!class_exists('Contact_Form')) {
    class Contact_Form {

        public function __construct() {
            add_shortcode('contact_form', array($this, 'render_form_shortcode'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('wp_ajax_submit_contact_form', array($this, 'handle_form_submission'));
            add_action('wp_ajax_nopriv_submit_contact_form', array($this, 'handle_form_submission'));
        }

        public function enqueue_scripts() {
            wp_enqueue_style('contact-form-style', plugin_dir_url(__FILE__) . '../assets/css/contact-form.css');
            wp_enqueue_script('contact-form-script', plugin_dir_url(__FILE__) . '../assets/js/contact-form.js', array('jquery'), null, true);
            wp_localize_script('contact-form-script', 'contact_form_vars', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('contact_form_nonce'),
            ));
        }
        public function render_form_shortcode() {
            ob_start();
            ?>
            <form id="contact-form" enctype="multipart/form-data">
                <label for="cf-name">Name:</label>
                <input type="text" id="cf-name" name="name" required>
                <label for="cf-email">Email:</label>
                <input type="email" id="cf-email" name="email" required>
                <label for="cf-phone">Phone Number:</label>
                <input type="tel" id="cf-phone" name="phone" required>
                <label for="cf-photo">Photo:</label>
                <input type="file" id="cf-photo" name="photo" required>
                <label for="cf-message">Message:</label>
                <textarea id="cf-message" name="message" required></textarea>
                <button type="submit">Submit</button>
            </form>
            <div id="cf-response"></div>
            <?php
            return ob_get_clean();
        }

        public function handle_form_submission() {
            check_ajax_referer('contact_form_nonce', 'nonce');

            if (!isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['message'])) {
                wp_send_json_error('Missing required fields.');
            }

            $name = sanitize_text_field($_POST['name']);
            $email = sanitize_email($_POST['email']);
            $phone = sanitize_text_field($_POST['phone']);
            $message = sanitize_textarea_field($_POST['message']);

            if (!empty($_FILES['photo']['name'])) {
                $upload = wp_handle_upload($_FILES['photo'], array('test_form' => false));
                if (isset($upload['url'])) {
                    $photo_url = $upload['url'];
                } else {
                    wp_send_json_error('Photo upload failed.');
                }
            }

            // Send email to admin
            $admin_email = get_option('admin_email');
            $subject = 'New Contact Form Submission';
            $body = "Name: $name\nEmail: $email\nPhone: $phone\nMessage: $message\nPhoto: $photo_url";
            wp_mail($admin_email, $subject, $body);

            // Send thank you email to user
            $user_subject = 'Thank You for Contacting Us';
            $user_body = 'Thank you for your message. We will get back to you shortly.';
            wp_mail($email, $user_subject, $user_body);

            // Save to database
            global $wpdb;
            $table_name = $wpdb->prefix . 'contact_form_entries';
            $wpdb->insert($table_name, array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'photo_url' => $photo_url,
                'message' => $message,
                'date' => current_time('mysql')
            ));

            wp_send_json_success('Form submitted successfully.');
        }
    }
}
?>

<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="wrap">
    <h1>Edit Contact Form Entry</h1>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_contact_form_entry">
        <input type="hidden" name="id" value="<?php echo esc_attr($entry->id); ?>">
        <?php wp_nonce_field('update_contact_form_entry_' . $entry->id); ?>

        <table class="form-table">
            <tr>
                <th><label for="cfht-name">Name</label></th>
                <td><input type="text" id="cfht-name" name="name" value="<?php echo esc_attr($entry->name); ?>" required></td>
            </tr>
            <tr>
                <th><label for="cfht-email">Email</label></th>
                <td><input type="email" id="cfht-email" name="email" value="<?php echo esc_attr($entry->email); ?>" required></td>
            </tr>
            <tr>
                <th><label for="cfht-phone">Phone Number</label></th>
                <td><input type="tel" id="cfht-phone" name="phone" value="<?php echo esc_attr($entry->phone); ?>" required></td>
            </tr>
            <tr>
                <th><label for="cfht-message">Message</label></th>
                <td><textarea id="cfht-message" name="message" required><?php echo esc_textarea($entry->message); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="cfht-photo">Photo</label></th>
                <td>
                    <input type="file" id="cfht-photo" name="photo">
                    <?php if (!empty($entry->photo_url)) : ?>
                        <p>Current Photo: <a href="<?php echo esc_url($entry->photo_url); ?>" target="_blank">View Photo</a></p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button-primary">Update Entry</button>
            <a href="<?php echo admin_url('admin.php?page=contact-form-submissions'); ?>" class="button-secondary">Cancel</a>
        </p>
    </form>
</div>

<div class="wrap">
    <h1><?php _e('Rabbit Booking Settings', 'rabbit-booking'); ?></h1>

    <form method="post">
        <?php wp_nonce_field('rabbit_booking_save_settings', 'rabbit_booking_settings_nonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="swish_number"><?php _e('Swish Number', 'rabbit-booking'); ?></label>
                </th>
                <td>
                    <input type="text" id="swish_number" name="swish_number" value="<?php echo esc_attr($swish_number); ?>" class="regular-text">
                    <p class="description"><?php _e('The Swish number that customers will pay to', 'rabbit-booking'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="email_sender"><?php _e('Email Sender', 'rabbit-booking'); ?></label>
                </th>
                <td>
                    <input type="email" id="email_sender" name="email_sender" value="<?php echo esc_attr($email_sender); ?>" class="regular-text">
                    <p class="description"><?php _e('The email address used as sender for booking confirmations', 'rabbit-booking'); ?></p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" class="button button-primary" value="<?php _e('Save Settings', 'rabbit-booking'); ?>">
        </p>
    </form>
</div>

<div class="wrap">
    <h1><?php _e('Rabbit Cage Bookings', 'rabbit-booking'); ?></h1>

    <div class="rabbit-booking-filters">
        <form method="get">
            <input type="hidden" name="page" value="rabbit-booking">

            <div class="filter-group">
                <label for="status"><?php _e('Status:', 'rabbit-booking'); ?></label>
                <select name="status" id="status">
                    <option value=""><?php _e('All', 'rabbit-booking'); ?></option>
                    <option value="pending" <?php selected($status_filter, 'pending'); ?>><?php _e('Pending', 'rabbit-booking'); ?></option>
                    <option value="confirmed" <?php selected($status_filter, 'confirmed'); ?>><?php _e('Confirmed', 'rabbit-booking'); ?></option>
                    <option value="completed" <?php selected($status_filter, 'completed'); ?>><?php _e('Completed', 'rabbit-booking'); ?></option>
                    <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>><?php _e('Cancelled', 'rabbit-booking'); ?></option>
                </select>
            </div>

            <div class="filter-group">
                <label for="date_from"><?php _e('From:', 'rabbit-booking'); ?></label>
                <input type="text" name="date_from" id="date_from" class="datepicker" value="<?php echo esc_attr($date_from); ?>">
            </div>

            <div class="filter-group">
                <label for="date_to"><?php _e('To:', 'rabbit-booking'); ?></label>
                <input type="text" name="date_to" id="date_to" class="datepicker" value="<?php echo esc_attr($date_to); ?>">
            </div>

            <div class="filter-group">
                <button type="submit" class="button"><?php _e('Filter', 'rabbit-booking'); ?></button>
                <a href="?page=rabbit-booking" class="button"><?php _e('Reset', 'rabbit-booking'); ?></a>
            </div>
        </form>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('ID', 'rabbit-booking'); ?></th>
                <th><?php _e('Reference', 'rabbit-booking'); ?></th>
                <th><?php _e('Customer', 'rabbit-booking'); ?></th>
                <th><?php _e('Dates', 'rabbit-booking'); ?></th>
                <th><?php _e('Rabbits', 'rabbit-booking'); ?></th>
                <th><?php _e('Cage Type', 'rabbit-booking'); ?></th>
                <th><?php _e('Price', 'rabbit-booking'); ?></th>
                <th><?php _e('Payment', 'rabbit-booking'); ?></th>
                <th><?php _e('Status', 'rabbit-booking'); ?></th>
                <th><?php _e('Actions', 'rabbit-booking'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)): ?>
                <tr>
                    <td colspan="10"><?php _e('No bookings found.', 'rabbit-booking'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo esc_html($booking->id); ?></td>
                        <td><?php echo esc_html($booking->payment_reference); ?></td>
                        <td>
                            <strong><?php echo esc_html($booking->customer_name); ?></strong><br>
                            <a href="mailto:<?php echo esc_attr($booking->customer_email); ?>"><?php echo esc_html($booking->customer_email); ?></a><br>
                            <?php echo esc_html($booking->customer_phone); ?>
                        </td>
                        <td>
                            <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($booking->start_date))); ?> -<br>
                            <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($booking->end_date))); ?>
                        </td>
                        <td>
                            <?php echo esc_html($booking->number_of_rabbits); ?> rabbits<br>
                            <?php echo esc_html($booking->number_of_cages); ?> cages<br>
                            <?php echo $booking->shared_cage ? __('Shared', 'rabbit-booking') : __('Individual', 'rabbit-booking'); ?>
                        </td>
                        <td><?php echo esc_html($booking->cage_type_name); ?></td>
                        <td><?php echo esc_html($booking->total_price); ?> kr</td>
                        <td>
                            <select class="payment-status" data-booking-id="<?php echo esc_attr($booking->id); ?>">
                                <option value="pending" <?php selected($booking->payment_status, 'pending'); ?>><?php _e('Pending', 'rabbit-booking'); ?></option>
                                <option value="paid" <?php selected($booking->payment_status, 'paid'); ?>><?php _e('Paid', 'rabbit-booking'); ?></option>
                                <option value="refunded" <?php selected($booking->payment_status, 'refunded'); ?>><?php _e('Refunded', 'rabbit-booking'); ?></option>
                            </select>
                        </td>
                        <td>
                            <select class="booking-status" data-booking-id="<?php echo esc_attr($booking->id); ?>">
                                <option value="pending" <?php selected($booking->booking_status, 'pending'); ?>><?php _e('Pending', 'rabbit-booking'); ?></option>
                                <option value="confirmed" <?php selected($booking->booking_status, 'confirmed'); ?>><?php _e('Confirmed', 'rabbit-booking'); ?></option>
                                <option value="completed" <?php selected($booking->booking_status, 'completed'); ?>><?php _e('Completed', 'rabbit-booking'); ?></option>
                                <option value="cancelled" <?php selected($booking->booking_status, 'cancelled'); ?>><?php _e('Cancelled', 'rabbit-booking'); ?></option>
                            </select>
                        </td>
                        <td>
                            <button class="button delete-booking" data-booking-id="<?php echo esc_attr($booking->id); ?>"><?php _e('Delete', 'rabbit-booking'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="rabbit-booking-form-container">
    <h2><?php esc_html_e('Book Rabbit Boarding', 'rabbit-booking'); ?></h2>

    <form id="rabbit-booking-form" method="post">
        <?php wp_nonce_field('rabbit_booking_form', 'rabbit_booking_nonce'); ?>

        <div class="form-group">
            <label for="customer_name"><?php esc_html_e('Your Name', 'rabbit-booking'); ?> *</label>
            <input type="text" id="customer_name" name="customer_name" required>
        </div>

        <div class="form-group">
            <label for="customer_email"><?php esc_html_e('Email Address', 'rabbit-booking'); ?> *</label>
            <input type="email" id="customer_email" name="customer_email" required>
        </div>

        <div class="form-group">
            <label for="customer_phone"><?php esc_html_e('Phone Number', 'rabbit-booking'); ?> *</label>
            <input type="tel" id="customer_phone" name="customer_phone" required>
        </div>

        <div class="form-group">
            <label for="start_date"><?php esc_html_e('Drop-off Date', 'rabbit-booking'); ?> *</label>
            <input type="text" id="start_date" name="start_date" class="datepicker" required>
        </div>

        <div class="form-group">
            <label for="end_date"><?php esc_html_e('Pick-up Date', 'rabbit-booking'); ?> *</label>
            <input type="text" id="end_date" name="end_date" class="datepicker" required>
        </div>

        <div class="form-group">
            <label for="number_of_rabbits"><?php esc_html_e('Number of Rabbits', 'rabbit-booking'); ?> *</label>
            <input type="number" id="number_of_rabbits" name="number_of_rabbits" min="1" value="1" required>
        </div>

        <div class="form-group">
            <label><?php esc_html_e('Cage Preference', 'rabbit-booking'); ?> *</label>
            <div class="radio-group">
                <input type="radio" id="shared_cage_yes" name="shared_cage" value="1" checked>
                <label for="shared_cage_yes"><?php esc_html_e('Keep rabbits together (if possible)', 'rabbit-booking'); ?></label>
            </div>
            <div class="radio-group">
                <input type="radio" id="shared_cage_no" name="shared_cage" value="0">
                <label for="shared_cage_no"><?php esc_html_e('Individual cages', 'rabbit-booking'); ?></label>
            </div>
        </div>

        <div class="form-group">
            <label for="cage_type"><?php esc_html_e('Cage Type', 'rabbit-booking'); ?> *</label>
            <select id="cage_type" name="cage_type" required>
                <?php foreach ($cage_types as $cage): ?>
                <option value="<?php echo esc_attr($cage->id); ?>"
                        data-price="<?php echo esc_attr($cage->price_per_day); ?>"
                        data-capacity="<?php echo esc_attr($cage->capacity); ?>">
                    <?php echo esc_html($cage->name); ?> -
                    <?php echo esc_html($cage->price_per_day); ?> kr/day
                    (<?php echo esc_html($cage->description); ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group calculated-info" style="display:none;">
            <label><?php esc_html_e('Booking Summary', 'rabbit-booking'); ?></label>
            <div class="summary-container">
                <p><?php esc_html_e('Number of cages needed:', 'rabbit-booking'); ?> <span id="number_of_cages"></span></p>
                <p><?php esc_html_e('Number of days:', 'rabbit-booking'); ?> <span id="number_of_days"></span></p>
                <p><?php esc_html_e('Price per day:', 'rabbit-booking'); ?> <span id="price_per_day"></span> kr</p>
                <p class="total-price"><?php esc_html_e('Total price:', 'rabbit-booking'); ?> <span id="total_price"></span> kr</p>
            </div>
        </div>

        <div id="availability-message" class="availability-message"></div>

        <div class="form-group">
            <button type="submit" id="check-availability" class="button button-primary"><?php esc_html_e('Check Availability', 'rabbit-booking'); ?></button>
            <button type="submit" id="submit-booking" class="button button-primary" style="display:none;"><?php esc_html_e('Book Now', 'rabbit-booking'); ?></button>
        </div>
    </form>
</div>

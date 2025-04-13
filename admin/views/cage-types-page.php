<div class="wrap">
    <h1><?php _e('Rabbit Cage Types', 'rabbit-booking'); ?></h1>

    <div class="cage-type-add-new">
        <h2><?php _e('Add New Cage Type', 'rabbit-booking'); ?></h2>
        <form id="add-cage-type-form">
            <div class="form-group">
                <label for="new-name"><?php _e('Name', 'rabbit-booking'); ?></label>
                <input type="text" id="new-name" name="name" required>
            </div>

            <div class="form-group">
                <label for="new-description"><?php _e('Description', 'rabbit-booking'); ?></label>
                <textarea id="new-description" name="description" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="new-price"><?php _e('Price per Day (kr)', 'rabbit-booking'); ?></label>
                <input type="number" id="new-price" name="price_per_day" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="new-capacity"><?php _e('Rabbit Capacity per Cage', 'rabbit-booking'); ?></label>
                <input type="number" id="new-capacity" name="capacity" min="1" value="1" required>
            </div>

            <div class="form-group">
                <label for="new-available"><?php _e('Total Available Cages', 'rabbit-booking'); ?></label>
                <input type="number" id="new-available" name="total_available" min="1" required>
            </div>

            <div class="form-group">
                <button type="submit" class="button button-primary"><?php _e('Add Cage Type', 'rabbit-booking'); ?></button>
            </div>
        </form>
    </div>

    <h2><?php _e('Existing Cage Types', 'rabbit-booking'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('ID', 'rabbit-booking'); ?></th>
                <th><?php _e('Name', 'rabbit-booking'); ?></th>
                <th><?php _e('Description', 'rabbit-booking'); ?></th>
                <th><?php _e('Price per Day', 'rabbit-booking'); ?></th>
                <th><?php _e('Capacity', 'rabbit-booking'); ?></th>
                <th><?php _e('Total Available', 'rabbit-booking'); ?></th>
                <th><?php _e('Actions', 'rabbit-booking'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($cage_types)): ?>
                <tr>
                    <td colspan="7"><?php _e('No cage types found.', 'rabbit-booking'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($cage_types as $cage): ?>
                    <tr class="cage-type-row" data-id="<?php echo esc_attr($cage->id); ?>">
                        <td><?php echo esc_html($cage->id); ?></td>
                        <td>
                            <span class="display-value"><?php echo esc_html($cage->name); ?></span>
                            <input type="text" class="edit-field" name="name" value="<?php echo esc_attr($cage->name); ?>" style="display:none;" required>
                        </td>
                        <td>
                            <span class="display-value"><?php echo esc_html($cage->description); ?></span>
                            <textarea class="edit-field" name="description" rows="3" style="display:none;" required><?php echo esc_textarea($cage->description); ?></textarea>
                        </td>
                        <td>
                            <span class="display-value"><?php echo esc_html($cage->price_per_day); ?> kr</span>
                            <input type="number" class="edit-field" name="price_per_day" value="<?php echo esc_attr($cage->price_per_day); ?>" step="0.01" min="0" style="display:none;" required>
                        </td>
                        <td>
                            <span class="display-value"><?php echo esc_html($cage->capacity); ?></span>
                            <input type="number" class="edit-field" name="capacity" value="<?php echo esc_attr($cage->capacity); ?>" min="1" style="display:none;" required>
                        </td>
                        <td>
                            <span class="display-value"><?php echo esc_html($cage->total_available); ?></span>
                            <input type="number" class="edit-field" name="total_available" value="<?php echo esc_attr($cage->total_available); ?>" min="1" style="display:none;" required>
                        </td>
                        <td class="actions">
                            <button class="button edit-cage-type"><?php _e('Edit', 'rabbit-booking'); ?></button>
                            <button class="button button-primary save-cage-type" style="display:none;"><?php _e('Save', 'rabbit-booking'); ?></button>
                            <button class="button cancel-edit" style="display:none;"><?php _e('Cancel', 'rabbit-booking'); ?></button>
                            <button class="button delete-cage-type"><?php _e('Delete', 'rabbit-booking'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

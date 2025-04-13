jQuery(document).ready(function($) {
    // Initialize datepickers
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    // Update booking status
    $('.booking-status').on('change', function() {
        var bookingId = $(this).data('booking-id');
        var status = $(this).val();

        $.ajax({
            type: 'POST',
            url: rabbit_booking.ajax_url,
            data: {
                action: 'update_booking_status',
                nonce: rabbit_booking.nonce,
                booking_id: bookingId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Show notification
                    $('<div class="notice notice-success is-dismissible"><p>' +
                        'Booking status updated successfully.</p></div>')
                        .insertBefore('.wp-list-table')
                        .delay(3000)
                        .fadeOut(function() {
                            $(this).remove();
                        });
                } else {
                    alert(response.data);
                }
            }
        });
    });

    // Update payment status
    $('.payment-status').on('change', function() {
        var bookingId = $(this).data('booking-id');
        var status = $(this).val();

        $.ajax({
            type: 'POST',
            url: rabbit_booking.ajax_url,
            data: {
                action: 'update_payment_status',
                nonce: rabbit_booking.nonce,
                booking_id: bookingId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Show notification
                    $('<div class="notice notice-success is-dismissible"><p>' +
                        'Payment status updated successfully.</p></div>')
                        .insertBefore('.wp-list-table')
                        .delay(3000)
                        .fadeOut(function() {
                            $(this).remove();
                        });
                } else {
                    alert(response.data);
                }
            }
        });
    });

    // Delete booking
    $('.delete-booking').on('click', function() {
        if (!confirm(rabbit_booking.confirm_delete)) {
            return;
        }

        var bookingId = $(this).data('booking-id');
        var row = $(this).closest('tr');

        $.ajax({
            type: 'POST',
            url: rabbit_booking.ajax_url,
            data: {
                action: 'delete_booking',
                nonce: rabbit_booking.nonce,
                booking_id: bookingId
            },
            success: function(response) {
                if (response.success) {
                    // Remove the row from the table
                    row.fadeOut(400, function() {
                        $(this).remove();

                        // Show notification
                        $('<div class="notice notice-success is-dismissible"><p>' +
                            'Booking deleted successfully.</p></div>')
                            .insertBefore('.wp-list-table')
                            .delay(3000)
                            .fadeOut(function() {
                                $(this).remove();
                            });

                        // If no more rows, add an empty row
                        if ($('.wp-list-table tbody tr').length === 0) {
                            $('.wp-list-table tbody').append(
                                '<tr><td colspan="10">No bookings found.</td></tr>'
                            );
                        }
                    });
                } else {
                    alert(response.data);
                }
            }
        });
    });

    // Edit cage type
    $('.edit-cage-type').on('click', function() {
        var row = $(this).closest('tr');

        // Show edit fields, hide display values
        row.find('.display-value').hide();
        row.find('.edit-field').show();

        // Show save and cancel buttons, hide edit and delete buttons
        row.find('.edit-cage-type, .delete-cage-type').hide();
        row.find('.save-cage-type, .cancel-edit').show();
    });

    // Cancel edit
    $('.cancel-edit').on('click', function() {
        var row = $(this).closest('tr');

        // Hide edit fields, show display values
        row.find('.edit-field').hide();
        row.find('.display-value').show();

        // Show edit and delete buttons, hide save and cancel buttons
        row.find('.save-cage-type, .cancel-edit').hide();
        row.find('.edit-cage-type, .delete-cage-type').show();
    });

    // Save cage type
    $('.save-cage-type').on('click', function() {
        var row = $(this).closest('tr');
        var cageId = row.data('id');

        // Validate form fields
        var valid = true;
        row.find('.edit-field[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('error');
                valid = false;
            } else {
                $(this).removeClass('error');
            }
        });

        if (!valid) {
            alert('Please fill in all required fields.');
            return;
        }

        // Get values from edit fields
        var name = row.find('.edit-field[name="name"]').val();
        var description = row.find('.edit-field[name="description"]').val();
        var pricePerDay = row.find('input[name="price_per_day"]').val();
                var capacity = row.find('input[name="capacity"]').val();
                var totalAvailable = row.find('input[name="total_available"]').val();

                $.ajax({
                    type: 'POST',
                    url: rabbit_booking.ajax_url,
                    data: {
                        action: 'update_cage_type',
                        nonce: rabbit_booking.nonce,
                        cage_id: cageId,
                        name: name,
                        description: description,
                        price_per_day: pricePerDay,
                        capacity: capacity,
                        total_available: totalAvailable
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update display values
                            row.find('td:nth-child(2) .display-value').text(name);
                            row.find('td:nth-child(3) .display-value').text(description);
                            row.find('td:nth-child(4) .display-value').text(pricePerDay + ' kr');
                            row.find('td:nth-child(5) .display-value').text(capacity);
                            row.find('td:nth-child(6) .display-value').text(totalAvailable);

                            // Show display values, hide edit fields
                            row.find('.display-value').show();
                            row.find('.edit-field').hide();

                            // Show edit button, hide save/cancel buttons
                            row.find('.edit-cage-type, .delete-cage-type').show();
                            row.find('.save-cage-type, .cancel-edit').hide();

                            // Show notification
                            $('<div class="notice notice-success is-dismissible"><p>' +
                                'Cage type updated successfully.</p></div>')
                                .insertBefore('.wp-list-table')
                                .delay(3000)
                                .fadeOut(function() {
                                    $(this).remove();
                                });
                        } else {
                            alert(response.data);
                        }
                    }
                });
            });

            // Delete cage type
            $('.delete-cage-type').on('click', function() {
                if (!confirm(rabbit_booking.confirm_delete)) {
                    return;
                }

                var row = $(this).closest('tr');
                var cageId = row.data('id');

                $.ajax({
                    type: 'POST',
                    url: rabbit_booking.ajax_url,
                    data: {
                        action: 'delete_cage_type',
                        nonce: rabbit_booking.nonce,
                        cage_id: cageId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove the row
                            row.fadeOut(400, function() {
                                $(this).remove();
                            });

                            // Show notification
                            $('<div class="notice notice-success is-dismissible"><p>' +
                                'Cage type deleted successfully.</p></div>')
                                .insertBefore('.wp-list-table')
                                .delay(3000)
                                .fadeOut(function() {
                                    $(this).remove();
                                });
                        } else {
                            alert(response.data);
                        }
                    }
                });
            });

            // Add new cage type
            $('#add-cage-type-form').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var name = form.find('input[name="name"]').val();
                var description = form.find('textarea[name="description"]').val();
                var pricePerDay = form.find('input[name="price_per_day"]').val();
                var capacity = form.find('input[name="capacity"]').val();
                var totalAvailable = form.find('input[name="total_available"]').val();

                $.ajax({
                    type: 'POST',
                    url: rabbit_booking.ajax_url,
                    data: {
                        action: 'add_cage_type',
                        nonce: rabbit_booking.nonce,
                        name: name,
                        description: description,
                        price_per_day: pricePerDay,
                        capacity: capacity,
                        total_available: totalAvailable
                    },
                    success: function(response) {
                        if (response.success) {
                            // Create new row and append to table
                            var newRow = '' +
                                '<tr class="cage-type-row" data-id="' + response.data.id + '">' +
                                '   <td>' + response.data.id + '</td>' +
                                '   <td>' +
                                '       <span class="display-value">' + name + '</span>' +
                                '       <input type="text" class="edit-field" name="name" value="' + name + '" style="display:none;" required>' +
                                '   </td>' +
                                '   <td>' +
                                '       <span class="display-value">' + description + '</span>' +
                                '       <textarea class="edit-field" name="description" rows="3" style="display:none;" required>' + description + '</textarea>' +
                                '   </td>' +
                                '   <td>' +
                                '       <span class="display-value">' + pricePerDay + ' kr</span>' +
                                '       <input type="number" class="edit-field" name="price_per_day" value="' + pricePerDay + '" step="0.01" min="0" style="display:none;" required>' +
                                '   </td>' +
                                '   <td>' +
                                '       <span class="display-value">' + capacity + '</span>' +
                                '       <input type="number" class="edit-field" name="capacity" value="' + capacity + '" min="1" style="display:none;" required>' +
                                '   </td>' +
                                '   <td>' +
                                '       <span class="display-value">' + totalAvailable + '</span>' +
                                '       <input type="number" class="edit-field" name="total_available" value="' + totalAvailable + '" min="1" style="display:none;" required>' +
                                '   </td>' +
                                '   <td class="actions">' +
                                '       <button class="button edit-cage-type">Edit</button>' +
                                '       <button class="button button-primary save-cage-type" style="display:none;">Save</button>' +
                                '       <button class="button cancel-edit" style="display:none;">Cancel</button>' +
                                '       <button class="button delete-cage-type">Delete</button>' +
                                '   </td>' +
                                '</tr>';

                            $('table tbody').append(newRow);

                            // Clear form
                            form[0].reset();

                            // Show notification
                            $('<div class="notice notice-success is-dismissible"><p>' +
                                'Cage type added successfully.</p></div>')
                                .insertBefore('.wp-list-table')
                                .delay(3000)
                                .fadeOut(function() {
                                    $(this).remove();
                                });

                            // Rebind event handlers to the new row
                            bindEventsToNewRow();
                        } else {
                            alert(response.data);
                        }
                    }
                });
            });

            // Bind events to a newly added row
            function bindEventsToNewRow() {
                // Edit cage type
                $('.edit-cage-type').last().on('click', function() {
                    var row = $(this).closest('tr');
                    row.find('.display-value').hide();
                    row.find('.edit-field').show();
                    row.find('.edit-cage-type, .delete-cage-type').hide();
                    row.find('.save-cage-type, .cancel-edit').show();
                });

                // Cancel edit
                $('.cancel-edit').last().on('click', function() {
                    var row = $(this).closest('tr');
                    row.find('.display-value').show();
                    row.find('.edit-field').hide();
                    row.find('.edit-cage-type, .delete-cage-type').show();
                    row.find('.save-cage-type, .cancel-edit').hide();
                });

                // Save cage type
                $('.save-cage-type').last().on('click', function() {
                    var row = $(this).closest('tr');
                    var cageId = row.data('id');

                    var name = row.find('input[name="name"]').val();
                    var description = row.find('textarea[name="description"]').val();
                    var pricePerDay = row.find('input[name="price_per_day"]').val();
                    var capacity = row.find('input[name="capacity"]').val();
                    var totalAvailable = row.find('input[name="total_available"]').val();

                    $.ajax({
                        type: 'POST',
                        url: rabbit_booking.ajax_url,
                        data: {
                            action: 'update_cage_type',
                            nonce: rabbit_booking.nonce,
                            cage_id: cageId,
                            name: name,
                            description: description,
                            price_per_day: pricePerDay,
                            capacity: capacity,
                            total_available: totalAvailable
                        },
                        success: function(response) {
                            if (response.success) {
                                row.find('td:nth-child(2) .display-value').text(name);
                                row.find('td:nth-child(3) .display-value').text(description);
                                row.find('td:nth-child(4) .display-value').text(pricePerDay + ' kr');
                                row.find('td:nth-child(5) .display-value').text(capacity);
                                row.find('td:nth-child(6) .display-value').text(totalAvailable);

                                row.find('.display-value').show();
                                row.find('.edit-field').hide();
                                row.find('.edit-cage-type, .delete-cage-type').show();
                                row.find('.save-cage-type, .cancel-edit').hide();
                            } else {
                                alert(response.data);
                            }
                        }
                    });
                });

                // Delete cage type
                $('.delete-cage-type').last().on('click', function() {
                    if (!confirm(rabbit_booking.confirm_delete)) {
                        return;
                    }

                    var row = $(this).closest('tr');
                    var cageId = row.data('id');

                    $.ajax({
                        type: 'POST',
                        url: rabbit_booking.ajax_url,
                        data: {
                            action: 'delete_cage_type',
                            nonce: rabbit_booking.nonce,
                            cage_id: cageId
                        },
                        success: function(response) {
                            if (response.success) {
                                row.fadeOut(400, function() {
                                    $(this).remove();
                                });
                            } else {
                                alert(response.data);
                            }
                        }
                    });
                });
            }
        });

<div class="wrap">
    <h1><?php _e('Booking Calendar', 'rabbit-booking'); ?></h1>

    <div id="booking-calendar"></div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize the calendar
    $('#booking-calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        events: [
            <?php
            global $wpdb;
            $bookings = $wpdb->get_results("
                SELECT b.*, ct.name as cage_type_name
                FROM {$wpdb->prefix}rabbit_bookings b
                JOIN {$wpdb->prefix}rabbit_cage_types ct ON b.cage_type_id = ct.id
                WHERE b.booking_status != 'cancelled'
            ");

            $events = array();
            foreach ($bookings as $booking) {
                $color = '';
                switch ($booking->booking_status) {
                    case 'pending':
                        $color = '#ffc107'; // Yellow
                        break;
                    case 'confirmed':
                        $color = '#28a745'; // Green
                        break;
                    case 'completed':
                        $color = '#6c757d'; // Gray
                        break;
                    default:
                        $color = '#007bff'; // Blue
                }

                echo "{
                    title: '{$booking->customer_name} ({$booking->number_of_rabbits} rabbits)',
                    start: '{$booking->start_date}',
                    end: '{$booking->end_date}',
                    url: '?page=rabbit-booking&booking_id={$booking->id}',
                    backgroundColor: '{$color}',
                    borderColor: '{$color}'
                },";
            }
            ?>
        ]
    });
});
</script>

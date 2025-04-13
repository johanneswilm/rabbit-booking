<?php

class Rabbit_Booking_Database {

    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Cage types table
        $table_cage_types = $wpdb->prefix . 'rabbit_cage_types';
        $sql_cage_types = "CREATE TABLE $table_cage_types (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text NOT NULL,
            price_per_day decimal(10,2) NOT NULL,
            capacity int NOT NULL,
            total_available int NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Bookings table
        $table_bookings = $wpdb->prefix . 'rabbit_bookings';
        $sql_bookings = "CREATE TABLE $table_bookings (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            customer_name varchar(255) NOT NULL,
            customer_email varchar(255) NOT NULL,
            customer_phone varchar(20) NOT NULL,
            start_date date NOT NULL,
            end_date date NOT NULL,
            number_of_rabbits int NOT NULL,
            shared_cage tinyint(1) NOT NULL,
            cage_type_id mediumint(9) NOT NULL,
            number_of_cages int NOT NULL,
            total_price decimal(10,2) NOT NULL,
            payment_status varchar(50) NOT NULL DEFAULT 'pending',
            payment_reference varchar(255),
            booking_status varchar(50) NOT NULL DEFAULT 'pending',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_cage_types);
        dbDelta($sql_bookings);

        // Insert default cage types if they don't exist
        if ($wpdb->get_var("SELECT COUNT(*) FROM $table_cage_types") == 0) {
            $wpdb->insert($table_cage_types, array(
                'name' => 'Standard Cage',
                'description' => 'Standard sized rabbit cage suitable for most rabbits',
                'price_per_day' => 50.00,
                'capacity' => 1,
                'total_available' => 10
            ));

            $wpdb->insert($table_cage_types, array(
                'name' => 'Premium Cage',
                'description' => 'Larger cage with more space and amenities',
                'price_per_day' => 75.00,
                'capacity' => 2,
                'total_available' => 5
            ));
        }
    }
}

<?php
/**
 * Plugin Name: Bakery Ordering
 * Description: A preliminary wholesale bakery dashboard. Ordering workflows are not yet implemented.
 * Version: 0.1.0
 * Text Domain: bakery-ordering
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function bakery_ordering_register_catalog_types() {
	register_post_type(
		'bo_product',
		array(
			'labels' => array( 'name' => 'Bakery Products', 'singular_name' => 'Bakery Product', 'add_new_item' => 'Add Bakery Product', 'edit_item' => 'Edit Bakery Product' ),
			'public' => false,
			'show_ui' => false,
			'show_in_rest' => false,
			'supports' => array( 'title', 'thumbnail' ),
			'capability_type' => 'post',
		)
	);
	register_taxonomy( 'bo_product_category', array( 'bo_product' ), array( 'labels' => array( 'name' => 'Bakery Product Categories', 'singular_name' => 'Bakery Product Category', 'search_items' => 'Search Product Categories', 'all_items' => 'Product Categories', 'edit_item' => 'Edit Product Category', 'add_new_item' => 'Add Product Category', 'new_item_name' => 'New Product Category Name' ), 'public' => false, 'show_ui' => false, 'show_in_rest' => false, 'hierarchical' => true, 'rewrite' => false, 'query_var' => false ) );
	register_post_type(
		'bo_price_group',
		array(
			'labels' => array( 'name' => 'Wholesale Price Groups', 'singular_name' => 'Wholesale Price Group' ),
			'public' => false,
			'show_ui' => false,
			'show_in_rest' => false,
			'supports' => array( 'title' ),
		)
	);
}
add_action( 'init', 'bakery_ordering_register_catalog_types' );

function bakery_ordering_price_groups() {
	return get_posts( array( 'post_type' => 'bo_price_group', 'post_status' => array( 'publish', 'draft' ), 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
}

function bakery_ordering_product_units() {
	$units = array( 'Each', 'Dozen', 'Pan', 'Loaf', 'Box', 'Case', 'Pound', 'Tray' );
	$custom = get_option( 'bakery_ordering_custom_product_units', array() );
	foreach ( is_array( $custom ) ? $custom : array() as $unit ) {
		if ( ! is_string( $unit ) ) {
			continue;
		}
		$unit = trim( sanitize_text_field( $unit ) );
		if ( '' === $unit ) {
			continue;
		}
		$exists = false;
		foreach ( $units as $existing ) {
			if ( 0 === strcasecmp( $existing, $unit ) ) {
				$exists = true;
				break;
			}
		}
		if ( ! $exists ) {
			$units[] = $unit;
		}
	}
	return $units;
}

function bakery_ordering_product_unit_suffix( $unit ) {
	$unit = strtolower( trim( sanitize_text_field( $unit ) ) );
	$suffixes = array(
		'each'  => 'ea',
		'dozen' => 'doz',
		'pound' => 'lb',
	);
	return isset( $suffixes[ $unit ] ) ? $suffixes[ $unit ] : $unit;
}

function bakery_ordering_clean_product_date( $value ) {
	$value = is_string( $value ) ? sanitize_text_field( $value ) : '';
	if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $parts ) || ! wp_checkdate( (int) $parts[2], (int) $parts[3], (int) $parts[1], $value ) ) {
		return '';
	}
	return $value;
}

function bakery_ordering_products() {
	return get_posts( array( 'post_type' => 'bo_product', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
}

function bakery_ordering_weekdays() {
	return array(
		'monday'    => 'Monday',
		'tuesday'   => 'Tuesday',
		'wednesday' => 'Wednesday',
		'thursday'  => 'Thursday',
		'friday'    => 'Friday',
		'saturday'  => 'Saturday',
		'sunday'    => 'Sunday',
	);
}

function bakery_ordering_clean_weekdays( $input ) {
	$weekdays = bakery_ordering_weekdays();
	$selected = array();
	foreach ( is_array( $input ) ? $input : array() as $day ) {
		if ( ! is_string( $day ) ) {
			continue;
		}
		$day = sanitize_key( $day );
		if ( isset( $weekdays[ $day ] ) ) {
			$selected[] = $day;
		}
	}
	return array_values( array_unique( $selected ) );
}

function bakery_ordering_weekday_summary( $selected ) {
	$weekdays = bakery_ordering_weekdays();
	$selected = bakery_ordering_clean_weekdays( $selected );
	if ( count( $selected ) === count( $weekdays ) ) {
		return 'Available every day';
	}
	if ( empty( $selected ) ) {
		return 'No weekdays selected';
	}
	$labels = array();
	foreach ( $selected as $day ) {
		$labels[] = $weekdays[ $day ];
	}
	return 'Available ' . implode( ', ', $labels );
}

function bakery_ordering_holidays() {
	return array(
		'new_years_day'          => array( 'name' => 'New Year’s Day', 'rule' => 'January 1' ),
		'martin_luther_king_day'  => array( 'name' => 'Martin Luther King Jr. Day', 'rule' => 'Third Monday in January' ),
		'presidents_day'          => array( 'name' => 'Presidents’ Day', 'rule' => 'Third Monday in February' ),
		'good_friday'             => array( 'name' => 'Good Friday', 'rule' => 'Friday before Easter' ),
		'easter_sunday'           => array( 'name' => 'Easter Sunday', 'rule' => 'Date changes each year' ),
		'mothers_day'             => array( 'name' => 'Mother’s Day', 'rule' => 'Second Sunday in May' ),
		'memorial_day'            => array( 'name' => 'Memorial Day', 'rule' => 'Last Monday in May' ),
		'fathers_day'             => array( 'name' => 'Father’s Day', 'rule' => 'Third Sunday in June' ),
		'juneteenth'              => array( 'name' => 'Juneteenth', 'rule' => 'June 19' ),
		'independence_day'        => array( 'name' => 'Independence Day', 'rule' => 'July 4' ),
		'labor_day'               => array( 'name' => 'Labor Day', 'rule' => 'First Monday in September' ),
		'indigenous_peoples_day'  => array( 'name' => 'Indigenous Peoples’ Day', 'rule' => 'Second Monday in October' ),
		'veterans_day'            => array( 'name' => 'Veterans Day', 'rule' => 'November 11' ),
		'thanksgiving'            => array( 'name' => 'Thanksgiving', 'rule' => 'Fourth Thursday in November' ),
		'christmas_eve'           => array( 'name' => 'Christmas Eve', 'rule' => 'December 24' ),
		'christmas_day'           => array( 'name' => 'Christmas Day', 'rule' => 'December 25' ),
		'new_years_eve'           => array( 'name' => 'New Year’s Eve', 'rule' => 'December 31' ),
	);
}

function bakery_ordering_nth_weekday_date( $year, $month, $weekday, $occurrence ) {
	$date = new DateTimeImmutable( sprintf( '%04d-%02d-01', $year, $month ), wp_timezone() );
	$offset = ( ( $weekday - (int) $date->format( 'N' ) ) + 7 ) % 7;
	return $date->modify( '+' . ( $offset + ( 7 * ( $occurrence - 1 ) ) ) . ' days' )->format( 'Y-m-d' );
}

function bakery_ordering_easter_date( $year ) {
	$a = $year % 19;
	$b = intdiv( $year, 100 );
	$c = $year % 100;
	$d = intdiv( $b, 4 );
	$e = $b % 4;
	$f = intdiv( $b + 8, 25 );
	$g = intdiv( $b - $f + 1, 3 );
	$h = ( 19 * $a + $b - $d - $g + 15 ) % 30;
	$i = intdiv( $c, 4 );
	$k = $c % 4;
	$l = ( 32 + ( 2 * $e ) + ( 2 * $i ) - $h - $k ) % 7;
	$m = intdiv( $a + ( 11 * $h ) + ( 22 * $l ), 451 );
	$value = $h + $l - ( 7 * $m ) + 114;
	$month = intdiv( $value, 31 );
	$day = ( $value % 31 ) + 1;
	return new DateTimeImmutable( sprintf( '%04d-%02d-%02d', $year, $month, $day ), wp_timezone() );
}

function bakery_ordering_clean_holidays( $input ) {
	$selected = array();
	$valid_holidays = bakery_ordering_holidays();
	foreach ( is_array( $input ) ? $input : array() as $holiday ) {
		if ( ! is_string( $holiday ) ) {
			continue;
		}
		$holiday = sanitize_key( $holiday );
		if ( isset( $valid_holidays[ $holiday ] ) ) {
			$selected[] = $holiday;
		}
	}
	return array_values( array_unique( $selected ) );
}

function bakery_ordering_format_calendar_date( $date ) {
	$date_object = DateTimeImmutable::createFromFormat( '!Y-m-d', $date, wp_timezone() );
	return $date_object ? wp_date( get_option( 'date_format' ), $date_object->getTimestamp(), wp_timezone() ) : $date;
}

function bakery_ordering_holiday_dates( $year ) {
	$year = absint( $year );
	$dates = array(
		'new_years_day'   => sprintf( '%04d-01-01', $year ),
		'juneteenth'      => sprintf( '%04d-06-19', $year ),
		'independence_day'=> sprintf( '%04d-07-04', $year ),
		'veterans_day'    => sprintf( '%04d-11-11', $year ),
		'christmas_eve'   => sprintf( '%04d-12-24', $year ),
		'christmas_day'   => sprintf( '%04d-12-25', $year ),
		'new_years_eve'   => sprintf( '%04d-12-31', $year ),
		'martin_luther_king_day' => bakery_ordering_nth_weekday_date( $year, 1, 1, 3 ),
		'presidents_day'         => bakery_ordering_nth_weekday_date( $year, 2, 1, 3 ),
		'memorial_day'           => ( new DateTimeImmutable( sprintf( '%04d-05-01', $year ), wp_timezone() ) )->modify( 'last monday of May' )->format( 'Y-m-d' ),
		'mothers_day'            => bakery_ordering_nth_weekday_date( $year, 5, 7, 2 ),
		'fathers_day'            => bakery_ordering_nth_weekday_date( $year, 6, 7, 3 ),
		'labor_day'              => bakery_ordering_nth_weekday_date( $year, 9, 1, 1 ),
		'indigenous_peoples_day' => bakery_ordering_nth_weekday_date( $year, 10, 1, 2 ),
		'thanksgiving'           => bakery_ordering_nth_weekday_date( $year, 11, 4, 4 ),
	);
	$easter = bakery_ordering_easter_date( $year );
	$dates['easter_sunday'] = $easter->format( 'Y-m-d' );
	$dates['good_friday'] = $easter->modify( '-2 days' )->format( 'Y-m-d' );
	return $dates;
}

function bakery_ordering_global_availability() {
	$settings = get_option( 'bakery_ordering_global_availability', array() );
	$settings = is_array( $settings ) ? $settings : array();
	$blocked_dates = isset( $settings['blocked_dates'] ) && is_array( $settings['blocked_dates'] ) ? $settings['blocked_dates'] : array();
	$clean_dates = array();
	foreach ( $blocked_dates as $date => $note ) {
		if ( ! is_string( $date ) || ! is_scalar( $note ) ) {
			continue;
		}
		$date = sanitize_text_field( $date );
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			$clean_dates[ $date ] = sanitize_text_field( $note );
		}
	}
	ksort( $clean_dates );
	return array(
		'closed_weekdays' => bakery_ordering_clean_weekdays( isset( $settings['closed_weekdays'] ) ? $settings['closed_weekdays'] : array() ),
		'closed_holidays' => bakery_ordering_clean_holidays( isset( $settings['closed_holidays'] ) ? $settings['closed_holidays'] : array() ),
		'blocked_dates'   => $clean_dates,
	);
}

function bakery_ordering_handle_catalog_actions() {
	if ( empty( $_POST['bakery_ordering_action'] ) ) {
		return;
	}
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to manage the bakery catalog.', 'bakery-ordering' ), '', array( 'response' => 403 ) );
	}
	$action = sanitize_key( wp_unslash( $_POST['bakery_ordering_action'] ) );
	check_admin_referer( 'bakery_ordering_catalog_' . $action );

	if ( 'edit_product_category' === $action ) {
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$term_id = isset( $_POST['term_id'] ) ? absint( $_POST['term_id'] ) : 0;
		$product = get_post( $product_id );
		$term = $term_id ? get_term( $term_id, 'bo_product_category' ) : null;
		if ( ! $product || 'bo_product' !== $product->post_type || ! $term || is_wp_error( $term ) ) {
			wp_die( esc_html__( 'Invalid product category.', 'bakery-ordering' ), '', array( 'response' => 400 ) );
		}
		$operation = isset( $_POST['category_operation'] ) ? sanitize_key( wp_unslash( $_POST['category_operation'] ) ) : '';
		if ( 'rename_category' === $operation ) {
			$new_name = isset( $_POST['category_name'] ) ? sanitize_text_field( wp_unslash( $_POST['category_name'] ) ) : '';
			if ( '' !== $new_name ) {
				$result = wp_update_term( $term_id, 'bo_product_category', array( 'name' => $new_name ) );
				if ( is_wp_error( $result ) ) {
					wp_die( esc_html( $result->get_error_message() ) );
				}
			}
		} elseif ( 'remove_from_product' === $operation ) {
			wp_remove_object_terms( $product_id, $term_id, 'bo_product_category' );
		}
		wp_safe_redirect( add_query_arg( array( 'bakery_dashboard' => '1', 'view' => 'products', 'updated' => '1' ), home_url( '/' ) ) );
		exit;
	}


	if ( 'add_category' === $action ) {
		$name = isset( $_POST['category_name'] ) ? sanitize_text_field( wp_unslash( $_POST['category_name'] ) ) : '';
		if ( '' !== $name ) {
			$result = wp_insert_term( $name, 'bo_product_category' );
			if ( is_wp_error( $result ) && 'term_exists' !== $result->get_error_code() ) {
				wp_die( esc_html( $result->get_error_message() ) );
			}
		}
		wp_safe_redirect( add_query_arg( array( 'bakery_dashboard' => '1', 'view' => 'categories', 'updated' => '1' ), home_url( '/' ) ) );
		exit;
	}

	if ( 'delete_category' === $action ) {
		$term_id = isset( $_POST['term_id'] ) ? absint( $_POST['term_id'] ) : 0;
		if ( $term_id ) {
			wp_delete_term( $term_id, 'bo_product_category' );
		}
		wp_safe_redirect( add_query_arg( array( 'bakery_dashboard' => '1', 'view' => 'categories', 'updated' => '1' ), home_url( '/' ) ) );
		exit;
	}

	if ( 'save_group' === $action ) {
		$name = isset( $_POST['group_name'] ) ? sanitize_text_field( wp_unslash( $_POST['group_name'] ) ) : '';
		$group_id = isset( $_POST['group_id'] ) ? absint( $_POST['group_id'] ) : 0;
		if ( '' !== $name ) {
			$result = wp_insert_post( array( 'ID' => $group_id, 'post_type' => 'bo_price_group', 'post_status' => 'publish', 'post_title' => $name ), true );
			if ( is_wp_error( $result ) ) {
				wp_die( esc_html( $result->get_error_message() ) );
			}
			if ( ! empty( $_POST['group_description'] ) ) {
				update_post_meta( $result, '_bo_group_description', sanitize_textarea_field( wp_unslash( $_POST['group_description'] ) ) );
			}
		}
		wp_safe_redirect( add_query_arg( array( 'bakery_dashboard' => '1', 'view' => 'groups', 'updated' => '1' ), home_url( '/' ) ) );
		exit;
	}

	if ( 'delete_group' === $action ) {
		$group_id = isset( $_POST['group_id'] ) ? absint( $_POST['group_id'] ) : 0;
		$group = get_post( $group_id );
		if ( $group && 'bo_price_group' === $group->post_type ) {
			foreach ( get_posts( array( 'post_type' => 'bo_product', 'post_status' => array( 'publish', 'draft' ), 'numberposts' => -1, 'fields' => 'ids' ) ) as $product_id ) {
				$prices = get_post_meta( $product_id, '_bo_group_prices', true );
				if ( is_array( $prices ) && isset( $prices[ $group_id ] ) ) {
					unset( $prices[ $group_id ] );
					update_post_meta( $product_id, '_bo_group_prices', $prices );
				}
			}
			wp_delete_post( $group_id, true );
		}
		wp_safe_redirect( add_query_arg( array( 'bakery_dashboard' => '1', 'view' => 'groups', 'updated' => '1' ), home_url( '/' ) ) );
		exit;
	}

	if ( 'delete_product' === $action ) {
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$product = get_post( $product_id );
		if ( $product && 'bo_product' === $product->post_type ) {
			wp_delete_post( $product_id, true );
		}
		wp_safe_redirect( add_query_arg( array( 'bakery_dashboard' => '1', 'view' => 'products', 'updated' => '1' ), home_url( '/' ) ) );
		exit;
	}

	if ( 'save_availability' === $action ) {
		$settings = bakery_ordering_global_availability();
		$settings['closed_weekdays'] = bakery_ordering_clean_weekdays( isset( $_POST['closed_weekdays'] ) ? wp_unslash( $_POST['closed_weekdays'] ) : array() );
		$holiday_input = isset( $_POST['closed_holidays'] ) && is_array( $_POST['closed_holidays'] ) ? wp_unslash( $_POST['closed_holidays'] ) : array();
		$settings['closed_holidays'] = bakery_ordering_clean_holidays( $holiday_input );
		$remove_dates = isset( $_POST['remove_blocked_dates'] ) && is_array( $_POST['remove_blocked_dates'] ) ? wp_unslash( $_POST['remove_blocked_dates'] ) : array();
		foreach ( $remove_dates as $date ) {
			if ( is_string( $date ) ) {
				unset( $settings['blocked_dates'][ sanitize_text_field( $date ) ] );
			}
		}
		$date_error = false;
		$date_input = isset( $_POST['blocked_date'] ) ? wp_unslash( $_POST['blocked_date'] ) : '';
		$date = is_string( $date_input ) ? sanitize_text_field( $date_input ) : '';
		if ( '' !== $date ) {
			$date_parts = array();
			if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $date, $date_parts ) && wp_checkdate( (int) $date_parts[2], (int) $date_parts[3], (int) $date_parts[1], $date ) ) {
				$note = isset( $_POST['blocked_date_note'] ) ? wp_unslash( $_POST['blocked_date_note'] ) : '';
				$settings['blocked_dates'][ $date ] = is_string( $note ) ? sanitize_text_field( $note ) : '';
			} else {
				$date_error = true;
			}
		}
		update_option( 'bakery_ordering_global_availability', $settings, false );
		$status_key = $date_error ? 'error' : 'updated';
		$redirect = add_query_arg( array( 'bakery_dashboard' => '1', 'view' => 'availability', $status_key => '1' ), home_url( '/' ) );
		wp_safe_redirect( $redirect );
		exit;
	}

	if ( 'save_product' === $action ) {
		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$existing = $product_id ? get_post( $product_id ) : null;
		if ( $existing && 'bo_product' !== $existing->post_type ) {
			wp_die( esc_html__( 'Invalid product.', 'bakery-ordering' ), '', array( 'response' => 400 ) );
		}
		$name = isset( $_POST['product_name'] ) ? sanitize_text_field( wp_unslash( $_POST['product_name'] ) ) : '';
		if ( '' === $name ) {
			wp_safe_redirect( add_query_arg( array( 'bakery_dashboard' => '1', 'view' => 'products', 'edit_product' => $product_id, 'error' => 'name' ), home_url( '/' ) ) );
			exit;
		}
		$product_id = wp_insert_post( array( 'ID' => $product_id, 'post_type' => 'bo_product', 'post_status' => 'publish', 'post_title' => $name, 'post_content' => isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '' ), true );
		if ( is_wp_error( $product_id ) ) {
			wp_die( esc_html( $product_id->get_error_message() ) );
		}
		$category_ids = isset( $_POST['product_categories'] ) && is_array( $_POST['product_categories'] ) ? array_values( array_unique( array_filter( array_map( 'absint', wp_unslash( $_POST['product_categories'] ) ) ) ) ) : array();
		$valid_category_ids = array();
		foreach ( $category_ids as $category_id ) {
			$term = get_term( $category_id, 'bo_product_category' );
			if ( $term && ! is_wp_error( $term ) ) {
				$valid_category_ids[] = $category_id;
			}
		}
		$new_category_names = isset( $_POST['new_product_categories'] ) && is_array( $_POST['new_product_categories'] ) ? wp_unslash( $_POST['new_product_categories'] ) : array();
		foreach ( $new_category_names as $new_category_name ) {
			if ( ! is_string( $new_category_name ) ) {
				continue;
			}
			$new_category_name = sanitize_text_field( $new_category_name );
			if ( '' === $new_category_name ) {
				continue;
			}
			$existing_term = get_term_by( 'name', $new_category_name, 'bo_product_category' );
			if ( $existing_term ) {
				$valid_category_ids[] = (int) $existing_term->term_id;
				continue;
			}
			$created_term = wp_insert_term( $new_category_name, 'bo_product_category' );
			if ( is_wp_error( $created_term ) ) {
				if ( 'term_exists' === $created_term->get_error_code() ) {
					$existing_term_id = absint( $created_term->get_error_data() );
					if ( $existing_term_id ) {
						$valid_category_ids[] = $existing_term_id;
					}
					continue;
				}
				wp_die( esc_html( $created_term->get_error_message() ) );
			}
			$valid_category_ids[] = (int) $created_term['term_id'];
		}
		$valid_category_ids = array_values( array_unique( array_map( 'absint', $valid_category_ids ) ) );
		wp_set_object_terms( $product_id, $valid_category_ids, 'bo_product_category', false );
		$sku = isset( $_POST['sku'] ) ? sanitize_text_field( wp_unslash( $_POST['sku'] ) ) : '';
		$pack_quantity = isset( $_POST['order_increment'] ) ? absint( $_POST['order_increment'] ) : 1;
		$pack_label = isset( $_POST['item_unit'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['item_unit'] ) ) ) : 'Each';
		if ( function_exists( 'mb_substr' ) ) {
			$pack_label = mb_substr( $pack_label, 0, 40 );
		} else {
			$pack_label = substr( $pack_label, 0, 40 );
		}
		$pack_label = '' !== $pack_label ? $pack_label : 'Each';
		$known_units = bakery_ordering_product_units();
		foreach ( $known_units as $known_unit ) {
			if ( 0 === strcasecmp( $known_unit, $pack_label ) ) {
				$pack_label = $known_unit;
				break;
			}
		}
		$standard_units = array( 'Each', 'Dozen', 'Pan', 'Loaf', 'Box', 'Case', 'Pound', 'Tray' );
		$custom_units = get_option( 'bakery_ordering_custom_product_units', array() );
		$custom_units = is_array( $custom_units ) ? $custom_units : array();
		$clean_custom_units = array();
		foreach ( $custom_units as $custom_unit ) {
			if ( is_string( $custom_unit ) ) {
				$custom_unit = trim( sanitize_text_field( $custom_unit ) );
				if ( '' !== $custom_unit ) {
					$clean_custom_units[] = $custom_unit;
				}
			}
		}
		$custom_units = $clean_custom_units;
		$unit_is_standard = false;
		foreach ( $standard_units as $standard_unit ) {
			if ( 0 === strcasecmp( $standard_unit, $pack_label ) ) {
				$unit_is_standard = true;
				break;
			}
		}
		if ( ! $unit_is_standard && ! in_array( $pack_label, $known_units, true ) ) {
			$custom_units[] = $pack_label;
			update_option( 'bakery_ordering_custom_product_units', array_values( array_unique( $custom_units ) ), false );
		}
		$pack_quantity = max( 1, $pack_quantity );
		$base_price = isset( $_POST['base_price'] ) ? wp_unslash( $_POST['base_price'] ) : '';
		$prices_in = isset( $_POST['group_prices'] ) && is_array( $_POST['group_prices'] ) ? wp_unslash( $_POST['group_prices'] ) : array();
		update_post_meta( $product_id, '_bo_sku', $sku );
		update_post_meta( $product_id, '_bo_unit', $pack_label );
		update_post_meta( $product_id, '_bo_pack_quantity', $pack_quantity );
		update_post_meta( $product_id, '_bo_pack_label', $pack_label );
		$available_weekdays = isset( $_POST['available_weekdays'] ) && is_array( $_POST['available_weekdays'] ) ? wp_unslash( $_POST['available_weekdays'] ) : array();
		update_post_meta( $product_id, '_bo_available_weekdays', bakery_ordering_clean_weekdays( $available_weekdays ) );
		$start_date = bakery_ordering_clean_product_date( isset( $_POST['product_start_date'] ) ? wp_unslash( $_POST['product_start_date'] ) : '' );
		$end_date = bakery_ordering_clean_product_date( isset( $_POST['product_end_date'] ) ? wp_unslash( $_POST['product_end_date'] ) : '' );
		if ( $start_date && $end_date && $end_date < $start_date ) {
			$end_date = $start_date;
		}
		if ( $start_date ) { update_post_meta( $product_id, '_bo_product_start_date', $start_date ); } else { delete_post_meta( $product_id, '_bo_product_start_date' ); }
		if ( $end_date ) { update_post_meta( $product_id, '_bo_product_end_date', $end_date ); } else { delete_post_meta( $product_id, '_bo_product_end_date' ); }
		update_post_meta( $product_id, '_bo_product_schedule_recurring', isset( $_POST['product_schedule_recurring'] ) ? '1' : '0' );
		$clean_base = '' === $base_price ? '' : sanitize_text_field( $base_price );
		$clean_base = '' === $clean_base ? '' : preg_replace( '/[^0-9.]/', '', $clean_base );
		update_post_meta( $product_id, '_bo_base_price', '' === $clean_base ? '' : number_format( (float) $clean_base, 2, '.', '' ) );
		$valid_groups = wp_list_pluck( bakery_ordering_price_groups(), 'ID' );
		$prices = array();
		foreach ( $prices_in as $group_id => $price ) {
			$group_id = absint( $group_id );
			$clean_price = preg_replace( '/[^0-9.]/', '', sanitize_text_field( $price ) );
			if ( in_array( $group_id, $valid_groups, true ) && '' !== $clean_price && is_numeric( $clean_price ) && (float) $clean_price >= 0 ) {
				$prices[ $group_id ] = $clean_price;
			}
		}
		update_post_meta( $product_id, '_bo_group_prices', $prices );
		$duplicate_source_id = isset( $_POST['duplicate_source_id'] ) ? absint( $_POST['duplicate_source_id'] ) : 0;
		$duplicate_source = $duplicate_source_id ? get_post( $duplicate_source_id ) : null;
		if ( ! empty( $_FILES['product_image']['name'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attachment_id = media_handle_upload( 'product_image', $product_id );
			if ( is_wp_error( $attachment_id ) ) {
				wp_die( esc_html( $attachment_id->get_error_message() ) );
			}
			set_post_thumbnail( $product_id, $attachment_id );
		} elseif ( $duplicate_source && 'bo_product' === $duplicate_source->post_type && has_post_thumbnail( $duplicate_source ) ) {
			set_post_thumbnail( $product_id, get_post_thumbnail_id( $duplicate_source ) );
		}
		if ( ! empty( $_POST['remove_image'] ) && has_post_thumbnail( $product_id ) ) {
			delete_post_thumbnail( $product_id );
		}
		$next = isset( $_POST['save_and_add'] ) ? array( 'view' => 'products', 'created' => '1' ) : array( 'view' => 'products', 'updated' => '1' );
		wp_safe_redirect( add_query_arg( array_merge( array( 'bakery_dashboard' => '1' ), $next ), home_url( '/' ) ) );
		exit;
	}
}
add_action( 'init', 'bakery_ordering_handle_catalog_actions', 20 );

function bakery_ordering_assets() {
	$view = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : '';
	if ( in_array( $view, array( 'products', 'groups', 'availability', 'categories' ), true ) ) {
		bakery_ordering_enqueue_catalog_assets();
		return;
	}
	wp_enqueue_style( 'bakery-ordering-dashboard', plugins_url( 'assets/css/dashboard.css', __FILE__ ), array(), filemtime( __DIR__ . '/assets/css/dashboard.css' ) );
	wp_enqueue_script( 'bakery-ordering-dashboard', plugins_url( 'assets/js/dashboard.js', __FILE__ ), array(), '0.1.0', true );
}

function bakery_ordering_shortcode_assets() {
	global $post;
	if ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'bakery_ordering_dashboard' ) && current_user_can( 'manage_options' ) ) {
		bakery_ordering_assets();
	}
}
add_action( 'wp_enqueue_scripts', 'bakery_ordering_shortcode_assets' );

function bakery_ordering_dashboard() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return '<p>' . esc_html__( 'The dashboard preview is currently available to administrators.', 'bakery-ordering' ) . '</p>';
	}
	$view = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : 'dashboard';
	if ( in_array( $view, array( 'products', 'groups', 'availability', 'categories' ), true ) ) {
		ob_start();
		require __DIR__ . '/templates/catalog.php';
		return ob_get_clean();
	}
	bakery_ordering_assets();
	ob_start();
	require __DIR__ . '/templates/dashboard.php';
	return ob_get_clean();
}

function bakery_ordering_enqueue_catalog_assets() {
	wp_enqueue_style( 'bakery-ordering-dashboard', plugins_url( 'assets/css/dashboard.css', __FILE__ ), array(), filemtime( __DIR__ . '/assets/css/dashboard.css' ) );
	wp_enqueue_style( 'bakery-ordering-catalog', plugins_url( 'assets/css/catalog.css', __FILE__ ), array( 'bakery-ordering-dashboard' ), filemtime( __DIR__ . '/assets/css/catalog.css' ) );
	wp_enqueue_script( 'bakery-ordering-catalog', plugins_url( 'assets/js/catalog.js', __FILE__ ), array(), filemtime( __DIR__ . '/assets/js/catalog.js' ), true );
	$product_id = isset( $_GET['edit_product'] ) ? absint( $_GET['edit_product'] ) : ( isset( $_GET['duplicate_product'] ) ? absint( $_GET['duplicate_product'] ) : 0 );
	wp_add_inline_script( 'bakery-ordering-catalog', 'window.bakeryOrderingProductSchedule = ' . wp_json_encode( array( 'start' => $product_id ? get_post_meta( $product_id, '_bo_product_start_date', true ) : '', 'end' => $product_id ? get_post_meta( $product_id, '_bo_product_end_date', true ) : '', 'recurring' => $product_id && '1' === get_post_meta( $product_id, '_bo_product_schedule_recurring', true ) ) ) . ';', 'before' );
}
add_shortcode( 'bakery_ordering_dashboard', 'bakery_ordering_dashboard' );

function bakery_ordering_preview() {
	if ( ! isset( $_GET['bakery_dashboard'] ) && ! isset( $_POST['bakery_ordering_action'] ) ) {
		return;
	}
	// This is a front-end route: use the logged-in cookie, not the admin-only auth cookie.
	if ( ! is_user_logged_in() ) {
		$preview_url = add_query_arg( 'bakery_dashboard', '1', home_url( '/' ) );
		wp_safe_redirect( wp_login_url( $preview_url ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this preview.', 'bakery-ordering' ), '', array( 'response' => 403 ) );
	}
	nocache_headers();
	bakery_ordering_assets();
	?><!doctype html>
	<html <?php language_attributes(); ?>>
	<head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?php esc_html_e( 'Hos Bakery — Dashboard Preview', 'bakery-ordering' ); ?></title><?php wp_head(); ?></head>
	<body class="bakery-ordering-preview"><?php echo bakery_ordering_dashboard(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in template. ?><?php wp_footer(); ?></body>
	</html><?php
	exit;
}
add_action( 'template_redirect', 'bakery_ordering_preview' );

function bakery_ordering_admin_menu() {
	add_menu_page( 'Bakery Ordering', 'Bakery Ordering', 'manage_options', 'bakery-ordering', 'bakery_ordering_admin_page', 'dashicons-store', 26 );
}
add_action( 'admin_menu', 'bakery_ordering_admin_menu' );

function bakery_ordering_admin_page() {
	?>
	<div class="wrap"><h1><?php esc_html_e( 'Bakery Ordering', 'bakery-ordering' ); ?></h1>
	<p><?php esc_html_e( 'Open the bakery workspace to manage your wholesale catalog and customer prices.', 'bakery-ordering' ); ?></p>
	<p><a class="button button-primary" href="<?php echo esc_url( add_query_arg( array( 'bakery_dashboard' => '1', 'view' => 'products' ), home_url( '/' ) ) ); ?>">Open Product Catalog</a> <a class="button" href="<?php echo esc_url( add_query_arg( 'bakery_dashboard', '1', home_url( '/' ) ) ); ?>">Open Dashboard Preview</a></p></div>
	<?php
}

/** Return a fixed, decorative icon; never accepts user-provided SVG. */
function bakery_ordering_icon( $name ) {
	$paths = array(
		'home' => '<path d="m3 10 9-7 9 7v11h-6v-7H9v7H3z"/>',
		'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 11h18"/>',
		'layers' => '<path d="m2 8 10-6 10 6-10 6zM2 13l10 6 10-6M2 18l10 6 10-6"/>',
		'croissant' => '<path d="M8 8c-3-3-6 0-5 4l-1 6 5-2m9-8c3-3 6 0 5 4l1 6-5-2M7 16 9 6q3-3 6 0l2 10q-5 4-10 0ZM10 5l1 13m3-13-1 13"/>',
		'history' => '<path d="M3 10a9 9 0 1 1 0 6M3 3v7h7m2-4v6l4 2"/>',
		'user' => '<circle cx="12" cy="7" r="4"/><path d="M3 22v-2a9 9 0 0 1 18 0v2z"/>',
		'truck' => '<path d="M2 5h12v13H2zM14 9h4l4 5v4h-8"/><circle cx="6" cy="19" r="2"/><circle cx="18" cy="19" r="2"/>',
		'delete' => '<path d="M3 6h18m-2 0-.9 14H5.9L5 6m4 0V4h6v2m-5 4v6m4-6v6"/>',
		'upload' => '<path d="M12 16V3m-5 5 5-5 5 5M4 15v5a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-5"/>',
		'edit' => '<path d="m4 16 12-12q2-2 4 0t0 4L8 20l-5 1zM14 6l4 4"/>',
		'duplicate' => '<rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3"/>',
		'bolt' => '<path d="m14 2-11 13h8l-1 9 11-14h-8z"/>',
		'cart' => '<path d="M1 3h3l4 14h12l3-10H5"/><circle cx="9" cy="22" r="1"/><circle cx="19" cy="22" r="1"/>',
		'pause' => '<circle cx="12" cy="12" r="10"/><path d="M9 8v8m6-8v8"/>',
		'help' => '<circle cx="12" cy="12" r="10"/><path d="M9 8a3 3 0 0 1 6 0c0 2-3 2-3 5m0 3v1"/>',
		'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 5 10 8L22 5"/>',
		'arrow' => '<path d="M3 12h18m-6-6 6 6-6 6"/>',
		'chevron' => '<path d="m9 4 8 8-8 8"/>',
	);
	return '<svg class="bo-icon" viewBox="0 0 24 26" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ( isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['home'] ) . '</svg>';
}

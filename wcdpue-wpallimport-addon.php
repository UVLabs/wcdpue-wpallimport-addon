<?php
/*
Plugin Name: WCDPUE WP All Import Addon
Description: WCDPUE add-on for WP All Import!
Version: 1.0.0
Author: Uriahs Victor
*/
//
// include "rapid-addon.php";
//
// include_once(ABSPATH.'wp-admin/includes/plugin.php');
//
// $wcdpue_addon = new RapidAddon( 'WCDPUE Add-on', 'wcdpue_addon' );
//
// $wcdpue_addon2 = new RapidAddon( 'WCDPUE Add-on 2', 'wcdpue_addon2' );
//
// $wcdpue_addon->add_field('_wcdpue_product_id', 'WooCommerce Product ID', 'text');
//
// $wcdpue_addon->add_field( '_wcdpue_product_id2', 'WooCommerce Product ID2', 'text', null, 'Pick the main keyword or keyphrase that this post/page is about.' );

include "rapid-addon.php";

include_once( ABSPATH.'wp-admin/includes/plugin.php' );

// add_action( 'pmxi_saved_post', 'wcdpue_addon_primary_category', 10, 1 );

$wcdpue_addon = new RapidAddon( 'WCDPUE Add-on', 'wcdpue_addon' );

$wcdpue_addon->add_field( 'wcdpue_product_id', 'Product ID', 'text', null, 'Enter the product id of the product here.' );

$wcdpue_addon->add_text( 'This is text that will appear as a normal paragraph.' );

$wcdpue_addon->set_import_function( 'wcdpue_addon_import' );

if (function_exists('is_plugin_active')) {

	if ( !is_plugin_active( "wcdpue-pro/tld-wc-dl-product-update-emails.php" ) || !is_plugin_active( "wp-all-import/plugin.php" ) ) {

		//Change this to have plugin URL
		$wcdpue_addon->admin_notice(
			'The WCDPUE PRO Add-On requires WP All Import <a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=wcdpue_pro" target="_blank">Pro</a> or <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>, and the <a href="https://yoast.com/wordpress/plugins/seo/">WCDPUE PRO</a> plugin.',
			array(
				'plugins' => array('wcdpue-pro/tld-wc-dl-product-update-emails.php')
			)
		);
	}

	if ( is_plugin_active( "wcdpue-pro/tld-wc-dl-product-update-emails.php" ) ) {

		$wcdpue_addon->run(
			array(
				"post_types" => array( "post" )
			)
		);

	}
}

function wcdpue_addon_import( $post_id, $data, $import_options ){

	global $wpdb;
	unset( $tld_wcdpue_no_spam );
	$tld_wcdpue_the_schedule_table = TLD_WCDPUE_SCHEDULED_TABLE;
	$tld_wcdpue_dls_table = TLD_WCDPUE_DLS_TABLE;
	$tld_wcdpue_product_id = $data['wcdpue_product_id'];
	$tld_wcdpue_post_parent = wp_get_post_parent_id ( $tld_wcdpue_product_id );

	$tld_wcdpue_query_result = $wpdb->get_results(
		"SELECT DISTINCT product_id, order_id, order_key, user_email
		FROM $tld_wcdpue_dls_table
		WHERE ( product_id = $tld_wcdpue_product_id )
		AND ( access_expires > NOW() OR access_expires IS NULL )
		"	);

		foreach ( $tld_wcdpue_query_result as $tld_wcdpue_result ) {

			if( ! in_array( $tld_wcdpue_result->user_email, $tld_wcdpue_no_spam ) ){

				$wpdb->insert(
					$tld_wcdpue_the_schedule_table,
					array(

						'product_id' 			=> $tld_wcdpue_result->product_id,
						'product_parent'	=> $tld_wcdpue_post_parent,
						'order_id' 				=> $tld_wcdpue_result->order_id,
						'order_key'				=> $tld_wcdpue_result->order_key,
						'user_email' 			=> $tld_wcdpue_result->user_email,
						// 'is_variable' 		=> 1

					)
				);
				$wcdpue_addon->log( '- logging....' );//throws error

				// $wcdpue_addon->log( 'Scheduled email for product id: ' . $tld_wcdpue_product_id . 'to send to: ' . $tld_wcdpue_result->user_email );

			}
			$tld_wcdpue_no_spam[] = $tld_wcdpue_result->user_email;

		}




		// $wpdb->insert(
		//   $tbl,
		//   array(
		//
		//     'product_id' 			=> $data['wcdpue_product_id'],
		//     'product_parent'	=> $data['wcdpue_product_id'],
		//     'order_id' 				=> $post_id,
		//     'order_key'				=> 'order key',
		//     'user_email' 			=> 'user email',
		//     'is_variable' 		=> 1
		//
		//   )
		// );

	}
	//
	// function wcdpue_addon_import( $post_id, $data, $import_options, $article ) {
	//
	// 	global $wcdpue_addon;
	//
	//     // all fields except for slider and image fields
	//     $fields = array(
	//     	'_yoast_wpseo_focuskw',
	//     	'_yoast_wpseo_title',
	//     	'_yoast_wpseo_metadesc',
	//     	'_yoast_wpseo_meta-robots-noindex',
	//     	'_yoast_wpseo_meta-robots-nofollow',
	//     	'_yoast_wpseo_meta-robots-adv',
	//     	'_yoast_wpseo_sitemap-include',
	//     	'_yoast_wpseo_sitemap-prio',
	//     	'_yoast_wpseo_canonical',
	//     	'_yoast_wpseo_redirect',
	//     	'_yoast_wpseo_opengraph-title',
	//     	'_yoast_wpseo_opengraph-description',
	//     	'_yoast_wpseo_twitter-title',
	//     	'_yoast_wpseo_twitter-description',
	//     	'_yoast_wpseo_primary_category_addon'
	//     );
	//
	//     // image fields
	//     $image_fields = array(
	//  		'_yoast_wpseo_opengraph-image',
	//  		'_yoast_wpseo_twitter-image'
	//     );
	//
	//     $fields = array_merge( $fields, $image_fields );
	//
	//     // update everything in fields arrays
	//     foreach ( $fields as $field ) {
	//     	if ( $field == '_yoast_wpseo_primary_category_addon' ) {
	//
	//            			$title = $data[$field];
	//
	//            			$cat_slug = sanitize_title( $title ); // Get the slug for the Primary Category so we can match it later
	//
	//            			update_post_meta( $post_id, '_yoast_wpseo_addon_category_slug', $cat_slug );
	//
	//            			// Set post metas for regular categories and product categories so we know if we can update them after pmxi_saved_post hook fires.
	//
	//            			if ( empty( $article['ID'] ) or $wcdpue_addon->can_update_meta( '_yoast_wpseo_primary_category', $import_options ) ) {
	//
	//            				update_post_meta( $post_id, '_yoast_wpseo_primary_category_can_update', 1 );
	//
	//            			} else {
	//
	//            				update_post_meta( $post_id, '_yoast_wpseo_primary_category_can_update', 0 );
	//
	//            			}
	//
	//            			if ( empty( $article['ID'] ) or $wcdpue_addon->can_update_meta( '_yoast_wpseo_primary_product_cat', $import_options ) ) {
	//
	//            				update_post_meta( $post_id, '_yoast_wpseo_primary_product_cat_can_update', 1 );
	//
	//            			} else {
	//
	//            				update_post_meta( $post_id, '_yoast_wpseo_primary_product_cat_can_update', 0 );
	//
	//            			}
	//
	//         } else {
	//
	//         	if ( empty($article['ID']) or $wcdpue_addon->can_update_meta( $field, $import_options ) ) {
	//
	//            		if ( in_array( $field, $image_fields ) ) {
	//
	//                		if ( $wcdpue_addon->can_update_image( $import_options ) ) {
	//
	//                    		$id = $data[$field]['attachment_id'];
	//
	//                    		$url = wp_get_attachment_url( $id );
	//
	//                    		update_post_meta( $post_id, $field, $url );
	//
	//                    	}
	//
	//                 } else {
	//
	// 	    	       	if ( $field == '_yoast_wpseo_focuskw' ) {
	//
	//     		       		update_post_meta( $post_id, $field, $data[$field] );
	// 	            		update_post_meta( $post_id, '_yoast_wpseo_focuskw_text_input', $data[$field] );
	//
	//             		} else {
	//
	// 	               		update_post_meta( $post_id, $field, $data[$field] );
	//
	//                 	}
	//             	}
	//         	}
	//     	}
	//     }
	//
	//     		// calculate _yoast_wpseo_linkdex
	//     if ( class_exists( 'WPSEO_Metabox' ) ) {
	//
	// 			wpseo_admin_init();
	//
	// 			$seo = new WPSEO_Metabox();
	//
	// 			$seo->calculate_results( get_post($post_id) );
	//     }
	// }
	//
	// function wcdpue_addon_primary_category( $post_id ) {
	//
	// 	$product_update = get_post_meta( $post_id, '_yoast_wpseo_primary_product_cat_can_update', true ); // Can we update product primary categories?
	//
	// 	$post_update = get_post_meta( $post_id, '_yoast_wpseo_primary_category_can_update', true ); // Can we update post primary categories?
	//
	// 	// Only proceed if we have permission to update one of them.
	//
	// 	if ( $post_update == 1 or $product_update == 1 ) {
	//
	// 		$cat_slug = get_post_meta( $post_id, '_yoast_wpseo_addon_category_slug', true );
	//
	// 		if ( !empty( $cat_slug ) ) {
	//
	// 			$post_type = get_post_type( $post_id );
	//
	// 			if ( !empty( $cat_slug ) and !empty( $post_type ) ) {
	//
	// 				if ( $post_type == 'product' and $product_update == 1 ) { // Products use 'product_cat' instead of 'categories'.
	//
	// 		    		$cat = get_term_by( 'slug', $cat_slug, 'product_cat' );
	//
	// 		  			$cat_id = $cat->term_id;
	//
	// 		  			if ( !empty( $cat_id ) ) {
	//
	// 		  				update_post_meta( $post_id, '_yoast_wpseo_primary_product_cat', $cat_id );
	//
	//
	// 	  				}
	//
	// 				} else {
	//
	// 					if ( $post_update == 1 ) {
	//
	// 						$cat = get_term_by( 'slug', $cat_slug, 'category' );
	//
	// 						$cat_id = $cat->term_id;
	//
	// 						if ( !empty( $cat_id ) ) {
	//
	// 							update_post_meta( $post_id, '_yoast_wpseo_primary_category', $cat_id );
	//
	// 						}
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}
	// 	delete_post_meta( $post_id, '_yoast_wpseo_primary_category_can_update' );
	// 	delete_post_meta( $post_id, '_yoast_wpseo_primary_product_cat_can_update' );
	// 	delete_post_meta( $post_id, '_yoast_wpseo_addon_category_slug' );
	// }
	//
	// add_filter( 'rapid_is_active_add_on', 'wcdpue_addon_is_active_add_on', 10, 3 );
	// function wcdpue_addon_is_active_add_on( $is_active, $post_type, $called_by ){
	//     if ( $called_by == 'wcdpue_addon' && $post_type == 'taxonomies' ) $is_active = false;
	//     return $is_active;
	// }

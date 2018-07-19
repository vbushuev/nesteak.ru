<?php
/**
 * Vffront hooks
 *
 * @package vffront
 */

/**
 * General
 *
 * @see  vffront_header_widget_region()
 * @see  vffront_get_sidebar()
 */
add_action( 'vffront_before_content', 'vffront_header_widget_region', 10 );
add_action( 'vffront_sidebar',        'vffront_get_sidebar',          10 );

/**
 * Header
 *
 * @see  vffront_skip_links()
 * @see  vffront_secondary_navigation()
 * @see  vffront_site_branding()
 * @see  vffront_primary_navigation()
 */
add_action( 'vffront_header', 'vffront_header_container',                 0 );
add_action( 'vffront_header', 'vffront_skip_links',                       5 );
add_action( 'vffront_header', 'vffront_site_branding',                    20 );
add_action( 'vffront_header', 'vffront_secondary_navigation',             30 );
add_action( 'vffront_header', 'vffront_header_container_close',           41 );
add_action( 'vffront_header', 'vffront_primary_navigation_wrapper',       42 );
add_action( 'vffront_header', 'vffront_primary_navigation',               50 );
add_action( 'vffront_header', 'vffront_primary_navigation_wrapper_close', 68 );

/**
 * Footer
 *
 * @see  vffront_footer_widgets()
 * @see  vffront_credit()
 */
add_action( 'vffront_footer', 'vffront_footer_widgets', 10 );
add_action( 'vffront_footer', 'vffront_credit',         20 );

/**
 * Homepage
 *
 * @see  vffront_homepage_content()
 * @see  vffront_product_categories()
 * @see  vffront_recent_products()
 * @see  vffront_featured_products()
 * @see  vffront_popular_products()
 * @see  vffront_on_sale_products()
 * @see  vffront_best_selling_products()
 */
add_action( 'homepage', 'vffront_homepage_content',      10 );
add_action( 'homepage', 'vffront_product_categories',    20 );
add_action( 'homepage', 'vffront_recent_products',       30 );
add_action( 'homepage', 'vffront_featured_products',     40 );
add_action( 'homepage', 'vffront_popular_products',      50 );
add_action( 'homepage', 'vffront_on_sale_products',      60 );
add_action( 'homepage', 'vffront_best_selling_products', 70 );

/**
 * Posts
 *
 * @see  vffront_post_header()
 * @see  vffront_post_meta()
 * @see  vffront_post_content()
 * @see  vffront_paging_nav()
 * @see  vffront_single_post_header()
 * @see  vffront_post_nav()
 * @see  vffront_display_comments()
 */
add_action( 'vffront_loop_post',           'vffront_post_header',          10 );
add_action( 'vffront_loop_post',           'vffront_post_meta',            20 );
add_action( 'vffront_loop_post',           'vffront_post_content',         30 );
add_action( 'vffront_loop_after',          'vffront_paging_nav',           10 );
add_action( 'vffront_single_post',         'vffront_post_header',          10 );
add_action( 'vffront_single_post',         'vffront_post_meta',            20 );
add_action( 'vffront_single_post',         'vffront_post_content',         30 );
add_action( 'vffront_single_post_bottom',  'vffront_post_nav',             10 );
add_action( 'vffront_single_post_bottom',  'vffront_display_comments',     20 );
add_action( 'vffront_post_content_before', 'vffront_post_thumbnail',       10 );

/**
 * Pages
 *
 * @see  vffront_page_header()
 * @see  vffront_page_content()
 * @see  vffront_display_comments()
 */
add_action( 'vffront_page',       'vffront_page_header',          10 );
add_action( 'vffront_page',       'vffront_page_content',         20 );
add_action( 'vffront_page_after', 'vffront_display_comments',     10 );

add_action( 'vffront_homepage',       'vffront_homepage_header',      10 );
add_action( 'vffront_homepage',       'vffront_page_content',         20 );

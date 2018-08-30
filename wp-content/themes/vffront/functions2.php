<?php
add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );
function custom_woocommerce_get_catalog_ordering_args( $args ) {
  $orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
    if ( 'list' == $orderby_value ) {
        // $args['orderby'] = 'parent';
        $args['orderby'] = [
            'term_group'=>'ASC',
            'title'=>'ASC'
        ];
        // $args['order'] = 'ASC';
        $args['meta_key'] = '';
    }
    return $args;
}
add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );
function custom_woocommerce_catalog_orderby( $sortby ) {
    $sortby['list'] = 'Сортировка по категории';
    return $sortby;
}
add_action('wp_ajax_oneclick', 'oneclick');
add_action('wp_ajax_nopriv_oneclick', 'oneclick');
function oneclick() {
    global $woocommerce;
    $product_id = $_POST['product_id'];
    $variation_id = isset($_POST['variation_id'])?$_POST['variation_id']:false;
    $quantity = $_POST['quantity'];

    // echo json_encode($_POST);

    if ($variation_id) {
        WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );
    }
    else {
        WC()->cart->add_to_cart( $product_id, $quantity);
    }
    $items = WC()->cart->get_cart();
    $res = [
        'item_count'=>$woocommerce->cart->cart_contents_count
    ];

    // $item_count = ;
    echo json_encode(array_merge($res,WC()->cart->get_totals()));
    wp_die();
}

function storefront_post_meta() {
    return '';
}
/**
 * Display navigation to next/previous post when applicable.
 */
function storefront_post_nav() {
    $args = array(
        'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next post:', 'storefront' ) . ' </span>%title',
        'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'storefront' ) . ' </span>%title',
        );
    // the_post_navigation( $args );
}

?>

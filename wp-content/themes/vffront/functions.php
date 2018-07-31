<?php

function remove_my_action() {
    remove_action( 'homepage', 'storefront_homepage_content');
    // remove_action( 'homepage', 'storefront_product_categories',    20 );
    remove_action( 'homepage', 'storefront_recent_products',       30 );
    remove_action( 'homepage', 'storefront_featured_products',     40 );
    remove_action( 'homepage', 'storefront_popular_products',      50 );
    remove_action( 'homepage', 'storefront_on_sale_products',      60 );
    remove_action( 'homepage', 'storefront_best_selling_products', 70 );

}
function overwrite_shortcode() {
    function vf_product_categories( $atts,$content,$tag ) {

        $atts = shortcode_atts( array(
			'limit'      => '3',
			'orderby'    => 'name',
			'order'      => 'ASC',
			'columns'    => '3',
			'hide_empty' => 1,
			'parent'     => '',
			'ids'        => '',
		), $atts, 'product_categories' );
        $ids        = array_filter( array_map( 'trim', explode( ',', $atts['ids'] ) ) );
        $hide_empty = ( true === $atts['hide_empty'] || 'true' === $atts['hide_empty'] || 1 === $atts['hide_empty'] || '1' === $atts['hide_empty'] ) ? 1 : 0;

        // // Get terms and workaround WP bug with parents/pad counts.
		$args = array(
			'orderby'    => $atts['orderby'],
			'order'      => $atts['order'],
			'hide_empty' => $hide_empty,
			'include'    => $ids,
			'pad_counts' => true,
			'child_of'   => $atts['parent'],
		);

		$product_categories = get_terms( 'product_cat', $args );
        // echo json_encode($product_categories,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        // print_r($product_categories);
        $ret = '';
        $limit = 3;
        foreach($product_categories as $cat){
            if($cat->parent!="0") continue;
            if($limit-- == 0)break;
            $ret.='<li>
                <h3>'.$cat->name.'</h3>
                <img src="https://nesteak.ru/wp-content/themes/vffront/assets/images/cat_child.png" alt="">
                <div>
                    <p>Краткая информация о продукции в несколько строчек и кнопка подробнее для просмотра остальной информации.</p>
                    <a href="/product-category/'.$cat->slug.'" class="info">Подробнее</a>
                </div>
            </li>';
        }
        // print_r($content);
        // print_r($tag);
        // extract( shortcode_atts( array( 'limit' => 5, "widget_title" => __('What Are People Saying', 'jo'), 'text_color' => "#000" ), $atts ) );
        // $content = "";
        // $loopArgs = array( "post_type" => "customers", "posts_per_page" => $limit, 'ignore_sticky_posts' => 1 );
        //
        // $postsLoop = new WP_Query( $loopArgs );
        // $content = "";
        //
        // $content .= '...';
        // $content .= get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
        // $content .= '...';
        // $content .= '...';
        //
        // wp_reset_query();
        // echo $ret;
        return $ret;
    }
    remove_shortcode('product_categories');
    add_shortcode( 'product_categories', 'vf_product_categories' );
 }
add_action( 'wp_loaded', 'overwrite_shortcode' );
add_action( 'init', 'remove_my_action');


function content($limit) {
  $content = explode(' ', get_the_content(), $limit);
  if (count($content)>=$limit) {
    array_pop($content);
    $content = implode(" ",$content).'...';
  } else {
    $content = implode(" ",$content);
  }
  $content = preg_replace('/\[.+\]/','', $content);
  $content = apply_filters('the_content', $content);
  $content = str_replace(']]>', ']]&gt;', $content);
  return $content;
}
function storefront_product_categories( $args ) {
    if ( storefront_is_woocommerce_activated() ) {

        $args = apply_filters( 'storefront_product_categories_args', array(
            'limit' 			=> -1,
            'columns' 			=> 3,
            'child_categories' 	=> 0,
            'orderby' 			=> 'name',
            'title'				=> __( 'Shop by Category', 'storefront' ),
        ) );

        $shortcode_content = storefront_do_shortcode( 'product_categories', apply_filters( 'storefront_product_categories_shortcode_args', array(
            'number'  => intval( $args['limit'] ),
            'columns' => intval( $args['columns'] ),
            'orderby' => esc_attr( $args['orderby'] ),
            'parent'  => esc_attr( $args['child_categories'] ),
        ) ) );


        // print_r($args);
        /**
         * Only display the section if the shortcode returns product categories
         */

        // if ( false !== strpos( $shortcode_content, 'product-category' ) ) {
            // echo '<section class="item"><div class="shell"><div class="cat"><h2>'. wp_kses_post( $args['title'] ) .'</h2>';
            echo '<section class="item"><div class="shell"><div class="cat"><h2>ВЫБЕРИТЕ ПРОДУКЦИЮ</h2>';
            echo '<ul class="flex jcsb v_center">';
            echo $shortcode_content;
            echo '</ul>';
            echo '</div></div></section>';

        // }

    }
}
// function woocommerce_output_content_wrapper() {
//     return;
// }
// function woocommerce_output_content_wrapper_end() {
//     return;
// }

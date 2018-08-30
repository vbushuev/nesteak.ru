<?php
include('functions2.php');
function remove_my_action() {
    // remove_action( 'homepage', 'storefront_homepage_content');
    // remove_action( 'homepage', 'storefront_product_categories',    20 );
    remove_action( 'homepage', 'storefront_recent_products',       30 );
    remove_action( 'homepage', 'storefront_featured_products',     40 );
    remove_action( 'homepage', 'storefront_popular_products',      50 );
    remove_action( 'homepage', 'storefront_on_sale_products',      60 );
    remove_action( 'homepage', 'storefront_best_selling_products', 70 );
    // remove_action( 'woocommerce_before_single_product' );
    // remove_action( 'woocommerce_after_single_product' );


    /**
     * Hook: woocommerce_single_product_summary.
     *
     * @hooked woocommerce_template_single_title - 5
     * @hooked woocommerce_template_single_rating - 10
     * @hooked woocommerce_template_single_price - 10
     * @hooked woocommerce_template_single_excerpt - 20
     * @hooked woocommerce_template_single_add_to_cart - 30
     * @hooked woocommerce_template_single_meta - 40
     * @hooked woocommerce_template_single_sharing - 50
     * @hooked WC_Structured_Data::generate_product_data() - 60
     */
    remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_title',    5 );
    remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_rating',   10 );
    remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_price',   10 );
    remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_excerpt',   20 );
    remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_add_to_cart',   30 );
    remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_meta',   40 );
    remove_action( 'woocommerce_single_product_summary','woocommerce_template_single_sharing',   50 );

    add_action( 'woocommerce_single_product_summary','woocommerce_template_single_title');
    add_action( 'woocommerce_single_product_summary','woocommerce_template_single_price');
    add_action( 'woocommerce_single_product_summary','woocommerce_template_single_add_to_cart');
    add_action( 'woocommerce_single_product_summary','woocommerce_template_single_rating');
    add_action( 'woocommerce_single_product_summary','woocommerce_template_single_meta');
    add_action( 'woocommerce_single_product_summary','woocommerce_template_single_excerpt');
    add_action( 'woocommerce_single_product_summary','woocommerce_template_single_sharing');
}
function overwrite_shortcode() {
    function vf_wcj_product_wholesale_price_table( $atts ) {

		$product_id = wcj_get_product_id_or_variation_parent_id( $this->the_product );

		if ( ! wcj_is_product_wholesale_enabled( $product_id ) ) {
			return '';
		}

		// Check for user role options
		$role_option_name_addon = '';
		$user_roles = get_option( 'wcj_wholesale_price_by_user_role_roles', '' );
		if ( ! empty( $user_roles ) ) {
			$current_user_role = wcj_get_current_user_first_role();
			foreach ( $user_roles as $user_role_key ) {
				if ( $current_user_role === $user_role_key ) {
					$role_option_name_addon = '_' . $user_role_key;
					break;
				}
			}
		}

		$wholesale_price_levels = array();
		if ( wcj_is_product_wholesale_enabled_per_product( $product_id ) ) {
			for ( $i = 1; $i <= apply_filters( 'booster_option', 1, get_post_meta( $product_id, '_' . 'wcj_wholesale_price_levels_number' . $role_option_name_addon, true ) ); $i++ ) {
				$level_qty                = get_post_meta( $product_id, '_' . 'wcj_wholesale_price_level_min_qty' . $role_option_name_addon . '_' . $i, true );
				$discount                 = get_post_meta( $product_id, '_' . 'wcj_wholesale_price_level_discount' . $role_option_name_addon . '_' . $i, true );
				$wholesale_price_levels[] = array( 'quantity' => $level_qty, 'discount' => $discount, );
			}
		} else {
			for ( $i = 1; $i <= apply_filters( 'booster_option', 1, get_option( 'wcj_wholesale_price_levels_number' . $role_option_name_addon, 1 ) ); $i++ ) {
				$level_qty                = get_option( 'wcj_wholesale_price_level_min_qty' . $role_option_name_addon . '_' . $i, PHP_INT_MAX );
				$discount                 = get_option( 'wcj_wholesale_price_level_discount_percent' . $role_option_name_addon . '_' . $i, 0 );
				$wholesale_price_levels[] = array( 'quantity' => $level_qty, 'discount' => $discount, );
			}
		}

		$discount_type = ( wcj_is_product_wholesale_enabled_per_product( $product_id ) ) ?
			get_post_meta( $product_id, '_' . 'wcj_wholesale_price_discount_type', true ) :
			get_option( 'wcj_wholesale_price_discount_type', 'percent' );

		$data_qty        = array();
		$data_price      = array();
		$data_discount   = array();
		$columns_styles  = array();
		$i = -1;
		foreach ( $wholesale_price_levels as $wholesale_price_level ) {
			$i++;
			if ( 0 == $wholesale_price_level['quantity'] && 'yes' === $atts['hide_if_zero_quantity'] ) {
				continue;
			}

			$the_price = '';

			if ( $this->the_product->is_type( 'variable' ) ) {
				// Variable
				$prices = $this->the_product->get_variation_prices( false );
				$min_key = key( $prices['price'] );
				end( $prices['price'] );
				$max_key = key( $prices['price'] );
				$min_product = wc_get_product( $min_key );
				$max_product = wc_get_product( $max_key );
				$min = wcj_get_product_display_price( $min_product );
				$max = wcj_get_product_display_price( $max_product );
				$min_original = $min;
				$max_original = $max;
				if ( 'fixed' === $discount_type ) {
					$min = $min - $wholesale_price_level['discount'];
					$max = $max - $wholesale_price_level['discount'];
				} else {
					$coefficient = 1.0 - ( $wholesale_price_level['discount'] / 100.0 );
					$min = $min * $coefficient;
					$max = $max * $coefficient;
				}
				if ( 'yes' !== $atts['hide_currency'] ) {
					$min = wc_price( $min );
					$max = wc_price( $max );
					$min_original = wc_price( $min_original );
					$max_original = wc_price( $max_original );
				}
				$the_price = ( $min != $max ) ? sprintf( '%s-%s', $min, $max ) : $min;
				$the_price_original = ( $min_original != $max_original ) ? sprintf( '%s-%s', $min_original, $max_original ) : $min_original;
			} else {
				// Simple etc.
				$the_price = wcj_get_product_display_price( $this->the_product );
				$the_price = apply_filters( 'wcj_product_wholesale_price_table_price_before', $the_price, $this->the_product );
				$the_price_original = $the_price;
				if ( 'price_directly' === $discount_type ) {
					$the_price = $wholesale_price_level['discount'];
				} elseif ( 'fixed' === $discount_type ) {
					$the_price = $the_price - $wholesale_price_level['discount'];
				} else { // 'percent'
					$coefficient = 1.0 - ( $wholesale_price_level['discount'] / 100.0 );
					$the_price = ( float ) $the_price * $coefficient;
				}
				$the_price_original = apply_filters( 'wcj_product_wholesale_price_table_price_after', $the_price_original, $this->the_product );
				$the_price          = apply_filters( 'wcj_product_wholesale_price_table_price_after', $the_price,          $this->the_product );
				if ( 'yes' !== $atts['hide_currency'] ) {
					$the_price = wc_price( $the_price );
					$the_price_original = wc_price( $the_price_original );
				}
			}

			$level_max_qty = ( isset( $wholesale_price_levels[ $i + 1 ]['quantity'] ) ) ?
				$atts['before_level_max_qty'] . ( $wholesale_price_levels[ $i + 1 ]['quantity'] - 1 ) : $atts['last_level_max_qty'];
			$data_qty[] = str_replace(
				array( '%level_qty%', '%level_min_qty%', '%level_max_qty%' ), // %level_qty% is deprecated
				array( $wholesale_price_level['quantity'], $wholesale_price_level['quantity'], $level_max_qty ),
				$atts['heading_format']
			);
			if ( 'yes' === $atts['add_price_row'] ) {
				$data_price[] = str_replace( array( '%old_price%', '%price%' ), array( $the_price_original, $the_price ), $atts['price_row_format'] );
			}
			if ( 'yes' === $atts['add_percent_row'] ) {
				if ( 'percent' === $discount_type ) {
					$data_discount[] = '-' . $wholesale_price_level['discount'] . '%';
				}
			}
			if ( 'yes' === $atts['add_discount_row'] ) {
				if ( 'fixed' === $discount_type ) {
					$data_discount[] = '-' . wc_price( $wholesale_price_level['discount'] );
				}
			}

			$columns_styles[] = $atts['columns_style'];
		}

		$table_rows = array( $data_qty, );
		if ( 'yes' === $atts['add_price_row'] ) {
			$table_rows[] = $data_price;
		}
		if ( 'yes' === $atts['add_percent_row'] ) {
			$table_rows[] = $data_discount;
		}

		if ( 'vertical' === $atts['table_format'] ) {
			$table_rows_modified = array();
			foreach ( $table_rows as $row_number => $table_row ) {
				foreach ( $table_row as $column_number => $cell ) {
					$table_rows_modified[ $column_number ][ $row_number ] = $cell;
				}
			}
			$table_rows = $table_rows_modified;
		}
        // return json_encode($columns_styles);
		return wcj_get_table_html( $table_rows,
			array( 'table_class' => 'wcj_product_wholesale_price_table', 'columns_styles' => $columns_styles, 'table_heading_type' => $atts['table_format'] ) );
	}
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
    // remove_shortcode('wcj_product_wholesale_price_table');
    // add_shortcode( 'wcj_product_wholesale_price_table', 'vf_wcj_product_wholesale_price_table' );
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
function woocommerce_output_content_wrapper() {
    return;
}
function woocommerce_output_content_wrapper_end() {
    return;
}

// Кастомные поля Админка

function slider() {
 register_post_type('slider', array(
  'public' => true,
  'supports' => array('title', 'thumbnail'),
  'labels' => array(
   'name' => 'Слайдер',
   'all_items' => 'Все слайды',
   'add_new' => 'Добавить слайд',
   'add_new_item' => 'Добавление слайда'
  ),
  'rewrite' => array( 'slug' => 'slider', 'with_front' => false ),
  'taxonomies' => array( 'slider' ),
  'publicly_queryable' => true,
  'has_archive' => true
 ));
 flush_rewrite_rules( false );
}

add_action('init', 'slider');

function popular_goods() {
 register_post_type('popular_goods', array(
  'public' => true,
  'supports' => array('title', 'thumbnail'),
  'labels' => array(
   'name' => 'Популярные товары',
   'all_items' => 'Все товары',
   'add_new' => 'Добавить товар',
   'add_new_item' => 'Добавление товара'
  ),
  'rewrite' => array( 'slug' => 'popular_goods', 'with_front' => false ),
  'taxonomies' => array( 'popular_goods' ),
  'publicly_queryable' => true,
  'has_archive' => true
 ));
 flush_rewrite_rules( false );
}

add_action('init', 'popular_goods');

function storefront_sorting_wrapper() {
    echo '<div class="storefront-sorting vf-sorting">';
    // dynamic_sidebar('header-1');

    echo do_shortcode('[pwb-all-brands per_page="10" image_size="thumbnail" hide_empty="false" order_by="name" order="ASC" title_position="none"]');
}

/**
 * Возможность загружать изображения для элементов указанных таксономий: категории, метки.
 *
 * Пример получения ID и URL картинки термина:
 * $image_id = get_term_meta( $term_id, '_thumbnail_id', 1 );
 * $image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
 *
 * @author: Kama (http://wp-kama.ru)
 *
 * @ver: 2.8
 */
if( is_admin() && ! class_exists('Term_Meta_Image') ){
	// init
	//add_action('current_screen', 'Term_Meta_Image_init');
	add_action('admin_init', 'Term_Meta_Image_init');
	function Term_Meta_Image_init(){
		$GLOBALS['Term_Meta_Image'] = new Term_Meta_Image();
	}

	class Term_Meta_Image {

		// для каких таксономий включить код. По умолчанию для всех публичных
		static $taxes = array(); // пример: array('category', 'post_tag');

		// название мета ключа
		static $meta_key = '_thumbnail_id';

		// URL пустой картинки
		static $add_img_url = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkAQMAAABKLAcXAAAABlBMVEUAAAC7u7s37rVJAAAAAXRSTlMAQObYZgAAACJJREFUOMtjGAV0BvL/G0YMr/4/CDwY0rzBFJ704o0CWgMAvyaRh+c6m54AAAAASUVORK5CYII=';

		public function __construct(){
			if( isset($GLOBALS['Term_Meta_Image']) ) return $GLOBALS['Term_Meta_Image']; // once

			$taxes = self::$taxes ? self::$taxes : get_taxonomies( array( 'public'=>true ), 'names' );

			foreach( $taxes as $taxname ){
				add_action("{$taxname}_add_form_fields",   array( & $this, 'add_term_image' ),     10, 2 );
				add_action("{$taxname}_edit_form_fields",  array( & $this, 'update_term_image' ),  10, 2 );
				add_action("created_{$taxname}",           array( & $this, 'save_term_image' ),    10, 2 );
				add_action("edited_{$taxname}",            array( & $this, 'updated_term_image' ), 10, 2 );

				add_filter("manage_edit-{$taxname}_columns",  array( & $this, 'add_image_column' ) );
				add_filter("manage_{$taxname}_custom_column", array( & $this, 'fill_image_column' ), 10, 3 );
			}
		}

		## поля при создании термина
		public function add_term_image( $taxonomy ){
			wp_enqueue_media(); // подключим стили медиа, если их нет

			add_action('admin_print_footer_scripts', array( & $this, 'add_script' ), 99 );
			$this->css();
			?>
			<div class="form-field term-group">
				<label><?php _e('Image', 'default'); ?></label>
				<div class="term__image__wrapper">
					<a class="termeta_img_button" href="#">
						<img src="<?php echo self::$add_img_url ?>" alt="">
					</a>
					<input type="button" class="button button-secondary termeta_img_remove" value="<?php _e( 'Remove', 'default' ); ?>" />
				</div>

				<input type="hidden" id="term_imgid" name="term_imgid" value="">
			</div>
			<?php
		}

		## поля при редактировании термина
		public function update_term_image( $term, $taxonomy ){
			wp_enqueue_media(); // подключим стили медиа, если их нет

			add_action('admin_print_footer_scripts', array( & $this, 'add_script' ), 99 );

			$image_id = get_term_meta( $term->term_id, self::$meta_key, true );
			$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : self::$add_img_url;
			$this->css();
			?>
			<tr class="form-field term-group-wrap">
				<th scope="row"><?php _e( 'Image', 'default' ); ?></th>
				<td>
					<div class="term__image__wrapper">
						<a class="termeta_img_button" href="#">
							<?php echo '<img src="'. $image_url .'" alt="">'; ?>
						</a>
						<input type="button" class="button button-secondary termeta_img_remove" value="<?php _e( 'Remove', 'default' ); ?>" />
					</div>

					<input type="hidden" id="term_imgid" name="term_imgid" value="<?php echo $image_id; ?>">
				</td>
			</tr>
			<?php
		}

		public function css(){
			?>
			<style>
				.termeta_img_button{ display:inline-block; margin-right:1em; }
				.termeta_img_button img{ display:block; float:left; margin:0; padding:0; min-width:100px; max-width:150px; height:auto; background:rgba(0,0,0,.07); }
				.termeta_img_button:hover img{ opacity:.8; }
				.termeta_img_button:after{ content:''; display:table; clear:both; }
			</style>
			<?php
		}

		## Add script
		public function add_script(){
			// выходим если не на нужной странице таксономии
			//$cs = get_current_screen();
			//if( ! in_array($cs->base, array('edit-tags','term')) || ! in_array($cs->taxonomy, (array) $this->for_taxes) )
			//  return;

			$title = __('Featured Image', 'default');
			$button_txt = __('Set featured image', 'default');
			?>
			<script>
			jQuery(document).ready(function($){
				var frame,
					$imgwrap = $('.term__image__wrapper'),
					$imgid   = $('#term_imgid');

				// добавление
				$('.termeta_img_button').click( function(ev){
					ev.preventDefault();

					if( frame ){ frame.open(); return; }

					// задаем media frame
					frame = wp.media.frames.questImgAdd = wp.media({
						states: [
							new wp.media.controller.Library({
								title:    '<?php echo $title ?>',
								library:   wp.media.query({ type: 'image' }),
								multiple: false,
								//date:   false
							})
						],
						button: {
							text: '<?php echo $button_txt ?>', // Set the text of the button.
						}
					});

					// выбор
					frame.on('select', function(){
						var selected = frame.state().get('selection').first().toJSON();
						if( selected ){
							$imgid.val( selected.id );
							$imgwrap.find('img').attr('src', selected.sizes.thumbnail.url );
						}
					} );

					// открываем
					frame.on('open', function(){
						if( $imgid.val() ) frame.state().get('selection').add( wp.media.attachment( $imgid.val() ) );
					});

					frame.open();
				});

				// удаление
				$('.termeta_img_remove').click(function(){
					$imgid.val('');
					$imgwrap.find('img').attr('src','<?php echo self::$add_img_url ?>');
				});
			});
			</script>

			<?php
		}

		## Добавляет колонку картинки в таблицу терминов
		public function add_image_column( $columns ){
			// подправим ширину колонки через css
			add_action('admin_notices', function(){
				echo '<style>.column-image{ width:50px; text-align:center; }</style>';
			});

			$num = 1; // после какой по счету колонки вставлять

			$new_columns = array( 'image'=>'' ); // колонка без названия...

			return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
		}

		public function fill_image_column( $string, $column_name, $term_id ){
			// если есть картинка
			if( $image_id = get_term_meta( $term_id, self::$meta_key, 1 ) )
				$string = '<img src="'. wp_get_attachment_image_url( $image_id, 'thumbnail' ) .'" width="50" height="50" alt="" style="border-radius:4px;" />';

			return $string;
		}

		## Save the form field
		public function save_term_image( $term_id, $tt_id ){
			if( isset($_POST['term_imgid']) && $image = (int) $_POST['term_imgid'] )
				add_term_meta( $term_id, self::$meta_key, $image, true );
		}

		## Update the form field value
		public function updated_term_image( $term_id, $tt_id ){
			if( ! isset($_POST['term_imgid']) ) return;

			if( $image = (int) $_POST['term_imgid'] )
				update_term_meta( $term_id, self::$meta_key, $image );
			else
				delete_term_meta( $term_id, self::$meta_key );
		}

	}

}
/**
 * 2.8 - исправил ошибку удаления картинки.
 */

<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `homepage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin.
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: Homepage
 *
 * @package storefront
 */

get_header(); ?>
<main class="main">
	<?php
	/**
	 * Functions hooked in to homepage action
	 *
	 * @hooked storefront_homepage_content      - 10
	 * @hooked storefront_product_categories    - 20
	 * @hooked storefront_recent_products       - 30
	 * @hooked storefront_featured_products     - 40
	 * @hooked storefront_popular_products      - 50
	 * @hooked storefront_on_sale_products      - 60
	 * @hooked storefront_best_selling_products - 70
	 */
	//do_action( 'homepage' ); ?>

	<section class="item" style="background: url('<?php the_field('line_section') ?>') no-repeat top center/contain">
        <div class="shell">
        	<!-- Перечень категорий -->
        	<div class="cat">
        		<h2>Выберите продукцию</h2>

    			<?php
	                $args = array(
	                    'taxonomy' => 'product_cat',
	                    'orderby'    => 'count',
	                    'order'      => 'DESC',
	                    'category__in' => '55,60,61',
	                    'hide_empty' => false
	                );

	                $product_categories = get_terms( $args );
	                $product_category = wp_get_post_terms($post->ID,'product_cat', $args);


	                $count = count($product_categories);
	                if ( $count > 0 ){
	                    echo '<ul class="flex jcsb v_center">';
	                    foreach ( $product_categories as $product_category ) {
							// $categ = $_product->get_categories();
						    // $term = get_term_by ( 'name' , strip_tags($categ), 'product_cat' );

							if(!in_array($product_category->term_id,[55,60,61]))continue;
							// echo json_encode($product_category);
							$category_thumbnail = get_woocommerce_term_meta($product_category->term_id, 'thumbnail_id', true);
						    $image = wp_get_attachment_url($category_thumbnail);
	                    	echo '<li  class="catalogue-menu-item"><h3>' . $product_category->name . '</h3>' . '<img alt="" src="'.$image.'" />' . '<div>
            					<p>' . $product_category->description . '</p>' . '<a class="info" href="' . get_term_link( $product_category ) . '">Подробнее</a></li>';

	                    }
	                    echo "</ul>";
	                }
                ?>


        	</div>
        </div>
    </section>

	<section class="item" style="background: url('<?php the_field('line_section') ?>') no-repeat top center/contain">
		<div class="shell">
			<!-- Перечень брендов -->
			<div class="brand width">
				<h2>Бренды</h2>
	                <?php echo do_shortcode('[pwb-all-brands per_page="10" image_size="thumbnail" hide_empty="false" order_by="name" order="ASC" title_position="none"]'); ?>
			</div>
		</div>
	</section>

	<section class="item" style="background: url('<?php the_field('line_section') ?>') no-repeat top center/contain">
		<div class="shell">
			<!-- Перечень товаров -->
			<div class="popular">
				<h2>Популярные товары</h2>

				<div class="list_product">
					<!-- Товар -->
					<?php
						$list_product = new WP_Query('post_type=popular_goods&order=ASC&posts_per_page=-1');
						if ( $list_product->have_posts() ) : ?>
						<!-- the loop -->
		              	<?php while ( $list_product->have_posts() ) : $list_product->the_post(); ?>

							<a href="<?php the_field('link_list_product'); ?>">
								<div class="product">
			                        <div class="thumbnail">
			                        	<?php the_post_thumbnail(); ?>
			                            <!-- <img src="images/tov.png" alt=""> -->
			                        </div>
			                        <div class="title">
			                        	<p><?php echo get_the_title(); ?></p>
			                            <!-- <p>Говядина + сердце (СБ) 100 г</p> -->
			                        </div>
			                        <div class="description">
			                        	<p>
			                        		<?php the_field('list_product_desc'); ?>
			                        	</p>
			                        </div>
			                        <div class="review">
			                        	<div class="rate">
			                            	<?php
			                            		$rate_list_product = get_field('rate_list_product');
												    if ( !empty( $rate_list_product ) ) :
												    	the_field('rate_list_product');
												endif;
			                            	?>
			                            </div>
			                            <?php
			                        		$review_list_product = get_field('review_list_product');
											    if ( !empty( $review_list_product ) ) :
											    	?>

											    	<div class="review_list">
											    		<i class="ic ic_review"></i>
											    	<?php
											    		the_field('review_list_product');
											    	?>
											    	</div>
											    	<?php
											endif;
			                        	?>
			                        </div>
			                        <div class="price">
			                            <div class="item">
			                            	<s><?php the_field('old_price_list_product'); ?></s>
			                            	<p><?php the_field('new_price_list_product'); ?></p>
			                            </div>
			                            <div class="item">
			                            	<input type="submit" value="Купить">
			                            	<a data-id="<?php the_field("product_id");?>" data-url="<?php the_field('link_list_product'); ?>" href="" class="onclick one-click-buy">Купить в 1 клик</a>

			                            </div>
			                        </div>
			                    </div>
			                </a>
			            <?php endwhile; ?>
	              		<!-- end of the loop -->

	              	<!-- pagination here -->

	              	<?php wp_reset_postdata(); ?>
	              	<?php else : ?>
		              <p><?php _e( 'Статей нет' ); ?></p>
		            <?php endif; ?>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function(){
				$('.one-click-buy').on('click',function(e){
					e.preventDefault();
 					const id = $(this).data('id');
					const ajax_url = "/wp-admin/admin-ajax.php";

					$.ajax ({
 						url: ajax_url,
 						type:'POST',
 						data:{
							action: 'oneclick',
							product_id: id,
							quantity: 1
						},//`action=onelick&product_id=${product_id}&quantity=1`,
 						success:function(results) {
							const cart = JSON.parse(results);
							console.debug(cart);
							$('body > header > div.top > div > div.basket > a > div > sup').text(cart.item_count);
							$('body > header > div.top > div > div.basket > a > div > sup').html('<span class="woocommerce-Price-amount amount">'+cart.cart_contents_total+'<span class="woocommerce-Price-currencySymbol">₽</span></span>');
    						location.href = '/checkout'; //Переход на оформление заказа
  						}
					});
				});
			});
		</script>
	</section>

	<section class="item" style="background: url('<?php the_field('line_section') ?>') no-repeat top center/contain">
		<div class="shell">
			<!-- Блог -->
			<div class="blog">
				<h2><?php echo get_cat_name( 59 ) ?></h2>

				<div class="owl-carousel slide_blog">
					<?php
					$categories = get_categories( ['parent' => 59,'hide_empty' => 0] );


					foreach($categories as $category) {
						// получим ID картинки из метаполя термина
						$image_id = get_term_meta( $category->term_id, '_thumbnail_id', 1 );
						// ссылка на полный размер картинки по ID вложения
						$image_url = wp_get_attachment_image_url( $image_id, 'full' );

						echo '<div class="item">';
						echo '<a href="'.get_category_link( $category->term_id ).'" class="more">';
						echo '<strong>'.$category->name.'</strong>';
						echo '<div class="img"><img src="'. $image_url .'" alt="" /></div>';
						echo '</a>';
						echo '</div>';
					}
					?>


				</div>
			</div>
		</div>
	</section>

	<?php if( '' !== get_post()->post_content ) { ?>
		<section class="item about bgn">
			<div class="shell column">
				<!-- Блок About -->
				<h2><?php the_title(); ?></h2>
				<div class="limit">
					<?php the_post(); the_content(); ?>
					<a href="#">Читать ещё</a>
				</div>

			</div>
		</section>
	<?php
	} ?>

</main>

	<!-- <div id="primary" class="content-area">
		<main id="main" class="site-main" role="main"> -->

			<?php
			/**
			 * Functions hooked in to homepage action
			 *
			 * @hooked storefront_homepage_content      - 10
			 * @hooked storefront_product_categories    - 20
			 * @hooked storefront_recent_products       - 30
			 * @hooked storefront_featured_products     - 40
			 * @hooked storefront_popular_products      - 50
			 * @hooked storefront_on_sale_products      - 60
			 * @hooked storefront_best_selling_products - 70
			 */
			// do_action( 'homepage' );
			?>

		<!-- </main>
	</div>-->
<?php
get_footer();

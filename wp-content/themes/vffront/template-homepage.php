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
	do_action( 'homepage' ); ?>

	<section class="item">
		<div class="shell">
			<!-- Перечень брендов -->
			<div class="brand width">
				<h2>Бренды</h2>

				<ul class="flex v_center jcsb width">
					<li>
						<a href="#">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/omkk.png" alt="">
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/baby_hit.png" alt="">
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/l_in_l.png" alt="">
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/indusha.png" alt="">
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/pipa.png" alt="">
						</a>
					</li>
					<li>
						<a href="#">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/m_com.png" alt="">
						</a>
					</li>
				</ul>
			</div>
		</div>
	</section>

	<section class="item">
		<div class="shell">
			<!-- Перечень товаров -->
			<div class="popular">
				<h2>Популярные товары</h2>

				<div class="list_product">
					<!-- Товар -->
					<?php
						$args = array(
    						'post_type' => 'product',
    						'orderby' => 'rating',
    						'order' => 'desc',
    						'posts_per_page' => 4,
							'meta_key' => '_regular_price',
							'meta_value' => '0',
							'meta_compare' => '>='
						);
						$loop = new WP_Query( $args );
						while ( $loop->have_posts() ) : $loop->the_post();
						global $product;
					?>
					<div class="product">
						<div class="thumbnail">
							<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->ID ), 'single-post-thumbnail' );?>
							<img src="<?php   echo empty($image[0])?woocommerce_placeholder_img_src():$image[0]; ?>" data-id="<?php echo $product->ID; ?>">
						</div>
						<div class="title">
							<p><?php the_title(); ?></p>
						</div>
						<div class="description">
							<p>
								<?php echo $product->get_short_description(); ?>
							</p>
						</div>
						<div class="review">
							<div class="rate">
								<?php
									$rating = $product->get_rating_count();
									for($i=0; $i<$rating; $i++)
									{
										echo '<span class="active"></span>';
									}
									for($i=0; $i<5-$rating; $i++)
									{
										echo '<span></span>';
									}

								?>

							</div>
							<div class="review_list">
								<i class="ic ic_review"></i>
								<span>16</span>
								<span>отзывов</span>
							</div>
						</div>
						<div class="price">
							<div class="item">
								<s><?php echo $product->get_regular_price(); ?></s>
								<p><span><?php echo $product->get_sale_price(); ?></span> Р</p>
							</div>
							<div class="item">
								<form action="<?php the_permalink(); ?>"><input type="submit" value="Купить"></form>
								<a href="<?php echo $product->add_to_cart_url(); ?>" class="onclick">Купить в 1 клик</a>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
				<?php wp_reset_query(); ?>
				</div>
			</div>
		</div>
	</section>

	<section class="item">
		<div class="shell">
			<!-- Блог -->
			<div class="blog">
				<h2><?php echo get_cat_name( 59 ) ?></h2>

				<div class="owl-carousel slide_blog">

					<?php
			            $news = new WP_Query('category__in=59&order=ASC&posts_per_page=-1'); ?>

			            <?php if ( $news->have_posts() ) : ?>

			            <!-- the loop -->
			            <?php while ( $news->have_posts() ) : $news->the_post(); ?>
							<div class="item">
								<strong><?php echo get_the_title(); ?></strong>
								<div class="img">
									<?php the_post_thumbnail(); ?>
								</div>
								<?php echo content('15'); ?>
								<a href="<?php the_permalink() ?>" class="more">Читать</a>
							</div>
							<br />
							<br />
			            <?php endwhile; ?>
						<!-- end of the loop -->

						<!-- pagination here -->

						<?php wp_reset_postdata(); ?>

						</ul>

					<?php else : ?>
		            	<p><?php _e( 'По Вашему запросу ничего не найдено' ); ?></p>
		            <?php endif; ?>

				</div>
			</div>
		</div>
	</section>

	<?php if( '' !== get_post()->post_content ) { ?>
		<section class="item about bgn">
			<div class="shell column">
				<!-- Блок About -->
				<h2><?php the_title(); ?></h2>
				<?php the_post(); the_content(); ?>

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

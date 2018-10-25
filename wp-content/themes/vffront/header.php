<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">


    <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory');?>/assets/style/main.css">
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory');?>/assets/style/owl.carousel.css">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="<?php bloginfo('stylesheet_directory');?>/assets/js/main.js"></script>
    <script src="<?php bloginfo('stylesheet_directory');?>/assets/js/owl.carousel.js"></script>
    <link rel="icon" href="<?php bloginfo('stylesheet_directory');?>/assets/images/favicon.png">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- header -->

<header class="header">

	<div class="scrolling">

		<!-- Верхний фон -->
	    <?php if( get_field( 'line_header','option' ) ): ?>
			<div class="tf" style="background: url('<?php the_field('line_header','option'); ?>') no-repeat center;"></div>
		<?php endif; ?>
	    <!-- \ Верхний фон -->

	    <!-- Общая шапка -->
	    <div class="top">
	        <div class="shell jcsb">
	            <div class="logo">
	                <a href="/">
	                    <img src="<?php the_field('logo','option'); ?>" alt="">
	                </a>
	            </div>
	            <div class="con flex v_center jcsa">
	            	<div class="tell flex column v_center">
		            	<a href="tel:<?php echo str_replace(array(' ', '<', '>', 'span', '/', ')', '(', '+', '-'), '', get_field('tel', 'option')); ?>">
							<?php the_field('tel', 'option'); ?>
						</a>
						<a href="tel:<?php echo str_replace(array(' ', '<', '>', 'span', '/', ')', '(', '+', '-'), '', get_field('tel2', 'option')); ?>">
							<?php the_field('tel2', 'option'); ?>
						</a>
					</div>
	                <?php the_field('info','option'); ?>
	            </div>
	            <div class="basket">
	            	<a href="/cart" class="flex v_center">
	                    <div class="basket_item">
	                        <i class="ic ic_basket_w"></i>
	                        <sup><?php echo WC()->cart->get_cart_contents_count(); ?></sup>
	                    </div>
	                    <span class="price flex v_center">
	                        <p><?php echo WC()->cart->get_cart_subtotal(); ?></p>
	                        <i class="ic ic_down_b"></i>
	                    </span>
	                </a>
	                <ul class="hidden">
						<?php
						foreach(WC()->cart->get_cart() as $item){
							$product = wc_get_product( $item['product_id'] );
							$thumbnail = get_the_post_thumbnail_url( $item['product_id'] );
							echo '<li><a class="name" href="'.get_permalink( $product->get_id() ).'"><img src="'.$thumbnail.'"/>'.$product->get_name().'</a><p class="right"><span class="quantity">'.$item['quantity'].'</span><span class="subtotal">'.$item['line_subtotal'].'Р</span></p></li>';
						}
						 ?>
	                	<!-- <li>Товар 1</li>
	                	<li>Товар 2</li>
	                	<li>Товар 3</li> -->
	                </ul>
	                <!-- <img src="images/basket.png" alt=""> -->
	            </div>
				<div class="cabinet">
					<a href="/my-account">Личный кабинет</a>
				</div>
	        </div>
	    </div>

	    <!-- Основная навигация -->
	    <nav class="nav">
	        <div class="shell jcsb v_center">
	        	<?php
		            wp_nav_menu( array(
						'theme_location'  => 'menu-top',
						'menu'            => 'main',
						'container'       => 'ul',
						'container_class' => '',
						'container_id'    => '',
						'menu_class'      => '',
						'menu_id'         => '',
						'echo'            => true,
						'fallback_cb'     => 'wp_page_menu',
						'before'          => '',
						'after'           => '',
						'link_before'     => '',
						'link_after'      => '',
						'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'depth'           => 0,
						'walker'          => '',
		            ) );
		          ?>
		          <div class="search">
		          	<?php get_product_search_form(); ?>
			    </div>
	        </div>
	    </nav>
		
	    <div class="mobi">
	    	Меню
	    </div>
	</div>

	<?php
		if( is_front_page() ) {
			?>
			    <!-- Слайдер -->
		        	<div class="owl-carousel slider_home">

						<?php
							$slider_home = new WP_Query('post_type=slider&order=ASC&posts_per_page=-1');
							if ( $slider_home->have_posts() ) : ?>
							<!-- the loop -->
			              	<?php while ( $slider_home->have_posts() ) : $slider_home->the_post(); ?>

								<div class="item item_slide flex v_center jcsb" style="background: url('<?php the_field('bg_block'); ?>') no-repeat center/cover;">
					                <div class="shell">
					                    <div class="inner">
					                    	<?php the_field('title_slider'); ?>
					                    </div>
					                    <div class="inner">
					                        <div class="product" style="display:none">
					                            <div class="prod_day">Товар дня</div>
					                            <div class="top">
					                                <div class="timer">
					                                    <span>16</span> :
					                                    <span>28</span> :
					                                    <span>24</span>
					                                </div>
					                            </div>
					                            <div class="thumbnail">
					                            	<!-- <?php //the_sub_field('images_popular_goods'); ?> -->
					                            	<?php the_post_thumbnail(); ?>
					                            </div>
					                            <div class="review">
					                                <div class="rate">
					                                	<?php
					                                		$rate_popular_goods = get_field('rate_popular_goods');
															    if ( !empty( $rate_popular_goods ) ) :
															    	the_field('rate_popular_goods');
															endif;
					                                	?>
					                                </div>

					                                	<?php
					                                		$review_popular_goods = get_field('review_popular_goods');
															    if ( !empty( $review_popular_goods ) ) :
															    	?>

															    	<div class="review_list">
															    		<i class="ic ic_review"></i>
															    	<?php
															    		the_field('review_popular_goods');
															    	?>
															    	</div>
															    	<?php
															endif;
					                                	?>
					                                    <!--
					                                    <span>16</span>
					                                    <span>отзывов</span> -->

					                            </div>
					                            <div class="title">
					                            	<p><?php the_field('title_popular_goods'); ?></p>
					                            </div>
					                            <div class="price">
					                                <div class="item">
					                                	<s><?php the_field('old_price_popular_goods'); ?></s>
					                                	<p><?php the_field('new_price_popular_goods'); ?></p>
					                                </div>
					                                <input type="submit" value="Купить">
					                            </div>
					                        </div>
					                    </div>
					                </div>
					            </div>

							<?php endwhile; ?>
			              	<!-- end of the loop -->

			              	<!-- pagination here -->

			              	<?php wp_reset_postdata(); ?>
			              	<?php else : ?>
				              <p><?php _e( 'Статей нет' ); ?></p>
				            <?php endif; ?>

			        </div>

			<?php
		}
	?>

</header>
<?php woocommerce_breadcrumb(); ?>

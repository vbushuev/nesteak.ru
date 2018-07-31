<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?><!doctype html>
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
    <script src="<?php bloginfo('stylesheet_directory');?>/assets/js/calendar.js"></script>
    <link rel="icon" href="<?php bloginfo('stylesheet_directory');?>/assets/images/favicon.png">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- header -->

<header class="header">

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
            	<a href="tel:<?php echo str_replace(array(' ', '<', '>', 'span', '/', ')', '(', '+', '-'), '', get_field('tel', 'option')); ?>">
					<?php the_field('tel', 'option'); ?>
				</a>
                <?php the_field('info','option'); ?>
            </div>
            <div class="basket">
            	<a href="/basket" class="flex v_center">
                    <div class="basket_item">
                        <i class="ic ic_basket_w"></i>
                        <sup><?php echo WC()->cart->get_cart_contents_count(); ?></sup>
                    </div>
                    <span class="price flex v_center">
                        <p><?php echo WC()->cart->get_cart_subtotal(); ?></p>
                        <i class="ic ic_down_b"></i>
                    </span>
                </a>
                <!-- <img src="images/basket.png" alt=""> -->
            </div>
        </div>
    </div>

    <!-- Основная навигация -->
    <nav class="nav">
        <div class="shell">
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
            <!-- <ul>
                <li class="menu_cat">
                    <a href="#">Каталог продукции</a>
                    <ul class="submenu">
                        <li><a href="#">Прод1</a></li>
                        <li><a href="#">Прод2</a></li>
                        <li><a href="#">Прод3</a></li>
                        <li><a href="#">Прод4</a></li>
                        <li><a href="#">Прод5</a></li>
                        <li><a href="#">Прод6</a></li>
                    </ul>
                </li>
                <li><a href="#">О продукции</a></li>
                <li><a href="#">Оплата и доставка</a></li>
                <li><a href="#">Информация</a></li>
                <li><a href="#">Контакты</a></li>
            </ul> -->
        </div>
    </nav>

	<?php
		if( is_front_page() ) {
			?>

				<!-- Слайдер -->
			    <div class="owl-carousel slider_home">
			        <div class="item item_slide flex v_center jcsb">
			            <div class="shell">
			                <div class="inner">
			                    <h2>настоящая белорусская тушенка <span>это:</span></h2>
			                    <ul>
			                        <li>Cделаная строго по ГОСТу 32125-2013</li>
			                        <li>Без замороженного мяса</li>
			                        <li>В составе только: тушеная говядина, лук репчатый, соль, лавровый лист, перец черный</li>
			                        <li>Массовая доля закладки мяса – 97,5%</li>
			                        <li>Только свежее качественное сырье</li>
			                    </ul>
			                </div>
			                <div class="inner">
								<!-- Товар -->
								<?php
									$args = array(
			    						'post_type' => 'product',
			    						'orderby' => 'rating',
			    						'order' => 'desc',
			    						'posts_per_page' => 1,
										'meta_key' => '_sale_price',
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

			                    <!-- <div class="product">
			                        <div class="prod_day">Товар дня</div>
			                        <div class="top">
			                            <div class="timer">
			                                <span>16</span> :
			                                <span>28</span> :
			                                <span>24</span>
			                            </div>
			                        </div>
			                        <div class="thumbnail">
			                            <img src="images/tov.png" alt="">
			                        </div>
			                        <div class="review">
			                            <div class="rate">
			                                <span class="active"></span>
			                                <span class="active"></span>
			                                <span class="active"></span>
			                                <span class="active"></span>
			                                <span></span>
			                            </div>
			                            <div class="review_list">
			                                <i class="ic ic_review"></i>
			                                <span>16</span>
			                                <span>отзывов</span>
			                            </div>
			                        </div>
			                        <div class="title">
			                            <p>Говядина тушеная ГОСТ ПЕРВЫЙ сорт 525 грамм</p>
			                        </div>
			                        <div class="price">
			                            <div class="item">
			                                <s>250</s>
			                                <p><span>190</span> Р</p>
			                            </div>
			                            <input type="submit" value="Купить">
			                        </div>
			                    </div> -->
								<?php endwhile; ?>
								<?php wp_reset_query(); ?>
			                </div>
			            </div>
			        </div>
			    </div>

			<?php
		}
	?>

</header>

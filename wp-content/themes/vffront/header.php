<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package vffront
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

<?php do_action( 'vffront_before_site' ); ?>
<!-- header -->

<header class="header">

	<!-- Верхний фон -->
	<div class="tf"></div>
	<!-- \ Верхний фон -->

	<!-- Общая шапка -->
	<div class="top">
		<div class="shell jcsb">
			<div class="logo">
				<a href="/">
					<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/logo.png" alt="">
				</a>
			</div>
			<div class="con flex v_center jcsa">
				<a href="tel:+79319747288" class="tel"><span>+7(931)</span>974-72-88</a>
				<p>
					Санкт-Петербург, ул.Белы Куна, д.3, <br />
					ТРК "Международный", 8-ой этаж, офис 851
				</p>
			</div>
			<div class="basket">
				<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/basket.png" alt="">
			</div>
		</div>
	</div>

	<!-- Основная навигация -->
	<nav class="nav">
		<div class="shell">
			<ul>
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
			</ul>
		</div>
	</nav>

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
					<div class="product">
						<div class="prod_day">Товар дня</div>
						<div class="top">
							<div class="timer">
								<span>16</span> :
								<span>28</span> :
								<span>24</span>
							</div>
						</div>
						<div class="thumbnail">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/tov.png" alt="">
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
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<?php
	do_action( 'vffront_before_header' );
	/**
		 * Functions hooked into vffront_header action
		 *
		 * @hooked vffront_header_container                 - 0
		 * @hooked vffront_skip_links                       - 5
		 * @hooked vffront_social_icons                     - 10
		 * @hooked vffront_site_branding                    - 20
		 * @hooked vffront_secondary_navigation             - 30
		 * @hooked vffront_product_search                   - 40
		 * @hooked vffront_header_container_close           - 41
		 * @hooked vffront_primary_navigation_wrapper       - 42
		 * @hooked vffront_primary_navigation               - 50
		 * @hooked vffront_header_cart                      - 60
		 * @hooked vffront_primary_navigation_wrapper_close - 68
		 */
	// do_action( 'vffront_header' );
	/**
	 * Functions hooked in to vffront_before_content
	 *
	 * @hooked vffront_header_widget_region - 10
	 * @hooked woocommerce_breadcrumb - 10
	 */
	do_action( 'vffront_before_content' );

	do_action( 'vffront_content_top' );
	?>

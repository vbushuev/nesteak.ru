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

	<section class="item">
		<div class="shell">
			<!-- Перечень категорий -->
			<div class="cat">
				<h2>Выберите продукцию</h2>

				<ul class="flex jcsb v_center">
					<li>
						<h3>Для детей</h3>
						<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/cat_child.png" alt="">
						<div>
							<p>Краткая информация о продукции в несколько строчек и кнопка подробнее для просмотра остальной информации.</p>
							<a href="#" class="info">Подробнее</a>
						</div>
					</li>
					<li>
						<h3>Для взрослых</h3>
						<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/cat_man.png" alt="">
						<div>
							<p>Краткая информация о продукции в несколько строчек и кнопка подробнее для просмотра остальной информации.</p>
							<a href="#" class="info">Подробнее</a>
						</div>
					</li>
					<li>
						<h3>Для домашних питомцев</h3>
						<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/cat_dogs.png" alt="">
						<div>
							<p>Краткая информация о продукции в несколько строчек и кнопка подробнее для просмотра остальной информации.</p>
							<a href="#" class="info">Подробнее</a>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</section>

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
					<div class="product">
						<div class="thumbnail">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/tov.png" alt="">
						</div>
						<div class="title">
							<p>Говядина + сердце (СБ) 100 г</p>
						</div>
						<div class="description">
							<p>
								Состав: говядина, вода питьевая, сердце говяжье, масло подсолнечное, крахмал картофельный как загуститель (3 %), соль йодированная.
							</p>
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
						<div class="price">
							<div class="item">
								<s>250</s>
								<p><span>190</span> Р</p>
							</div>
							<div class="item">
								<input type="submit" value="Купить">
								<a href="#" class="onclick">Купить в 1 клик</a>
							</div>
						</div>
					</div>
					<!-- Товар -->
					<div class="product">
						<div class="thumbnail">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/tov.png" alt="">
						</div>
						<div class="title">
							<p>Говядина + сердце (СБ) 100 г</p>
						</div>
						<div class="description">
							<p>
								Состав: говядина, вода питьевая, сердце говяжье, масло подсолнечное, крахмал картофельный как загуститель (3 %), соль йодированная.
							</p>
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
						<div class="price">
							<div class="item">
								<s>250</s>
								<p><span>190</span> Р</p>
							</div>
							<div class="item">
								<input type="submit" value="Купить">
								<a href="#" class="onclick">Купить в 1 клик</a>
							</div>
						</div>
					</div>
					<!-- Товар -->
					<div class="product">
						<div class="thumbnail">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/tov.png" alt="">
						</div>
						<div class="title">
							<p>Говядина + сердце (СБ) 100 г</p>
						</div>
						<div class="description">
							<p>
								Состав: говядина, вода питьевая, сердце говяжье, масло подсолнечное, крахмал картофельный как загуститель (3 %), соль йодированная.
							</p>
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
						<div class="price">
							<div class="item">
								<s>250</s>
								<p><span>190</span> Р</p>
							</div>
							<div class="item">
								<input type="submit" value="Купить">
								<a href="#" class="onclick">Купить в 1 клик</a>
							</div>
						</div>
					</div>
					<!-- Товар -->
					<div class="product">
						<div class="thumbnail">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/tov.png" alt="">
						</div>
						<div class="title">
							<p>Говядина + сердце (СБ) 100 г</p>
						</div>
						<div class="description">
							<p>
								Состав: говядина, вода питьевая, сердце говяжье, масло подсолнечное, крахмал картофельный как загуститель (3 %), соль йодированная.
							</p>
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
						<div class="price">
							<div class="item">
								<s>250</s>
								<p><span>190</span> Р</p>
							</div>
							<div class="item">
								<input type="submit" value="Купить">
								<a href="#" class="onclick">Купить в 1 клик</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="item">
		<div class="shell">
			<!-- Блог -->
			<div class="blog">
				<h2>блог</h2>

				<div class="owl-carousel slide_blog">
					<div class="item">
						<strong>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repudiandae, eum.</strong>
						<div class="img">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/blog.jpg" alt="">
						</div>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus asperiores possimus sed sint, ea tempore.</p>
						<a href="#" class="more">Читать</a>
					</div>
					<div class="item">
						<strong>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repudiandae, eum.</strong>
						<div class="img">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/blog.jpg" alt="">
						</div>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus asperiores possimus sed sint, ea tempore.</p>
						<a href="#" class="more">Читать</a>
					</div>
					<div class="item">
						<strong>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repudiandae, eum.</strong>
						<div class="img">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/blog.jpg" alt="">
						</div>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus asperiores possimus sed sint, ea tempore.</p>
						<a href="#" class="more">Читать</a>
					</div>
					<div class="item">
						<strong>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repudiandae, eum.</strong>
						<div class="img">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/blog.jpg" alt="">
						</div>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus asperiores possimus sed sint, ea tempore.</p>
						<a href="#" class="more">Читать</a>
					</div>
					<div class="item">
						<strong>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repudiandae, eum.</strong>
						<div class="img">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/blog.jpg" alt="">
						</div>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Necessitatibus asperiores possimus sed sint, ea tempore.</p>
						<a href="#" class="more">Читать</a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="item about bgn">
		<div class="shell column">
			<!-- Блок About -->
			<h2>О компании NeСтейк</h2>

			<div class="text">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Saepe ullam rem dolorum doloribus, et aspernatur corporis, aut placeat. Suscipit quia alias rerum asperiores esse, dignissimos aliquid ullam atque temporibus ipsum, eaque placeat dolores ad impedit maiores deserunt praesentium eligendi delectus sit voluptates similique beatae unde commodi. Numquam quisquam similique beatae.</p>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Unde suscipit doloremque at amet consequuntur iste quod minus, nesciunt deserunt eos.</p>
			</div>
		</div>
	</section>

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

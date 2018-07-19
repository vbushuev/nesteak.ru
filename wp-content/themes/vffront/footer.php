<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package vffront
 */

?>




		</div><!-- .col-full -->
	</div><!-- #content -->

	<?php do_action( 'vffront_before_footer' ); ?>

	<footer class="footer">
				<!-- Нижний фон -->
				<div class="tf"></div>
				<!-- \ Нижний фон -->

				<div class="shell v_center jcsb">
					<div class="logo">
						<a href="/">
							<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/logo.png" alt="">
						</a>
						<ul class="soc flex v_center">
							<li>
								<a href="#">
									<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/vk.png" alt="">
								</a>
							</li>
							<li>
								<a href="#">
									<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/fb.png" alt="">
								</a>
							</li>
							<li>
								<a href="#">
									<img src="<?php bloginfo('stylesheet_directory');?>/assets/images/tw.png" alt="">
								</a>
							</li>
						</ul>
						<p>(с) НеСтейк 2018. Все права защищены.</p>
					</div>
					<nav class="nav_f">
						<ul>
							<li><a href="#">Оплата</a></li>
							<li><a href="#">Возврат</a></li>
							<li><a href="#">Оптовикам</a></li>
							<li><a href="#">О компании</a></li>
							<li><a href="#">Доставка</a></li>
							<li><a href="#">Как заказать</a></li>
							<li><a href="#">Новости</a></li>
							<li><a href="#">Контакты</a></li>
						</ul>
					</nav>
				</div>
				<?php
				/**
				 * Functions hooked in to vffront_footer action
				 *
				 * @hooked vffront_footer_widgets - 10
				 * @hooked vffront_credit         - 20
				 */
				do_action( 'vffront_footer' ); ?>
			</footer>

	<?php do_action( 'vffront_after_footer' ); ?>

</div><!-- #page -->

<div class="bgc"></div>
<?php wp_footer(); ?>

</body>
</html>

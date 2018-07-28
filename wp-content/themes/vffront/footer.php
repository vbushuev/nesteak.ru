<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>




		</div><!-- .col-full -->
	</div><!-- #content -->

	<footer class="footer">
		<!-- Нижний фон -->
		<?php if( get_field( 'line_footer','option' ) ): ?>
			<div class="tf" style="background: url('<?php the_field('line_footer','option'); ?>') no-repeat center;"></div>
		<?php endif; ?>
		<!-- \ Нижний фон -->

		<div class="shell v_center jcsb">
			<div class="logo">
				<a href="/">
					<img src="<?php the_field('logo','option'); ?>" alt="">
				</a>
				<?php
					if( have_rows('social','option') ): ?>
						<ul class="soc flex v_center">
					    <?php while ( have_rows('social','option') ) : the_row();
					    	?>
					        <li>
								<a href="<?php the_sub_field('link','option'); ?>">
									<img src="<?php the_sub_field('link_img','option'); ?>" alt="">
								</a>
							</li>
							<?php
					    endwhile;?>
						</ul>
					<?php else :

					    // no rows found

					endif;
				?>
				<p><?php the_field('copyright','option'); ?></p>
			</div>
			<nav class="nav_f">
				<?php
	            wp_nav_menu( array(
					'theme_location'  => 'menu-footer',
					'menu'            => 'footer', 
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
	    	</nav>
		</div>
		
	</footer>

</div><!-- #page -->

<div class="bgc"></div>
<?php wp_footer(); ?>

</body>
</html>

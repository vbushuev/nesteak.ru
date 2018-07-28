<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'nesteak' );
function storefront_site_branding() {
	?>
	<div class="logo">
	<!-- <div class="site-branding"> -->
		<?php storefront_site_title_or_logo(); ?>
	</div>
	<?php
}
function storefront_footer_widgets() {
	storefront_site_branding();
	$rows    = intval( apply_filters( 'storefront_footer_widget_rows', 1 ) );
	$regions = intval( apply_filters( 'storefront_footer_widget_columns', 4 ) );

	for ( $row = 1; $row <= $rows; $row++ ) :

		// Defines the number of active columns in this footer row.
		for ( $region = $regions; 0 < $region; $region-- ) {
			if ( is_active_sidebar( 'footer-' . strval( $region + $regions * ( $row - 1 ) ) ) ) {
				$columns = $region;
				break;
			}
		}

		if ( isset( $columns ) ) : ?>
			<div class=<?php echo '"footer-widgets row-' . strval( $row ) . ' col-' . strval( $columns ) . ' fix"'; ?>><?php

				for ( $column = 1; $column <= $columns; $column++ ) :
					$footer_n = $column + $regions * ( $row - 1 );

					if ( is_active_sidebar( 'footer-' . strval( $footer_n ) ) ) : ?>

						<div class="block footer-widget-<?php echo strval( $column ); ?>">
							<?php dynamic_sidebar( 'footer-' . strval( $footer_n ) ); ?>
						</div><?php

					endif;
				endfor; ?>

			</div><!-- .footer-widgets.row-<?php echo strval( $row ); ?> --><?php

			unset( $columns );
		endif;
	endfor;
}
function storefront_product_search() {
	?><div class="con flex v_center jcsa">
				<a href="tel:+79319747288" class="tel"><span>+7(931)</span>974-72-88</a>
				<p>
					Санкт-Петербург, ул.Белы Куна, д.3, <br>
					ТРК "Международный", 8-ой этаж, офис 851
				</p>
			</div><?php
	if ( storefront_is_woocommerce_activated() ) {
		if ( is_cart() ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		?>
	<ul id="site-header-cart" class="site-header-cart menu">
		<li class="<?php echo esc_attr( $class ); ?>">
			<?php storefront_cart_link(); ?>
		</li>
		<li>
			<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
		</li>
	</ul>
	<?php
	}
}
function storefront_header_cart() {
	return;
	if ( storefront_is_woocommerce_activated() ) {
		if ( is_cart() ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
	?>
	<ul id="site-header-cart" class="site-header-cart menu">
		<li class="<?php echo esc_attr( $class ); ?>">
			<?php storefront_cart_link(); ?>
		</li>
		<li>
			<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
		</li>
	</ul>
	<?php
	}
}
function storefront_credit() {

	?>
	<div class="site-info">
		<?php echo esc_html( apply_filters( 'storefront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ); ?> Все права защищены.

	</div><!-- .site-info -->
	<?php
}

<?php
/**
 * Vffront template functions.
 *
 * @package vffront
 */

if ( ! function_exists( 'vffront_display_comments' ) ) {
	/**
	 * Vffront display comments
	 *
	 * @since  1.0.0
	 */
	function vffront_display_comments() {
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || '0' != get_comments_number() ) :
			comments_template();
		endif;
	}
}

if ( ! function_exists( 'vffront_comment' ) ) {
	/**
	 * Vffront comment template
	 *
	 * @param array $comment the comment array.
	 * @param array $args the comment args.
	 * @param int   $depth the comment depth.
	 * @since 1.0.0
	 */
	function vffront_comment( $comment, $args, $depth ) {
		if ( 'div' == $args['style'] ) {
			$tag = 'div';
			$add_below = 'comment';
		} else {
			$tag = 'li';
			$add_below = 'div-comment';
		}
		?>
		<<?php echo esc_attr( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
		<div class="comment-body">
		<div class="comment-meta commentmetadata">
			<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 128 ); ?>
			<?php printf( wp_kses_post( '<cite class="fn">%s</cite>', 'vffront' ), get_comment_author_link() ); ?>
			</div>
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<em class="comment-awaiting-moderation"><?php esc_attr_e( 'Your comment is awaiting moderation.', 'vffront' ); ?></em>
				<br />
			<?php endif; ?>

			<a href="<?php echo esc_url( htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ); ?>" class="comment-date">
				<?php echo '<time datetime="' . get_comment_date( 'c' ) . '">' . get_comment_date() . '</time>'; ?>
			</a>
		</div>
		<?php if ( 'div' != $args['style'] ) : ?>
		<div id="div-comment-<?php comment_ID() ?>" class="comment-content">
		<?php endif; ?>
		<div class="comment-text">
		<?php comment_text(); ?>
		</div>
		<div class="reply">
		<?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		<?php edit_comment_link( __( 'Edit', 'vffront' ), '  ', '' ); ?>
		</div>
		</div>
		<?php if ( 'div' != $args['style'] ) : ?>
		</div>
		<?php endif; ?>
	<?php
	}
}

if ( ! function_exists( 'vffront_footer_widgets' ) ) {
	/**
	 * Display the footer widget regions.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function vffront_footer_widgets() {
		$rows    = intval( apply_filters( 'vffront_footer_widget_rows', 1 ) );
		$regions = intval( apply_filters( 'vffront_footer_widget_columns', 4 ) );

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
}

if ( ! function_exists( 'vffront_credit' ) ) {
	/**
	 * Display the theme credit
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function vffront_credit() {
		?>
		<div class="site-info">
			<?php echo esc_html( apply_filters( 'vffront_copyright_text', $content = '&copy; ' . get_bloginfo( 'name' ) . ' ' . date( 'Y' ) ) ); ?>
			<?php if ( apply_filters( 'vffront_credit_link', true ) ) { ?>
			<br />
			<?php
				if ( apply_filters( 'vffront_privacy_policy_link', true ) && function_exists( 'the_privacy_policy_link' ) ) {
					the_privacy_policy_link( '', '<span role="separator" aria-hidden="true"></span>' );
				}
			?>
			<?php echo '<a href="https://woocommerce.com" target="_blank" title="' . esc_attr__( 'WooCommerce - The Best eCommerce Platform for WordPress', 'vffront' ) . '" rel="author">' . esc_html__( 'Built with Vffront &amp; WooCommerce', 'vffront' ) . '</a>.' ?>
			<?php } ?>
		</div><!-- .site-info -->
		<?php
	}
}

if ( ! function_exists( 'vffront_header_widget_region' ) ) {
	/**
	 * Display header widget region
	 *
	 * @since  1.0.0
	 */
	function vffront_header_widget_region() {
		if ( is_active_sidebar( 'header-1' ) ) {
		?>
		<div class="header-widget-region" role="complementary">
			<div class="col-full">
				<?php dynamic_sidebar( 'header-1' ); ?>
			</div>
		</div>
		<?php
		}
	}
}

if ( ! function_exists( 'vffront_site_branding' ) ) {
	/**
	 * Site branding wrapper and display
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function vffront_site_branding() {
		?>
		<div class="site-branding">
			<?php vffront_site_title_or_logo(); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'vffront_site_title_or_logo' ) ) {
	/**
	 * Display the site title or logo
	 *
	 * @since 2.1.0
	 * @param bool $echo Echo the string or return it.
	 * @return string
	 */
	function vffront_site_title_or_logo( $echo = true ) {
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			$logo = get_custom_logo();
			$html = is_home() ? '<h1 class="logo">' . $logo . '</h1>' : $logo;
		} elseif ( function_exists( 'jetpack_has_site_logo' ) && jetpack_has_site_logo() ) {
			// Copied from jetpack_the_site_logo() function.
			$logo    = site_logo()->logo;
			$logo_id = get_theme_mod( 'custom_logo' ); // Check for WP 4.5 Site Logo
			$logo_id = $logo_id ? $logo_id : $logo['id']; // Use WP Core logo if present, otherwise use Jetpack's.
			$size    = site_logo()->theme_size();
			$html    = sprintf( '<a href="%1$s" class="site-logo-link" rel="home" itemprop="url">%2$s</a>',
				esc_url( home_url( '/' ) ),
				wp_get_attachment_image(
					$logo_id,
					$size,
					false,
					array(
						'class'     => 'site-logo attachment-' . $size,
						'data-size' => $size,
						'itemprop'  => 'logo'
					)
				)
			);

			$html = apply_filters( 'jetpack_the_site_logo', $html, $logo, $size );
		} else {
			$tag = is_home() ? 'h1' : 'div';

			$html = '<' . esc_attr( $tag ) . ' class="beta site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a></' . esc_attr( $tag ) .'>';

			if ( '' !== get_bloginfo( 'description' ) ) {
				$html .= '<p class="site-description">' . esc_html( get_bloginfo( 'description', 'display' ) ) . '</p>';
			}
		}

		if ( ! $echo ) {
			return $html;
		}

		echo $html;
	}
}

if ( ! function_exists( 'vffront_primary_navigation' ) ) {
	/**
	 * Display Primary Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function vffront_primary_navigation() {
		?>
		<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_html_e( 'Primary Navigation', 'vffront' ); ?>">
		<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span><?php echo esc_attr( apply_filters( 'vffront_menu_toggle_text', __( 'Menu', 'vffront' ) ) ); ?></span></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location'	=> 'primary',
					'container_class'	=> 'primary-navigation',
					)
			);

			wp_nav_menu(
				array(
					'theme_location'	=> 'handheld',
					'container_class'	=> 'handheld-navigation',
					)
			);
			?>
		</nav><!-- #site-navigation -->
		<?php
	}
}

if ( ! function_exists( 'vffront_secondary_navigation' ) ) {
	/**
	 * Display Secondary Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function vffront_secondary_navigation() {
	    if ( has_nav_menu( 'secondary' ) ) {
		    ?>
		    <nav class="secondary-navigation" role="navigation" aria-label="<?php esc_html_e( 'Secondary Navigation', 'vffront' ); ?>">
			    <?php
				    wp_nav_menu(
					    array(
						    'theme_location'	=> 'secondary',
						    'fallback_cb'		=> '',
					    )
				    );
			    ?>
		    </nav><!-- #site-navigation -->
		    <?php
		}
	}
}

if ( ! function_exists( 'vffront_skip_links' ) ) {
	/**
	 * Skip links
	 *
	 * @since  1.4.1
	 * @return void
	 */
	function vffront_skip_links() {
		?>
		<a class="skip-link screen-reader-text" href="#site-navigation"><?php esc_attr_e( 'Skip to navigation', 'vffront' ); ?></a>
		<a class="skip-link screen-reader-text" href="#content"><?php esc_attr_e( 'Skip to content', 'vffront' ); ?></a>
		<?php
	}
}

if ( ! function_exists( 'vffront_homepage_header' ) ) {
	/**
	 * Display the page header without the featured image
	 *
	 * @since 1.0.0
	 */
	function vffront_homepage_header() {
		edit_post_link( __( 'Edit this section', 'vffront' ), '', '', '', 'button vffront-hero__button-edit' );
		?>
		<header class="entry-header">
			<?php
			the_title( '<h1 class="entry-title">', '</h1>' );
			?>
		</header><!-- .entry-header -->
		<?php
	}
}

if ( ! function_exists( 'vffront_page_header' ) ) {
	/**
	 * Display the page header
	 *
	 * @since 1.0.0
	 */
	function vffront_page_header() {
		?>
		<header class="entry-header">
			<?php
			vffront_post_thumbnail( 'full' );
			the_title( '<h1 class="entry-title">', '</h1>' );
			?>
		</header><!-- .entry-header -->
		<?php
	}
}

if ( ! function_exists( 'vffront_page_content' ) ) {
	/**
	 * Display the post content
	 *
	 * @since 1.0.0
	 */
	function vffront_page_content() {
		?>
		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'vffront' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .entry-content -->
		<?php
	}
}

if ( ! function_exists( 'vffront_post_header' ) ) {
	/**
	 * Display the post header with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function vffront_post_header() {
		?>
		<header class="entry-header">
		<?php
		if ( is_single() ) {
			vffront_posted_on();
			the_title( '<h1 class="entry-title">', '</h1>' );
		} else {
			if ( 'post' == get_post_type() ) {
				vffront_posted_on();
			}

			the_title( sprintf( '<h2 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
		}
		?>
		</header><!-- .entry-header -->
		<?php
	}
}

if ( ! function_exists( 'vffront_post_content' ) ) {
	/**
	 * Display the post content with a link to the single post
	 *
	 * @since 1.0.0
	 */
	function vffront_post_content() {
		?>
		<div class="entry-content">
		<?php

		/**
		 * Functions hooked in to vffront_post_content_before action.
		 *
		 * @hooked vffront_post_thumbnail - 10
		 */
		do_action( 'vffront_post_content_before' );

		the_content(
			sprintf(
				__( 'Continue reading %s', 'vffront' ),
				'<span class="screen-reader-text">' . get_the_title() . '</span>'
			)
		);

		do_action( 'vffront_post_content_after' );

		wp_link_pages( array(
			'before' => '<div class="page-links">' . __( 'Pages:', 'vffront' ),
			'after'  => '</div>',
		) );
		?>
		</div><!-- .entry-content -->
		<?php
	}
}

if ( ! function_exists( 'vffront_post_meta' ) ) {
	/**
	 * Display the post meta
	 *
	 * @since 1.0.0
	 */
	function vffront_post_meta() {
		?>
		<aside class="entry-meta">
			<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search.

			?>
			<div class="vcard author">
				<?php
					echo get_avatar( get_the_author_meta( 'ID' ), 128 );
					echo '<div class="label">' . esc_attr( __( 'Written by', 'vffront' ) ) . '</div>';
					echo sprintf( '<a href="%1$s" class="url fn" rel="author">%2$s</a>', esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ), get_the_author() );
				?>
			</div>
			<?php
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( __( ', ', 'vffront' ) );

			if ( $categories_list ) : ?>
				<div class="cat-links">
					<?php
					echo '<div class="label">' . esc_attr( __( 'Posted in', 'vffront' ) ) . '</div>';
					echo wp_kses_post( $categories_list );
					?>
				</div>
			<?php endif; // End if categories. ?>

			<?php
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', __( ', ', 'vffront' ) );

			if ( $tags_list ) : ?>
				<div class="tags-links">
					<?php
					echo '<div class="label">' . esc_attr( __( 'Tagged', 'vffront' ) ) . '</div>';
					echo wp_kses_post( $tags_list );
					?>
				</div>
			<?php endif; // End if $tags_list. ?>

		<?php endif; // End if 'post' == get_post_type(). ?>

			<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
				<div class="comments-link">
					<?php echo '<div class="label">' . esc_attr( __( 'Comments', 'vffront' ) ) . '</div>'; ?>
					<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'vffront' ), __( '1 Comment', 'vffront' ), __( '% Comments', 'vffront' ) ); ?></span>
				</div>
			<?php endif; ?>
		</aside>
		<?php
	}
}

if ( ! function_exists( 'vffront_paging_nav' ) ) {
	/**
	 * Display navigation to next/previous set of posts when applicable.
	 */
	function vffront_paging_nav() {
		global $wp_query;

		$args = array(
			'type' 	    => 'list',
			'next_text' => _x( 'Next', 'Next post', 'vffront' ),
			'prev_text' => _x( 'Previous', 'Previous post', 'vffront' ),
			);

		the_posts_pagination( $args );
	}
}

if ( ! function_exists( 'vffront_post_nav' ) ) {
	/**
	 * Display navigation to next/previous post when applicable.
	 */
	function vffront_post_nav() {
		$args = array(
			'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next post:', 'vffront' ) . ' </span>%title',
			'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'vffront' ) . ' </span>%title',
			);
		the_post_navigation( $args );
	}
}

if ( ! function_exists( 'vffront_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function vffront_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time> <time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			_x( 'Posted on %s', 'post date', 'vffront' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo wp_kses( apply_filters( 'vffront_single_post_posted_on_html', '<span class="posted-on">' . $posted_on . '</span>', $posted_on ), array(
			'span' => array(
				'class'  => array(),
			),
			'a'    => array(
				'href'  => array(),
				'title' => array(),
				'rel'   => array(),
			),
			'time' => array(
				'datetime' => array(),
				'class'    => array(),
			),
		) );
	}
}

if ( ! function_exists( 'vffront_product_categories' ) ) {
	/**
	 * Display Product Categories
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function vffront_product_categories( $args ) {

		if ( vffront_is_woocommerce_activated() ) {

			$args = apply_filters( 'vffront_product_categories_args', array(
				'limit' 			=> 3,
				'columns' 			=> 3,
				'child_categories' 	=> 0,
				'orderby' 			=> 'name',
				'title'				=> __( 'Shop by Category', 'vffront' ),
			) );

			$shortcode_content = vffront_do_shortcode( 'product_categories', apply_filters( 'vffront_product_categories_shortcode_args', array(
				'number'  => intval( $args['limit'] ),
				'columns' => intval( $args['columns'] ),
				'orderby' => esc_attr( $args['orderby'] ),
				'parent'  => esc_attr( $args['child_categories'] ),
			) ) );

			/**
			 * Only display the section if the shortcode returns product categories
			 */
			if ( false !== strpos( $shortcode_content, 'product-category' ) ) {

				echo '<section class="vffront-product-section vffront-product-categories" aria-label="' . esc_attr__( 'Product Categories', 'vffront' ) . '">';

				do_action( 'vffront_homepage_before_product_categories' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				do_action( 'vffront_homepage_after_product_categories_title' );

				echo $shortcode_content;

				do_action( 'vffront_homepage_after_product_categories' );

				echo '</section>';

			}
		}
	}
}

if ( ! function_exists( 'vffront_recent_products' ) ) {
	/**
	 * Display Recent Products
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function vffront_recent_products( $args ) {

		if ( vffront_is_woocommerce_activated() ) {

			$args = apply_filters( 'vffront_recent_products_args', array(
				'limit'   => 4,
				'columns' => 4,
				'orderby' => 'date',
				'order'   => 'desc',
				'title'   => __( 'New In', 'vffront' ),
			) );

			$shortcode_content = vffront_do_shortcode( 'products', apply_filters( 'vffront_recent_products_shortcode_args', array(
				'orderby'  => esc_attr( $args['orderby'] ),
				'order'    => esc_attr( $args['order'] ),
				'per_page' => intval( $args['limit'] ),
				'columns'  => intval( $args['columns'] ),
			) ) );

			/**
			 * Only display the section if the shortcode returns products
			 */
			if ( false !== strpos( $shortcode_content, 'product' ) ) {

				echo '<section class="vffront-product-section vffront-recent-products" aria-label="' . esc_attr__( 'Recent Products', 'vffront' ) . '">';

				do_action( 'vffront_homepage_before_recent_products' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				do_action( 'vffront_homepage_after_recent_products_title' );

				echo $shortcode_content;

				do_action( 'vffront_homepage_after_recent_products' );

				echo '</section>';

			}
		}
	}
}

if ( ! function_exists( 'vffront_featured_products' ) ) {
	/**
	 * Display Featured Products
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function vffront_featured_products( $args ) {

		if ( vffront_is_woocommerce_activated() ) {

			$args = apply_filters( 'vffront_featured_products_args', array(
				'limit'      => 4,
				'columns'    => 4,
				'orderby'    => 'date',
				'order'      => 'desc',
				'visibility' => 'featured',
				'title'      => __( 'We Recommend', 'vffront' ),
			) );

			$shortcode_content = vffront_do_shortcode( 'products', apply_filters( 'vffront_featured_products_shortcode_args', array(
				'per_page'   => intval( $args['limit'] ),
				'columns'    => intval( $args['columns'] ),
				'orderby'    => esc_attr( $args['orderby'] ),
				'order'      => esc_attr( $args['order'] ),
				'visibility' => esc_attr( $args['visibility'] ),
			) ) );

			/**
			 * Only display the section if the shortcode returns products
			 */
			if ( false !== strpos( $shortcode_content, 'product' ) ) {

				echo '<section class="vffront-product-section vffront-featured-products" aria-label="' . esc_attr__( 'Featured Products', 'vffront' ) . '">';

				do_action( 'vffront_homepage_before_featured_products' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				do_action( 'vffront_homepage_after_featured_products_title' );

				echo $shortcode_content;

				do_action( 'vffront_homepage_after_featured_products' );

				echo '</section>';

			}
		}
	}
}

if ( ! function_exists( 'vffront_popular_products' ) ) {
	/**
	 * Display Popular Products
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function vffront_popular_products( $args ) {

		if ( vffront_is_woocommerce_activated() ) {

			$args = apply_filters( 'vffront_popular_products_args', array(
				'limit'   => 4,
				'columns' => 4,
				'orderby' => 'rating',
				'order'   => 'desc',
				'title'   => __( 'Fan Favorites', 'vffront' ),
			) );

			$shortcode_content = vffront_do_shortcode( 'products', apply_filters( 'vffront_popular_products_shortcode_args', array(
				'per_page' => intval( $args['limit'] ),
				'columns'  => intval( $args['columns'] ),
				'orderby'  => esc_attr( $args['orderby'] ),
				'order'    => esc_attr( $args['order'] ),
			) ) );

			/**
			 * Only display the section if the shortcode returns products
			 */
			if ( false !== strpos( $shortcode_content, 'product' ) ) {

				echo '<section class="vffront-product-section vffront-popular-products" aria-label="' . esc_attr__( 'Popular Products', 'vffront' ) . '">';

				do_action( 'vffront_homepage_before_popular_products' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				do_action( 'vffront_homepage_after_popular_products_title' );

				echo $shortcode_content;

				do_action( 'vffront_homepage_after_popular_products' );

				echo '</section>';

			}
		}
	}
}

if ( ! function_exists( 'vffront_on_sale_products' ) ) {
	/**
	 * Display On Sale Products
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @param array $args the product section args.
	 * @since  1.0.0
	 * @return void
	 */
	function vffront_on_sale_products( $args ) {

		if ( vffront_is_woocommerce_activated() ) {

			$args = apply_filters( 'vffront_on_sale_products_args', array(
				'limit'   => 4,
				'columns' => 4,
				'orderby' => 'date',
				'order'   => 'desc',
				'on_sale' => 'true',
				'title'   => __( 'On Sale', 'vffront' ),
			) );

			$shortcode_content = vffront_do_shortcode( 'products', apply_filters( 'vffront_on_sale_products_shortcode_args', array(
				'per_page' => intval( $args['limit'] ),
				'columns'  => intval( $args['columns'] ),
				'orderby'  => esc_attr( $args['orderby'] ),
				'order'    => esc_attr( $args['order'] ),
				'on_sale'  => esc_attr( $args['on_sale'] ),
			) ) );

			/**
			 * Only display the section if the shortcode returns products
			 */
			if ( false !== strpos( $shortcode_content, 'product' ) ) {

				echo '<section class="vffront-product-section vffront-on-sale-products" aria-label="' . esc_attr__( 'On Sale Products', 'vffront' ) . '">';

				do_action( 'vffront_homepage_before_on_sale_products' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				do_action( 'vffront_homepage_after_on_sale_products_title' );

				echo $shortcode_content;

				do_action( 'vffront_homepage_after_on_sale_products' );

				echo '</section>';

			}
		}
	}
}

if ( ! function_exists( 'vffront_best_selling_products' ) ) {
	/**
	 * Display Best Selling Products
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since 2.0.0
	 * @param array $args the product section args.
	 * @return void
	 */
	function vffront_best_selling_products( $args ) {
		if ( vffront_is_woocommerce_activated() ) {

			$args = apply_filters( 'vffront_best_selling_products_args', array(
				'limit'   => 4,
				'columns' => 4,
				'orderby' => 'popularity',
				'order'   => 'desc',
				'title'	  => esc_attr__( 'Best Sellers', 'vffront' ),
			) );

			$shortcode_content = vffront_do_shortcode( 'products', apply_filters( 'vffront_best_selling_products_shortcode_args', array(
				'per_page' => intval( $args['limit'] ),
				'columns'  => intval( $args['columns'] ),
				'orderby'  => esc_attr( $args['orderby'] ),
				'order'    => esc_attr( $args['order'] ),
			) ) );

			/**
			 * Only display the section if the shortcode returns products
			 */
			if ( false !== strpos( $shortcode_content, 'product' ) ) {

				echo '<section class="vffront-product-section vffront-best-selling-products" aria-label="' . esc_attr__( 'Best Selling Products', 'vffront' ) . '">';

				do_action( 'vffront_homepage_before_best_selling_products' );

				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';

				do_action( 'vffront_homepage_after_best_selling_products_title' );

				echo $shortcode_content;

				do_action( 'vffront_homepage_after_best_selling_products' );

				echo '</section>';

			}
		}
	}
}

if ( ! function_exists( 'vffront_homepage_content' ) ) {
	/**
	 * Display homepage content
	 * Hooked into the `homepage` action in the homepage template
	 *
	 * @since  1.0.0
	 * @return  void
	 */
	function vffront_homepage_content() {
		while ( have_posts() ) {
			the_post();

			get_template_part( 'content', 'homepage' );

		} // end of the loop.
	}
}

if ( ! function_exists( 'vffront_social_icons' ) ) {
	/**
	 * Display social icons
	 * If the subscribe and connect plugin is active, display the icons.
	 *
	 * @link http://wordpress.org/plugins/subscribe-and-connect/
	 * @since 1.0.0
	 */
	function vffront_social_icons() {
		if ( class_exists( 'Subscribe_And_Connect' ) ) {
			echo '<div class="subscribe-and-connect-connect">';
			subscribe_and_connect_connect();
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'vffront_get_sidebar' ) ) {
	/**
	 * Display vffront sidebar
	 *
	 * @uses get_sidebar()
	 * @since 1.0.0
	 */
	function vffront_get_sidebar() {
		get_sidebar();
	}
}

if ( ! function_exists( 'vffront_post_thumbnail' ) ) {
	/**
	 * Display post thumbnail
	 *
	 * @var $size thumbnail size. thumbnail|medium|large|full|$custom
	 * @uses has_post_thumbnail()
	 * @uses the_post_thumbnail
	 * @param string $size the post thumbnail size.
	 * @since 1.5.0
	 */
	function vffront_post_thumbnail( $size = 'full' ) {
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( $size );
		}
	}
}

if ( ! function_exists( 'vffront_primary_navigation_wrapper' ) ) {
	/**
	 * The primary navigation wrapper
	 */
	function vffront_primary_navigation_wrapper() {
		echo '<div class="vffront-primary-navigation"><div class="col-full">';
	}
}

if ( ! function_exists( 'vffront_primary_navigation_wrapper_close' ) ) {
	/**
	 * The primary navigation wrapper close
	 */
	function vffront_primary_navigation_wrapper_close() {
		echo '</div></div>';
	}
}

if ( ! function_exists( 'vffront_header_container' ) ) {
	/**
	 * The header container
	 */
	function vffront_header_container() {
		echo '<div class="col-full">';
	}
}

if ( ! function_exists( 'vffront_header_container_close' ) ) {
	/**
	 * The header container close
	 */
	function vffront_header_container_close() {
		echo '</div>';
	}
}

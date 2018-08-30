<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

get_header(); ?>

		<main class="main">

		<?php while ( have_posts() ) : the_post();

			if ( is_category('59') ) {
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
			} else {
				get_template_part( 'content', 'category' );
			}

		endwhile; // End of the loop. ?>

		</main><!-- #main -->

<?php
get_footer();

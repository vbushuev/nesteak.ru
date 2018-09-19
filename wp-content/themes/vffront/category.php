<?php
/**
 * The template for displaying all single posts.
 *
 * @package storefront
 */

get_header(); ?>

		<main class="main">
			<?php 
				if ( is_category('59') ) {
					?>
						<div class="shell jcsb">
					<?php
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
					?>
						</div>
					<?php
				} else {
					?>
						<div class="shell jcsb">
					<?php
						if ( have_posts() ) : // если имеются записи в блоге.
							?>
								<div class="list_of_single flex jcsa">
									<?php
									while (have_posts()) : the_post();  // запускаем цикл обхода материалов блога
									?>
									<div class="item_single">
										<a href="<?php the_permalink(); ?>">
											<h2><?php the_title(); ?></h2>
											<?php the_post_thumbnail(); ?>
											<?php echo content('20'); ?>
										</a>
									</div>
									<?php endwhile;  // завершаем цикл.
									?>
								</div>
							<?php
						endif;
						/* Сбрасываем настройки цикла. Если ниже по коду будет идти еще один цикл, чтобы не было сбоя. */
						wp_reset_query();
					?>
						</div>
					<?php
				}
			?>
		</main><!-- #main -->

<?php
get_footer();

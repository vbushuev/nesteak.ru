<?php
    $news = new WP_Query('category__in=59&order=ASC&posts_per_page=-1'); ?>

    <?php if ( $news->have_posts() ) : ?>

    <!-- the loop -->
    <?php while ( $news->have_posts() ) : $news->the_post(); ?>
        <div class="item">
            <a href="<?php the_permalink() ?>" class="more">
                <strong><?php echo get_the_title(); ?></strong>
                <div class="img">
                    <?php the_post_thumbnail(); ?>
                </div>
                <?php echo content('15'); ?>
            </a>
        </div>
    <?php endwhile; ?>
    <!-- end of the loop -->

    <!-- pagination here -->

    <?php wp_reset_postdata(); ?>

    <!-- </ul> -->

<?php else : ?>
    <p><?php _e( 'По Вашему запросу ничего не найдено' ); ?></p>
<?php endif; ?>

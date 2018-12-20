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
<style>
	/* .address-field,.woocommerce-shipping-fields{
		display: none;
	} */
</style>
<script>
	const showDiscount = () => {
		const total_discount = $('.cart-discount .amount').text().replace(/[\D]+/ig,'');
		const sub_total = $('.cart-subtotal .amount').text().replace(/[\D]+/ig,'');

		if(total_discount && sub_total){
			const percent = Math.floor(100*total_discount/sub_total);
			console.debug('subtotal found',total_discount/sub_total,total_discount,sub_total,percent)

			$('.woocommerce-remove-coupon').remove();
			$('.cart-discount.coupon-discount th').text('Скидка');
			$('.cart-discount.coupon-discount td .amount').css('display','inline-block');

			$('.cart_item > .product-subtotal,.cart_item > .product-total:not(.discount-assigned)').addClass('discount-assigned').each(function(){
				let price = parseFloat($(this).find('.amount').text().replace(/[\D]+/ig,''))/100;
				let newPrice = (price*(100-percent)/100).toFixed(2);
				console.debug('this price',percent,price,price*(100-percent)/100);
				$(this).find('.amount').addClass('amount-stroke');
				$(this).append(`<span class="woocommerce-Price-amount amount"><small>-${percent}%</small>&nbsp;&nbsp;${newPrice}<span class="woocommerce-Price-currencySymbol">₽</span></span>`)
			});
		}
	};
	const shippingMethodSelector = 'select.shipping_method'
	const checkShipping = () =>{
		const shipping = $(shippingMethodSelector).val();
		const regex = /local_pickup/ig;
		console.debug('change shiping',shipping, regex.test(shipping));
		if(shipping.match(/.*local_pickup.*/))$('.address-field,.woocommerce-shipping-fields').hide(); else $('.address-field,.woocommerce-shipping-fields').show();
	}
	const shippingShow = () => {
		if(!$(shippingMethodSelector).length) return;
		// $('.address-field').hide();
		checkShipping();
		$(shippingMethodSelector).on('click change',checkShipping).addClass('assigned');
	}
	const showMap = () => {
		if($('.cart_item > .product-subtotal,.cart_item > .product-total').length){
			$('.ymaps').show();
		}else $('.ymaps').hide();
	}
	// jQuery.ajaxSetup({
	// 	global: true,
	// 	complete: ()=>{
	// 		console.debug('ajax complete global');
	//   		showDiscount();
	// 		shippingShow();
	// 		showMap();
	// 	}
	// });
	jQuery(document).ready(function(){
		showDiscount();
		shippingShow();
		showMap();
	});
	// $( document.body ).on( 'updated_cart_totals', function(){
	// 	console.debug('ajax updated_cart_totals');
  	// 	showDiscount();
	// 	shippingShow();
	// 	showMap();
	// });
	jQuery( document ).on( 'updated_checkout updated_checkout updated_cart_totals', function(){
		console.debug('updated_checkout updated_checkout updated_cart_totals');
  		showDiscount();
		shippingShow();
		showMap();
	});
	// jQuery(document).ajaxComplete(function() {
	// // $(document).ajaxStop(function() {
	// 	console.debug('ajax complete');
  	// 	showDiscount();
	// 	shippingShow();
	// 	showMap();
	// });
</script>

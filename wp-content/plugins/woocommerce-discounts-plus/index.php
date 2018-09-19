<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
Plugin Name: WooCommerce Discounts Plus
Plugin URI: http://shop.androidbubbles.com/product/woocommerce-discounts-plus/
Description: An amazing WooCommerce extension to implement multiple discount criterias with ultimate convenience.

Author: Fahad Mahmood
Version: 3.0.0
Author URI: https://profiles.wordpress.org/fahadmahmood/
License: GPL3

    Copyright (C) 2013  Fahad Mahmood

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	include_once('inc/functions.php');



	$plugins_activated = apply_filters( 'active_plugins', get_option( 'active_plugins' ));
if ( !in_array( 'woocommerce/woocommerce.php', $plugins_activated )) return; // Check if WooCommerce is active

	global
			$wdp_pro,
			$wdp_dir,
			$s2_enabled,
			$s2_pro,
			$s2_discounts,
			$wdp_new_price,
			$wcdp_data,
			$wdpp_obj,
			$wdp_discount_condition,
			$wdp_discount_types,
			$wdp_halt,
			$wdp_new_price_sp,
			$wdp_new_price_shop,
			$woocommerce_variations_separate,
			$product_variations_qty,
			$wdp_pricing_scale;

	$wdp_pricing_scale = false;
	$product_variations_qty = array();
	$woocommerce_variations_separate = get_option( 'woocommerce_variations_separate', 'yes' );
	$s2_pro = $wdp_halt = false;
	if(class_exists('c_ws_plugin__s2member_utils_conds')){
		$s2_pro = c_ws_plugin__s2member_utils_conds::pro_is_installed();
	}
	$wcdp_data = get_plugin_data(__FILE__);
	$s2_enabled = in_array( 's2member/s2member.php',  $plugins_activated);
	$wdp_dir = plugin_dir_path(__FILE__) ;
	$pro_class = $wdp_dir.'classes/wdp_pro.php';
	$wdp_pro = file_exists($pro_class);

	$s2_discounts = get_option('wdp_s2member')?true:false;
	$wdp_discount_condition = get_option('woocommerce_plus_discount_condition', 'default');

	$wdp_new_price = (get_option( 'woocommerce_show_discounted_price' ) == 'yes' );
	$wdp_tiers_status = (get_option( 'woocommerce_tiers' ) == 'yes' );

	$wdp_new_price_sp = (get_option( 'woocommerce_show_discounted_price_sp' ) == 'yes' );
	$wdp_new_price_shop = (get_option( 'woocommerce_show_discounted_price_shop' ) == 'yes' );
	//pree($wdp_pro);

if ( !class_exists( 'wdp_core_factory' ) ) {
	abstract class wdp_core_factory
	{
		function __construct()
		{
		}
		abstract function gj_logic();
	}
}

if ( !class_exists( 'Woo_Discounts_Plus_Plugin' ) ) {

	class Woo_Discounts_Plus_Plugin extends wdp_core_factory {
		var $wdp_dir;
		var $opts = 6;
		var $discount_love;
		var $plus_discount_calculated = false;
		var $premium_link = 'http://shop.androidbubbles.com/product/woocommerce-discounts-plus';
		var $watch_tutorial = 'http://androidbubble.com/blog/wdp';
		var $s2member = 'http://androidbubble.com/blog/s2member';
		var $contact_developer = 'http://www.androidbubbles.com/contact';
		var $per_product;
		var $wdp_pro = false;
		var $display_dicounted_in_cart = true;
		var $discounted_items = array();
		var $currency = '';
		var $woocommerce_weight_unit;
		var $qty_total = 0;


		public function __construct() {
			parent::__construct();

			if(isset($_GET['debug'])){
				pre('applicables: '.wdp_woocommerce_discount_applicable());

				if(function_exists('WC')){
					$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
					pre(WC()->session);
					$chosen_shipping = $chosen_methods[0];
					pre($chosen_shipping);
				}


				exit;
			}

			if(!wdp_woocommerce_discount_applicable() && !is_admin()){

				return;
			}

			$this->opts = get_option('wcdp_criteria_no', $this->opts);

			global $wdp_pro, $pro_class;


			if($wdp_pro){
				$this->wdp_pro = true;
				//define('WDP_PER_PRODUCT', false);
				//include_once($pro_class);
			}else{
				//define('WDP_PER_PRODUCT', true);
			}

			//pree($wdp_pro);
			//$this->per_product = WDP_PER_PRODUCT;

			$this->current_tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general';

			$this->settings_tabs = array(
				'plus_discount' => __( 'Discounts Plus'.($this->wdp_pro?'+':''), 'wcdp' )
			);

			add_action( 'admin_enqueue_scripts', array( $this, 'wdp_enqueue_scripts_admin' ) );
			add_action( 'wp_head', array( $this, 'wdp_enqueue_scripts' ) );

			add_filter( 'plugin_wdp_links_' . plugin_basename( __FILE__ ), array( $this, 'wdp_links' ) );

			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array($this, 'wdp_plugin_links') );

			add_action( 'woocommerce_settings_tabs', array( $this, 'add_tab' ), 10 );

			// Run these actions when generating the settings tabs.
			foreach ( $this->settings_tabs as $name => $label ) {
				add_action( 'woocommerce_settings_tabs_' . $name, array( $this, 'settings_tab_action' ), 10 );
				add_action( 'woocommerce_update_options_' . $name, array( $this, 'save_settings' ), 10 );
			}

			// Add the settings fields to each tab.
			add_action( 'woocommerce_plus_discount_settings', array( $this, 'add_settings_fields' ), 10 );

			//add_action( 'woocommerce_loaded', array( $this, 'woocommerce_loaded' ) );
			 add_action( 'plugins_loaded', array( $this, 'woocommerce_loaded' ) );

		}

		/**
         * Main processing hooks
		 */
		public function woocommerce_loaded() {

			global $wdp_halt;

			if(is_user_logged_in()){
				$wdp_get_current_user_role = wdp_get_current_user_role();
				//pree($wdp_get_current_user_role);
				$woocommerce_user_roles = get_option( 'woocommerce_user_roles', array() );
				//pree($woocommerce_user_roles);
				$wdp_halt = (!empty($woocommerce_user_roles) && in_array($wdp_get_current_user_role, $woocommerce_user_roles));
			}

			//pree(is_user_logged_in().' - '.$wdp_halt);
			if($wdp_halt)
			return;

			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ))) && get_option( 'woocommerce_enable_plus_discounts', 'yes' ) == 'yes' ) {
				$this->currency = get_woocommerce_currency_symbol();
				$this->woocommerce_weight_unit = get_option('woocommerce_weight_unit');
			//if ( get_option( 'woocommerce_enable_plus_discounts', 'yes' ) == 'yes' ) {

				add_action( 'woocommerce_before_calculate_totals', array( $this, 'wdp_before_calculate' ), 10, 1 );
				add_action( 'woocommerce_calculate_totals', array( $this, 'wdp_after_calculate' ), 10, 1 );
				add_action( 'woocommerce_before_cart_table', array( $this, 'before_cart_table' ) );
				add_action( 'woocommerce_single_product_summary', array( $this, 'single_product_summary' ), 45 );
				add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'filter_subtotal_price' ), 10, 2 );
				add_filter( 'woocommerce_checkout_item_subtotal', array( $this, 'filter_subtotal_price' ), 10, 2 );
				add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'filter_subtotal_order_price' ), 10, 3 );
				//if($this->per_product){
					add_filter( 'woocommerce_product_write_panel_tabs', array( $this, 'wdp_product_write_panel_tabs' ) );
					add_filter( 'woocommerce_product_write_panels', array( $this, 'wdp_product_write_panels' ) );
					add_action( 'woocommerce_process_product_meta', array( $this, 'wdp_process_meta' ) );
				//}
				add_filter( 'woocommerce_cart_product_subtotal', array( $this, 'filter_cart_product_subtotal' ), 10, 3 );
				add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'order_update_meta' ) );


				if ( version_compare( WOOCOMMERCE_VERSION, "2.1.0" ) >= 0 ) {
					add_filter( 'woocommerce_cart_item_price', array( $this, 'filter_item_price' ), 10, 2 );
					add_filter( 'woocommerce_get_price_html', array( $this, 'filter_item_price_single' ), 10, 2 );
					//add_filter( 'woocommerce_cart_item_price', array( $this, 'filter_item_price' ), 10, 3 );

					add_filter( 'woocommerce_update_cart_validation', array( $this, 'filter_before_calculate' ), 10, 1 );
				} else {
					add_filter( 'woocommerce_cart_item_price_html', array( $this, 'filter_item_price' ), 10, 2 );
				}



				if($this->wdp_pro && class_exists('Woo_Discounts_Plus_Pro')){
					//$wdpp = new Woo_Discounts_Plus_Pro;
					//add_action( 'woocommerce_before_cart', array($wdpp, 'wdp_need_discount') );
				}else{
					$this->wdp_pro = false;
				}

			}

		}



		/**
		 * Add action links under WordPress > Plugins
		 *
		 * @param $links
		 * @return array
		 */
		public function wdp_links( $links ) {

			$settings_slug = 'woocommerce';

			if ( version_compare( WOOCOMMERCE_VERSION, "2.1.0" ) >= 0 ) {

				$settings_slug = 'wc-settings';

			}

			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=' . $settings_slug . '&tab=plus_discount' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}

		/**
		 * For given product, and quantity return the price modifying factor (percentage discount) or value to deduct (flat discount).
		 *
		 * @param $product_id
		 * @param $quantity
		 * @param $order
		 * @return float
		 */

		function gj_logic(){

			$ret = false;
			//Pro Feature
			return $ret;
		}

		protected function get_discounted_coeff( $product_id, $quantity, $composite_qty = 0 ) {

			pre($this->sale_applied());

			if(in_array($product_id, wc_get_product_ids_on_sale()) && !$this->sale_applied()){
				return 1;
			}

			global $s2_enabled, $s2_discounts;

			$fp = get_option( 'woocommerce_discount_type', '' );

			$s2member_discount = 0;

			if($s2_enabled && $s2_discounts){
				$s2member_discount = wdp_s2member_discount();
				$percentage = min( 1.0, max( 0, ( 100.0 - round( $s2member_discount, 2 ) ) / 100.0 ) );
				$return = ($fp=='flat' ? $s2member_discount : $percentage);

			//pree($s2_enabled);exit;
			//pree($s2_discounts);exit;
			}else{

				$this->discounted_items[] = $product_id;



				$q = array( 0.0 );
				$d = array( 0.0 );

				$plus_discount_enabled = plus_discount_enabled($product_id, true);
				//pree($plus_discount_enabled);

				switch($plus_discount_enabled){
					case 'default':
						$wdpq = get_option( 'wdp_qd', array() );//array();//
						//pree($wdpq);
					break;
					case 'category_based':
						$terms = get_the_terms( $product_id, 'product_cat' );
						$dc_cat_id = get_post_meta($product_id, 'dc_cat_id', true);
						foreach ($terms as $term) {
							if($term->term_id == $dc_cat_id){
								$product_cat_id = $term->term_id;
							}

						}
						$e_key = 'wdp_qd_'.$product_cat_id;

						if($product_cat_id>0)
						$wdpq = get_option( $e_key, array() );


					break;
				}

				//pree($wdpq);
				//pree($this->opts);
				for ( $i = 0; $i < $this->opts; $i++ ) {


					if($this->gj_logic()){
						//pre($this->qty_total);
						if($wdpq[$i]['q']>0){
							$qv = $wdpq[$i]['q'];
							$dv = $wdpq[$i]['d'];
							array_push( $q, $qv>0 ? $qv : 0.0 );
							array_push( $d, $dv>0 ? $dv : 0.0 );
						}

					}else{

						//if($this->per_product){
						//pree($wdpq);
						switch($plus_discount_enabled){
							case 'default':
							case 'category_based':
								//pree($wdpq[$i]['q']);
								if($wdpq[$i]['q']>0){
									//pree($wdpq[$i]);
									//pree($q);pree($d);
									//percentage with global
									$qv = $wdpq[$i]['q'];
									$dv = $wdpq[$i]['d'];

									array_push( $q, $qv>0 ? $qv : 0.0 );
									array_push( $d, $dv>0 ? $dv : 0.0 );
									//pree($q);pree($d);
								}else{

								}
							break;
							default:

								$qv = get_post_meta( $product_id, "plus_discount_quantity_$i", true );
								//pree($qv);
								array_push( $q,  $qv);

								if ( get_option( 'woocommerce_discount_type', '' ) == 'flat' ) {
									$dsf = get_post_meta( $product_id, "plus_discount_discount_flat_$i", true );
									//pre($i);
									//pre($d);
									//pree($dsf);
									array_push( $d,  $dsf? $dsf : 0.0 );
								} else {
									//percentage with per product
									$dv = get_post_meta( $product_id, "plus_discount_discount_$i", true );



									array_push( $d, $dv?$dv:0.0 );
								}
							break;
						}

					}
					//pree($q);pree($d);
					//pree($quantity);
					//pree($product_id);
					//pree($composite_qty);
					$compare_qty = $quantity;

					if(!empty($composite_qty) && array_key_exists($product_id, $composite_qty)){
						$compare_qty = $composite_qty[$product_id];
					}

					if ( $compare_qty >= $q[$i] && $q[$i] > $q[0] ) {
						$q[0] = $q[$i];
						$d[0] = $d[$i];
					}
				}

				//pree($q);pree($d);//exit;

				// for percentage discount convert the resulting discount from % to the multiplying coefficient
				$return = ( $fp == 'flat' ) ? max( 0, $d[0] ) : min( 1.0, max( 0, ( 100.0 - round( $d[0], 2 ) ) / 100.0 ) );

				//pree($return);

			}
			//pree(get_post_meta( $product_id ));
			//exit;
			//pree($return);//exit;
			return $return;

		}

		/**
		 * Filter product price so that the discount is visible.
		 *
		 * @param $price
		 * @param $values
		 * @return string
		 */


		public function get_product_discount($product_id){
			//return;
			global $wdp_tiers_status, $wdp_discount_types, $woocommerce_variations_separate, $product_variations_qty;

			//pree($product_id);
			//pree($wdp_discount_types);



			$applied_bracket = 0;
			$is_flat = (get_option( 'woocommerce_discount_type', '' ) == 'flat');
			$quantity_ordered = $this->discount_love[$product_id]['quantity'];
			//pre($quantity_ordered);
			$_pf = new WC_Product_Factory();
			$_product = $_pf->get_product($product_id);
			$get_parent_id = ($_product->get_parent_id());

			$composite_quantity = ((!empty($product_variations_qty) && array_key_exists($get_parent_id, $product_variations_qty))?$product_variations_qty[$get_parent_id]:0);


			//pree($product_variations_qty);
			//pree($_product->parent->get_id());//not working


			if ($_product instanceof WC_Product_Variation && $_product->parent ) {
				//pree($_product->parent);
				$pda = get_post_meta( $_product->parent->id );
				$wdp_discount_type = $wdp_discount_types[$_product->parent->id];
				//pree($_product->parent->id);
			}else{
				$pda = get_post_meta( $product_id );
				$wdp_discount_type = $wdp_discount_types[$product_id];
			}


			//pree($pda);
			$plus_discount_arr = array();
			if(!empty($pda)){
				foreach($pda as $k=>$v){
					//pree($k);pree($v);
					$pdq = substr($k, 0, strlen('plus_discount_quantity_'))=='plus_discount_quantity_';
					//pree($pdq);
					if(substr($k, 0, strlen('plus_discount_'))!='plus_discount_'){
						unset($pda[$k]);
					}elseif($pdq){
						$qty = current($v);
						//pre($qty);
						//pree($qty);
						if($qty>0){
							$ku = str_replace('plus_discount_quantity_', 'plus_discount_discount_'.($is_flat?'flat_':''), $k);
							//pree($ku);

							$vu = (isset($pda[$ku])?current($pda[$ku]):0);
							$plus_discount_arr[$qty] = $vu;

							//pree($quantity_ordered.' >= '.$qty.' ? '.$qty.' : '.$applied_bracket);

							$compare_quantity = ($composite_quantity?$composite_quantity:$quantity_ordered);

							$applied_bracket = $compare_quantity>=$qty?$qty:$applied_bracket;

							//pre($applied_bracket.' - '.$ab_inner);
							//$applied_bracket = $ab_inner;//($applied_bracket==0?$ab_inner:$applied_bracket);
						}
					}
				}

			}

			//pree($plus_discount_arr);
			//pree($applied_bracket);
			//pre($qty);

			$orig_price = $this->discount_love[$product_id]['orig_price'];
			$qty_price = round($orig_price*$quantity_ordered, 2);

			$applied_disc = (isset($plus_discount_arr[$applied_bracket])?$plus_discount_arr[$applied_bracket]:0);



			//pree($quantity_ordered.''.'>'.''.$applied_bracket);
			//pree($wdp_tiers_status);
			//pree($applied_bracket.' - '.$quantity_ordered.' - '.$applied_bracket.' - '.$wdp_tiers_status);

			//pree($applied_disc);

			if($applied_bracket>0 && $quantity_ordered>$applied_bracket && $wdp_tiers_status){
				//pree($applied_disc);
				$tiers = floor($quantity_ordered/$applied_bracket);
				//pree($tiers);
				$applied_disc *= $tiers;
				//pree($applied_disc);
			}
			//pre($plus_discount_arr);
			//pre($applied_bracket);
			//pree($applied_disc);

			if($is_flat){
				//pree($wdp_discount_type);

				switch($wdp_discount_type){

					case 'weight':

						$disc_price = $orig_price;
						$qty_disc_price = round(($orig_price*$quantity_ordered)-$applied_disc, 2);

					break;

					default:
					case 'quantity':
						//exit;
						$disc_price = $orig_price-$applied_disc;
						//pree($disc_price);
						//pree($applied_disc);
						//$qty_disc_price = round($disc_price*$quantity_ordered, 2);
						$qty_disc_price = round(($orig_price*$quantity_ordered)-$applied_disc, 2);

						//(($this->discount_love[$ac_id]['orig_price']*$values['quantity'])-$flat_discounted);
						//$applied_disc *= $quantity_ordered;
						//$applied_disc = round(($orig_price*$quantity_ordered)-$applied_disc, 2);
					break;

				}


				//pree($disc_price.' | '.$qty_disc_price.' | '.$applied_disc);

			}elseif($qty_price>0){
				$qty_disc_price = ($qty_price-$applied_disc);
				$disc_price = ($qty_disc_price/$quantity_ordered);

				//pree($qty_price.' - '.$applied_disc);
				//pree($qty_disc_price.' - '.$quantity_ordered);
				//pree($disc_price);

			}
			//pre($disc_price);
			//pre($qty_disc_price);

			$this->discount_love[$product_id]['disc_price'.($is_flat?'_flat':'')] = $disc_price;
			$this->discount_love[$product_id]['disc_price_qty'.($is_flat?'_flat':'')] = $qty_disc_price;
			$this->discount_love[$product_id]['disc_amount'.($is_flat?'_flat':'')] = $applied_disc;
			//pree($this->discount_love);


		}

		public function filter_item_price( $price, $values ) {

			//return $price;
			//exit;
			global $wdp_new_price;
			//pree($price);pree($values);exit;
			//pre($this->discounted_items);
			//pre($wdp_new_price);
			$is_flat = (get_option( 'woocommerce_discount_type', '' ) == 'flat');
			$_product = $values['data'];
			$ac_id = $this->get_product_id( $_product );
			$this->get_product_discount($ac_id);
			$coeff = $this->discount_love[$ac_id]['coeff'];
			//pree($coeff);
			//pree($values);
			if ( !$values || @!$values['data'] || in_array($_product->get_id(), wc_get_product_ids_on_sale()) || ($coeff==0 && !$is_flat)) {
				return $price;
			}

			if ( $this->coupon_check() ) {
				return $price;
			}

			//pre(__METHOD__);

			if (!plus_discount_enabled($ac_id)){//$_product->id)) {
				return $price;
			}

			/*if ( ( get_option( 'woocommerce_show_on_item', 'yes' ) == 'no' ) ) {
				return $price;
			}*/
			//pre(get_option( 'woocommerce_discount_type', '' ));
			if ($is_flat) {
				//return $price; // for flat discount this filter has no meaning
			}
			//pre($this->discount_love);
			if ( empty( $this->discount_love ) || !isset( $this->discount_love[$ac_id] )
				|| !isset( $this->discount_love[$ac_id]['orig_price'] ) || !isset( $this->discount_love[$ac_id]['coeff'] )
			) {
				$this->gather_discount_love();
			}

			if ( $coeff == 1.0 && !$is_flat) {
				return $price; // no price modification
			}

			//pree($values['quantity']);

			//pree($discprice);

			if ( $is_flat ) {
				$flat_less = $this->discount_love[$ac_id]['flat_less'];
				//pree($flat_less);
				//pree($this->discount_love);
				//exit;
				//pre($this->discount_love);
				//pree($flat_less);exit;
				//pree($this->discount_love[$ac_id]);
				$_regular_price = $this->discount_love[$ac_id]['orig_price'];
				$dprice = $_regular_price-$flat_less;
				//pree($dprice);
				$discprice = $oldprice = wdp_get_formatted_price( $dprice );
				if($flat_less>0){

					//pree($this->discount_love[$ac_id]);
					$discprice = wdp_get_formatted_price( round($this->discount_love[$ac_id]['disc_price_flat'], 2) );
					$oldprice = wdp_get_formatted_price( number_format((float)($this->discount_love[$ac_id]['orig_price']), 2) );
				}
					//pre($discprice);
					//pre($oldprice);
				//pree($discprice.' - Flat');

			}else{

				if(in_array($_product->get_id(), $this->discounted_items)){
					$discprice = wdp_get_formatted_price( number_format((float)$_product->get_price(), 2) );
				}else{

					$discprice = wdp_get_formatted_price( number_format((float)($_product->get_price() * $coeff), 2) );
				}

				$oldprice = wdp_get_formatted_price( number_format((float)($this->discount_love[$ac_id]['orig_price']), 2) );

				//pree($discprice.' - %');
			}

			//pree($discprice. '- Out');

			$old_css = esc_attr( get_option( 'woocommerce_css_old_price', 'color: #777; text-decoration: line-through; margin-right: 4px;' ) );

			$new_css = esc_attr( get_option( 'woocommerce_css_new_price', 'color: #4AB915; font-weight: bold;' ) );

			$np = $oldprice;
			/*if($discprice!=$oldprice){
				$np = "<span class='discount-info' title='" . sprintf( __( '%s%% Discounts Plus applied!', 'wcdp' ), round( ( 1.0 - $coeff ) * 100.0, 2 ) ) . "'>" .
			"<span class='old-price' style='$old_css' data-block='liza'>$oldprice</span>" .
			"<span class='new-price on' style='$new_css'>".$discprice."</span></span>";
			}else{
				$np = $oldprice;
			}*/
			//pree($this->discount_love);
			return ($wdp_new_price?$np:$oldprice);

		}


		/**
		 * Filter product price so that the discount is visible.
		 *
		 * @param $price
		 * @param $values
		 * @return string
		 */
		public function filter_subtotal_price( $price, $values ) {
			//pree($price);
			//return $price;
			//pre($price);pre($values);
			$is_flat = (get_option( 'woocommerce_discount_type', '' ) == 'flat');

			if ( !$values || !$values['data'] ) {
				return $price;
			}
			if ( $this->coupon_check() ) {
				return $price;
			}
			$_product = $values['data'];
			$ac_id = $this->get_product_id( $_product );
			//pre(__METHOD__);

			if (!plus_discount_enabled($ac_id)){//$_product->id)) {
				return $price;
			}


			if ( ( get_option( 'woocommerce_show_on_subtotal', 'yes' ) == 'no' ) ) {
				return $price;
			}

			if ( empty( $this->discount_love ) || !isset( $this->discount_love[$ac_id] )
				|| !isset( $this->discount_love[$ac_id]['orig_price'] ) || !isset( $this->discount_love[$ac_id]['coeff'] )
			) {
				$this->gather_discount_love();
			}

			$ac_id = $this->get_product_id( $_product );
			$coeff = $this->discount_love[$ac_id]['coeff'];

			$percent_discounted = '';
			//pree($this->discount_love);exit;
			//pree($coeff);




			if ( ( $is_flat && $coeff == 0 ) || ( get_option( 'woocommerce_discount_type', '' ) == '' && $coeff == 1.0 ) ) {


				return $price; // no price modification
			}

			$new_css = esc_attr( get_option( 'woocommerce_css_new_price', 'color: #4AB915; font-weight: bold;' ) );



			if($is_flat){
				//pree($this->discount_love);
				$flat_discounted = $this->discount_love[$ac_id]['disc_amount_flat'];//($this->discount_love[$ac_id]['disc_amount_flat']!=''?$this->discount_love[$ac_id]['disc_amount_flat']:$this->discount_love[$ac_id]['flat_less']);
				$flat_price = $this->discount_love[$ac_id]['disc_price_qty_flat'];//(($this->discount_love[$ac_id]['orig_price']*$values['quantity'])-$flat_discounted);
				$price = wdp_get_formatted_price($flat_price);
				//pree($flat_discounted.' | '.$flat_price.' | '.$price);
			}else{
				$percent_discounted = round( ( 1 - $coeff ) * 100, 2 );
			}

			//pree($percent_discounted);

			$plus_info = sprintf( __( '%s скидка', 'wcdp' ), ( $is_flat ? wdp_get_formatted_price($flat_discounted) : (  $percent_discounted. "%" ) ) );

			$show_w = "<span class='discount-info' title='$plus_info'>" .
			"<span>$price</span>" .
			"<span class='new-price tw' style='$new_css' data-block='loco'> ($plus_info)</span></span>";

			return $show_w;
		}



		/**
		 * Hook to woocommerce_cart_product_subtotal filter.
		 *
		 * @param $subtotal
		 * @param $_product
		 * @param $quantity
		 * @param WC_Cart $cart
		 * @return string
		 */
		public function filter_cart_product_subtotal( $subtotal, $_product, $quantity ) { //cart per line
			//pree($subtotal);
			//$this->discounted_items[] = 83;
			//return 0;
			//pre($subtotal);pre($_product);pre($quantity);
			//pre($this->discounted_items);
			$ac_id = $this->get_product_id( $_product );

			//pree($_product->get_id());

			if(in_array($_product->get_id(), $this->discounted_items))
			return $subtotal;

			//pree($subtotal);

			//pre($ac_id);exit;

			if ( !$_product || !$quantity ) {
				return $subtotal;
			}
			if ( $this->coupon_check() ) {
				return $subtotal;
			}
			pre(__METHOD__);

			//pree($subtotal);

			if (!plus_discount_enabled($ac_id)){//$_product->id)) {
				return $subtotal;
			}

			//pree($subtotal);

			$coeff = $this->discount_love[$ac_id]['coeff'];
			$is_flat = ( get_option( 'woocommerce_discount_type', '' ) == 'flat' );
			if ($is_flat) {
				$newsubtotal = wdp_get_formatted_price( max( 0, ( $_product->get_price() * $quantity ) - $coeff ) );
			} else {
				//pree($_product->get_price() .' * '. $quantity .' * '. $coeff);
				$newsubtotal = wdp_get_formatted_price( $_product->get_price() * $quantity );//* $coeff //fm 02/08/2018 as the price has already been multiplied by $coeff and now only qty multiplication required
			}
			//pree($newsubtotal);
			return $newsubtotal;

		}

		public function filter_item_price_single( $price_html, $product ) {

			global $woocommerce, $wdp_new_price_sp, $wdp_new_price_shop;

			if(!is_admin() && ((is_product() && $wdp_new_price_sp) || (is_shop() && $wdp_new_price_shop))){

				$items = $woocommerce->cart->get_cart();

				if(!empty($items)){

					foreach($items as $item => $values) {
						if($product->get_id()==$values['product_id']){
							$price_html = $this->filter_item_price($unit_price, $values);
						}
					}
				}

			}

			return $price_html;
		}

		/**
		 * Gather discount information to the array $this->discount_coefs
		 */
		protected function gather_discount_love() {



			global $woocommerce, $wdp_discount_types, $woocommerce_variations_separate, $product_variations_qty;



			$all_qty = array();
			$cart = $woocommerce->cart;
			$this->discount_love = (!empty($this->discount_love)?$this->discount_love:array());

			$is_flat = (get_option( 'woocommerce_discount_type', '' ) == 'flat');
			$plus_discount_type_globally = (get_option( 'woocommerce_plus_discount_type', 'quantity' ));

			if ( sizeof( $cart->cart_contents ) > 0 ) {

				//pre(count($cart->cart_contents));
				//pree($cart->cart_contents);
				//echo count($cart->cart_contents);exit;
				$q = 0;
				foreach ( $cart->cart_contents as $cart_item_key => $values ) { $q++;
					//pree($cart_item_key);
					//pree($values);
					$_product = $values['data'];
					$ac_id = $this->get_product_id( $_product );
					$actual_id = $this->get_actual_id( $_product );
					//pree($actual_id);
					$plus_discount_type = plus_discount_type($actual_id, true); //using actual product ID for global/product settings instead of each variation to avoid complexity
					$plus_discount_type = ($plus_discount_type==$plus_discount_type_globally?$plus_discount_type_globally:'quantity');
					$wdp_discount_types[$actual_id] = $plus_discount_type; //an extra item in this array to cover main/actual product ID
					$wdp_discount_types[$ac_id] = $plus_discount_type;

					$quantity = 0;
					//pree($_product instanceof WC_Product_Variation && $_product->parent);exit;


					if ($woocommerce_variations_separate == 'no' && $_product instanceof WC_Product_Variation && $_product->parent ) {
						$parent = $_product->parent;

						$plus_discount_type_inner = plus_discount_type($parent->id, true);
						$plus_discount_type_inner = ($plus_discount_type_inner==$plus_discount_type_inner?$plus_discount_type_globally:'quantity');
						//pree($plus_discount_type_inner);
						$wdp_discount_types[$parent->get_id()] = $plus_discount_type_inner;
						//pree($parent->get_id());
						//pree($ac_id);

						//
						//pree($parent->id);
						//pree($cart->cart_contents);exit;
						//echo count($cart->cart_contents);exit;
						//foreach ( $cart->cart_contents as $valuesInner ) { //pree($q);
							//pree($valuesInner);
							//$p = $valuesInner['data'];
							//if ( $p instanceof WC_Product_Variation && $p->parent && $p->parent->id == $parent->id ) {
								//pree($valuesInner['quantity']);//exit;
								//$quantity += $valuesInner['quantity'];
								//$quantity = $valuesInner['quantity'];
								//pree($values);
								$quantity = $values['quantity'];
								//pree($quantity);
								$this->discount_love[$_product->variation_id]['quantity'] = $quantity;

								//pree($_product->get_weight());
								switch($plus_discount_type_inner){
									default:
									case 'quantity':
										$quantity = $values['quantity'];
										$product_variations_qty[$parent->get_id()] += $quantity;
										$all_qty[] = $product_variations_qty[$parent->get_id()];
									break;
									case 'weight':
										$quantity = ($values['quantity']*$_product->get_weight());
										$all_qty[] = $quantity;
									break;
								}

								//pree($this->discount_love);
								//pree($all_qty);
								//pree($quantity);
							//}
						//}

					} else {
						switch($plus_discount_type){
							default:
							case 'quantity':
								$quantity = $values['quantity'];
							break;
							case 'weight':
								$quantity = ($values['quantity']*$_product->get_weight());
							break;
						}
						$all_qty[] = $quantity;
						//pree($quantity);
					}

					//pree($all_qty);
					//pree($product_variations_qty);
					$max = max($all_qty);
					//pree($max);
					//pree($this->opts);
					if($max>$this->opts)
					$this->opts = $max;

					//$ac_id = $this->get_product_id( $_product );
					//pre($ac_id);
					//pre($this->discount_love);
					//pree($this->gj_logic());
					//pree($this->opts);

					if($this->gj_logic()){
						$this->discount_love[$ac_id]['coeff'] = $this->get_discounted_coeff( $this->get_actual_id($_product), $this->qty_total ); //$_product->get_id()
					}else{
						//pree($this->get_actual_id($_product).' - '.$quantity);
						//pree($this->qty_total);
						$this->discount_love[$ac_id]['coeff'] = $this->get_discounted_coeff( $this->get_actual_id($_product), $quantity, $product_variations_qty); //$_product->get_id()
						//pree($this->get_product_id($_product).' - '.$this->get_actual_id($_product).' - '.$this->discount_love[$ac_id]['coeff']);
					}



					$this->discount_love[$ac_id]['orig_price'] = $_product->get_price();

					$this->discount_love[$ac_id]['quantity'] = $quantity;

					//pree($is_flat);

					if($is_flat){
						//pree($this->discount_love[$ac_id]);
						//pree($quantity);
						$flat_less = $this->discount_love[$ac_id]['coeff'];
						//pree($flat_less);
					}else{
						$flat_less = ((($quantity*$this->discount_love[$ac_id]['orig_price'])-$this->discount_love[$ac_id]['coeff'])/$quantity);
					}


					$this->discount_love[$ac_id]['flat_less'] = $flat_less;

					//pree($this->discount_love);
				}
				//pree($this->discount_love);exit;
				//exit;
			}

			//pree(max($all_qty));
			//pree($this->discount_love);

		}

		/**
		 * Filter product price so that the discount is visible during order viewing.
		 *
		 * @param $price
		 * @param $values
		 * @return string
		 */
		public function filter_subtotal_order_price( $price, $values, $order ) {
			//pree($price);
			//return $price;
			//pre($price);pre($values);pre($order);
			if ( !$values || !$order ) {
				return $price;
			}
			if ( $this->coupon_check() ) {
				return $price;
			}
			//pre($values);
			$_product = get_product( $values['product_id'] );
			//pre(__METHOD__);
			if (!plus_discount_enabled($values['product_id'])) {
				return $price;
			}
			if ( ( get_option( 'woocommerce_show_on_order_subtotal', 'yes' ) == 'no' ) ) {
				return $price;
			}
			$actual_id = $values['product_id'];
			if ( $_product && $_product instanceof WC_Product_Variable && $values['variation_id'] ) {
				$actual_id = $values['variation_id'];
			}
			$discount_love = $this->gather_discount_love_from_order( $order->id );
			if ( empty( $discount_love ) ) {
				return $price;
			}
			@$coeff = $discount_love[$actual_id]['coeff'];
			if ( !$coeff ) {
				return $price;
			}
			$discount_type = get_post_meta( $order->id, '_woocommerce_discount_type', true );
			if ( ( $discount_type == 'flat' && $coeff == 0 ) || ( $discount_type == '' && $coeff == 1.0 ) ) {
				return $price; // no price modification
			}
			$new_css = esc_attr( get_option( 'woocommerce_css_new_price', 'color: #4AB915; font-weight: bold;' ) );
			$plus_info = sprintf( __( 'With %s discount', 'wcdp' ), ( $discount_type == 'flat' ? wdp_get_formatted_price($coeff) : ( round( ( 1 - $coeff ) * 100, 2 ) . "%" ) ) );

			return "<span class='discount-info' title='$plus_info'>" .
			"<span>$price</span>" .
			"<span class='new-price th' style='$new_css' data-block='teco'> ($plus_info)</span></span>";

		}

		/**
		 * Gather discount information from order.
		 *
		 * @param $order_id
		 * @return array
		 */
		protected function gather_discount_love_from_order( $order_id ) {

			$meta = get_post_meta( $order_id, '_woocommerce_discount_love', true );

			if ( !$meta ) {
				return null;
			}

			$order_discount_love = json_decode( $meta, true );
			//pree($order_discount_love);
			return $order_discount_love;

		}

		/**
		 * Hook to woocommerce_before_calculate_totals action.
		 *
		 * @param WC_Cart $cart
		 */
		//CART TOTAL
		public function wdp_before_calculate( WC_Cart $cart ) {

			global $wdp_discount_types;
			if ( $this->coupon_check() ) {
				return;
			}

			if ($this->plus_discount_calculated) {
				return;
			}

			$this->gather_discount_love();

			if ( sizeof( $cart->cart_contents ) > 0 ) {

				foreach ( $cart->cart_contents as $cart_item_key => $values ) {
					$_product = $values['data'];
					$ac_id = $this->get_product_id( $_product );
					$this->get_product_discount($ac_id);
					pre(__METHOD__);
					if (!plus_discount_enabled($ac_id)){//$_product->id)) {
						continue;
					}

					$wdp_discount_type = $wdp_discount_types[$ac_id];

					$is_flat = (get_option( 'woocommerce_discount_type', '' ) == 'flat');
					if($is_flat){
						//pree($this->discount_love);
						switch($wdp_discount_type){

							case 'weight':


								$row_base_price = $this->discount_love[$ac_id]['disc_price_qty_flat']/$this->discount_love[$ac_id]['quantity'];
								//pree($row_base_price);
							break;

							case 'quantity':
								$coeff = $this->discount_love[$ac_id]['flat_less'];
								$orig_price = $this->discount_love[$ac_id]['orig_price'];
								//pree($this->discount_love);
								$row_base_price = $orig_price-$coeff;//$this->discount_love[$ac_id]['disc_price_flat'];//
								//pree($row_base_price);exit;
							break;
						}

						//pree($row_base_price);

					} else {
						//pree($_product->get_price() .' * '. $this->discount_love[$ac_id]['coeff']);
						$row_base_price = $_product->get_price() * $this->discount_love[$ac_id]['coeff'];
					}

					$values['data']->set_price( $row_base_price );
				}

				$this->plus_discount_calculated = true;

			}
			//pree($values);
		}

		public function filter_before_calculate( $res ) {

			global $woocommerce;
			//pree($res);
			if ($this->plus_discount_calculated) {
				return $res;
			}

			$cart = $woocommerce->cart;

			if ( $this->coupon_check() ) {
				return $res;
			}

			$this->gather_discount_love();

			if ( sizeof( $cart->cart_contents ) > 0 ) {
				pre(__METHOD__);
				foreach ( $cart->cart_contents as $cart_item_key => $values ) {
					$_product = $values['data'];
					$ac_id = $this->get_product_id( $_product );

					if (!plus_discount_enabled($ac_id)){//$_product->id)) {
						continue;
					}
					$is_flat = ( get_option( 'woocommerce_discount_type', '' ) == 'flat' );
					if ($is_flat) {
						$row_base_price = max( 0, $_product->get_price() - ( $this->discount_love[$ac_id]['coeff'] / $values['quantity'] ) );
					} else {
						$row_base_price = $_product->get_price() * $this->discount_love[$ac_id]['coeff'];
					}

					//pree($row_base_price );
					$values['data']->set_price( $row_base_price );
					//pree($values);
				}

				$this->plus_discount_calculated = true;

			}

			//pree($res);


			return $res;

		}

		/**
		 * @param $product
		 * @return int
		 */
		protected function get_actual_id( $product ) {
			$ret = 0;
			if(isset($product->parent_id) && $product->parent_id>0){
				$ret = $product->parent_id;
			}else{
				$ret = $this->get_product_id( $product );
			}
			return $ret;
		}
		protected function get_product_id( $product ) {

			if ( $product instanceof WC_Product_Variation ) {
				return $product->variation_id;
			} elseif(method_exists($product, 'get_id')) {
				return $product->get_id();
			}elseif(isset($product->id)){
				return $product->id;
			}else{
				//LEAVING THIS SECTION EMPTY FOR NOW
				return 0;
			}

		}

		/**
		 * Hook to woocommerce_calculate_totals.
		 *
		 * @param WC_Cart $cart
		 */
		public function wdp_after_calculate( WC_Cart $cart ) {

			//pree($cart);
			//return;
			if ( $this->coupon_check() ) {
				return;
			}

			if ( sizeof( $cart->cart_contents ) > 0 ) {
				pre(__METHOD__);
				foreach ( $cart->cart_contents as $cart_item_key => $values ) {
					$_product = $values['data'];
					$ac_id = $this->get_product_id( $_product );
					if (!plus_discount_enabled($ac_id)){//$_product->id)) {
						continue;
					}
					//pree($values);
					//pree($_product);

					if($this->display_dicounted_in_cart){
					}else{
						$values['data']->set_price( $this->discount_love[$ac_id]['orig_price'] );
					}
					//pree($this->discount_love[$ac_id]['orig_price']);
				}
			}

			//pree($this->discount_love);

		}

		/**
		 * Show discount info in cart.
		 */
		public function before_cart_table() {

			if ( get_option( 'woocommerce_cart_info' ) != '' ) {
				echo "<div class='cart-show-discounts'>";
				echo get_option( 'woocommerce_cart_info' );
				echo "</div>";
			}

		}




		/**
		 * Store discount info in order as well
		 *
		 * @param $order_id
		 */
		public function order_update_meta( $order_id ) {

			update_post_meta( $order_id, "_woocommerce_discount_type", get_option( 'woocommerce_discount_type', '' ) );
			update_post_meta( $order_id, "_woocommerce_discount_love", json_encode( sanitize_wdp_data($this->discount_love) ) );

		}

		/**
		 * Display discount information in Product Detail.
		 */
		public function single_product_summary() {

			global $thepostid, $post;
			if ( !$thepostid ) $thepostid = $post->ID;

			echo "<div class='productinfo-show-discounts'>";
			echo get_post_meta( $thepostid, 'plus_discount_text_info', true );
			echo "</div>";

		}

		/**
		 * Add entry to Product Settings.
		 */
		public function wdp_product_write_panel_tabs() {

			$style = '';

			if ( version_compare( WOOCOMMERCE_VERSION, "2.1.0" ) >= 0 ) {
				$style = 'style = "padding: 10px !important"';
			}

			echo '<li class="discounts_plus_tab discounts_plus_options"><a href="#discounts_plus_product_data" '.$style.'><span>' . __( 'Discounts Plus'. ($this->wdp_pro?'+':''), 'wcdp' ) . '</span></a></li>';

		}


		public function wdp_global_panels() {



			?>
			<script type="text/javascript">
				jQuery( document ).ready( function () {
					var e = jQuery( '#discounts_plus_product_data' );
					<?php
					for($i = 1; $i <= $this->opts; $i++) :
					?>
					e.find( '.block<?php echo $i; ?>' ).hide();
					e.find( '.options_group<?php echo max($i, 2); ?>' ).hide();
					e.find( '#def_disc_criteria<?php echo max($i, 2); ?>' ).hide();
					e.find( '#def_disc_criteria<?php echo $i; ?>' ).click( function () {
						/*if ( <?php echo $i; ?> == 1 || ( e.find( '#plus_discount_quantity_<?php echo max($i-1, 1); ?>' ).val() != '' &&
							<?php if ( get_option( 'woocommerce_discount_type', '' ) == 'flat' ) : ?>
							e.find( '#plus_discount_discount_flat_<?php echo max($i-1, 1); ?>' ).val() != ''
						<?php else: ?>
						e.find( '#plus_discount_discount_<?php echo max($i-1, 1); ?>' ).val() != ''
						<?php endif; ?>
						) )
						{*/
							e.find( '.block<?php echo $i; ?>' ).show();
							e.find( '.options_group<?php echo min($i+1, $this->opts); ?>' ).show();
							e.find( '#def_disc_criteria<?php echo min($i+1, ($this->opts-1)); ?>' ).show();
							e.find( '#def_disc_criteria<?php echo $i; ?>' ).hide( );
							e.find( '#delete_discount_line<?php echo min($i+1, $this->opts); ?>' ).show();
							e.find( '#delete_discount_line<?php echo $i; ?>' ).hide( );
						/*}
						else
						{
							alert( '<?php _e( 'Please fill in the current line before adding new line.', 'wcdp' ); ?>' );
						}*/
					} );
					e.find( '#delete_discount_line<?php echo max($i, 1); ?>' ).hide();
					e.find( '#delete_discount_line<?php echo $i; ?>' ).click( function () {
						e.find( '.block<?php echo max($i-1, 1); ?>' ).hide( );
						e.find( '.options_group<?php echo min($i, $this->opts); ?>' ).hide( );
						e.find( '#def_disc_criteria<?php echo min($i, ($this->opts-1)); ?>' ).hide( );
						e.find( '#def_disc_criteria<?php echo max($i-1, 1); ?>' ).show();
						e.find( '#delete_discount_line<?php echo min($i, $this->opts); ?>' ).hide( );
						e.find( '#delete_discount_line<?php echo max($i-1, 2); ?>' ).show();
						e.find( '#plus_discount_quantity_<?php echo max($i-1, 1); ?>' ).val( '' );
						<?php
							if ( get_option( 'woocommerce_discount_type', '' ) == 'flat' ) :
						?>
						e.find( '#plus_discount_discount_flat_<?php echo max($i-1, 1); ?>' ).val( '' );
						<?php else: ?>
						e.find( '#plus_discount_discount_<?php echo max($i-1, 1); ?>' ).val( '' );
						<?php endif; ?>
					} );
					<?php
					endfor;
					for ($i = 1, $j = 2; $i < $this->opts; $i++, $j++) {
						$cnt = 1;
						if (get_post_meta($thepostid, "plus_discount_quantity_$i", true) || get_post_meta($thepostid, "plus_discount_quantity_$j", true)) {
							?>
					e.find( '.block<?php echo $i; ?>' ).show();
					e.find( '.options_group<?php echo $i; ?>' ).show();
					e.find( '#def_disc_criteria<?php echo $i; ?>' ).hide();
					e.find( '#delete_discount_line<?php echo $i; ?>' ).hide();
					e.find( '.options_group<?php echo min($i+1, $this->opts); ?>' ).show();
					e.find( '#def_disc_criteria<?php echo min($i+1, $this->opts); ?>' ).show();
					e.find( '#delete_discount_line<?php echo min($i+1, $this->opts); ?>' ).show();
					<?php
					$cnt++;
				}
			}
			if ($cnt >= $this->opts) {
				?>e.find( '#def_disc_criteria<?php echo $this->opts; ?>' ).show();
					<?php
			}
			?>
				} );
			</script>

			<div id="discounts_plus_product_data" class="panel woocommerce_options_panel">

				<div class="options_group">
					<?php

					woocommerce_wp_checkbox( array( 'id' => 'plus_discount_enabled', 'value' => plus_discount_enabled($thepostid) ? 'yes': get_post_meta( $thepostid, 'plus_discount_enabled', true ) , 'label' => __( 'Discounts Plus enabled', 'wcdp' ) ) );
					woocommerce_wp_textarea_input( array( 'id' => "plus_discount_text_info", 'label' => __( 'Discounts Plus special offer text in product description', 'wcdp' ), 'description' => __( 'Optionally enter Discounts Plus information that will be visible on the product page.', 'wcdp' ), 'desc_tip' => 'yes', 'class' => 'fullWidth' ) );
					?>
				</div>

				<?php
				for ( $i = 1;
				      $i < $this->opts;
				      $i++ ) :
					?>

					<div class="options_group<?php echo $i; ?>">
						<a id="def_disc_criteria<?php echo $i; ?>" class="button-secondary"
						   href="#block<?php echo $i; ?>"><?php _e( 'Define discount criteria', 'wcdp' ); ?></a>
						<a id="delete_discount_line<?php echo $i; ?>" class="button-secondary"
						   href="#block<?php echo $i; ?>"><?php _e( 'Remove discount criteria', 'wcdp' ); ?></a>

						<div class="block<?php echo $i; ?> <?php echo ( $i % 2 == 0 ) ? 'even' : 'odd' ?>">
							<?php
							woocommerce_wp_text_input( array( 'id' => "plus_discount_quantity_$i", 'label' => __( 'Quantity (min.)', 'wcdp' ), 'type' => 'number', 'description' => __( 'Quantity on which the discount criteria will apply?', 'wcdp' ), 'custom_attributes' => array(
								'step' => '1',
								'min' => '1'
							) ) );
							if ( get_option( 'woocommerce_discount_type', '' ) == 'flat' ) {
								woocommerce_wp_text_input( array( 'id' => "plus_discount_discount_flat_$i", 'type' => 'number', 'label' => sprintf( __( 'Discount (%s)', 'wcdp' ), $this->currency ), 'description' => sprintf( __( 'Enter the flat discount in %s.', 'wcdp' ), $this->currency ), 'custom_attributes' => array(
									'step' => 'any',
									'min' => '0'
								) ) );
							} else {
								woocommerce_wp_text_input( array( 'id' => "plus_discount_discount_$i", 'type' => 'number', 'label' => __( 'Discount (%)', 'wcdp' ), 'description' => __( 'Discount percentage (Range: 0 to 100).', 'wcdp' ), 'custom_attributes' => array(
									'step' => 'any',
									'min' => '0',
									'max' => '100'
								) ) );
							}
							?>
						</div>
					</div>

				<?php
				endfor;
				?>

				<div class="options_group<?php echo $this->opts; ?>">
					<a id="delete_discount_line<?php echo $this->opts; ?>" class="button-secondary"
					   href="#block<?php echo $this->opts; ?>"><?php _e( 'Remove discount criteria', 'wcdp' ); ?></a>
				</div>

				<br/>

			</div>

		<?php
		}
		/**
		 * Add entry content to Product Settings.
		 */
		public function wdp_product_write_panels() {

			global $thepostid, $post, $wdp_pro;

			if ( !$thepostid ) $thepostid = $post->ID;

			$_product = wc_get_product( $thepostid );
			$variations = (method_exists($_product,'get_available_variations')?$_product->get_available_variations():array());
			//pre($variations);exit;
			?>
			<script type="text/javascript">
				jQuery( document ).ready( function () {
					var e = jQuery( '#discounts_plus_product_data' );
					<?php
					for($i = 1; $i <= $this->opts; $i++) :
					?>
					e.find( '.block<?php echo $i; ?>' ).hide();
					e.find( '.options_group<?php echo max($i, 2); ?>' ).hide();
					e.find( '#def_disc_criteria<?php echo max($i, 2); ?>' ).hide();
					e.find( '#def_disc_criteria<?php echo $i; ?>' ).click( function () {
						/*if ( <?php echo $i; ?> == 1 || ( e.find( '#plus_discount_quantity_<?php echo max($i-1, 1); ?>' ).val() != '' &&
							<?php if ( get_option( 'woocommerce_discount_type', '' ) == 'flat' ) : ?>
							e.find( '#plus_discount_discount_flat_<?php echo max($i-1, 1); ?>' ).val() != ''
						<?php else: ?>
						e.find( '#plus_discount_discount_<?php echo max($i-1, 1); ?>' ).val() != ''
						<?php endif; ?>
						) )
						{*/
							e.find( '.block<?php echo $i; ?>' ).show();
							e.find( '.options_group<?php echo min($i+1, $this->opts); ?>' ).show();
							e.find( '#def_disc_criteria<?php echo min($i+1, ($this->opts-1)); ?>' ).show();
							e.find( '#def_disc_criteria<?php echo $i; ?>' ).hide( );
							e.find( '#delete_discount_line<?php echo min($i+1, $this->opts); ?>' ).show();
							e.find( '#delete_discount_line<?php echo $i; ?>' ).hide( );
						/*}
						else
						{
							alert( '<?php _e( 'Please fill in the current line before adding new line.', 'wcdp' ); ?>' );
						}*/
					} );
					e.find( '#delete_discount_line<?php echo max($i, 1); ?>' ).hide();
					e.find( '#delete_discount_line<?php echo $i; ?>' ).click( function () {
						e.find( '.block<?php echo max($i-1, 1); ?>' ).hide( );
						e.find( '.options_group<?php echo min($i, $this->opts); ?>' ).hide( );
						e.find( '#def_disc_criteria<?php echo min($i, ($this->opts-1)); ?>' ).hide( );
						e.find( '#def_disc_criteria<?php echo max($i-1, 1); ?>' ).show();
						e.find( '#delete_discount_line<?php echo min($i, $this->opts); ?>' ).hide( );
						e.find( '#delete_discount_line<?php echo max($i-1, 2); ?>' ).show();
						e.find( '#plus_discount_quantity_<?php echo max($i-1, 1); ?>' ).val( '' );
						<?php
							if ( get_option( 'woocommerce_discount_type', '' ) == 'flat' ) :
						?>
						e.find( '#plus_discount_discount_flat_<?php echo max($i-1, 1); ?>' ).val( '' );
						<?php else: ?>
						e.find( '#plus_discount_discount_<?php echo max($i-1, 1); ?>' ).val( '' );
						<?php endif; ?>
					} );
					<?php
					endfor;
					for ($i = 1, $j = 2; $i < $this->opts; $i++, $j++) {
						$cnt = 1;
						if (get_post_meta($thepostid, "plus_discount_quantity_$i", true) || get_post_meta($thepostid, "plus_discount_quantity_$j", true)) {
							?>
					e.find( '.block<?php echo $i; ?>' ).show();
					e.find( '.options_group<?php echo $i; ?>' ).show();
					e.find( '#def_disc_criteria<?php echo $i; ?>' ).hide();
					e.find( '#delete_discount_line<?php echo $i; ?>' ).hide();
					e.find( '.options_group<?php echo min($i+1, $this->opts); ?>' ).show();
					e.find( '#def_disc_criteria<?php echo min($i+1, $this->opts); ?>' ).show();
					e.find( '#delete_discount_line<?php echo min($i+1, $this->opts); ?>' ).show();
					<?php
					$cnt++;
				}
			}
			if ($cnt >= $this->opts) {
				?>e.find( '#def_disc_criteria<?php echo $this->opts; ?>' ).show();
					<?php
			}
			?>
				} );
			</script>

			<div id="discounts_plus_product_data" class="panel woocommerce_options_panel">

				<div class="options_group">
					<?php
					//woocommerce_wp_checkbox( array( 'id' => 'plus_discount_enabled', 'value' => plus_discount_enabled($thepostid)?'yes':get_post_meta( $thepostid, 'plus_discount_enabled', true ), 'label' => __( 'Discounts Plus enabled', 'wcdp' ) ) );
					//echo plus_discount_enabled($thepostid);
					$pf = (!$wdp_pro?'':' - (Premium Feature)');
					woocommerce_wp_radio(array( 'id' => 'plus_discount_enabled', 'value' => plus_discount_enabled($thepostid, true), 'label' => __( 'Discounts Plus enabled', 'wcdp' ), 'options' => array('default' => 'Default (Global Settings)'.$pf, 'category_based' => 'Category based discount criteria'.$pf, 'yes' => 'Product based (Use criteria defined below)', 'no' => 'No') ));
					woocommerce_wp_radio(array( 'id' => 'plus_discount_type', 'value' => plus_discount_type($thepostid, true), 'label' => __( 'Discounts Plus Type', 'wcdp' ), 'options' => array('quantity' => 'Quantity (Default)'.$pf, 'weight' => 'Weight'.$pf) ));
					if(!empty($variations)){
						$plus_discount_excluding = get_post_meta($thepostid, 'plus_discount_excluding', true);
						//pree($plus_discount_excluding);
						$var_options = array('' => 'None');
						foreach($variations as $var_atts){
							$variation = wc_get_product($var_atts['variation_id']);
							$var_atts['sku'] = ($var_atts['sku']!=''?$var_atts['sku'].' - ':'');
							$var_options[$var_atts['variation_id']] = '#'.$var_atts['variation_id'].' - '.$variation->get_title().' - '.$var_atts['sku'].wdp_get_formatted_price($var_atts['display_price']);
						}

						woocommerce_wp_select_multiple(array( 'id' => 'plus_discount_excluding', 'value' => $plus_discount_excluding, 'label' => __( 'Excluding Variations', 'wcdp' ), 'options' => $var_options ));

					}
					woocommerce_wp_textarea_input( array( 'id' => "plus_discount_text_info", 'label' => __( 'Discounts Plus special offer text in product description', 'wcdp' ), 'description' => __( 'Optionally enter Discounts Plus information that will be visible on the product page.', 'wcdp' ), 'desc_tip' => 'yes', 'class' => 'fullWidth' ) );

					if(plus_discount_enabled($thepostid, true)=='yes')
					woocommerce_wp_radio(array( 'id' => 'plus_discount_product_display', 'value' => plus_discount_product_display($thepostid, true), 'label' => __( 'Discounts display on product page', 'wcdp' ), 'options' => array('no' => 'Turn OFF', 'yes' => 'Turn ON') ));
					?>
				</div>

				<?php
				for ( $i = 1;
				      $i < $this->opts;
				      $i++ ) :
					?>

					<div class="options_group<?php echo $i; ?>">
						<a id="def_disc_criteria<?php echo $i; ?>" class="button-secondary"
						   href="#block<?php echo $i; ?>"><?php _e( 'Define discount criteria', 'wcdp' ); ?></a>
						<a id="delete_discount_line<?php echo $i; ?>" class="button-secondary"
						   href="#block<?php echo $i; ?>"><?php _e( 'Remove discount criteria', 'wcdp' ); ?></a>

						<div class="block<?php echo $i; ?> <?php echo ( $i % 2 == 0 ) ? 'even' : 'odd' ?>">
							<?php
							woocommerce_wp_text_input( array( 'id' => "plus_discount_quantity_$i", 'label' => __( 'Quantity (min.)', 'wcdp' ), 'type' => 'number', 'description' => __( 'Quantity on which the discount criteria will apply?', 'wcdp' ), 'custom_attributes' => array(
								'step' => '1',
								'min' => '1'
							) ) );
							if ( get_option( 'woocommerce_discount_type', '' ) == 'flat' ) {
								woocommerce_wp_text_input( array( 'id' => "plus_discount_discount_flat_$i", 'type' => 'number', 'label' => sprintf( __( 'Discount (%s)', 'wcdp' ), $this->currency ), 'description' => sprintf( __( 'Enter the flat discount in %s.', 'wcdp' ), $this->currency ), 'custom_attributes' => array(
									'step' => 'any',
									'min' => '0'
								) ) );
							} else {
								woocommerce_wp_text_input( array( 'id' => "plus_discount_discount_$i", 'type' => 'number', 'label' => __( 'Discount (%)', 'wcdp' ), 'description' => __( 'Discount percentage (Range: 0 to 100).', 'wcdp' ), 'custom_attributes' => array(
									'step' => 'any',
									'min' => '0',
									'max' => '100'
								) ) );
							}
							?>
						</div>
					</div>

				<?php
				endfor;
				?>

				<div class="options_group<?php echo $this->opts; ?>">
					<a id="delete_discount_line<?php echo $this->opts; ?>" class="button-secondary"
					   href="#block<?php echo $this->opts; ?>"><?php _e( 'Remove discount criteria', 'wcdp' ); ?></a>
				</div>

				<br/>

			</div>

		<?php
		}

		/**
		 * Enqueue frontend dependencies.
		 */
		public function wdp_enqueue_scripts() {

			wp_enqueue_style( 'woocommercediscounts_plus-style', plugins_url( 'css/style.css', __FILE__ ), array(), time() );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script(
				'wdp-scripts',
				plugins_url('js/scripts.js', __FILE__),
				array('jquery')
			);


		}

		/**
		 * Enqueue backend dependencies.
		 */
		public function wdp_enqueue_scripts_admin() {

			global $wdpp_obj;

			wp_enqueue_style( 'woocommercediscounts_plus-style-admin', plugins_url( 'css/admin.css', __FILE__ ), array(), time() );


			wp_enqueue_script(
				'wdp-scripts',
				plugins_url('js/admin.js', __FILE__),
				array('jquery'),
				date('Yhi')
			);

			$wdp_obj = array('woocommerce_weight_unit' => $this->woocommerce_weight_unit);
			wp_localize_script( 'wdp-scripts', 'wdp_obj', $wdp_obj );

			$scripts_array = array(
				'sale_applied' => ($wdpp_obj->sale_applied()?'true':'false')
			);
			wp_localize_script( 'wdp-scripts', 'wcdp_obj', $scripts_array );

		}

		/**
		 * Updating post meta.
		 *
		 * @param $post_id
		 */
		public function wdp_process_meta( $post_id ) {

			if ( isset( $_POST['plus_discount_text_info'] ) ) update_post_meta( $post_id, 'plus_discount_text_info', stripslashes( sanitize_wdp_data($_POST['plus_discount_text_info']) ) );

			if ( isset( $_POST['plus_discount_enabled'] ) && $_POST['plus_discount_enabled'] != '' ) {
				update_post_meta( $post_id, 'plus_discount_enabled', stripslashes( sanitize_wdp_data($_POST['plus_discount_enabled']) ) );
			}
			if ( isset( $_POST['plus_discount_product_display'] ) && $_POST['plus_discount_product_display'] != '' ) {
				update_post_meta( $post_id, 'plus_discount_product_display', stripslashes( sanitize_wdp_data($_POST['plus_discount_product_display']) ) );
			}

			if ( isset( $_POST['plus_discount_excluding'] ) && !empty($_POST['plus_discount_excluding']) ) {
				update_post_meta( $post_id, 'plus_discount_excluding', sanitize_wdp_data($_POST['plus_discount_excluding']) );
				//pree($_POST);exit;
			}
			if ( isset( $_POST['plus_discount_type'] ) && $_POST['plus_discount_type'] != '' ) {
				update_post_meta( $post_id, 'plus_discount_type', stripslashes( sanitize_wdp_data($_POST['plus_discount_type']) ) );
			}


			for ( $i = 1; $i < $this->opts; $i++ ) {
				if ( isset( $_POST["plus_discount_quantity_$i"] ) ) update_post_meta( $post_id, "plus_discount_quantity_$i", stripslashes( sanitize_wdp_data($_POST["plus_discount_quantity_$i"]) ) );
				if ( ( get_option( 'woocommerce_discount_type', '' ) == 'flat' ) ) {
					if ( isset( $_POST["plus_discount_discount_flat_$i"] ) ) update_post_meta( $post_id, "plus_discount_discount_flat_$i", stripslashes( sanitize_wdp_data($_POST["plus_discount_discount_flat_$i"]) ) );
				} else {
					if ( isset( $_POST["plus_discount_discount_$i"] ) ) update_post_meta( $post_id, "plus_discount_discount_$i", stripslashes( sanitize_wdp_data($_POST["plus_discount_discount_$i"]) ) );
				}
			}

		}

		/**
		 * @access public
		 * @return void
		 */
		public function add_tab() {

			$settings_slug = 'woocommerce';

			if ( version_compare( WOOCOMMERCE_VERSION, "2.1.0" ) >= 0 ) {

				$settings_slug = 'wc-settings';

			}

			foreach ( $this->settings_tabs as $name => $label ) {
				$class = 'nav-tab';
				if ( $this->current_tab == $name )
					$class .= ' nav-tab-active';
				echo '<a href="' . admin_url( 'admin.php?page=' . $settings_slug . '&tab=' . $name ) . '" class="' . $class . '">' . $label . '</a>';
			}

		}

		/**
		 * @access public
		 * @return void
		 */
		public function settings_tab_action() {

			global $woocommerce_settings;

			// Determine the current tab in effect.
			$current_tab = $this->get_tab_in_view( current_filter(), 'woocommerce_settings_tabs_' );

			do_action( 'woocommerce_plus_discount_settings' );

			// Display settings for this tab (make sure to add the settings to the tab).
			woocommerce_admin_fields( $woocommerce_settings[$current_tab] );

		}

		/**
		 * Save settings in a single field in the database for each tab's fields (one field per tab).
		 */
		public function save_settings() {

			global $woocommerce_settings;

			// Make sure our settings fields are recognised.
			$this->add_settings_fields();

			$current_tab = $this->get_tab_in_view( current_filter(), 'woocommerce_update_options_' );
			woocommerce_update_options( $woocommerce_settings[$current_tab] );

		}

		/**
		 * Get the tab current in view/processing.
		 */
		public function get_tab_in_view( $current_filter, $filter_base ) {

			return str_replace( $filter_base, '', $current_filter );

		}


		/**
		 * Add settings fields for each tab.
		 */
		public function add_settings_fields() {
			global $woocommerce_settings;

			// Load the prepared form fields.
			$this->init_form_fields();

			if ( is_array( $this->fields ) )
				foreach ( $this->fields as $k => $v )
					$woocommerce_settings[$k] = $v;
		}

		/**
		 * Prepare form fields to be used in the various tabs.
		 */
		public function init_form_fields() {
			global $woocommerce, $s2_enabled, $wcdp_data;

			$default_settings =  array(

				array( 'name' => __( 'Discounts Plus'.($this->wdp_pro?'+':''), 'wcdp' ), 'type' => 'title', 'desc' => __( 'The following options are specific to product Discounts Plus.', 'wcdp' ) . '<br /><br/><strong><i>' . __( 'After changing the settings, it is recommended to clear all sessions in WooCommerce &gt; <a href="admin.php?page=wc-status">System Status</a> &gt; <a href="admin.php?page=wc-status&tab=tools">Tools</a>.', 'wcdp' ) . '</i></strong>', 'id' => 'wdpplus_discounts_options' ),

				array(
					'name' => __( 'Discounts Plus globally enabled', 'wcdp' ),
					'id' => 'woocommerce_enable_plus_discounts',
					'desc' => __( 'This option will be overridden by specific product(on page) settings.', 'wcdp' ),
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => 'yes'
				),

				array(
					'title' => __( 'Ignore User Roles', 'wcdp' ),
					'id' => 'woocommerce_user_roles',
					'desc' => sprintf( __( 'Select the user roles which you want to ignore for discounts. Multiple selection is possible by holding ctrl key.', 'wcdp' )),
					'desc_tip' => true,
					'std' => 'yes',
					'type' => 'multiselect',
					'css' => 'min-width:200px;',
					'options' => wdp_get_user_roles()
				),
				array(
					'title' => __( 'Discount Type', 'wcdp' ),
					'id' => 'woocommerce_discount_type',
					'desc' => sprintf( __( 'Select the type of discount. Percentage Discount deducts amount of %% from price while Flat Discount deducts fixed amount in %s', 'wcdp' ), $this->currency ),
					'desc_tip' => true,
					'std' => 'yes',
					'type' => 'select',
					'css' => 'min-width:200px;',
					'class' => 'chosen_select',
					'options' => array(
						'' => __( 'Percentage Discount', 'wcdp' ),
						'flat' => __( 'Flat Discount', 'wcdp' )
					)
				),

				array(
					'name' => __( 'Treat product variations separately', 'wcdp' ),
					'id' => 'woocommerce_variations_separate',
					'desc' => __( 'You need to have this option unchecked to apply discounts to variations by shared quantity.', 'wcdp' ),
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => 'yes'
				),

				array(
					'name' => __( 'No effect if a coupon code is applied', 'wcdp' ),
					'id' => 'woocommerce_remove_discount_on_coupon',
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => 'yes'
				),
				array(
					'name' => __( 'No effect if product and/or variation already on SALE!', 'wcdp' ),
					'id' => 'woocommerce_discount_on_sale',
					'desc' => 'By default products on sale are excluded from discount criteria.',
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => 'yes'
				),

				array(
					'name' => __( 'Apply same discount on next multiples?', 'wcdp' ),
					'id' => 'woocommerce_tiers',
					'desc' => __( 'e.g. Qty. 10 gets $1 discount so Qty. 20 will get discount of $2.', 'wcdp' ),
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => 'no'
				),

				array(
					'name' => __( 'Show discount information next to item subtotal price', 'wcdp' ),
					'id' => 'woocommerce_show_on_subtotal',
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => 'yes'
				),

				array(
					'name' => __( 'Show discount information next to item subtotal price in order history', 'wcdp' ),
					'id' => 'woocommerce_show_on_order_subtotal',
					'desc' => __( 'Includes showing discount in order e-mails and invoices.', 'wcdp' ),
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => 'yes'
				),

				array(
					'name' => __( 'Optionally enter information about discounts visible on cart page.', 'wcdp' ),
					'id' => 'woocommerce_cart_info',
					'type' => 'textarea',
					'css' => 'width:100%; height: 75px;'
				),

				array(
					'name' => __( 'Show discounted price in cart view', 'wcdp' ),
					'id' => 'woocommerce_show_discounted_price',
					'desc' => __( 'Display the changed value of price with a line-through on original price.', 'wcdp' ),
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => 'yes'
				),
				array(
					'name' => __( 'Show discounted price on single product page', 'wcdp' ),
					'id' => 'woocommerce_show_discounted_price_sp',
					'desc' => __( 'Display the changed value of price with a line-through on original price.', 'wcdp' ),
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => ''
				),
				array(
					'name' => __( 'Show discounted price in shop or products list', 'wcdp' ),
					'id' => 'woocommerce_show_discounted_price_shop',
					'desc' => __( 'Display the changed value of price with a line-through on original price.', 'wcdp' ),
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => ''
				),


				array(
					'name' => __( 'Optionally change the CSS for old price on cart before discounting.', 'wcdp' ),
					'id' => 'woocommerce_css_old_price',
					'type' => 'textarea',
					'css' => 'width:100%;',
					'default' => 'color: #777; text-decoration: line-through; margin-right: 4px;'
				),

				array(
					'name' => __( 'Optionally change the CSS for new price on cart after discounting.', 'wcdp' ),
					'id' => 'woocommerce_css_new_price',
					'type' => 'textarea',
					'css' => 'width:100%;',
					'default' => 'color: #4AB915; font-weight: bold;'
				),

				array(
					'title' => __( 'Discounts Based On'.($this->wdp_pro?'':' - (Premium Feature)'), 'wcdp' ),
					'id' => 'woocommerce_plus_discount_type',
					'desc' => sprintf( __( 'You can offer discounts based on Quantity (Default) and Weight (%s) as well.', 'wcdp' ), $this->woocommerce_weight_unit ),
					'desc_tip' => true,
					'std' => 'yes',
					'type' => 'select',
					'css' => 'min-width:200px;',
					'class' => 'chosen_select',
					'options' => array(
						'quantity' => __( 'Quantity (Default)', 'wcdp' ),
						'weight' => __( 'Weight ('.$this->woocommerce_weight_unit.')', 'wcdp' )
					)
				),

				array(
					'title' => __( 'Discount Available Conditionally?'.($this->wdp_pro?'':' - (Premium Feature)'), 'wcdp' ),
					'id' => 'woocommerce_plus_discount_condition',
					'desc' => sprintf( __( 'You can offer discounts conditionally, like discounts are available only on store pickup. No discounts if shipping required etc.', 'wcdp' ), $this->woocommerce_weight_unit ),
					'desc_tip' => true,
					'std' => 'yes',
					'type' => 'select',
					'css' => 'min-width:200px;',
					'class' => 'chosen_select',
					'options' => array(
						'default' => __( 'Default (No conditions)', 'wcdp' ),
						'no_shipping' => __( 'No Shipping', 'wcdp' ),
						'only_shipping' => __( 'Only Shipping', 'wcdp' ),
					)
				),

				array(
					'name' => __( 'Apply discount on shipping decision only', 'wcdp' ),
					'id' => 'woocommerce_show_discounts_on_shipping_decision',
					'desc' => __( 'Discount will not be applied on cart or single product page until user will decide store pickup or shipping required.', 'wcdp' ),
					'std' => 'yes',
					'type' => 'checkbox',
					'default' => 'no'
				),

				);

				if($this->wdp_pro){
					$gj_logic = wdp_extra_logics('gj_logic');
					if(!empty($gj_logic))
					$default_settings[] = $gj_logic;
				}

				$default_settings[] = array( 'type' => 'sectionend', 'id' => 'wdpplus_discounts_options' );

				$default_settings[] = array(
					'desc' => __(($this->wdp_pro?'Discount Available Conditionally? <a href="'.admin_url().'admin.php?page=wc_wcdp" target="_blank">Click here to define error messages</a><br />':'').'If you find the <a target="_blank" href="https://wordpress.org/plugins/woocommerce-discounts-plus/screenshots/">'.$wcdp_data['Name'].'</a> extension useful, please visit our online store for more <a target="_blank" href="http://shop.androidbubbles.com">premium products</a>.<br />
					<a class="wdp-optional-wrappers button">Click here to display optional/layout settings</a>'),
					'id' => 'woocommerce_plus_discount_notice_text',
					'type' => 'title'
				);

				$default_settings[] = array( 'type' => 'sectionend', 'id' => 'woocommerce_plus_discount_notice_text' );


			// Define settings
			$this->fields['plus_discount'] = apply_filters( 'woocommerce_plus_discount_settings_fields',$default_settings); // End settings



			$js = "

					jQuery('#woocommerce_enable_plus_discounts').change(function() {

						jQuery('#woocommerce_cart_info, #woocommerce_variations_separate, #woocommerce_discount_type, #woocommerce_css_old_price, #woocommerce_css_new_price, #woocommerce_show_on_item, #woocommerce_show_on_subtotal, #woocommerce_show_on_order_subtotal').closest('tr').hide();

						if ( jQuery(this).attr('checked') ) {
							//jQuery('#woocommerce_cart_info').closest('tr').show();
							jQuery('#woocommerce_variations_separate').closest('tr').show();
							jQuery('#woocommerce_discount_type').closest('tr').show();
							//jQuery('#woocommerce_css_old_price').closest('tr').show();
							//jQuery('#woocommerce_css_new_price').closest('tr').show();
							jQuery('#woocommerce_show_on_item').closest('tr').show();
							//jQuery('#woocommerce_show_on_subtotal').closest('tr').show();
							jQuery('#woocommerce_show_on_order_subtotal').closest('tr').show();
						}

					}).change();
					jQuery('.nav-tab.nav-tab-active').addClass('wdp');
					jQuery('form#mainform table.form-table').addClass('wdp-tbl');
					jQuery('<div class=\"wdp-guy\"><ul><li class=\"".($this->wdp_pro?'hide':'')."\">Go Premium!</li><li>Video Tutorial</li><li class=\"".($s2_enabled?'':'hide')."\">s2member Plugin</li><li>Contact Developer</li></ul></div>').insertBefore($('form#mainform table.form-table.wdp-tbl'));
					jQuery('.wdp-guy ul li:nth-child(1)').click(function(){
						window.open('".$this->premium_link."');
					});

					jQuery('.wdp-guy ul li:nth-child(2)').click(function(){
						window.open('".$this->watch_tutorial."');
					});

					jQuery('.wdp-guy ul li:nth-child(3)').click(function(){
						window.open('".$this->s2member."');
					});

					jQuery('.wdp-guy ul li:nth-child(4)').click(function(){
						window.open('".$this->contact_developer."');
					});

				";

			$this->run_js( $js );

		}

		/**
		 * Includes inline JavaScript.
		 *
		 * @param $js
		 */
		protected function run_js( $js ) {

			global $woocommerce;

			if ( function_exists( 'wc_enqueue_js' ) ) {
				wc_enqueue_js( $js );
			} else {
				$woocommerce->add_inline_js( $js );
			}

		}

		/**
         * @return bool
		 */
		protected function coupon_check() {

			global $woocommerce;

			if ( get_option( 'woocommerce_remove_discount_on_coupon', 'yes' ) == 'no' ) return false;
			return !( empty( $woocommerce->cart->applied_coupons ) );


		}

		protected function sale_applied() {

			if ( get_option( 'woocommerce_discount_on_sale', 'yes' ) == 'yes' ){
				return true;
			}else{
				return false;
			}
		}



		public function wdp_plugin_links($links) {



			$settings_link = '<a href="admin.php?page=wc-settings&tab=plus_discount">Settings</a>';

			if($this->wdp_pro){
				array_unshift($links, $settings_link);
			}else{

				$this->premium_link = '<a href="'.$this->premium_link.'" title="Go Premium" target=_blank>Go Premium</a>';
				array_unshift($links, $settings_link, $this->premium_link);

			}


			return $links;
		}

		public function action_woocommerce_short_description_free( $post_excerpt )   {
			echo $this->filter_woocommerce_short_description_free($post_excerpt);
		}
		public function filter_woocommerce_short_description_free( $post_excerpt )   {
			// make filter magic happen here...
			global $wdp_pricing_scale;

			if($wdp_pricing_scale)
			return;

			if(is_product() && !in_array(get_the_ID(), wc_get_product_ids_on_sale()) && plus_discount_product_display(get_the_ID())){

				$wdpq = array();



				$meta = get_post_meta(get_the_id());
				//pree($meta);
				$_regular_price = get_post_meta(get_the_id(), '_regular_price', true);
				$_price = get_post_meta(get_the_id(), '_price');
				//pree($_regular_price);
				//pree($_price);
				$is_flat = (get_option( 'woocommerce_discount_type', '' ) == 'flat');

				if(!empty($meta)){
					$r_array = array();
					foreach($meta as $k=>$arr){

						$qd = substr($k, strlen('plus_discount_'), 1);

						$index = substr($k, -1, 1);

						//plus_discount_quantity_1
						//plus_discount_discount_1
						//plus_discount_discount_flat_4

						if(in_array($qd, array('q'))){

							//$arr = get_post_meta( get_the_id(), "plus_discount_quantity_$index" );

						}elseif(in_array($qd, array('d'))){

							if($is_flat){
								$arr = get_post_meta( get_the_id(), "plus_discount_discount_flat_$index" );
							}else{
								$arr = get_post_meta( get_the_id(), "plus_discount_discount_$index" );
							}

						}else{
							$arr = array();
						}

						if(!empty($arr)){
							$val = current($arr);
							if($val>0)
							$wdpq[$index][$qd] = $val;
						}

					}
				}


				if(!empty($wdpq)){
					$post_excerpt .= '<div class="wdp_price_scale"><h4 class="wsdps">Таблица скидок:</h4>';
					$post_excerpt .= '<ul class="wsdps"><li><strong>Кол-во</strong><strong>Скидка</strong></li>';
					//pree($wdpq);
					foreach($wdpq as $dpq){


						if($is_flat){
							$dprice = round(((($dpq['q']*current($_price))-$dpq['d'])/$dpq['q']), 2);

							$spi = (current($_price)-$dprice);
							//pree($spi);
							$price = wdp_get_formatted_price($dprice).' (Save '.wdp_get_formatted_price($spi).' per item)';
						}else{
							$price = $dpq['d'].'percentage';//$_regular_price-($_regular_price*($dpq['d']/100));
						}

						$post_excerpt_Arr[$dpq['q']] = '<li><span>'.$dpq['q'].'%s</span><span>'.$price.'</span></li>';
					}

					ksort($post_excerpt_Arr);
					//pree($post_excerpt_Arr);
					if(!empty($post_excerpt_Arr)){
						$i = 0;
						foreach($post_excerpt_Arr as $item){ $i++;
							//echo $i.' > '.count($post_excerpt_Arr);
							if($i==count($post_excerpt_Arr))
							$post_excerpt .= sprintf($item, '+');
							else
							$post_excerpt .= sprintf($item, '');
						}
					}
					$post_excerpt = str_replace('percentage', '%', $post_excerpt);
					$post_excerpt .= '</ul></div>';
				}

			}else{

			}
			$wdp_pricing_scale = true;

			return $post_excerpt;
		}


		function is_pro(){
		}

	}





	//new Woo_Discounts_Plus_Plugin();

}




	function wdp_settings_posted(){



	}

	add_action('admin_init', 'wdp_settings_posted');


	function wdp_head(){

		global $product;

		$wdpq = get_option( 'wdp_qd' );
		$ajax_nonce = wp_create_nonce( "wcdp_cc" );
?>
	<script type="text/javascript" language="javascript">

		var wdp_qd = {};
		//var wdp_pp = <?php //echo WDP_PER_PRODUCT?1:0; ?>;
		var wdp_security = '<?php echo $ajax_nonce; ?>';
		//if(!wdp_pp)
		wdp_qd = jQuery.parseJSON('<?php echo json_encode($wdpq); ?>');

	</script>

    <style type="text/css">
	<?php if(
				(isset($_GET['tab']) && $_GET['tab']=='plus_discount')
			||
				(isset($_GET['page']) && in_array($_GET['page'], array('wdp-s2member-settings')))
			): ?>
	div.error{
		display:none;
	}
	<?php endif; ?>
	#wdp_settings h3 {
		padding: 0 12px;
	}
	#wdp_settings .postbox,
	#wdp_settings .postbox .inside,
	#wdp_settings #poststuff{
		float:left;
		overflow:hidden;
		width:100%;
	}
	.s2_roles_and_criteria{
	}
	.s2_roles_and_criteria li label {
		display: inline-block;
		width: 116px;
	}
	.s2_roles_and_criteria li select,
	.s2_roles_and_criteria li input[type="text"]{
		width:200px;
	}
	a[href="admin.php?page=wdp-s2member-settings"] {
		background-color: #d66060;
		color:#fff !important;
		border-top: 2px solid #d66060;
		border-bottom: 2px solid #d66060;
		font-weight:bold;
	}
	a[href="admin.php?page=wdp-s2member-settings"]:hover {
		border-top: 2px solid #fff;
		border-bottom: 2px solid #fff;
		color:#d66060 !important;
	}
	.s2_roles_guide{
		width:98%;
		float:left;
	}
	.s2_roles_guide a{
		text-decoration:none;
	}
	.s2_roles_guide a:hover{
		text-decoration:underline;
	}
	.s2_roles_guide ul{
		width:50%;
		float:left;
		margin-top: 0;
	}

	.s2_roles_guide .s2_roles_guide_right {
		border-left: 1px solid #eee;
		float: right;
		padding: 0 0 0 20px;
		width: 45%;
	}
	.s2_roles_guide .s2_roles_guide_right ol {
		margin:0;
		padding:0;
		list-style:inside decimal;
	}
	.s2_roles_guide .video_tutorials li {
		width: 100%;
	}
	.s2_roles_guide .video_tutorials li strong{
		font-weight: normal;
	}
	.s2_roles_guide .video_tutorials li iframe {
		display: block;
		margin: 0 0 0 19px;
	}
	</style>
<?php
	}

	add_action('admin_head', 'wdp_head');




	if($wdp_pro){

		include_once($pro_class);

		if(class_exists('Woo_Discounts_Plus_Pro'))
		$wdpp = new Woo_Discounts_Plus_Pro();


		//add_filter( 'woocommerce_short_description', 'filter_woocommerce_short_description_pro', 10, 1 );

		add_action('admin_menu', 'wdpp_admin_menu');



	}
	else{
		$wdpp = new Woo_Discounts_Plus_Plugin();

	}

	$wdpp_obj = $wdpp;

	// add_filter( 'woocommerce_short_description', array($wdpp, 'filter_woocommerce_short_description_free'), 10, 1 );
	// add_filter( 'woocommerce_add_to_cart_handler', array($wdpp, 'filter_woocommerce_short_description_free'), 10, 1 );
	add_action( 'woocommerce_add_to_cart', array($wdpp, 'action_woocommerce_short_description_free'), 10, 1 );

<?php if ( ! defined( 'ABSPATH' ) ) exit; 


	function sanitize_wdp_data( $input ) {

		if(is_array($input)){
		
			$new_input = array();
	
			foreach ( $input as $key => $val ) {
				$new_input[ $key ] = (is_array($val)?sanitize_wdp_data($val):sanitize_text_field( $val ));
			}
			
		}else{
			$new_input = sanitize_text_field($input);
		}
		
		return $new_input;
	}
	
	if(!function_exists('pree')){
	function pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		
		}	 
	} 
	
	if(!function_exists('pre')){
	function pre($data){
			if(isset($_GET['debug'])){
				pree($data);
			}
		}	 
	} 
	
	function wdp_s2_roles(){
		global $wp_roles;
		$s2_options = &$GLOBALS['WS_PLUGIN__']['s2member']['o'];
		$s2_roles = array();
		if(!empty($wp_roles) && isset($wp_roles->roles) && !empty($wp_roles->roles)){
			foreach($wp_roles->roles as $key=>$arr){
				if(substr($key, 0, strlen('s2member_level'))=='s2member_level'){
					$s2_key = str_replace('s2member_', '', $key).'_label';
					if(array_key_exists($s2_key, $s2_options)){
						$s2_roles[$key] = $s2_options[$s2_key];
					}
				}
			}
		}
		return $s2_roles;
	}
	
	function wdp_admin_init(){
		//if ( !in_array( 's2member/s2member.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;
		//pre(get_option( 'active_plugins' ));
		
	}
	
	add_action('admin_init', 'wdp_admin_init');
	
	if(!function_exists('wdp_s2member_admin_menu')){
		function wdp_s2member_admin_menu() {
			global $wcdp_data;
			
			$menu = apply_filters('ws_plugin__s2member_during_add_admin_options_menu_slug', 'ws-plugin--s2member-start', get_defined_vars());
			add_submenu_page($menu, $wcdp_data['Name'], 'Discounts Plus', 'activate_plugins', 'wdp-s2member-settings', 'wdp_s2member_settings');
			
		}	
	}	
	add_action('admin_menu', 'wdp_s2member_admin_menu');
	
	require_once('s2member-settings.php');
	
	function wdp_s2member_discount(){
		$role = wdp_s2member_access_level();
		$fp = get_option('wdp_'.$role);
		return $fp;
	}
	
	function wdp_s2member_access_level(){
		
		$role = '';
		
		$s2_roles = wdp_s2_roles();
		$user_id = get_current_user_id();
		
		$wp_capabilities = get_user_meta($user_id, 'wp_capabilities', true);
		//pree($wp_capabilities);
		//pree($s2_roles);
		
		$valid_roles = array();
		
		if(is_array($wp_capabilities) && is_array($s2_roles)){
			$valid_roles = array_intersect_key($wp_capabilities, $s2_roles);
		}
		
		//pree($valid_roles);
		if(!empty($valid_roles)){
			$valid_roles = array_keys($valid_roles);
			$role = current($valid_roles);
		}
		
		return $role;
	}
	
	function wdp_wp_init(){
		global $wdp_discount_condition;
		
		if(isset($_GET['need_shipping'])){
			wdp_sessions();
			$_SESSION['need_shipping'] = ($_GET['need_shipping']=='true'?1:0);
		}elseif(!isset($_SESSION['need_shipping'])){
			wdp_sessions();
			$on_shipping_decision = (get_option( 'woocommerce_show_discounts_on_shipping_decision', 'yes' ) == 'yes');
			$_SESSION['need_shipping'] = (($on_shipping_decision && $wdp_discount_condition!='default')?1:0);
		}
		
		
		if(isset($_GET['wdp_s2member_access_level'])){
			wdp_s2member_access_level();
		}
		
			

				
	}
	
	add_action('init', 'wdp_wp_init');
	
	function wdp_get_current_user_role() {
		global $wp_roles;
	
		$current_user = wp_get_current_user();
		$roles = $current_user->roles;
		$role = array_shift( $roles );
	
		return isset( $wp_roles->role_names[ $role ] ) ? $role : FALSE;
	}		
	
	if(!function_exists('wdp_sessions')){
		function wdp_sessions(){
			if (!session_id()){
				ob_start();
				@session_start();
			}
		}
	}
	
	function plus_discount_type($product_id){
		
		$plus_discount_type = get_post_meta($product_id, "plus_discount_type", true );		
		return ($plus_discount_type!='weight'?'quantity':'weight');
	
	}
		
	function plus_discount_product_display($product_id, $actual = false){

		$product_settings = get_post_meta($product_id, "plus_discount_product_display", 'no' );
		
		if($product_settings=='yes' && plus_discount_enabled($product_id, true)=='yes'){
		
		}else{
			$product_settings = 'no';
		}
		
		if(!$actual){
			switch($product_settings){
				case 'yes':
					$product_settings = true;
				break;
				case 'no':
				default:
					$product_settings = false;
				break;				
			}
		}

		return $product_settings;		
	}
	
	function plus_discount_enabled($product_id, $actual = false){
		
		$product_settings = get_post_meta($product_id, "plus_discount_enabled", true );
		//pree($product_id.'-'.$product_settings);
		if($product_settings==''){
			$_product = wc_get_product( $product_id );

			if($_product instanceof WC_Product_Variation){
				//pree($_product->id);
				$variation_excluded = get_post_meta($_product->get_id(), "plus_discount_excluding", true );
				$variation_excluded = is_array($variation_excluded)?$variation_excluded:array();
				//pree($variation_settings);
				
				$product_settings = get_post_meta($_product->get_id(), "plus_discount_enabled", true );
				
				if($product_settings!='no' && in_array($product_id, $variation_excluded)){
					$product_settings = 'no';
				}
				
				//pree($product_settings);
			}
		}
		//pree($product_id.'-'.$product_settings);
		//if it's not yet been touched or maybe it's no
		//pre($product_settings);
		switch($product_settings){
			case 'yes':
				$product_settings = 'yes';
			break;
			case 'no':
				$product_settings = 'no';
			break;		
			default:
			case 'default':			
			
				if($actual){
					$product_settings = ($product_settings!=''?$product_settings:'no');					
				}else{
					$product_settings = get_option( 'woocommerce_enable_plus_discounts', 'no' );
				}
				
			break;		
		}
		//pre($product_settings);		
		
		if(!$actual){
			switch($product_settings){
				case 'yes':
					$product_settings = true;
				break;
				case 'no':
					$product_settings = false;
				break;				
			}
		}
		//pre($product_settings);		
		return $product_settings;
	}
		
	if(!function_exists('woocommerce_wp_select_multiple')){	
		function woocommerce_wp_select_multiple( $field ) {
			global $thepostid, $post;
		
			$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
			$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
			$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
			$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
			//$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
			$field['value']         = isset( $field['value'] ) ? $field['value'] : ( get_post_meta( $thepostid, $field['id'], true ) ? get_post_meta( $thepostid, $field['id'], true ) : array() );
				
			$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
			//pree($field);
			// Custom attribute handling
			$custom_attributes = array();
		
			if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		
				foreach ( $field['custom_attributes'] as $attribute => $value ){
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
				}
			}
		
			echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '[]" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" ' . implode( ' ', $custom_attributes ) . ' multiple="multiple">';
		
			foreach ( $field['options'] as $key => $value ) {
				//echo '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
		        echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $field['value'] ) ? 'selected="selected"' : '' ) . '>' . esc_html( $value ) . '</option>';
			}
		
			echo '</select> ';
		
			if ( ! empty( $field['description'] ) ) {
		
				if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
					echo wc_help_tip( $field['description'] );
				} else {
					echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
				}
			}
			echo '</p>';
		}	
	}

	function wdp_get_product_id($item){
		$product_id = isset($item['product_id'])?$item['product_id']:false;
		if(!$product_id && method_exists($item,'item')){
			$product_id = $item->get_product_id();
		}
		return $product_id;
	}
		
	function wdp_woocommerce_cart_item_name( $product_get_name, $cart_item, $cart_item_key ){
		
		
		$product_id = wdp_get_product_id($cart_item);
		echo $product_get_name;
		$plus_discount_type = plus_discount_type($product_id, true);
		switch($plus_discount_type){
			case 'weight':
			//pree($product_id);
			$product_id = ($cart_item['variation_id']?$cart_item['variation_id']:$product_id);
			
			$_product = wc_get_product( $product_id );
			
			
?>
<div class="plus_discount_type"><?php echo ucwords($plus_discount_type); ?>: <?php echo $_product->get_weight().get_option( 'woocommerce_weight_unit' ); ?><?php echo ($cart_item['quantity']>1?' <small>x'.$cart_item['quantity'].'</small>':''); ?></div>	
<?php		
			break;
		}
	}
	
	add_filter('woocommerce_cart_item_name', 'wdp_woocommerce_cart_item_name', 10, 3);			
		
	add_action( 'woocommerce_before_checkout_form', 'wdp_woocommerce_add_checkout_notice', 11 );
	
	function wdp_woocommerce_add_checkout_notice() {
		
		global $wdp_discount_condition;
	
		
		
		$error_messages = wcdp_get_error_messages();
		switch($wdp_discount_condition){
			case 'no_shipping':
				if(!isset($_SESSION['need_shipping']) || !$_SESSION['need_shipping'])
				wc_print_notice( __( $error_messages['no_shipping'][0], 'woocommerce' ), 'error' );	
				else
				wc_print_notice( __( $error_messages['no_shipping'][1], 'woocommerce' ), 'error' );	
			break;
			case 'only_shipping':
				if(isset($_SESSION['need_shipping']) || !$_SESSION['need_shipping'])
				wc_print_notice( __( $error_messages['only_shipping'][0], 'woocommerce' ), 'error' );	
				elseif(!isset($_SESSION['need_shipping']) || (isset($_SESSION['need_shipping']) && !$_SESSION['need_shipping']))
				wc_print_notice( __( $error_messages['only_shipping'][1], 'woocommerce' ), 'error' );	
			break;

		}
	}	
	
	function wcdp_get_error_messages(){
		
		$checkout_url = wc_get_checkout_url();		
		$arr = array();
		
		$wcdp_error_messages = get_option('wcdp_dac_error_messages', array());
		//pree($wcdp_error_messages);exit;
		
		$need_shipping_true = (is_admin()?'%s':$checkout_url.'?need_shipping=true');
		$need_shipping_false = (is_admin()?'%s':$checkout_url.'?need_shipping=false');
		
		
		$arr['no_shipping'][0] = sprintf('Discounts are available only with "pickup from store" option. If you need shipment so discounts will be waved off. <br /><a class="button alt" href="%s">click here for shipping option</a>', $need_shipping_true);
		$arr['no_shipping'][1] = sprintf('Discounts are available only with "pickup from store" option. If you need discount so you need to pickup your order from our store. <br /><a class="button alt" href="%s">click here for discounts</a>', $need_shipping_false);
		$arr['only_shipping'][0] = sprintf('Discounts are available only with shipping. Are you interested in pickup from store?<br /><a class="button alt" href="%s">click here for shipping option</a>', $need_shipping_false);
		$arr['only_shipping'][1] = sprintf('Discounts are available only with shipping. Are you interested in getting discounts?<br /><a class="button alt" href="%s">click here for discounts</a>', $need_shipping_true);

		
		if(!empty($wcdp_error_messages)){

			foreach($wcdp_error_messages as $key=>$data){
				if(!empty($data)){
					foreach($data as $k=>$d){
						if(trim($d)!='' && $arr[$key][$k]){
							
							switch($key){
								case 'no_shipping':
									if($k){
										$d = sprintf($d, $need_shipping_false);
									}else{
										$d = sprintf($d, $need_shipping_true);
									}
								break;
								case 'only_shipping':
									if($k){
										$d = sprintf($d, $need_shipping_true);
									}else{
										$d = sprintf($d, $need_shipping_false);
									}
								break;
							}
							
							$arr[$key][$k] = stripslashes($d);
						}
					}
				}				
			}
		}
		
		return $arr;
	}
		
	function wdp_get_formatted_price($price) {
		
		$symbol = get_woocommerce_currency_symbol();
		$currency_pos = get_option( 'woocommerce_currency_pos' );
		$price_format = '%1$s%2$s';
		
		switch ( $currency_pos ) {
		case 'left' :
		  $price_format = '%1$s%2$s';
		break;
		case 'right' :
		  $price_format = '%2$s%1$s';
		break;
		case 'left_space' :
		  $price_format = '%1$s&nbsp;%2$s';
		break;
		case 'right_space' :
		  $price_format = '%2$s&nbsp;%1$s';
		break;
		}
	
		$negative = ($price < 0);
		$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, $symbol, $price );
	  	return $formatted_price;
	}
	 
	function wdp_woocommerce_header_scripts(){
		
		global $wdp_discount_condition;
?>
	<style type="text/css">
	<?php
		if($wdp_discount_condition=='no_shipping' && wdp_woocommerce_discount_applicable()){
?>
			.woocommerce-shipping-fields{
				display:none;	
			}
<?php			
		}
		/*if(get_option('eufdc_billing_off', 0)){
?>
			.woocommerce-billing-fields{
				display:none;	
			}
<?php			
		}
		if(get_option('eufdc_order_comments_off', 0)){
?>

<?php			
		}		*/		
	?>
	</style>
<?php		
	}
	
	add_action('wp_head', 'wdp_woocommerce_header_scripts');
	
	add_filter( 'woocommerce_checkout_fields' , 'wdp_woocommerce_override_checkout_fields' );
	
	function wdp_woocommerce_discount_applicable(){
		
		global $wdp_discount_condition;
		
		$ret = true;
		
		wdp_sessions();
		$on_shipping_decision = (get_option( 'woocommerce_show_discounts_on_shipping_decision', 'yes' ) == 'yes');
		
		$decision = ($wdp_discount_condition!='default' && $on_shipping_decision && !isset($_SESSION['need_shipping']));
		
		if($decision){
			$ret = false;
		}
		
		switch($wdp_discount_condition){
			case 'no_shipping':
				//wdp_sessions();
				
				if($_SESSION['need_shipping'])
				$ret = false;				
				
			break;
			case 'only_shipping':
				//wdp_sessions();
				
				if(!$_SESSION['need_shipping'])
				$ret = false;				
				
			break;			
		}

		if(isset($_GET['debug'])){
			//wdp_sessions();
			pre('wdp_discount_condition: '.$wdp_discount_condition);
			pre('on_shipping_decision: '.$on_shipping_decision);
			pre('_SESSION need_shipping: '.!isset($_SESSION['need_shipping']));
			pre('ret: '.$ret);
			pre('decision: '.$decision);
			pre($_SESSION);
			//exit;
		}		
				
		//pree($ret);
		return $ret;
		
		
	}
	
	function wdp_woocommerce_override_checkout_fields( $fields ) {
		 
		global $wdp_discount_condition;
		
		if($wdp_discount_condition=='no_shipping' && wdp_woocommerce_discount_applicable()){
			unset($fields['shipping']['shipping_first_name']);
			unset($fields['shipping']['shipping_last_name']);
			unset($fields['shipping']['shipping_company']);
			unset($fields['shipping']['shipping_address_1']);
			unset($fields['shipping']['shipping_address_2']);
			unset($fields['shipping']['shipping_city']);
			unset($fields['shipping']['shipping_postcode']);
			unset($fields['shipping']['shipping_country']);
			unset($fields['shipping']['shipping_state']);
			unset($fields['shipping']['shipping_phone']);	
			unset($fields['shipping']['shipping_address_2']);
			unset($fields['shipping']['shipping_postcode']);
			unset($fields['shipping']['shipping_company']);
			unset($fields['shipping']['shipping_last_name']);
			unset($fields['shipping']['shipping_email']);
			unset($fields['shipping']['shipping_city']);	
		}
		
		/*if(get_option('eufdc_billing_off', 0)){
			unset($fields['billing']['billing_first_name']);
			unset($fields['billing']['billing_last_name']);
			unset($fields['billing']['billing_company']);
			unset($fields['billing']['billing_address_1']);
			unset($fields['billing']['billing_address_2']);
			unset($fields['billing']['billing_city']);
			unset($fields['billing']['billing_postcode']);
			unset($fields['billing']['billing_country']);
			unset($fields['billing']['billing_state']);
			unset($fields['billing']['billing_phone']);	
			unset($fields['billing']['billing_address_2']);
			unset($fields['billing']['billing_postcode']);
			unset($fields['billing']['billing_company']);
			unset($fields['billing']['billing_last_name']);
			unset($fields['billing']['billing_email']);
			unset($fields['billing']['billing_city']);
		}
		
		if(get_option('eufdc_order_comments_off', 0))
		unset($fields['order']['order_comments']);
		
		*/
		
		return $fields;
	}	
	
	function wdp_get_user_roles(){
		$ret = array();
		global $wp_roles;
		if(!empty($wp_roles) && isset($wp_roles->roles) && !empty($wp_roles->roles)){
			$ret['default'] = 'Default';
			foreach($wp_roles->roles as $key=>$arr){
				$ret[$key] = $arr['name'];
			}
		}		
		return $ret;
	}	
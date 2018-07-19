<?php
/**
 * Vffront Admin Class
 *
 * @author   WooThemes
 * @package  vffront
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Vffront_Admin' ) ) :
	/**
	 * The Vffront admin class
	 */
	class Vffront_Admin {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'admin_menu', 				array( $this, 'welcome_register_menu' ) );
			add_action( 'admin_enqueue_scripts', 	array( $this, 'welcome_style' ) );
		}

		/**
		 * Load welcome screen css
		 *
		 * @param string $hook_suffix the current page hook suffix.
		 * @return void
		 * @since  1.4.4
		 */
		public function welcome_style( $hook_suffix ) {
			global $vffront_version;

			if ( 'appearance_page_vffront-welcome' === $hook_suffix ) {
				wp_enqueue_style( 'vffront-welcome-screen', get_template_directory_uri() . '/assets/css/admin/welcome-screen/welcome.css', $vffront_version );
				wp_style_add_data( 'vffront-welcome-screen', 'rtl', 'replace' );
			}
		}

		/**
		 * Creates the dashboard page
		 *
		 * @see  add_theme_page()
		 * @since 1.0.0
		 */
		public function welcome_register_menu() {
			add_theme_page( 'Vffront', 'Vffront', 'activate_plugins', 'vffront-welcome', array( $this, 'vffront_welcome_screen' ) );
		}

		/**
		 * The welcome screen
		 *
		 * @since 1.0.0
		 */
		public function vffront_welcome_screen() {
			require_once( ABSPATH . 'wp-load.php' );
			require_once( ABSPATH . 'wp-admin/admin.php' );
			require_once( ABSPATH . 'wp-admin/admin-header.php' );

			global $vffront_version;
			?>

			<div class="vffront-wrap">
				<section class="vffront-welcome-nav">
					<span class="vffront-welcome-nav__version">Vffront <?php echo esc_attr( $vffront_version ); ?></span>
					<ul>
						<li><a href="https://wordpress.org/support/theme/vffront" target="_blank"><?php esc_attr_e( 'Support', 'vffront' ); ?></a></li>
						<li><a href="https://docs.woocommerce.com/documentation/themes/vffront/" target="_blank"><?php esc_attr_e( 'Documentation', 'vffront' ); ?></a></li>
						<li><a href="https://woocommerce.wordpress.com/category/vffront/" target="_blank"><?php esc_attr_e( 'Development blog', 'vffront' ); ?></a></li>
					</ul>
				</section>

				<div class="vffront-logo">
					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/admin/vffront-icon.svg" alt="Vffront" />
				</div>

				<div class="vffront-intro">
					<?php
					/**
					 * Display a different message when the user visits this page when returning from the guided tour
					 */
					$referrer = wp_get_referer();

					if ( strpos( $referrer, 'sf_starter_content' ) !== false ) {
						echo '<h1>' . sprintf( esc_attr__( 'Setup complete %sYour Vffront adventure begins now 🚀%s ', 'vffront' ), '<span>', '</span>' ) . '</h1>';
						echo '<p>' . esc_attr__( 'One more thing... You might be interested in the following Vffront extensions and designs.', 'vffront' ) . '</p>';
					} else {
						echo '<p>' . esc_attr__( 'Hello! You might be interested in the following Vffront extensions and designs.', 'vffront' ) . '</p>';
					}
					?>
				</div>

				<div class="vffront-enhance">
					<div class="vffront-enhance__column vffront-bundle">
						<h3><?php esc_attr_e( 'Vffront Extensions Bundle', 'vffront' ); ?></h3>
						<span class="bundle-image">
							<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/admin/welcome-screen/vffront-bundle-hero.png" alt="Vffront Extensions Hero" />
						</span>

						<p>
							<?php esc_attr_e( 'All the tools you\'ll need to define your style and customize Vffront.', 'vffront' ); ?>
						</p>

						<p>
							<?php esc_attr_e( 'Make it yours without touching code with the Vffront Extensions bundle. Express yourself, optimize conversions, delight customers.', 'vffront' ); ?>
						</p>


						<p>
							<a href="https://woocommerce.com/products/vffront-extensions-bundle/?utm_source=product&utm_medium=upsell&utm_campaign=vffrontaddons" class="vffront-button" target="_blank"><?php esc_attr_e( 'Read more and purchase', 'vffront' ); ?></a>
						</p>
					</div>
					<div class="vffront-enhance__column vffront-child-themes">
						<h3><?php esc_attr_e( 'Alternate designs', 'vffront' ); ?></h3>
						<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/admin/welcome-screen/child-themes.jpg" alt="Vffront Powerpack" />

						<p>
							<?php esc_attr_e( 'Quickly and easily transform your shops appearance with Vffront child themes.', 'vffront' ); ?>
						</p>

						<p>
							<?php esc_attr_e( 'Each has been designed to serve a different industry - from fashion to food.', 'vffront' ); ?>
						</p>

						<p>
							<?php esc_attr_e( 'Of course they are all fully compatible with each Vffront extension.', 'vffront' ); ?>
						</p>

						<p>
							<a href="https://woocommerce.com/product-category/themes/vffront-child-theme-themes/?utm_source=product&utm_medium=upsell&utm_campaign=vffrontaddons" class="vffront-button" target="_blank"><?php esc_attr_e( 'Check \'em out', 'vffront' ); ?></a>
						</p>
					</div>
				</div>

				<div class="automattic">
					<p>
					<?php printf( esc_html__( 'An %s project', 'vffront' ), '<a href="https://automattic.com/"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/admin/welcome-screen/automattic.png" alt="Automattic" /></a>' ); ?>
					</p>
				</div>
			</div>
			<?php
		}

		/**
		 * Welcome screen intro
		 *
		 * @since 1.0.0
		 */
		public function welcome_intro() {
			require_once( get_template_directory() . '/inc/admin/welcome-screen/component-intro.php' );
		}

		/**
		 * Output a button that will install or activate a plugin if it doesn't exist, or display a disabled button if the
		 * plugin is already activated.
		 *
		 * @param string $plugin_slug The plugin slug.
		 * @param string $plugin_file The plugin file.
		 */
		public function install_plugin_button( $plugin_slug, $plugin_file ) {
			if ( current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' ) ) {
				if ( is_plugin_active( $plugin_slug . '/' . $plugin_file ) ) {
					/**
					 * The plugin is already active
					 */
					$button = array(
						'message' => esc_attr__( 'Activated', 'vffront' ),
						'url'     => '#',
						'classes' => 'disabled',
					);
				} elseif ( $url = $this->_is_plugin_installed( $plugin_slug ) ) {
					/**
					 * The plugin exists but isn't activated yet.
					 */
					$button = array(
						'message' => esc_attr__( 'Activate', 'vffront' ),
						'url'     => $url,
						'classes' => 'activate-now',
					);
				} else {
					/**
					 * The plugin doesn't exist.
					 */
					$url = wp_nonce_url( add_query_arg( array(
						'action' => 'install-plugin',
						'plugin' => $plugin_slug,
					), self_admin_url( 'update.php' ) ), 'install-plugin_' . $plugin_slug );
					$button = array(
						'message' => esc_attr__( 'Install now', 'vffront' ),
						'url'     => $url,
						'classes' => ' install-now install-' . $plugin_slug,
					);
				}
				?>
				<a href="<?php echo esc_url( $button['url'] ); ?>" class="vffront-button <?php echo esc_attr( $button['classes'] ); ?>" data-originaltext="<?php echo esc_attr( $button['message'] ); ?>" data-slug="<?php echo esc_attr( $plugin_slug ); ?>" aria-label="<?php echo esc_attr( $button['message'] ); ?>"><?php echo esc_attr( $button['message'] ); ?></a>
				<a href="https://wordpress.org/plugins/<?php echo esc_attr( $plugin_slug ); ?>" target="_blank"><?php esc_attr_e( 'Learn more', 'vffront' ); ?></a>
				<?php
			}
		}

		/**
		 * Check if a plugin is installed and return the url to activate it if so.
		 *
		 * @param string $plugin_slug The plugin slug.
		 */
		public function _is_plugin_installed( $plugin_slug ) {
			if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) ) {
				$plugins = get_plugins( '/' . $plugin_slug );
				if ( ! empty( $plugins ) ) {
					$keys        = array_keys( $plugins );
					$plugin_file = $plugin_slug . '/' . $keys[0];
					$url         = wp_nonce_url( add_query_arg( array(
						'action' => 'activate',
						'plugin' => $plugin_file,
					), admin_url( 'plugins.php' ) ), 'activate-plugin_' . $plugin_file );
					return $url;
				}
			}
			return false;
		}
		/**
		 * Welcome screen enhance section
		 *
		 * @since 1.5.2
		 */
		public function welcome_enhance() {
			require_once( get_template_directory() . '/inc/admin/welcome-screen/component-enhance.php' );
		}

		/**
		 * Welcome screen contribute section
		 *
		 * @since 1.5.2
		 */
		public function welcome_contribute() {
			require_once( get_template_directory() . '/inc/admin/welcome-screen/component-contribute.php' );
		}

		/**
		 * Get product data from json
		 *
		 * @param  string $url       URL to the json file.
		 * @param  string $transient Name the transient.
		 * @return [type]            [description]
		 */
		public function get_vffront_product_data( $url, $transient ) {
			$raw_products = wp_safe_remote_get( $url );
			$products     = json_decode( wp_remote_retrieve_body( $raw_products ) );

			if ( ! empty( $products ) ) {
				set_transient( $transient, $products, DAY_IN_SECONDS );
			}

			return $products;
		}
	}

endif;

return new Vffront_Admin();

<?php

/**
 *
 * @package   WooCommerce Affiliate Product New Tab
 * @author    Abdelrahman Ashour < abdelrahman.ashour38@gmail.com >
 * @license   GPL-2.0+
 * @copyright 2018 Ash0ur


 * Plugin Name: WooCommerce Affiliate Product New Tab
 * Description: The plugin opens the external/affiliate products links in a new tab.
 * Version:      1.0.0
 * Author:       Abdelrahman Ashour
 * Author URI:   https://profiles.wordpress.org/ashour
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'Woo_External_Product_New_Tab' ) ) :

	class Woo_Affiliate_Product_New_Tab {

		private static $instance;

		public static function init() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		public function __construct() {
			$this->setup_actions();
		}

		public static function plugin_activated() {
			if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				die( '<h3> WooCommerce plugin must be active </h3>' );
			}
		}

		public function setup_actions() {
			add_filter( 'woocommerce_loop_add_to_cart_args', array( $this, 'open_in_new_tab_arg' ), 1000, 2 );
			remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
			add_action( 'woocommerce_external_add_to_cart', array( $this, 'custom_external_single_link' ) );
		}

		public function custom_external_single_link() {
			global $product;

			if ( ! $product->add_to_cart_url() ) {
				return;
			}

			$product_url = $product->add_to_cart_url();
			$button_text = $product->single_add_to_cart_text();

			do_action( 'woocommerce_before_add_to_cart_button' ); ?>
			<a href="<?php echo esc_url( $product_url ); ?>" rel="nofollow" class="single_add_to_cart_button button alt" target="_blank"><?php echo esc_html( $button_text ); ?></a>
			<?php do_action( 'woocommerce_after_add_to_cart_button' );
		}

		public function open_in_new_tab_arg( $args_arr, $product ) {
			if ( 'external' === $product->get_type() ) {
				$args_arr['attributes']['target'] = "_blank";
			}
			return $args_arr;
		}

	}

	add_action( 'plugins_loaded', array( 'Woo_Affiliate_Product_New_Tab', 'init' ), 10 );
	register_activation_hook( __FILE__, array( 'Woo_Affiliate_Product_New_Tab', 'plugin_activated' ) );

endif;

<?php

class WC_Integration {}

class WC_Cart
{
	public function get_cart_subtotal() { return false; }
	public function get_cart_contents_count() { return false; }
}

class WooCommerce
{
	protected static $_instance = null;
	public $cart = null;

	public function __construct() {
		$this->cart = new WC_Cart();
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

function is_product() { return false; }
function is_cart() { return false; }
function wc_get_cart_url() { return false; }
function is_checkout() { return false; }
function wc_get_page_id() { return false; }

function wc() {
	return WooCommerce::instance();
}

// Global for backwards compatibility.
$GLOBALS['woocommerce'] = wc();

class WC_Widget_Cart extends Ecwid_Widget_NSF_Minicart
{
	public function __construct() {
		parent::__construct();
	}

	public function widget( $args, $instance ) {
		echo '<div class="widget_shopping_cart_content">';
		echo parent::_render_widget_content( $args, $instance );
		echo '</div>';
	}
}
add_action( 'widgets_init', 'my_register_widgets' );
function my_register_widgets() {
	register_widget( 'WC_Widget_Cart' );
}
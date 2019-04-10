<?php

class WC_Integration {}

class WC_Cart
{
	public function get_cart_subtotal() { return '<span title="ec-cart-subtotal">0</span>'; }
	public function get_cart_contents_count() { return '<span title="ec-cart-count">0</span>'; }
	public function get_cart_total() { return 0; }
	public function get_cart_item_quantities() { return 0; }
}

class WooCommerce
{
	protected static $_instance = null;
	public $cart = null;

	public function __construct() {

		global $allowedtags;
		$allowedtags['span'] = array('title' => true);

		$this->cart = new WC_Cart();
	}

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function plugin_url() {}
	public static function checkout() {}
}


function wc_get_cart_url() { return Ecwid_Store_Page::get_cart_url(); }
function wc_get_page_id( $page ) { return wc_get_cart_url(); /*Ecwid_Store_Page::get_current_store_page_id();*/ }

function is_product() { return true; }
function is_cart() { return true; }
function is_checkout() { return true; }
function storefront_is_woocommerce_activated() { return true; }
function is_shop() { return true; }
function is_woocommerce() { return true; }


function wc_get_featured_product_ids() { return false; }
function wc_get_product_ids_on_sale() { return false; }
function woocommerce_get_product_thumbnail() { return false; }
function get_product_search_form() { return false; }
function wc_get_product() { return false; }
function woocommerce_mini_cart() { return false; }
function is_product_taxonomy() { return false; }
function is_product_category() { return false; }


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

class WC_Widget_Product_Search extends Ecwid_Widget_Search
{
	protected $_hide_title = true;
	public function __construct() {
		parent::__construct();
	}
}

class WC_Widget_Product_Categories extends Ecwid_Widget_Vertical_Categories_List
{
	public function __construct() {
		parent::__construct();
	}
}

class WC_Widget_Recently_Viewed extends Ecwid_Widget_Recently_Viewed
{
	public function __construct() {
		parent::__construct();
	}
}



// add_action( 'the_post', function(){
add_action( 'wp_footer', function(){
	echo <<<HTML
	<script>
	jQuery(document).ready(function(){
		Ecwid.OnCartChanged.add( function( cart ) {
			if( jQuery('[title="ec-cart-count"]').length ) {
				jQuery('[title="ec-cart-count"]').text( cart.productsQuantity );
			}

			if( jQuery('[title="ec-cart-subtotal"]').length ) {
				var price = 0;
				jQuery( cart.items ).each(function(){
					price += this.product.price;
				})
				jQuery('[title="ec-cart-subtotal"]').text( Ecwid.formatCurrency( price ) );
			}
		});
	});
	</script>
HTML;
}, 99 );

add_action( 'widgets_init', 'ecwid_integration_woo_register_widgets' );
function ecwid_integration_woo_register_widgets() {
	// register_widget( 'WC_Widget_Cart' );
	register_widget( 'WC_Widget_Product_Search' );
	register_widget( 'WC_Widget_Product_Categories' );
}



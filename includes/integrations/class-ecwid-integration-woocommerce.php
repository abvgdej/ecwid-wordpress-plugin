<?php

/*
*
* Classes
*
*/
class WC_Integration {}

class WC_Cart
{
	public function get_cart_subtotal() { return '<span title="ec-cart-subtotal">0</span>'; }
	public function get_cart_contents_count() { return '<span title="ec-cart-count">0</span>'; }
	public function get_cart_total() { return 0; }
	public function get_cart_item_quantities() { return 0; }
}

class WC_Product
{
	public function get_price_html() { return false; }
	public function get_average_rating() { return false; }
	public function add_to_cart_url() { return false; }
	public function add_to_cart_text() { return false; }
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


function wc_get_cart_url() { 
	return Ecwid_Store_Page::get_cart_url();
}
function wc_get_page_id( $page ) {
	$fake_post = new stdClass();
	$fake_post->ID = 'ec-cart';
	$fake_post->filter = 'sample';

	return $fake_post;
}


/*
*
* Functions
*
*/
function is_product() { return true; }
function is_cart() { return true; }
function is_checkout() { return true; }
function storefront_is_woocommerce_activated() { return true; }
function is_shop() { return true; }
function is_woocommerce() { return true; }


function wc_get_rating_html() { return false; }
function wc_get_featured_product_ids() { return false; }
function wc_get_product_ids_on_sale() { return false; }
function woocommerce_get_product_thumbnail() { return false; }
function get_product_search_form() { return false; }
function woocommerce_mini_cart() { return false; }
function is_product_taxonomy() { return false; }
function is_product_category() { return false; }
function wc_get_product( $post = false ) { return new WC_Product(); }


/*
*
* Init woo
*
*/
function wc() { return WooCommerce::instance(); }
$GLOBALS['woocommerce'] = wc(); // Global for backwards compatibility.
$GLOBALS['product'] = wc_get_product();

/*
*
* Widgets
*
*/
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
	protected $_hide_title = true;
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


/*
*
* Hooks
*
*/
add_filter( 'pre_post_link', function( $permalink, $post, $leavename ){
	if( is_object( $post ) && $post->ID == 'ec-cart' ) {
		$ec_post = get_post( Ecwid_Store_Page::get_current_store_page_id() );
		return '/' . $ec_post->post_name . str_replace( Ecwid_Store_Page::get_store_url(), '/', wc_get_cart_url() );
	}

	return $permalink;
}, 10, 3);

add_action( 'wp_footer', function(){
	$scriptjs_url = 'https://' . Ecwid_Config::get_scriptjs_domain() . '/script.js?' . get_ecwid_store_id() . ecwid_get_scriptjs_params();
	
	echo '<script data-cfasync="false" type="text/javascript" src="' . $scriptjs_url . '"></script>';
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
});

add_action( 'widgets_init', function() {
	// register_widget( 'WC_Widget_Cart' );
	register_widget( 'WC_Widget_Product_Search' );
	register_widget( 'WC_Widget_Product_Categories' );
});



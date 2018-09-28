<?php

class Ecwid_Static_Home_Page {
	
	const OPTION_IS_ENABLED = 'ecwid_static_home_page_enabled';
	
	const OPTION_VALUE_ENABLED = 'Y';
	const OPTION_VALUE_DISABLED = 'N';
	const OPTION_VALUE_AUTO = '';
	
	const CACHE_DATA = 'static_home_page_data';
	const PARAM_VALID_FROM = 'static_home_page_valid_from';
	
	public function __construct() {
		
		add_option( self::OPTION_IS_ENABLED );
		
		if ( !is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}
	
	public function enqueue_scripts()
	{
		$data = $this->get_data_for_current_page();

		if ( !$data || !is_array( $data->cssFiles ) || empty ( $data->cssFiles ) ) return;

		EcwidPlatform::enqueue_script( 'static-home-page' );

		foreach ( $data->cssFiles as $ind => $item ) {
			wp_enqueue_style( 'ecwid-static-home-page-' . $ind, $item );
		}
		
		if ( @$data->scripts ) {
			foreach ($data->scripts as $item) {
				wp_add_inline_script( 'ecwid-static-home-page', $item );
			}
		}
	}
	
	public static function get_data_for_current_page()
	{
		if ( !self::is_enabled() ) {
			return null;
		}
		
		if ( Ecwid_Seo_Links::is_enabled() && Ecwid_Seo_Links::is_product_browser_url() ) {
			return null;
		}

		$data = self::_maybe_fetch_data();
		
		if ( $data ) {
			$html = $data->htmlCode;
			$data->scripts = array();
			preg_match_all('!<script>(.*?)</script>!s', $data->htmlCode, $matches);
			foreach ( $matches[1] as $match ) {
				$data->scripts[] = $match;
			}
			
			$data->htmlCode = preg_replace('!<script>(.*?)</script>!s', '', $data->htmlCode);
		}
		
		if ( $data ) {
			return $data;
		}
		
		return null;
	}
	
	protected static function _maybe_fetch_data()
	{
		$possible_params = array(
			'lang',
			'default_category_id'
		);
		
		$params = array();
		foreach ( $possible_params as $name ) {
			$data = Ecwid_Store_Page::get_store_page_data( $name );
			if ( $data ) {
				$params[$name] = $data;
			}
		}
		
		if ( !@$params['lang'] ) {
			$lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			$lang = substr( $lang, 0, strpos( $lang, ';' ) );
			$params['lang'] = $lang;
		}
		
		if ( Ecwid_Seo_Links::is_enabled() ) {
			$params['clean_links'] = 'true';
			$params['base_url'] = get_permalink();
		}
		
		$url = 'https://storefront.ecwid.com/home-page/' . get_ecwid_store_id() . '/static-code?';
		foreach ( $params as $name => $value ) {
			$url .= $name . '=' . urlencode( $value ) . '&'; 
		}
		
		$data = EcwidPlatform::get_from_catalog_cache( $url );

		if ( !$data ) {
			$data = EcwidPlatform::fetch_url( $url, array( 'timeout' => 3 ) );
		}
		
		if ( $data && @$data['data'] ) {
		
			EcwidPlatform::store_in_catalog_cache( $url, $data );
			$data = @json_decode( $data['data'] );
			
			return $data;
		}

		return null;
	}
	
	public static function is_enabled()
	{
		if ( get_option( self::OPTION_IS_ENABLED ) == self::OPTION_VALUE_ENABLED ) {
			return true;
		}
		
		if ( !self::is_feature_available() ) {
			return false;
		}		
		
		if ( get_option( self::OPTION_IS_ENABLED ) == self::OPTION_VALUE_DISABLED ) {
			return false;
		}
		
		return true;
	}

	public static function is_feature_available()
	{
		$api = new Ecwid_Api_V3();

		return $api->is_store_feature_enabled( Ecwid_Api_V3::FEATURE_STATIC_HOME_PAGE )
		       && $api->is_store_feature_enabled( Ecwid_Api_V3::FEATURE_NEW_PRODUCT_LIST );
	}
}

$__ecwid_static_home_page = new Ecwid_Static_Home_Page();
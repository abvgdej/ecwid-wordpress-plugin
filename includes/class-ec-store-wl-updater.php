<?php

class Ec_Store_WL_Updater
{
	public $transient_slug = 'ec_store_updater';
	public $plugin_slug = 'ecwid-shopping-cart';

	function __construct() {
		// $this->transient_slug = get_plugin_data( __FILE__ );

		add_filter( 'plugins_api', array($this, 'set_plugin_info') , 20, 3 );
		add_filter( 'site_transient_update_plugins', array($this, 'push_update') );
		
		add_action( 'upgrader_process_complete', array($this, 'after_update'), 10, 2 );
	}

	public function set_plugin_info( $res, $action, $args ){
		// do nothing if this is not about getting plugin information
		if( 'plugin_information' !== $action ) {
			return false;
		}
	 
		// do nothing if it is not our plugin
		if( $this->plugin_slug !== $args->slug ) {
			return false;
		}
	 
		$result = $this->get_update();

		if(
			!is_wp_error( $result )
			&& isset( $result['response']['code'] ) 
			&& $result['response']['code'] == 200 
			&& !empty( $result['body'] )
		) {
	 
			$data = json_decode( $result['body'] );
			$res = new stdClass();
	 
			$res->name = $data->name;
			$res->slug = $this->plugin_slug;
			$res->version = $data->version;
			$res->tested = $data->tested;
			$res->requires = $data->requires;
			$res->author = '<a href="https://shopsettings.com">Shopsettings.com</a>';
			$res->author_profile = 'https://profiles.wordpress.org/ecwid';
			$res->download_link = $data->download_url;
			$res->trunk = $data->download_url;
			$res->requires_php = '5.3';
			$res->last_updated = $data->last_updated;
			$res->sections = array(
				'description' => $data->sections->description,
				'installation' => $data->sections->installation,
				'changelog' => $data->sections->changelog
				// you can add your custom sections (tabs) here
			);
	 
			// in case you want the screenshots tab, use the following HTML format for its content:
			// <ol><li><a href="IMG_URL" target="_blank"><img src="IMG_URL" alt="CAPTION" /></a><p>CAPTION</p></li></ol>
			if( !empty( $data->sections->screenshots ) ) {
				$res->sections['screenshots'] = $data->sections->screenshots;
			}

			$res->banners = array(
				'low' => $data->banners->low,
				'high' => $data->banners->high
			);

			return $res;
		}
	 
		return false;
	}

	public function get_update() {
		
		$result = get_transient( $this->transient_slug );

		if( false == $result ) {
	 
			// info.json is the file with the actual plugin information on your server
			$result = wp_remote_get( 'http://lamp.ecwid.net/~meteor/wl/info.json', array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/json'
				) )
			);

			if( 
				!is_wp_error( $result ) 
				&& isset( $result['response']['code'] )
				&& $result['response']['code'] == 200
				&& !empty( $result['body'] )
			) {
				set_transient( $this->transient_slug, $result, 86400 );
			}
		}

		return $result;
	}

	public function push_update( $transient ){

		if ( empty($transient->checked ) ) {
			return $transient;
		}

		$result = $this->get_update();

		if( $result ) {
	 
			$data = json_decode( $result['body'] );

			// $plugin_version = get_plugin_data( __FILE__ );
			// your installed plugin version should be on the line below! You can obtain it dynamically of course 

			if(
				$data
				&& version_compare( '1.0', $data->version, '<' )
				&& version_compare($data->requires, get_bloginfo('version'), '<' )
			) {
				$res = new stdClass();
				$res->slug = 'ecwid-shopping-cart';
				$res->plugin = 'ecwid-shopping-cart/ecwid-shopping-cart.php';
				$res->new_version = $data->version;
				$res->tested = $data->tested;
				$res->package = $data->download_url;
	           		$transient->response[$res->plugin] = $res;
	           		//$transient->checked[$res->plugin] = $data->version;
	           	}
	 
		}

		return $transient;
	}


	function after_update( $upgrader_object, $options ) {
		if ( $options['action'] == 'update' && $options['type'] === 'plugin' )  {
			// just clean the cache when new plugin version is installed
			delete_transient( $this->transient_slug );
		}
	}

}

new Ec_Store_WL_Updater();

?>
<?php

class Ecwid_Customizer {
	public static function register($wp_customize) {
		
		if (version_compare(phpversion(), '5.3', '<')) {
			return;
		}
		
		$section_name = 'ecwid_settings';
		
		$wp_customize->add_section(
			$section_name,
			array(
				'title' => __('Footer Settings', 'mytheme'),
				'priority' => 100,
				'capability' => 'edit_theme_options',
				'description' => __('Change footer options here.', 'mytheme'),
				'active_callback' => function() { return Ecwid_Store_Page::is_store_page();}
			)
		);

		$wp_customize->add_setting( 'ecwid_show_minicart',
			array(
				'default' => 'f1f1f1',
				'type' => 'ecwid_cookie'
			)
		);
		$wp_customize->add_setting( 'ecwid_show_search',
			array(
				'default' => 'f1f1f1',
				'type' => 'ecwid_cookie'
			)
		);	
		
		$wp_customize->add_control( 'ecwid_show_search', array(
			'label'      => __( 'Display Search', 'documentation' ),
			'section'    => $section_name,
			'type'       => 'checkbox',
			'std'        => '1'
		) );


		$wp_customize->add_control( 'ecwid_show_minicart', array(
			'label'      => __( 'Display SMinic', 'documentation' ),
			'section'    => $section_name,
			'type'       => 'checkbox',
			'std'        => '1'
		) );
		
		$wp_customize->add_control( new WP_Customize_Control(
			$wp_customize,
			'footer_bg_color_control',
			array(
				'label'    => __( 'Footer Background Color', 'mytheme' ),
				'section'  => 'mytheme_footer_options',
				'settings' => 'footer_bg_color',
				'priority' => 10,
			)
		));

		$wp_customize->selective_refresh->add_partial( 'footer_bg_color_control', array(
			'selector' => '#ecwid-shopping-cart-shortcode', // You can also select a css class
		) );
	}
	
	public function update_settings()
	{
		
	}
	
	public function preview_settings()
	{
		
	}
}

add_action( 'customize_register' , array( 'Ecwid_Customizer', 'register' ) );
add_action( 'customize_preview_ecwid_cookie', array( 'Ecwid_Customizer', 'update_settings' ) );
add_action( 'customize_update_ecwid_cookie', array( 'Ecwid_Customizer', 'update_settings' ) );
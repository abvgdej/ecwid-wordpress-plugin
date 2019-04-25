<?php

$plugin_dir = '/Users/marvin/wporg/ecwid-shopping-cart/src';
$guten_dir = '/Users/marvin/wporg/ecwid-shopping-cart/gutenberg/src';

$store_to_script = array(
	'store' => 'block',
	'product' => 'product',
	'buynow' => 'buynow',
	'search' => 'search',
	'categories' => 'categories',
	'category-page' => 'category-page',
	'product-page' => 'product-page',
	'filters-page' => 'filters-page',	
	'cart-page' => 'cart-page'
);

$scss_plugin = "$plugin_dir/css/gutenberg/editor.scss";
$scss_guten = "$guten_dir/src/block/editor.scss";
$compiled_scss_guten = "$guten_dir/dist/blocks.editor.build.css";
$compiled_scss_plugin = "$plugin_dir/css/gutenberg/blocks.editor.build.css";
$compiled_js_guten = "$guten_dir/dist/blocks.build.js";
$compiled_js_plugin = "$plugin_dir/js/gutenberg/blocks.build.js";

chdir("$guten_dir");

$commands = [];

foreach ( $store_to_script as $plugin_name => $guten_name ) {
	$commands[] = "cp $plugin_dir/js/gutenberg/$plugin_name.jsx $guten_dir/src/$guten_name/block.js";
}
$commands[] = "cp $scss_plugin $scss_guten";
$commands[] = "npm run build";
$commands[] = "cp $compiled_scss_guten $compiled_scss_plugin";
$commands[] = "cp $compiled_js_guten $compiled_js_plugin";

foreach ( $store_to_script as $plugin_name => $guten_name ) {
        $commands[] = "cp $guten_dir/src/$guten_name/block.js $plugin_dir/js/gutenberg/$plugin_name.jsx";
}

foreach ($commands as $command) {
	echo $command . PHP_EOL;
	$result = exec($command, $output);
	echo implode(" ", $output) . PHP_EOL;
	if ( $result != 0 ) {
		break;
	}
}


echo "Over" . PHP_EOL;

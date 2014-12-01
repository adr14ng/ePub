<?php
/**
 * Plugin Name: CSUN ePub
 * Description: Take the catalog website and turn it into the catalog ePub
 * Version: 0.1
 * Author: Candace Walden
 * Author URI: http://candybrie.com
 */

	defined('ABSPATH') or die("No script kiddies please!");		//security
	
	$plug_in_dir = dirname(__FILE__);

	//Add settings page
	require_once $plug_in_dir . '/epub-options.php';
	$epub_options_page = new EpubOptions();


	function epub_activate() {
		if( !current_user_can('activate_plugins') )
			return;
	}
	register_activation_hook(__FILE__, 'epub_activate');
	
	function epub_style($hook) 
	{
		if($hook !== "tools_page_epub-create")
			return;
		
		$basedir = plugin_dir_url(__FILE__);
		
		wp_enqueue_style( 'epub-style', $basedir . 'style.css' );
		wp_enqueue_script( 'jquery-ui', $basedir.'jquery-ui.min.js');
		wp_enqueue_script( 'epub-script', $basedir.'script.js');
	}
	add_action( 'admin_enqueue_scripts', 'epub_style' );
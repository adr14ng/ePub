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
			
		epub_add_options();
	}
	register_activation_hook(__FILE__, 'epub_activate');
	
	function epub_uninstall() {
		if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
			exit();
			
		epub_remove_options();
	}
	register_uninstall_hook(__FILE__, 'epub_uninstall');
	
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
	
	function epub_add_options()
	{
		$order = array('options', 'cover', 'toc', 'undergrad', 'student-services',
			'special-programs', 'gened', 'grad', 'credential',
			'courses', 'policies', 'faculty', 'emeriti');
		$general = array(
			'title' => 'CSUN Catalog',
			'creator' => 'Undergraduate Studies',
			'language' => 'en-US',
			'rights' => '',
			'publisher' => 'California State University, Northridge',
		);
		$cover = array('title' => 'University Catalog');
		$toc = array('title' => "Table of Contents");
		$csun = array(
			'title' => "CSUN",
			'pages' => array(29206,29208,27367,27377,27378,27382,
				29210,29212,29221,29223,35172),
		);
		$undergrad = array(
			'title' => 'Undergraduate Programs',
			'pages' => array(29215,32736,29227,34971,34967,29225),
			'policies' => 'on',
			'proglist' => 'on'
		);
		$student_services = array(
			'title' => 'Student Services',
			'pages' => array(29219),
		);
		$special_programs = array(
			'title' => 'Special Programs',
			'pages' => array(29229),
		);
		$gened = array(
			'title'	=> 'General Education',
			'pages' => array(28561, 47095, 29162, 29160),
			'category' => 'on',
			'upper' => 'on',
			'ic' => 'on'
		);
		$grad = array(
			'title' => 'Research and Graduate Studies',
			'pages' => array(186),
			'proglist' => true,
			'certlist' => 28943,
		);
		$credential = array(
			'title' => 'Credential Office',
			'pages' => array(28825),
		);
		$courses = array(
			'title' => 'Courses of Study',
			'listTitle' => 'Colleges, Departments and Programs',
			'policies' => 'on',
			'categories'=> array('acct','afric','afrs','ais','anth','art','aas',
				'asian','at','biol','gbus','blaw','calif','cas','chem','chs',
				'cadv','ctva','cecm','cd','coms','comp','bus','deaf','econ',
				'elps','epc','ece','eed','cecs','engl','eoh','fcs','fin',
				'gws','geog','geol','hhd','hsci','hist','humsex','coh','huma',
				'js','jour','kin','lrs','ling','mgt','msem','mkt','math',
				'educ','meis','amc','me','mcll','mus','nurs','phil','pt',
				'phys','pols','psy','mpa','qs','rtm','rs','csm','sed','csbs',
				'swrk','soc','sped','som','th','univ','urbs'
			)
		);
		$policies = array(
			'title' => 'Policies',
			'categories' => array(134,139, 157,158,159,160,161,162),
		);
		$faculty = array('title' => 'Faculty and Administration');
		$emeriti = array('title' => "Emeriti");
	
	
		add_option('epub_order', $order, '', 'no');
		add_option('epub_general', $general, '', 'no');
		add_option('epub_cover', $cover, '', 'no');
		add_option('epub_toc', $toc, '', 'no');
		add_option('epub_csun', $csun, '', 'no');
		add_option('epub_undergrad', $undergrad, '', 'no');
		add_option('epub_student-services', $student_services, '', 'no');
		add_option('epub_special-programs', $special_programs, '', 'no');
		add_option('epub_gened', $gened, '', 'no');
		add_option('epub_grad', $grad, '', 'no');
		add_option('epub_credential', $credential, '', 'no');
		add_option('epub_courses', $courses, '', 'no');
		add_option('epub_policies', $policies, '', 'no');
		add_option('epub_faculty', $faculty, '', 'no');
		add_option('epub_emeriti', $emeriti, '', 'no');
	}
	
	function epub_remove_options()
	{
		//get epub order to try to find additional epub options to delete?
		delete_option('epub_order');
		delete_option('epub_general');
		delete_option('epub_cover');
		delete_option('epub_toc');
		delete_option('epub_csun');
		delete_option('epub_undergrad');
		delete_option('epub_student-services');
		delete_option('epub_special-programs');
		delete_option('epub_gened');
		delete_option('epub_grad');
		delete_option('epub_credential');
		delete_option('epub_courses');
		delete_option('epub_policies');
		delete_option('epub_faculty');
		delete_option('epub_emeriti');
	}
<?php

print_r($_POST);

//epub->plugs->wp-content->base
$base_url = dirname(dirname(dirname(dirname(__FILE__))));
require_once($base_url.'/wp-admin/admin.php');

if(isset($_POST['action']) && $_POST['action'] == 'epub-creation')
{
	//check_admin_referer('epub-creation');
	
	$plug_in_dir = dirname(__FILE__);

	//Add content filtering
	add_filter('the_content', 'filter_tags');
	add_filter('acf/format_value_for_api/type=wp_wysiwyg', 'filter_acf_tags', 10, 3);
	add_filter('acf/format_value_for_api/type=wysiwyg', 'filter_acf_tags', 10, 3);
	
	//Load the plugin
	require_once $plug_in_dir . '/file-structure.php';
	require_once $plug_in_dir . '/courses-of-study.php';
	require_once $plug_in_dir . '/content.php';
	
	list($content, $df_options) = default_values();
	
	if(isset($_POST['default']))
	{
		set_time_limit(300);
		create_book($content, $df_options);
	}
	else
	{
		/*$options = array_intersect_key($_POST, $df_options);
		array_merge($df_options, $options);
		
		create_book($content, $options);*/
	}
	
	/*
	//Redirect back to page
	if(isset($_POST['return']))
		wp_redirect( $_POST['return'] );
	else
		wp_redirect( admin_url() );
	
	*/
}


function default_values() {
	
	$content = array(
		'cover' => array( 'title' => 'Univeristy Catalog'),
		'toc' => array('title' => "Table of Contents"),
		'csun' => array(
					'title' => "CSUN",
					'pages' => array(
							0 	=> 29206,
							1 	=> 29208,
							//2 => , academic calendar
							3 	=> 27367,
							4 	=> 27377,
							5 	=> 27378,
							6 	=> 27382,
							7 	=> 29210,
							8 	=> 29212,
							9 	=> 29221,
							10 	=> 29223,
							11 	=> 35172
						),
				),
		'undergrad' => array(
					'title' => 'Undergraduate Programs',
					'pages' => array(
							0 	=> 29215,
							1 	=> 32736,
							2 	=> 29227,
							3 	=> 34971,
							4 	=> 34967,
							5 	=> 29225,
						),
					'policies' => true,
					'proglist' => true
				),
		'student-services' => array(
					'title' => 'Student Services',
					'pages' => array(
							0 	=> 29219
						),
				),
		'special-programs' => array(
					'title' => 'Special Programs',
					'pages' => array(
							0 	=> 29229
						),
				),
		'gened' => array(
					'title'	=> 'General Education',
					'pages' => array(
							0 	=> 28561,
							1 	=> 29162,
							2 	=> 29160
						),
					'category' => true,
					'upper' => true,
					'ic' => true
				),
		'grad' => array(
					'title' => 'Research and Graduate Studies',
					'pages' => array(
							0 	=> 186
						),
					'proglist' => true,
				),
		'credential' => array(
					'title' => 'Credential Office',
					'pages' => array(
							0 	=> 28825
						),
					'proglist' => true,
				),
		'study' => array(
					'title' => 'Courses of Study',
					'listTitle' => 'Colleges, Departments and Programs',
					'categories' => array(
							'acct' 	=> 17,
							'afric' => 3,
							'afrs' 	=> 60,
							'ais' 	=> 4,
							'anth' 	=> 5,
							'art' 	=> 6,
							'aas' 	=> 7,
							'asian' => 8,
							'at' 	=> 452,
							'biol' 	=> 9,
							'gbus' 	=> 25,
							'blaw' 	=> 18,
							'calif' => 10,
							'cas' 	=> 12,
							'chem' 	=> 11,
							'chs' 	=> 13,
							'cadv' 	=> 31,
							'ctva' 	=> 32,
							'cecm' 	=> 50,
							'cd' 	=> 37,
							'coms' 	=> 36,
							'comp' 	=> 38,
							'bus' 	=> 457,	//college
							'deaf' 	=> 40,
							'econ' 	=> 23,
							'elps' 	=> 41,
							'epc' 	=> 42,
							'ece' 	=> 43,
							'eed' 	=> 44,
							'cecs' 	=> 100,	//college
							'engl' 	=> 101,
							'eoh' 	=> 102,
							'fcs' 	=> 47,
							'fin' 	=> 24,
							'gws' 	=> 88,
							'geog' 	=> 90,
							'geol' 	=> 103,
							'hhd' 	=> 53,	//college
							'hsci'	=> 55,
							'hist' 	=> 64,
							'humsex'=> 428,
							'coh' 	=> 429,	//college
							'huma' 	=> 489,
							'js' 	=> 94,
							'jour' 	=> 95,
							'kin' 	=> 105,
							'lrs' 	=> 107,
							'ling' 	=> 108,
							'mgt' 	=> 27,
							'msem' 	=> 69,
							'mkt' 	=> 28,
							'math' 	=> 70,
							'educ' 	=> 167,	//college
							'meis' 	=> 74,
							'amc' 	=> 168,	//college
							'me' 	=> 73,
							'mcll' 	=> 75,
							'mus' 	=> 83,
							'nurs' 	=> 56,
							'phil' 	=> 61,
							'pt' 	=> 96,
							'phys' 	=> 120,
							'pols' 	=> 63,
							'psy' 	=> 121,
							'mpa' 	=> 122,
							'qs' 	=> 123,
							'rtm' 	=> 124,
							'rs' 	=> 91,
							'csm' 	=> 169,
							'sed' 	=> 125,
							'csbs' 	=> 166,	//college
							'swrk' 	=> 132,
							'soc' 	=> 126,
							'sped' 	=> 127,
							'som' 	=> 29,
							'th' 	=> 129,
							'univ' 	=> 130,	//need special case
							'urbs' 	=> 131,
						),
					'policies' => true
				),
		'policies' => array(
					'title' => 'Policies',
					'categories' => array(
							'enrollment-regulations'			=> 134,
							'fees' 								=> 139,
							'privacy-and-student-information'	=> 157,
							'nondiscrimination-policy' 			=> 158,
							'student-conduct' 					=> 159,
							'admission-procedures-and-policies' => 160,
							'university-regulations' 			=> 161,
							'other-policies' 					=> 162
						),
				),
		'faculty' => array('title' => 'Faculty and Administration'),
		'emeriti'  => array('title' => "Emeriti"),
	);
	
	$options = array(
		'title' => 'CSUN Catalog',
		'creator' => 'Undergraduate Studies',
		'language' => 'en-US',
		'rights' => '',
		'publisher' => 'California State Univeristy, Northridge',
		'bookid' => '20142015CSUN'
	);
		
	return array($content, $options);
}

function filter_tags($content)
{
	//remove space things
	$content = preg_replace('/&nbsp;/i', '', $content);

	//remove span tags
	$content = preg_replace('/<\s*\/*(span)[^>]*>/i', '', $content);
	
	//remove style attributes
	$content = preg_replace('/style=((\'.*?\')|(".*?"))/i', '', $content);
	
	//remove iFrame and it's content
	$content = preg_replace('/<iframe (.)*<\/iframe>/i', '', $content);
	
	//remove internal links
	$content = preg_replace('/<\s*a[^>]*href="http:\/\/www.csun.edu\/catalog[^>]*>([\s\S]*?)<\s*\/a>/i', '$1', $content);
	
	return $content;
}

function filter_acf_tags($value, $post_id, $field)
{
	$value = apply_filters('the_content',$value);
	return $value;
}

<?php


//epub->plugs->wp-content->base
$base_url = dirname(dirname(dirname(dirname(__FILE__))));
require_once($base_url.'/wp-admin/admin.php');

//epub->plugs->wp-content
$book_dir = $base_url.'/wp-content/uploads/epub';

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
		
		$dir = sanitize_file_name($df_options['title']);
		$zip_file = $book_dir.'/'.$dir.'.epub';
		$book_dir .= '/'.$dir;
		
		echo $book_dir;
		
		mkdir($book_dir, 0775, true);
		create_book($content, $df_options);
	}
	else
	{
		$options = array_intersect_key($_POST, $df_options);
		array_merge($df_options, $options);

		$dir = sanitize_file_name($options['title']);
		$zip_file = $book_dir.'/'.$dir.'.epub';
		$book_dir .= '/'.$dir;

		mkdir($book_dir, 0775, true);
		create_book($content, $options);
	}

	
	create_archive($zip_file);
	
	header("Content-disposition: attachment; filename=".$dir.".epub");
	header("Content-type: application/epub+zip");
	readfile($zip_file);
	
	if(isset($_POST['return']))
		wp_redirect( $_POST['return'] );
	else
		wp_redirect( admin_url() );
}

function create_archive($epubFile)
{
	global $book_dir;
	$excludes = array('mimetype.zip');
	
	$mimeZip = $book_dir.'/mimetype.zip';
	$zipFile = sys_get_temp_dir() . '/book.zip';
	
	if(!copy($mimeZip, $zipFile))
		throw new Exception("Unable to copy temporary archive file.");
		
	$zip = new ZipArchive();
	if ($zip->open($zipFile, ZipArchive::CREATE) != true) {
		throw new Exception("Unable to open archive '$zipFile'");
	}
	
	$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($book_dir), RecursiveIteratorIterator::SELF_FIRST);
	foreach($files as $file)
	{
		if(in_array(basename($file), $excludes))
			continue;
		
		if(is_dir($file))
		{
			$zip->addEmptyDir(str_replace("$book_dir/", '', "$file/"));
		}
		elseif(is_file($file)) {
			$zip->addFromString(str_replace("$book_dir/", '', $file), file_get_contents($file));
		}
	}
	
	$zip->close();
	
	rename($zipFile, $epubFile);
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
					'pages' => 186,
					'proglist' => true,
					'certlist' => 28943,
				),
		'credential' => array(
					'title' => 'Credential Office',
					'pages' => array(
							0 	=> 28825
						),
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
		'publisher' => 'California State University, Northridge',
		'bookid' => '20142015CSUN',
		'cover' => 'default',
	);
		
	return array($content, $options);
}

function filter_tags($content)
{
	//remove space things
	$content = preg_replace('/&nbsp;/i', '', $content);

	//remove span tags
	$content = preg_replace('/<\s*\/*(span)[^>]*>/i', '', $content);
	
	//remove center tags
	$content = preg_replace('/<\s*\/*(center)[^>]*>/i', '', $content);
	
	//remove section tags
	$content = preg_replace('/<\s*\/*(section)[^>]*>/i', '', $content);
	
	//remove article tags
	$content = preg_replace('/<\s*\/*(article)[^>]*>/i', '', $content);
	
	//change list-style to class
	$content = preg_replace('/style=[\'|"]list-style-type:([^;]*);[\'|"]/i', 'class="$1"', $content);
	
	//remove style attributes
	$content = preg_replace('/style=((\'.*?\')|(".*?"))/i', '', $content);
	
	//remove start
	$content = preg_replace('/start=((\'.*?\')|(".*?"))/i', '', $content);
	
	//remove type
	$content = preg_replace('/type=((\'.*?\')|(".*?"))/i', '', $content);
	
	//remove target
	$content = preg_replace('/target=((\'.*?\')|(".*?"))/i', '', $content);
	
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

function lower_headings($content, $level)
{
	$heading = h.$level;
	
	while(strpos($content, $heading) !== FALSE) : 
		//remove h6
		$content = preg_replace('/(<\s*\/*)(h6)([^>]*>)/i', '$1p$3', $content);
		//move down h5
		$content = preg_replace('/(<\s*\/*)(h5)([^>]*>)/i', '$1h6$3', $content);
		//move down h4
		$content = preg_replace('/(<\s*\/*)(h4)([^>]*>)/i', '$1h5$3', $content);
		//move down h3
		$content = preg_replace('/(<\s*\/*)(h3)([^>]*>)/i', '$1h4$3', $content);
		//move down h2
		$content = preg_replace('/(<\s*\/*)(h2)([^>]*>)/i', '$1h3$3', $content);
		//move down h1
		$content = preg_replace('/(<\s*\/*)(h1)([^>]*>)/i', '$1h2$3', $content);
	endwhile;

	return $content;
}

function clear_double_headings($content){
	$content = preg_replace('/<[^>]*extra-header[^>]*>[\s\S]*?<[^>]*>/i', '', $content);
	return $content;
}

function add_ids($content) {
	//ensure h2's have ids
	$content = preg_replace_callback(
		'/<h2 (?:(?!id=))+([^>]*>)(.*?)<\/h2>/i', 
		function ($perams) {
			$id = strtolower(sanitize_key(strip_tags($perams[2])));
			return '<h2 id="'.$id.'" '.$perams[1].$perams[2].'</h2>';
		},
		$content);
		
	return $content;
}

function get_sublinks($content) {		
	//get the ids of the h2s
	preg_match_all( '/<h2 id="([^"]*)"([^>]*)>(.*?)<\/h2>/i', $content, $out, PREG_SET_ORDER);
	
	//make sublinks
	foreach($out as $key=>$item){
		$sublink[$key]['title'] = strip_tags($item[3]);
		$sublink[$key]['file'] = $item[1];
	}
	
	return $sublink;
}

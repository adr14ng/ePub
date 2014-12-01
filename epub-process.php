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
	
	list($df_content, $df_options) = default_values();
	
	if(isset($_POST['default']))
	{
		set_time_limit(300);
		
		$dir = sanitize_file_name($df_options['title']);
		$zip_file = $book_dir.'/'.$dir.'.epub';
		$book_dir .= '/'.$dir;
		
		echo $book_dir;
		
		mkdir($book_dir, 0775, true);
		create_book($df_content, $df_options);
	}
	else
	{
		print_r($_POST);
		return;
		/*
		$options = array_intersect_key($_POST['options'], $df_options);
		array_merge($df_options, $options);

		$dir = sanitize_file_name($options['title']);
		$zip_file = $book_dir.'/'.$dir.'.epub';
		$book_dir .= '/'.$dir;

		mkdir($book_dir, 0775, true);
		create_book($_POST['content'], $options);
		*/
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
		'cover' => array( 'title' => 'University Catalog'),
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
							0	=> 186
						),
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
							0	=> 'acct',
							1	=> 'afric',
							2	=> 'afrs',
							3	=> 'ais',
							4	=> 'anth',
							5	=> 'art',
							6	=> 'aas',
							7	=> 'asian',
							8	=> 'at',
							9	=> 'biol',
							10	=> 'gbus',
							11	=> 'blaw',
							12	=> 'calif',
							13	=> 'cas',
							14	=> 'chem',
							15	=> 'chs',
							16	=> 'cadv',
							17	=> 'ctva',
							18	=> 'cecm',
							19	=> 'cd',
							20	=> 'coms',
							21	=> 'comp',
							22	=> 'bus',	//college
							23	=> 'deaf',
							24	=> 'econ',
							25	=> 'elps',
							26	=> 'epc',
							27	=> 'ece',
							28	=> 'eed',
							29	=> 'cecs',	//college
							30	=> 'engl',
							31	=> 'eoh',
							32	=> 'fcs',
							33	=> 'fin',
							34	=> 'gws',
							35	=> 'geog',
							36	=> 'geol',
							37	=> 'hhd',	//college
							38	=> 'hsci',
							39	=> 'hist',
							40	=> 'humsex',
							41	=> 'coh',	//college
							42	=> 'huma',
							43	=> 'js',
							44	=> 'jour',
							45	=> 'kin',
							46	=> 'lrs',
							47	=> 'ling',
							48	=> 'mgt',
							49	=> 'msem',
							50	=> 'mkt',
							51	=> 'math',
							52	=> 'educ',	//college
							53	=> 'meis',
							54	=> 'amc',	//college
							55	=> 'me',
							56	=> 'mcll',
							57	=> 'mus',
							58	=> 'nurs',
							59	=> 'phil',
							60	=> 'pt',
							61	=> 'phys',
							62	=> 'pols',
							63	=> 'psy',
							64	=> 'mpa',
							65	=> 'qs',
							66	=> 'rtm',
							67	=> 'rs',
							68	=> 'csm',	//college
							69	=> 'sed',
							70	=> 'csbs',	//college
							71	=> 'swrk',
							72	=> 'soc',
							73	=> 'sped',
							74	=> 'som',
							75	=> 'th',
							76	=> 'univ',	//need special case
							77	=> 'urbs',
						),
					'policies' => true
				),
		'policies' => array(
					'title' => 'Policies',
					'categories' => array(
							0	=> 134,
							1 	=> 139,
							2	=> 157,
							3 	=> 158,
							4 	=> 159,
							5 	=> 160,
							6 	=> 161,
							7 	=> 162
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

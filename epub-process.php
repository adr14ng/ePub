<?php


//epub->plugs->wp-content->base
$base_url = dirname(dirname(dirname(dirname(__FILE__))));
require_once($base_url.'/wp-admin/admin.php');

//epub->plugs->wp-content
$book_dir = $base_url.'/wp-content/uploads/epub';

if(isset($_POST['action']) && $_POST['action'] == 'epub-creation')
{
	check_admin_referer('epub-creation');
	
	$plug_in_dir = dirname(__FILE__);

	//Add content filtering
	add_filter('the_content', 'filter_tags');
	add_filter('acf/format_value_for_api/type=wp_wysiwyg', 'filter_acf_tags', 10, 3);
	add_filter('acf/format_value_for_api/type=wysiwyg', 'filter_acf_tags', 10, 3);
	
	//Load the plugin
	require_once $plug_in_dir . '/file-structure.php';
	require_once $plug_in_dir . '/courses-of-study.php';
	require_once $plug_in_dir . '/content.php';
	
	if(isset($_POST['submit']) && $_POST['submit'] === 'submit')
	{
		epub_save_options();
		
		$options = $_POST['options'];
		$dir = sanitize_file_name($options['bookid']);
		$name = $dir.'.epub';
		$zip_file = $book_dir.'/'.$name;
		$book_dir .= '/'.$dir;

		mkdir($book_dir, 0775, true);
		
		
		if(isset($_FILES['cover-image']) && (stripos($_FILES['cover-image']['type'], 'image') !== FALSE))
		{
			mkdir($book_dir.'/OEBPS/images', 0775, true);
			$image = explode('.', $_FILES['cover-image']['name']);
			$cover_image = 'images/cover.'.$image[1];
			if(move_uploaded_file($_FILES['cover-image']['tmp_name'], $book_dir.'/OEBPS/'.$cover_image))
			{
				$_POST['content']['cover']['image'] = $cover_image;
				$options['cover'] = $cover_image;
				$options['cover-type'] = $_FILES['cover-image']['type'];
			}
		}
		
		create_book($_POST['content'], $options);
		
		create_archive($zip_file);
		
		send_file($zip_file, $name);
		
		exit();
	}
	elseif(isset($_POST['submit']) && $_POST['submit'] === 'save')
	{
		epub_save_options();
	}
	elseif(isset($_POST['submit']) && $_POST['submit'] === 'default')
	{
		$new_options = default_options();
		epub_save_options($new_options);
	}

	if(isset($_POST['return']))
		wp_safe_redirect( $_POST['return'] );
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

function send_file( $file, $name )
{
	if ( !is_file( $file ) )
	{
		header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
		exit;
	}
	elseif ( !is_readable( $file ) )
	{
		header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
		exit;
	}
	else
	{
		header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
		header("Pragma: public");
		header("Expires: 0");
		header("Accept-Ranges: bytes");
		header("Connection: keep-alive");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-type: application/epub+zip");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=\"$name\"");
		header('Content-Length: '.filesize($file));
		header("Content-Transfer-Encoding: binary");
		ob_end_clean(); //Fix to solve "corrupted compressed file" error
		readfile($file);
	}
}

/**
 *	Saves the configuration of the ePub for the next creation.
 */
function epub_save_options($new_content = false)
{
	if(!empty($new_content))
	{
		//the two arrays we have are options and content
		$options = $new_content['options'];
		$content = $new_content['content'];
	}
	else
	{
		//the two arrays we have are options and content
		$options = $_POST['options'];
		$content = $_POST['content'];
	}
	
	//options has its own entry in the database
	$options = epub_sanitize_options('options', $options);
	update_option('epub_general', $options);
	$order[] = 'options';		//options will always be first
	
	//each level under content has it's own entry in the database
	foreach($content as $key => $item)
	{
		$key = sanitize_key($key);
		$key = check_new_key($key, $item);
		$item = epub_sanitize_options($key, $item);
		update_option('epub_'.$key, $item);
		$order[] = $key;		//keep track of the order of the content
	}
	
	update_option('epub_order', $order);
}

/**
 * Creates the key name when a new page/group is added
 */
function check_new_key($key, $item)
{
	if(strpos($key, 'new') !== FALSE)
	{
		if(strpos($key, 'page') !== FALSE)
		{
			if(isset($item['title']))
			{
				$key = sanitize_key($item['title']);
			}
		}
		elseif(strpos($key, 'group') !== FALSE)
		{
			if(isset($item['title']))
			{
				$key = sanitize_key($item['title']);
				$key .='-group';
			}
		}
	}
	
	return $key;
}

/**
 *	Sanitizes an option array before entering it into the database
 */
function epub_sanitize_options($key, $item)
{
	foreach($item as $k => $v)
	{
		$k = sanitize_key($k);
		if($k === 'categories' && $key === 'courses')		//array of depts
		{
			$safe[$k] = epub_sanitize_cats($v);
		}
		elseif($k === 'pages' || $k === 'categories' )		//array of ids
		{
			$safe[$k] = epub_sanitize_ids($v);
		}
		else
		{
			$safe[$k] = sanitize_text_field($v);
		}
	}

	return $safe;
}

/**
 *	Ensures that the dept slug array is sanitized
 */
function epub_sanitize_cats($content)
{
	//ignore keys, we just want the order
	foreach($content as $v)
	{
		$safe[] = sanitize_title($v);
	}

	return $safe;
}

/**
 *	Ensures that the page id array is sanitized
 */
function epub_sanitize_ids($content)
{
	foreach($content as $v)
	{
		$safe[] = (int)$v;
	}

	return $safe;
}

/**
 *	Filter out tags/entities that cause problems in XHTML
 */
function filter_tags($content)
{
	//remove space things
	$content = preg_replace('/&nbsp;/i', ' ', $content);

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
	$content = preg_replace('/<\s*a[^>]*href="http:\/\/www(?:test)?.csun.edu\/catalog[^>]*>([\s\S]*?)<\s*\/a>/i', '$1', $content);
	
	return $content;
}

/**
 *	Add filters to ACFs
 */
function filter_acf_tags($value, $post_id, $field)
{
	$value = apply_filters('the_content',$value);
	return $value;
}

/**
 *	Reduces header levels to below the level indicated 
 *	(e.g. if level=2, the highest header will be H3)
 *  This method keeps the relative header levels unless
 *	it drops below H6.
 */
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

/**
 *	Removes headers with the extra-header class. Ensures
 *  certain headers that are printed twice on the website
 *	aren't in the book.
 */
function clear_double_headings($content){
	$content = preg_replace('/<[^>]*extra-header[^>]*>[\s\S]*?<[^>]*>/i', '', $content);
	return $content;
}

/**
 *	Adds IDs to H2s if the do not already have one.
 *  These IDs can be used to create sublinks.
 */
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

/**
 *	Locates H2's and their id and creates a link item
 *	based on this information for use in TOC methods
 */
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

function default_options()
{
	$options = array
	(
		"title" => "University Catalog",
		"creator" => "Undergraduate Studies",
		"language" => "en-US",
		"rights" => "",
		"publisher" => "California State University, Northridge",
		"bookid" => "CSUN1291645180",
	);
	$content = array
	(
		"cover" => array
		(
			"title" => "University Catalog",
		),
		"toc" => array
		(
			"title" => "Table of Contents",
		),
		"csun" => array
		(
			"title" => "CSUN",
			"pages" => array
			(
				0 => 29194,
				1 => 29206,
				2 => 59000,
				3 => 33662,
				4 => 29210,
				5 => 27367,
				6 => 27378,
				7 => 27377,
				8 => 27382,
				9 => 59579,
				10 => 59586,
				11 => 59583,
				12 => 29221,
				13 => 29223,
				14 => 35172,
			),
		),
		"undergrad" => array
		(
			"title" => "Undergraduate Programs",
			"admissionpol" => "on",
			"pages" => array
			(
				0 => 34967,
				1 => 34971,
			),
			"policies" => "on",
			"proglist" => "on",
		),
		"undergraduate-services-group" => array
		(
			"title" => "Undergraduate Services",
			"categories" => array
			(
				0 => 580,
			),
		),
		"student-services-centereop-satellite-group" => array
		(
			"title" => "Student Services Center/EOP Satellite",
			"categories" => array
			(
				0 => 583,
			),
		),
		"student-services-group" => array
		(
			"title" => "Student Services",
			"categories" => array
			(
				0 => 581,
			),
		),
		"special-programs-group" => array
		(
			"title" => "Special Programs",
			"categories" => array
			(
				0 => 584,
			),
		),
		"gened" => array
		(
			"title" => "General Education",
			"pages" => array
			(
				0 => 28561,
				1 => 47095,
				2 => 29162,
				3 => 29160,
			),
			"category" => "on",
			"upper" => "on",
			"ic" => "on",
		),
		"grad" => array
		(
			"title" => "Research and Graduate Studies",
			"pages" => array
			(
				0 => 186,
			),
			'policies' => 'on',
			"proglist" => "on",
			"certlist" => "on",
		),
		"credential" => array
		(
			"title" => "Credential Office",
			"pages" => array
			(
				0 => 28825,
			),
		),
		"thetsengcollege" => array
		(
			"title" => "The Tseng College",
			"pages" => array
			(
				0 => 35172,
			),
		),
		"courses" => array
		(
			"title" => "Courses of Study",
			"listTitle" => "Colleges, Departments and Programs",
			"policies" => "on",
			"categories" => array
			(
				0 => "acctis",
				1 => "afric",
				2 => "afrs",
				3 => "ais",
				4 => "anth",
				5 => "art",
				6 => "aas",
				7 => "asian",
				8 => "at",
				9 => "biol",
				10 => "gbus",
				11 => "bhp",
				12 => "blaw",
				13 => "calif",
				14 => "cas",
				15 => "chem",
				16 => "chs",
				17 => "cadv",
				18 => "ctva",
				19 => "cecm",
				20 => "cd",
				21 => "coms",
				22 => "comp",
				23 => "bus",
				24 => "deaf",
				25 => "econ",
				26 => "elps",
				27 => "epc",
				28 => "ece",
				29 => "eed",
				30 => "cecs",
				31 => "engl",
				32 => "eoh",
				33 => "fcs",
				34 => "fin",
				35 => "gws",
				36 => "geog",
				37 => "geol",
				38 => "hhd",
				39 => "hsci",
				40 => "hist",
				41 => "humsex",
				42 => "coh",
				43 => "huma",
				44 => "js",
				45 => "jour",
				46 => "kin",
				47 => "lrs",
				48 => "ling",
				49 => "mgt",
				50 => "msem",
				51 => "mkt",
				52 => "math",
				53 => "me",
				54 => "educ",
				55 => "meis",
				56 => "amc",
				57 => "mcll",
				58 => "mus",
				59 => "nurs",
				60 => "phil",
				61 => "pt",
				62 => "phys",
				63 => "pols",
				64 => "mpp",
				65 => "psm",
				66 => "psy",
				67 => "mpa",
				68 => "qs",
				69 => "rtm",
				70 => "rs",
				71 => "csm",
				72 => "sed",
				73 => "csbs",
				74 => "swrk",
				75 => "soc",
				76 => "sped",
				77 => "som",
				78 => "th",
				79 => "univ",
				80 => "urbs",
			),
		),
		"policies" => array
		(
			"title" => "Policies",
			"categories" => array
			(
				0 => 134,
				1 => 139,
				2 => 157,
				3 => 158,
				4 => 159,
				5 => 160,
				6 => 161,
				7 => 595,
			),
		),
		"faculty" => array
		(
			"title" => "Faculty and Administration",
		),
		"emeriti" => array
			(
				"title" => "Emeriti",
			),
	);
	
	return array('options' => $options, 'content' => $content);
}
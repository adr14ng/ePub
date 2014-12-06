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
	
	if(isset($_POST['submit']) && $_POST['submit'] === 'Submit')
	{
		epub_save_options();
		
		$options = $_POST['options'];
		$dir = sanitize_file_name($options['title']);
		$zip_file = $book_dir.'/'.$dir.'.epub';
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
		
		header("Content-disposition: attachment; filename=".$dir.".epub");
		header("Content-type: application/epub+zip");
		readfile($zip_file);
	}
	elseif(isset($_POST['submit']) && $_POST['submit'] === 'Save')
	{
		epub_save_options();
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

function epub_save_options()
{
	$options = $_POST['options'];
	$content = $_POST['content'];
	
	$options = epub_sanitize_options('options', $options);
	update_option('epub_general', $options);
	$order[] = 'options';
	
	foreach($content as $key => $item)
	{
		$key = sanitize_key($key);
		$item = epub_sanitize_options($key, $item);
		update_option('epub_'.$key, $item);
		$order[] = $key;
	}
	
	update_option('epub_order', $order);
	
	return array($options, $content);
}

function epub_sanitize_options($key, $item)
{
	foreach($item as $k => $v)
	{
		$k = sanitize_key($k);
		if($k === 'categories' && $key === 'courses')
		{
			$safe[$k] = epub_sanitize_cats($v);
		}
		elseif($k === 'pages' || $k === 'categories' )
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

function epub_sanitize_cats($content)
{
	//ignore keys, we just want the order
	foreach($content as $v)
	{
		$safe[] = sanitize_title($v);
	}

	return $safe;
}

function epub_sanitize_ids($content)
{
	foreach($content as $v)
	{
		$safe[] = (int)$v;
	}

	return $safe;
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

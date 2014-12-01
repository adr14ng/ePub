<?php
/** * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Print all the pages.
 *
 * 1. All Content
 *   a. Print all
 *   b. Header/Footer
 *   c. Print Pages
 * 2. Faculty
 * 3. Policies
 * 4. Graduate Studies
 * 5. General Education
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

function print_content($content) {
	global $book_dir;
	$dir = $book_dir.'/OEBPS/';
	$index = 0;

	foreach($content as $type => $section){
		$title = $section['title'];
		$filename = strtolower(sanitize_key($title));
		$file_names[$index] = array(
					'title' =>$title, 
					'file' => $filename
				);
		
		ob_start();		//start recording output
		
		//Cover
		if($type === 'cover')
		{
			print_header($title, false);
			cover();
			print_footer();
		}
		//Table of Contents
		else if($type === 'toc')
		{
			//wait til the end to do this
			$toc_index = $index;
		}
		//Undergraduate Programs
		else if($type === 'undergrad')
		{
			print_header($title);
			
			if(isset($section['pages']))
			{
				$file_names[$index]['sublinks'] = print_pages($section['pages'], $filename);
			}
				
			if(isset($section['policies']) && $section['policies'])
			{
				$file_names[$index]['sublinks'][] = undergrad_policies();
			}
				
			if(isset($section['proglist']) && $section['proglist'])
			{
				$file_names[$index]['sublinks'][] = degree_list();
			}
			
			print_footer();
		}
		//General Education
		else if($type === 'gened')
		{
			print_header($title);
			
			if(isset($section['pages']))
			{
				$file_names[$index]['sublinks'] = print_pages($section['pages'], $filename);
			}
				
			if(isset($section['category']) && $section['category'])
			{
				$file_names[$index]['sublinks'][] = categories();
			}
			
			if(isset($section['upper']) && $section['upper'])
			{
				$file_names[$index]['sublinks'][] = upper_division();
			}
			
			if(isset($section['ic']) && $section['ic'])
			{
				$file_names[$index]['sublinks'][] = info_competence();
			}
				
			print_footer();
		}
		//Graduate Studies
		else if($type === 'grad')
		{
			print_header($title);
			
			if(isset($section['pages'])) 
			{
				$file_names[$index]['sublinks'] = print_graduate_page($section['pages']);
			}
				
			if(isset($section['proglist']) && $section['proglist'])
			{
				$file_names[$index]['sublinks'][] = print_grad_program_list();
			}
			
			if(isset($section['certlist']) && $section['certlist'])
			{
				$file_names[$index]['sublinks'][] = print_certificate_list($section['certlist']);
			}
			
			print_footer();
		}
		//Credential
		else if($type === 'credential')
		{
			print_header($title);
			
			if(isset($section['pages']))
			{
				$file_names[$index]['sublinks'] = print_pages($section['pages'], $filename);
			}
				
			print_footer();
		}
		//Courses of Study
		else if($type === 'study')
		{
			$file_names[$index]['subpages'] = courses_of_study($section);
			
			print_header($title);
			print_footer();
		}
		//Policies
		else if($type === 'policies')
		{
			print_header($title);
			
			if(isset($section['categories']))
			{
				$file_names[$index]['sublinks'] = print_policies($section['categories']);
			}
			
			print_footer();
		}
		//Faculty
		else if($type === 'faculty')
		{
			print_header($title);
			
			$file_names[$index]['sublinks'] = print_faculty();
			
			print_footer();
		}
		//Emeriti
		else if($type === 'emeriti')
		{
			print_header($title);
			
			$file_names[$index]['sublinks'] = print_emeriti();
			
			print_footer();
		}
		//Non Special Requests
		else 
		{
			print_header($title);
			
			if(isset($section['pages']))
				$file_names[$index]['sublinks'] = print_pages($section['pages'], $filename);
				
			print_footer();
		}

		$content = ob_get_contents();		//save output
		ob_end_clean();						//discard buffer
		
		$f = fopen($dir.$filename.'.xhtml', "w");
		fwrite($f, $content);
		fclose($f);
		
		$index++;
	}
	
	//add table of contents
	if(isset($toc_index))
		table_of_contents($file_names, $toc_index, true);
	
	return $file_names;
}

function print_header($title, $h1 = true){ 
echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<title>
			<?php echo $title; ?>
		</title>
	</head>
	<body>
	<?php if($h1) : ?>
		<h1><?php echo $title; ?></h1>
	<?php endif;
}

function print_footer() { ?>

	</body>
</html>
<?php }

function cover() {
echo <<<COV
	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
		width="100%" height="100%" viewBox="0 0 573 800" preserveAspectRatio="xMidYMid meet">
		<image width="600" height="800" xlink:href="./images/cover.png" />
	</svg>
COV;
}

function print_pages($pages, $base_class = '') {	
	if(!empty($pages) && !is_array($pages))
		$pages = (array)$pages;
	
	if(is_array($pages)){
		foreach($pages as $page_id){
			$page = get_post($page_id);

			if($page)
			{
				$content = $page->post_content;
				$content = apply_filters('the_content', $content);
				$content = clear_double_headings($content);
				if(count($pages) > 1) {
					$content = lower_headings($content, 2);
					$sublinks[] = array('title' => $page->post_title, 'file' => $page->post_name);
				}
				else
				{
					$content = add_ids($content);
					$sublinks = get_sublinks($content);
				}
				
				$class = $base_class.' '.$page->post_name;
				
				echo '<div id="'.$page->post_name.'" class="'.$class.' page">';
				if((count($pages) > 1)&&($page->post_name !== 'general-education'))
					echo '<h2>'.$page->post_title.'</h2>';
				echo $content;
				echo '</div>';
			}
		
		}
	}
	
	return $sublinks;
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Table of Contents
 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

 function table_of_contents($file_names, $toc_index, $sublinks) {
	global $book_dir;
	$dir = $book_dir.'/OEBPS/';
	
	ob_start(); 
	print_header($file_names[$toc_index]['title'], false); ?>
	
		<h1><?php echo $file_names[$toc_index]['title']; ?></h1>
		<ol class="toc">
<?php
		foreach($file_names as $key => $link) :
			if($key !== $toc_index) :
				echo '<li><a href="'.$link['file'].'.xhtml">'.$link['title'].'</a></li>';
				
				if($sublinks && isset($link['sublinks']) && 
					!(strpos($link['title'], 'aculty') || strpos($link['title'], 'meriti'))) :
					echo '<li><ol class="toc sublink">';
					foreach($link['sublinks'] as $sublink) :
						echo '<li><a href="'.$link['file'].'.xhtml#'.$sublink['file'].'">'.$sublink['title'].'</a></li>';
					endforeach;
					echo '</ol></li>';
				endif;
				
			endif;
		endforeach
?>
		</ol>
<?php	
	print_footer();
	
	$content = ob_get_contents();		//save output
	ob_end_clean();						//discard buffer
		
	$f = fopen($dir.$file_names[$toc_index]['file'].'.xhtml', "w");
	fwrite($f, $content);
	fclose($f);
 }
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 
 * Faculty Methods
 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function print_faculty() {
	global $post;
	
	$query_faculty = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'faculty',
		'posts_per_page' => 10000,));
		
		
	if($query_faculty->have_posts()): ?>
		<div class="faculty">
			
			<?php while ($query_faculty->have_posts()) { 
				$query_faculty->the_post();
				
				if( strpos(get_the_term_list(  $post->ID, 'department_shortname', '', ', '), 'Emeriti') === FALSE):
					echo '<h2 id="'.$post->post_name.'">'.get_the_title().'</h2>';
					the_content();
					$sublinks[] = array('title' => $post->post_title, 'file' => $post->post_name);
				endif;
				
			} ?>
			
		</div>
	<?php endif;
	
	return $sublinks;
}

function print_emeriti() {
	global $post;
	
	$query_faculty = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'faculty',
		'department_shortname' => 'emeriti',
		'posts_per_page' => 1000,));
	
	
	if($query_faculty->have_posts()): ?>
		<div class="emeriti faculty">
			<?php while ($query_faculty->have_posts()) {
				$query_faculty->the_post();
				echo '<h2 id="'.$post->post_name.'">'.get_the_title().'</h2>';
				the_content();
				
				$sublinks[] = array('title' => $post->post_title, 'file' => $post->post_name);
			} ?>
		</div>
	<?php endif;
	
	return $sublinks;
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * Policy Methods
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function print_policies($terms) { 
	global $post;
	?>
	
	<div class="policies">
<?php 
		foreach($terms as $id) {
			$subsublinks = array();
			$term = get_term($id, 'policy_categories');			
			$query_policies = new WP_Query(array(
				'post_type' => 'policies', 
				'orderby' => 'title', 
				'order' => 'ASC',  
				'policy_categories' => $term->slug, 
				'posts_per_page' => 1000,));
								
			if($query_policies->have_posts()) 
			{
				echo '<h2 id="'.$term->slug.'">' . $term->name .'</h2>';
				while($query_policies->have_posts())
				{ 
					$query_policies->the_post();
					echo '<h3 id="'.$term->slug.'-'.$post->post_name.'">'.get_the_title().'</h3>';
					
					$content = get_the_content();
					$content = apply_filters('the_content', $content);
					$content = lower_headings($content, 3);
					
					echo $content;
					
					$subsublinks[] = array('title' => $post->post_title, 'file' => $term->slug.'-'.$post->post_name);
				}
				
				$sublinks[] = array('title' => $term->name, 'file' => $term->slug, 'sublinks' => $subsublinks);
			}
		}
?>
	</div>
<?php

	return $sublinks;
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * Graduate Studies Methods
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
 
 function print_graduate_page($page_id) {	

	$page = get_post($page_id[0]);

	if($page)
	{
		$content = $page->post_content;
		$content = apply_filters('the_content', $content);
		$content = add_ids($content);
		$sublinks = get_sublinks($content);
		list($policy_content, $policy_links) = grad_policies();
		
		foreach($sublinks as $key => $link) {
			if(isset($link['file']) && $link['file'] === 'grad-policies') {
				$sublinks[$key]['sublinks'] = $policy_links;
				break;
			}
		}
		
		$content = preg_replace('/(<div id="policies"[^>]*>)([\s\S]*?)(<\/div>)/i', '$1'.$policy_content.'$3', $content);
				
		$class = 'graduate-studies '.$page->post_name;
				
		echo '<div id="'.$page->post_name.'" class="'.$class.' page">';
		echo $content;
		echo '</div>';

	}
	
	return $sublinks;
}
 
 function grad_policies(){
	global $post;
	
	$query_policies = new WP_Query(array(
		'orderby' => 'name', 
		'order' => 'ASC',  
		'policy_tags' => 'graduate',
		'post_type' => 'policies',
		'posts_per_page' => 1000,));
	
	$policy_content = '';
	if($query_policies->have_posts()) : 
		while($query_policies->have_posts()) : $query_policies->the_post(); 
		
			$policy_content .= '<h3 id="'.$post->post_name.'">'.get_the_title().'</h3>';
			$policy_content .= apply_filters('the_content', get_the_content()); 
			
			$sublinks[] = array('title' => $post->post_title, 'file' => $post->post_name);
			
		endwhile;
	endif;
	
	return array($policy_content, $sublinks);
}
 
function print_grad_program_list() { 
	global $post;
?>
	<div class="grad-studies grad-programs">
	<h2 id="graduate-list">Graduate Degree Programs List</h2>
	<ul class = "degree-list">
<?php 
	$query_programs = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',  
		'degree_level' => 'master,doctorate',
		'posts_per_page' => 1000,));
					
	if($query_programs->have_posts()) : while($query_programs->have_posts()) : $query_programs->the_post();
		$program_title = get_grad_program_name();

		$post_option=get_field('option_title');
		if(isset($post_option)&&$post_option!=='')
			$program_title = $program_title.' - '.$post_option.' Option';
			
		$link = get_program_file($post->ID).'#'.$post->post_name;
							
		echo '<li><a href="'.$link.'">'.$program_title.'</a></li>';
		
	endwhile; endif;
?>
	</ul>
	</div>
<?
	return array('title' => 'Graduate Degree Programs List', 'file' => 'graduate-list');
}

function print_certificate_list($id) { 
	global $post;
?>
	<div class="grad-studies grad-programs">
	<h2 id="certificate-list">Post-Baccalaureate University Certificate Programs</h2>
	
<?php
	$page = get_post($id);
	$content = $page->post_content;
	$content = apply_filters('the_content', $content);
	echo $content;
?>	
	
	<ul class = "degree-list">
<?php 
	$query_programs = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',  
		'degree_level' => 'certificate',
		'posts_per_page' => 1000,));
					
	if($query_programs->have_posts()) : while($query_programs->have_posts()) : $query_programs->the_post();
		$program_title = get_grad_program_name();

		$post_option=get_field('option_title');
		if(isset($post_option)&&$post_option!=='')
			$program_title = $program_title.' - '.$post_option.' Option';
			
		$link = get_program_file($post->ID).'#'.$post->post_name;
							
		echo '<li><a href="'.$link.'">'.$program_title.'</a></li>';
		
	endwhile; endif;
?>
	</ul>
<?php
	$extra_list = get_field('program_list', $id);
	$extra_list = apply_filters('the_content', $extra_list);
	echo $extra_list;
?>
	</div>
<?
	return array('title' => 'Post-Baccalaureate University Certificate Programs', 'file' => 'certificate-list');
}

function get_grad_program_name() {
	$degree = get_field('degree_type');
	$program_title = get_the_title();

	if ($degree === 'certificate' || $degree === 'Certificate') {
		if (strpos($program_title, 'Certificate') === FALSE)
			$program_title .= ' Certificate';
	}
	else 
	{
		$program_title = $program_title.', '.$degree;
	}
	
	return $program_title;
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * General Education Methods
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
 function categories() { ?>
	<div class="gen-ed categories">
		<h2 id="gened-sec">General Education Sections</h2>
	<?php 
		$terms = get_terms('general_education');
		foreach($terms as $term) {

			if($term->slug !== 'ic' && $term->slug !== 'ud') {

				$query_policies = new WP_Query(array(
					'post_type' => 'courses', 
					'orderby' => 'title', 
					'order' => 'ASC',  
					'general_education' => $term->slug, 
					'posts_per_page' => 1000,));

				if($query_policies->have_posts()) {
					echo '<h3 id="'.$term->slug.'">'.$term->description.'</h3>';
					echo '<ul class="ge-course-list">';
					while($query_policies->have_posts()) {
						$query_policies->the_post();
						echo '<li>'.get_the_title().'</li>';
					}
					echo '</ul>';
					
					$sublinks[] = array('title' => $term->description, 'file' => $term->slug);
				}
			}
		} ?>
	</div>
<?php
	return array('title' => 'General Education Sections', 'file' => 'gened-sec', 'sublinks' => $sublinks);
}

function upper_division() {?>
	<div class="gen-ed upper-division">
		<h2 id="gened-ud">General Education - Upper Division</h2>
	<?php 
		$terms = get_terms('general_education');
		foreach($terms as $term) {

			if($term->slug !== 'ic' && $term->slug !== 'ud') {

				$query_policies = new WP_Query(array(
					'post_type' => 'courses', 
					'orderby' => 'title', 
					'order' => 'ASC',  
					'general_education' => 'ud+'.$term->slug, 
					'posts_per_page' => 1000,));

				if($query_policies->have_posts()) {
					echo '<h3 id="'.$term->slug.'-ud">'.$term->description.'</h3>';
					echo '<ul class="ge-course-list">';
					while($query_policies->have_posts()) {
						$query_policies->the_post();
						echo '<li>'.get_the_title().'</li>';
					}
					echo '</ul>';
					
					$sublinks[] = array('title' => $term->description, 'file' => $term->slug.'-ud');
				}
			}
		} ?>
	</div>
<?php
	return array('title' => 'Upper Division', 'file' => 'gened-ud', 'sublinks' => $sublinks);
}

function info_competence() {?>
	<div class="gen-ed information-competence">
		<h2 id="gened-ic">General Education - Information Competence</h2>
	<?php 
		$terms = get_terms('general_education');
		foreach($terms as $term) {

			if($term->slug !== 'ic' && $term->slug !== 'ud') {

				$query_policies = new WP_Query(array(
					'post_type' => 'courses', 
					'orderby' => 'title', 
					'order' => 'ASC',  
					'general_education' => 'ic+'.$term->slug, 
					'posts_per_page' => 1000,));

				if($query_policies->have_posts()) {
					echo '<h3 id="'.$term->slug.'-ic">'.$term->description.'</h3>';
					echo '<ul class="ge-course-list">';
					while($query_policies->have_posts()) {
						$query_policies->the_post();
						echo '<li>'.get_the_title().'</li>';
					}
					echo '</ul>';
					
					$sublinks[] = array('title' => $term->description, 'file' => $term->slug.'-ic');
				}
			}
		} ?>
	</div>
<?php
	return array('title' => 'Information Competence', 'file' => 'gened-ic', 'sublinks' => $sublinks);
}
 
 /* * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * Undergraduate Studies Methods
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function undergrad_policies(){
	global $post;
	
	$query_policies = new WP_Query(array(
		'orderby' => 'name', 
		'order' => 'ASC',  
		'policy_tags' => 'undergrad',
		'post_type' => 'policies',
		'posts_per_page' => 1000,));
		
	if($query_policies->have_posts()) : 
	?>
	<div class = "undergrad undergrad-policies">
		<h2 id="undergrad-policies">Undergraduate Programs, Policies and Procedures</h2>
		<?php while($query_policies->have_posts()) : $query_policies->the_post(); ?>
			<h3 id="<?php echo $post->post_name; ?>"><?php echo the_title(); ?></h3>
			<?php echo the_content(); 
			$sublinks[] = array('title' => $post->post_title, 'file' => $post->post_name);
			?>
		<?php endwhile; ?>
	</div>
	<?php endif;
	
	return array('title' => 'Undergraduate Programs, Policies and Procedures', 'file' => 'undergrad-policies', 'sublinks' => $sublinks);
}

function degree_list() {
	global $post;
	
	$query_programs = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',  
		'degree_level' => 'major',
		'post_type' => 'programs',
		'posts_per_page' => 1000,));
		
	if($query_programs->have_posts()) : ?>
		<div class="program-list undergrad">
		<h2 id="undergrad-degree-list">Undergraduate Degree Program List</h2>
		<ul class = "degree-list">
		<?php while($query_programs->have_posts()) : $query_programs->the_post(); 
			$link = get_program_file($post->ID).'#'.$post->post_name;
		?>
		<li>
		<a href="<?php echo $link; ?>">
<?php			$degree = get_field('degree_type');
			$program_title = get_the_title();
			$post_option=get_field('option_title');
			
			echo $program_title.', '.$degree;
			
			if(isset($post_option)&&$post_option!=='')
				echo ' - '.$post_option.' Option';
?>
		</a>
		</li>
		<?php endwhile; ?>
		</ul>
		</div>
	<?php endif;
	
	return array('title' => 'Undergraduate Degree Program List', 'file' => 'undergrad-degree-list');
}

function get_program_file($id) {
	$terms =  wp_get_post_terms( $id, 'department_shortname' );
		
	foreach($terms as $term){
	//ge and top level terms can't be the category
		if($term->parent != 0 && $term->parent != 511) {
			//save the description of the category that works
			$dpt = $term->description;
		}
	}
		
	if(!isset($dpt)){		//if it only has a top level
		foreach($terms as $term){
			if($term->slug !== 'ge') {
				//save the description of the category that works
				$dpt = $term->description;
			}
		}
	}
		
	$file = strtolower(sanitize_key($dpt)).'.xhtml';
	
	return $file;
}
	
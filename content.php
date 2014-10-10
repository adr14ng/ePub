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

	$dir = './book/OEBPS/';

	foreach($conent as $type => $section){
		$title = $section['title'];
		$filename = strtolower(sanitize_file_name($title));
		$file_names[] = array(
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
			$toc_index = count($file_names) - 1;
		}
		//Undergraduate Programs
		else if($type === 'undergrad')
		{
			print_header($title)
			
			if(isset($section['pages']))
				print_pages($section['pages'], $filename);
				
			if(isset($section['policies']) && $section['policies'])
				undergrad_policies();
				
			if(isset($section['proglist']) && $section['proglist'])
				degree_list();
			
			print_footer();
		}
		//General Education
		else if($type === 'gened')
		{
			print_header($title)
			
			if(isset($section['pages']))
				print_pages($section['pages'], $filename);
				
			if(isset($section['category']) && $section['category'])
				categories();
			
			if(isset($section['upper']) && $section['upper'])
				upper_division();
			
			if(isset($section['ic']) && $section['ic'])
				if_competence();
				
			print_footer();
		}
		//Graduate Studies
		else if($type === 'grad')
		{
			print_header($title)
			
			if(isset($section['pages']))
				print_pages($section['pages'], $filename);
				
			if(isset($section['proglist']) && $section['proglist'])
				print_grad_program_list();
			
			print_footer();
		}
		//Credential
		else if($type === 'credential')
		{
			print_header($title)
			
			if(isset($section['pages']))
				print_pages($section['pages'], $filename);
				
			if(isset($section['proglist']) && $section['proglist'])
				print_certificate_list();
				
			print_footer();
		}
		//Courses of Study
		else if($type === 'study')
		{
			$index = count($file_names) - 1;
			$file_names[$index]['subpages'] = courses_of_study($section);
			
			print_header($title)
			print_footer();
		}
		//Policies
		else if($type === 'policies')
		{
			print_header($title)
			
			if(isset($section['categories']))
				print_policies($section['categories']);
			
			print_footer();
		}
		//Faculty
		else if($type === 'faculty')
		{
			print_header($title)
			
			print_faculty();
			
			print_footer();
		}
		//Emeriti
		else if($type === 'emeriti')
		{
			print_header($title)
			
			print_emeriti();
			
			print_footer();
		}
		//Non Special Requests
		else 
		{
			print_header($title)
			
			if(isset($section['pages']))
				print_pages($section['pages'], $filename);
				
			print_footer();
		}

		$content = ob_get_contents();		//save output
		ob_end_clean();						//discard buffer
		
		$f = fopen($dir.$filename.'.xhtml', "w");
		fwrite($f, $content);
		fclose($f);
	}
	
	//add table of contents
	if(isset($toc_index))
		table_of_contents($file_names, $toc_index);
	
	return $file_names;
}

function print_header($title, $h1 = true){ ?>
<?xml version='1.0' encoding='utf-8'?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>
			<?php echo $title; ?>
		</title>
	</head>
	<body>
	<?php if($h1) ?>
		<h1><?php echo $title; ?></h1>
<?php }

function print_footer() { ?>

	</body>
</html>
<?php }

function cover() {?>
	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
	  width="100%" height="100%" viewBox="0 0 573 800" preserveAspectRatio="xMidYMid meet">
	 <image width="600" height="800" xlink:href="./images/cover.png" />
 </svg>
<?php}

function print_pages($pages, $class = '') {

	if(!empty($pages) && !is_array($pages))
		$pages = (array)$pages;
	
	if(is_array($pages)){
		foreach($pages as $page_slug){
			$page = get_posts(array('name' => $page_slug, 'post_type' => 'page'));
			
			if($page)
			{
				echo '<div class="'.$class.' page">'
				echo '<h2>'.$page[0]->title.'</h2>';
				echo $page[0]->post_content;
				echo '</div>';
			}
		
		}
	}
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Table of Contents
 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

 function table_of_contents($file_names, $toc_index) {
	$dir = './book/OEBPS/';
	
	ob_start();
	print_header($file_names[$toc_index]['title']);
	
	foreach($file_names as $link) : 
		echo '<p><a href="'.$link['file'].'.xhtml">'.$link['title'].'</a></p>';
	
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
	$query_faculty = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'faculty'
		'department_shortname' => '-emeriti',
		'posts_per_page' => 1000,));
		
		
	if($query_faculty->have_posts()): ?>
		<div class="faculty">
			<h1>Faculty and Administration</h1>
			
			<?php while ($query_faculty->have_posts()) { 
				$query_faculty->the_post();
				
				echo '<h2>'.get_the_title().'</h2>';
				the_content();
				
			} ?>
			
		</div>
	<?php endif; 
}

function print_emeriti() {
	$query_faculty = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'faculty'
		'department_shortname' => 'emeriti',
		'posts_per_page' => 1000,));
	
	
	if($query_faculty->have_posts()): ?>
		<div class="emeriti faculty">
			<h1>Emeritus Faculty</h1>
			<?php while ($query_faculty->have_posts()) {
				$query_faculty->the_post();
				echo '<h2>'.get_the_title().'</h2>';
				the_content();
			} ?>
		</div>
	<?php endif;
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * Policy Methods
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function print_policies($terms) { ?>
	
	<div class="policies">
		<h1>Policies and Procedures</h1>
<?php 
		foreach($terms as $id) {
			$term = get_term($id, 'policy_categories')			
			$query_policies = new WP_Query(array(
				'post_type' => 'policies', 
				'orderby' => 'title', 
				'order' => 'ASC',  
				'policy_categories' => $term->slug, 
				'posts_per_page' => 1000,));
								
			if($query_policies->have_posts()) 
			{
				echo '<h2>' . $term->name .'</h2>';
				while($query_policies->have_posts())
				{ 
					$query_policies->the_post();
					echo '<h3>'.get_the_title().'</h3>';
					the_content();
				}
			}
		}
?>
	</div>
<?php
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * Graduate Studies Methods
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function print_grad_program_list() { ?>
	<div class="grad-studies grad-programs">
	<h2>Graduate Degree Programs List</h2>
<?php 
	query_programs = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',  
		'degree_level' => 'master,doctorate',
		'posts_per_page' => 1000,));
					
	if($query_programs->have_posts()) : while($query_programs->have_posts()) : $query_programs->the_post();
		$program_title = get_grad_program_name();

		$post_option=get_field('option_title');
		if(isset($post_option)&&$post_option!=='')
			$program_title = $program_title.', '.$post_option;
							
		echo '<p>'.$program_title.'</p>';
		
	endwhile; endif;
?>
	</div>
<?
}

function print_certificate_list() { ?>
	<div class="grad-studies grad-programs">
	<h2>Post-Baccalaureate University Certificate Program List</h2>
<?php 
	query_programs = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',  
		'degree_level' => 'certificate',
		'posts_per_page' => 1000,));
					
	if($query_programs->have_posts()) : while($query_programs->have_posts()) : $query_programs->the_post();
		$program_title = get_grad_program_name();

		$post_option=get_field('option_title');
		if(isset($post_option)&&$post_option!=='')
			$program_title = $program_title.', '.$post_option;
							
		echo '<p>'.$program_title.'</p>';
		
	endwhile; endif;
?>
	</div>
<?
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
		$program_title = $degree.', '.$program_title;
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
		<h2>General Education Sections</h2>
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
					echo '<h3>'.$term->description.'</h3>';
					while($query_policies->have_posts()) {
						$query_policies->the_post();
						echo '<p>'.get_the_title().'</p>';
					}
				}
			}
		} ?>
	</div>
<?php
}

function upper_division() {?>
	<div class="gen-ed upper-division">
		<h2>General Education - Upper Division</h2>
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
					echo '<h3>'.$term->description.'</h3>';
					while($query_policies->have_posts()) {
						$query_policies->the_post();
						echo '<p>'.get_the_title().'</p>';
					}
				}
			}
		} ?>
	</div>
<?php

}

function info_competence() {?>
	<div class="gen-ed information-competence">
		<h2>General Education - Information Competence</h2>
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
					echo '<h3>'.$term->description.'</h3>';
					while($query_policies->have_posts()) {
						$query_policies->the_post();
						echo '<p>'.get_the_title().'</p>';
					}
				}
			}
		} ?>
	</div>
<?php
}
 
 /* * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * 
 * Undergraduate Studies Methods
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
function undergrad_policies(){
	$query_policies = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',  
		'policy_tags' => 'undergrad',
		'post_type' => 'policies',
		'posts_per_page' => 1000,));
		
	if($query_policies->have_posts()) : ?>
	<div class = "undergrad undergrad-policies">
		<h2>Undergraduate Programs, Policies and Procedures</h2>
		<?php while($query_policies->have_posts()) : $query_policies->the_post(); ?>
			<h3><?php echo the_title(); ?></h3>
			<?php echo the_content(); ?>
		<?php endwhile; ?>
	</div>
	<?php endif; 
}

function degree_list() {
	$query_programs = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',  
		'degree_level' => 'major',
		'post_type' => 'programs',
		'posts_per_page' => 1000,));
		
	if($query_programs->have_posts()) : ?>
		<div class="program-list undergrad">
		<h2> Undergraduate Degree Program List</h2>
		<?php while($query_programs->have_posts()) : $query_programs->the_post();
			$degree = get_field('degree_type');
			$program_title = get_the_title();
			$post_option=get_field('option_title');
			
			echo $program_title.', '.$degree;
			
			if(isset($post_option)&&$post_option!=='')
				echo ' - '.$post_option;
		endwhile; ?>
		</div>
	<?php endif;
}
	
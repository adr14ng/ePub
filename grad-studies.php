<?php

function print_grad_studies() {
	//rgs
	print_pages(array('research-and-graduate-studies'));
	//masters and doctorates
	print_grad_program_list();
	//credential office
	print_pages(array('credential-office'));
	//certificate list
	print_certificate_list();
}

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
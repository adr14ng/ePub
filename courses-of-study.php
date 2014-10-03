<?php

//get all the departments we want
//if a college get college page

function courses_of_study() {
	$terms = get_terms('general_education');
	//sort by description
	//get the "bad" parents/categories
	//if it's a college
}

//get course policies
function course_policies() {
	$policies = array('course-numbering-system', 'online-course-designations', 
		'course-types', 'course-requisites-definition-of-terms');
	
	echo '<div class="policies course-of-study">'
	foreach($policies as $policy_slug) {
		$policy = get_posts(array('name' => $policy_slug, 'post_type' => 'policies'));
		
		if($policy)
		{
			echo '<h3>'.$policy[0]->title.'</h3>';
			echo $policy[0]->post_content;
		}
	}
	echo '</div>';
}

function print_college($college_slug)
{
	$college = get_posts(array('name' => $college_slug, 'post_type' => 'page'));
	$id = $college[0]->ID;
	//college title
	echo $college[0]->post_title;
	//college contact
	the_field('contact', $id);
	//college program list
	print_program_list($college_slug)
	//college content
	echo $college[0]->post_content;
	//college courses
	$values = get_field('college_courses', $id);
	if ( $values != false) :
		echo '<h2>Courses</h2>';
		the_field('college_courses', $id);
	endif; 
}

function get_course_of_study($deptterm) {

	$deptterm = get_term_by( 'slug', $dept, 'department_shortname' );
	$dept = $deptterm->slug;

	$title = $deptterm->description;

	$collegeterm = get_term($deptterm->parent, get_query_var('department_shortname') );
	$college = $collegeterm->description;
?>
	<div class = "main course-of-study">
		<h1><?php echo $title; ?></h1>
		<h2><?php echo $college; ?></h2>
		
<?php 
		print_contact($dept);
		print_dept_faculty($dept);
		print_dept_emeriti($dept);
		print_program_list($dept);
		print_department($dept);
		print_prograns($dept);
		print_courses($dept); 
?>
	</div>
<?php }



function print_courses($dept) {
	$query_course = new WP_Query(array(
		'post_type' => 'courses', 
		'orderby' => 'title', 
		'order' => 'ASC',  
		'department_shortname' => $dept, 
		'posts_per_page' => 1000,)
		);
		
	if($query_course->have_posts()) : ?>
		<div class="courses course-of-study">
		
		<?php while($query_course->have_posts()) : $query_course->the_post();
			echo '<h4>'.get_the_title().'</h4>';
			the_content();
		endwhile; ?>
		</div>
	<?php endif;
}

function print_programs($dept) {
	echo '<div class="programs course-of-study">';
	$levels = array('major', 'honor', 'minor', 'master', 'doctorate', 'credential', 'credential', 'certificate');
	$authorizations = false;
		
	foreach($levels as $level) : 
	
		$query_programs = new WP_Query(array(
			'orderby' => 'title', 
			'order' => 'ASC',  
			'degree_level' => $level,
			'department_shortname' => $dept,
			'posts_per_page' => 1000,));
				
		if($query_programs->have_posts()) :
			while($query_programs->have_posts()) : $query_programs->the_post();
				$degree = get_field('degree_type');
				if(($level !== "credential" || ((!$authorizations) && ($degree === 'credential' || $degree === 'Credential'))) ||
					($level === "credential" && ($degree === 'authorization' || $degree === 'Authorization') && $authorizations) ) :
					print_program();
				endif;
			endwhile; 
		endif; 
	endforeach;
	
	echo '</div>';
}

function print_program() { ?>
	<div class="program course-of-study">
		<h3><?php echo get_program_name(); ?></h3>
							
		<?php $post_option=get_field('option_title');

		if(isset($post_option)&&$post_option!=='') 
			echo '<h4>'.$post_option.'</h4>';

		echo '<h3>Overview</h3>';
		the_content();
		
		$values = get_field('slos');
		if ( $values != false) { 
			echo '<h3>Student Learning Outcomes</h3>';
			the_field('slos');
		} 
		
		$values = get_field('program_requirements');
		if ( $values != false) {
			echo '<h3>Requirements</h3>';
			the_field('program_requirements');
		}
							
	echo '</div>'
}

function print_program_list($dept) { ?>
	<div class="program-list course-of-study">
		<h3>Programs</h3>
		<?php 
		$levels = array('major', 'honor', 'minor', 'master', 'doctorate', 'credential', 'credential', 'certificate');
		$authorizations = false;
		
		foreach($levels as $level) { 
		
			$query_programs = new WP_Query(array(
				'orderby' => 'title', 
				'order' => 'ASC',  
				'degree_level' => $level,
				'department_shortname' => $dept,
				'posts_per_page' => 1000,));
					
			if($query_programs->have_posts()) {
			
				if($level === 'major')
						echo '<h4>Undergraduate</h4> ';
				elseif($level === 'master')
					echo '<h4>Graduate</h4> ';
				elseif( ($level = 'credential' && !$authorizations) || $level = 'certificate')
						echo '<h4>'.ucwords($level).'</h4> ';
				
				while($query_programs->have_posts()) { $query_programs->the_post();
					$degree = get_field('degree_type');
					
					if(($level !== "credential" || ((!$authorizations) && ($degree === 'credential' || $degree === 'Credential'))) ||
						($level === "credential" && ($degree === 'authorization' || $degree === 'Authorization') && $authorizations) ) {
						
						$program_title = get_program_name();

						$post_option=get_field('option_title');
						if(isset($post_option)&&$post_option!=='')
							$program_title = $program_title.', '.$post_option;
							
						echo '<p>'.$program_title.'</p>';
					}
				} 
			}
		}
		?>
	</div>
<?}

function get_program_name() {
	$degree = get_field('degree_type');
	$program_title = get_the_title();


	if ($degree === 'credential' || $degree === 'Credential'){
		if (strpos($program_title, 'Credential') === FALSE)
			$program_title .= ' Credential';
	}
	else if ($degree === 'authorization' || $degree === 'Authorization'){
		if (strpos($program_title, 'Authorization') === FALSE)
			$program_title .= ' Authorization';
	}
	else if ($degree === 'certificate' || $degree === 'Certificate') {
		if (strpos($program_title, 'Certificate') === FALSE)
			$program_title .= ' Certificate';
	}
	else if ($degree === 'minor' || $degree === 'Minor'){
		$program_title = $degree.' in '.$program_title;
	}
	else if ($degree === 'honors' || $degree === 'Honors' ){
		$program_title = $program_title;
	}
	else {
		$program_title = $degree.', '.$program_title;
	}
	
	return $program_title;
}

function print_department($dept) { 
	$query_department = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'departments'
		'department_shortname' => $dept,
		'posts_per_page' => 1000,));
		
		
	if($query_department->have_posts()): ?>
	
		<div class="department-info course-of-study">
			
		<?php $query_department->the_post(); ?>

		<?php $values = get_field('mission_statement');
		if ( $values != false) : ?>
			<h3>Mission Statement</h3>
			<?php the_field('mission_statement'); ?>
		<?php endif; ?>
		
		<?php $values = get_field('academic_advisement');
		if ( $values != false) : ?>
			<h3>Academic Advisement</h3>
			<?php the_field('academic_advisement'); ?>
		<?php endif; ?>
				
		<?php $values = get_field('careers');
		if ( $values != false) : ?>
			<h3>Careers</h3>
			<?php the_field('careers'); ?>
		<?php endif; ?>
				
		<?php $values = get_field('accreditation');
		if ( $values != false) : ?>
			<h3>Accreditation</h3>
			<?php the_field('accreditation'); ?>
		<?php endif; ?>
				
			<?php $values = get_field('honors');
		if ( $values != false) : ?>
			<h3>Honors</h3>
			<?php the_field('honors'); ?>
		<?php endif; ?>
				
		<?php $values = get_field('student_orgs');
		if ( $values != false) : ?>
			<h3>Clubs and Societies</h3>
			<?php the_field('student_orgs'); ?>
		<?php endif; ?>
			
		<?php the_content(); ?>
	
		</div>

<?php endif;
}

function print_dept_faculty($dept) {
	$query_faculty = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'faculty'
		'department_shortname' => $dept.',-emeriti',
		'posts_per_page' => 1000,));
		
		
	if($query_faculty->have_posts()): ?>
		<div class="faculty course-of-study">
			<h3>Faculty</h3>
			
			<?php while ($query_faculty->have_posts()) { 
				$query_faculty->the_post();
				
				the_title();
				if( $post_counter != count( $posts ) ) 
					echo ', ';
			} ?>
			
		</div>
	<?php endif; 
}

function print_dept_emeriti($dept) {
	$query_faculty = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'faculty'
		'department_shortname' => 'emeriti+'.$dept,
		'posts_per_page' => 1000,));
	
	
	if($query_faculty->have_posts()): ?>
		<div class="emeriti course-of-study">
			<h3>Emeritus Faculty</h3>
			<?php while ($query_faculty->have_posts()) {
				$query_faculty->the_post();
				the_title();
				if( $post_counter != count( $posts ) ) 
					echo ', ';
			} ?>
		</div>
	<?php endif;
}

function print_contact($dept) { 
	$query_department = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'departments'
		'department_shortname' => $dept,
		'posts_per_page' => 1));

	if($query_department->have_posts()): $query_department->the_post(); ?>
	
		<div class="contact course-of-study">
			<?php the_field('contact'); ?>
		</div>

<?php endif;
}
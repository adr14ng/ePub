<?php

//get all the departments we want
//if a college get college page

function courses_of_study($content) {
	$colleges = array('cecs', 'hhd', 'coh', 'educ', 'amc', 'csbs', 'csm');
	$dir = './book/OEBPS/';
	
	$filename = strtolower(sanitize_file_name($content['listTitle']));
	$filenames[0] = array(
			'title' => $content['listTitle'],
			'file' => $filename
		);
	
	//Programs of Study
	foreach($content['categories'] as $slug => $id)
	{
		ob_start();
		
		if(in_array($slug, $colleges))
		{
			$page = get_posts(array('name' => $slug, 'post_type' => 'page'));
			$title = $page[0]->post_title;
			
			print_header($title, false);
			print_college($page[0]);
			print_footer();
		}
		elseif($slug === 'univ')
		{
			$deptterm = get_term_by( 'slug', $slug, 'department_shortname' );
			$title = $deptterm->description;
			
			print_header($title);
			echo '<h2>Office of Undergraduate Studies</h2>';
			print_courses('univ');
			print_footer();
		}
		else
		{
			$deptterm = get_term_by( 'slug', $slug, 'department_shortname' );
			$title = $deptterm->description;
			
			print_header($title, false);
			get_course_of_study($deptterm);
			print_footer();
		}
				
		$output = ob_get_contents();		//save output
		ob_end_clean();						//discard buffer
		
		$filename = strtolower(sanitize_file_name($title));
		$filenames[] = array(
					'title' =>$title, 
					'file' => $filename
				);
		
		$f = fopen($dir.$filename.'.xhtml', "w");
		fwrite($f, $output);
		fclose($f);
	}
	
	//Course Policies
	if(isset($content['policies']) && $content['policies']) {
		$title = 'Course Policies';
		ob_start();
		
		print_header($title);
		course_policies();
		print_footer();
		
		$output = ob_get_contents();		//save output
		ob_end_clean();						//discard buffer
		
		$filename = strtolower(sanitize_file_name($title));
		$filenames[] = array(
					'title' =>$title, 
					'file' => $filename
				);
		
		$f = fopen($dir.$filename.'.xhtml', "w");
		fwrite($f, $output);
		fclose($f);
	}
	
	//Table of Contents
	table_of_contents($filenames, 0);		//make this
	
	return $filenames;
}

//get course policies
function course_policies() {
	$policies = array('course-numbering-system', 'online-course-designations', 
		'course-types', 'course-requisites-definition-of-terms');
	
	echo '<div class="policies course-of-study">';
	foreach($policies as $policy_slug) {
		$policy = get_posts(array('name' => $policy_slug, 'post_type' => 'policies'));
		
		if($policy)
		{
			echo '<h3>'.$policy[0]->title.'</h3>';
			echo apply_filters('the_content', $policy[0]->post_content);
		}
	}
	echo '</div>';
}

function print_college($college)
{
	$id = $college->ID;
	global $post;

	//college title
	echo '<h1>'.$college->post_title.'</h1>';
	//college contact
	echo apply_filters('the_content', get_field('contact', $id));
	//college program list
	echo $college->name;
	print_program_list($college->post_name);
	//college content
	echo apply_filters('the_content', $college->post_content);
	//college courses
	$values = get_field('college_courses', $id);
	if($college->post_name === 'csm')
	{
		print_courses('sci');
	}
	elseif ( $values != false)
	{
		echo '<h2>Courses</h2>';
		echo apply_filters('the_content', get_field('college_courses', $id));
	} 
}

function get_course_of_study($deptterm) {
	$dept = $deptterm->slug;

	$title = $deptterm->description;

	$collegeterm = get_term($deptterm->parent, get_query_var('department_shortname') );
	$college = $collegeterm->description;
?>
	<div class = "main course-of-study">
		<h1><?php echo $title; ?></h1>
		<?php if($dept !== 'bus')
			echo '<h2>'.$college.'</h2>';
		
		print_contact($dept);
		
		if($dept !== 'bus') 
		{
			print_dept_faculty($dept);
			print_dept_emeriti($dept);
		}
		
		print_program_list($dept);
		print_department($dept);
		print_programs($dept);
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
		<h3>Courses</h3>
		<?php while($query_course->have_posts()) : $query_course->the_post();
			echo '<h4>'.get_the_title().'</h4>';
			echo apply_filters('the_content', get_the_content());
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
		<h2><?php echo get_program_name(); ?></h2>
							
		<?php $post_option=get_field('option_title');

		if(isset($post_option)&&$post_option!=='') 
			echo '<h3 class="subtitle">'.$post_option.'</h3>';

		echo '<h3>Overview</h3>';
		the_content();
		
		$values = get_field('slos');
		if ( $values != false) { 
			echo '<h3>Student Learning Outcomes</h3>';
			echo apply_filters('the_content', get_field('slos'));
		} 
		
		$values = get_field('program_requirements');
		if ( $values != false) {
			echo '<h3>Requirements</h3>';
			echo apply_filters('the_content', get_field('program_requirements'));
		}
							
	echo '</div>';
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
				{
						echo '<h4>Undergraduate</h4> ';
				}
				elseif($level === 'master')
				{
					echo '<h4>Graduate</h4> ';
				}
				elseif( ($level === 'credential' && !$authorizations) || $level === 'certificate')
				{
					echo '<h4>'.ucwords($level).'</h4> ';
				}
				
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
		'post_type' => 'departments',
		'department_shortname' => $dept,
		'posts_per_page' => 1000,));
		
		
	if($query_department->have_posts()): ?>
	
		<div class="department-info course-of-study">
			
		<?php $query_department->the_post(); ?>

		<?php $values = get_field('mission_statement');
		if ( $values != false) : ?>
			<h3>Mission Statement</h3>
			<?php echo apply_filters('the_content', get_field('mission_statement')); ?>
		<?php endif; ?>
		
		<?php $values = get_field('academic_advisement');
		if ( $values != false) : ?>
			<h3>Academic Advisement</h3>
			<?php echo apply_filters('the_content', get_field('academic_advisement')); ?>
		<?php endif; ?>
				
		<?php $values = get_field('careers');
		if ( $values != false) : ?>
			<h3>Careers</h3>
			<?php echo apply_filters('the_content', get_field('careers')); ?>
		<?php endif; ?>
				
		<?php $values = get_field('accreditation');
		if ( $values != false) : ?>
			<h3>Accreditation</h3>
			<?php echo apply_filters('the_content', get_field('accreditation')); ?>
		<?php endif; ?>
				
			<?php $values = get_field('honors');
		if ( $values != false) : ?>
			<h3>Honors</h3>
			<?php echo apply_filters('the_content', get_field('honors')); ?>
		<?php endif; ?>
				
		<?php $values = get_field('student_orgs');
		if ( $values != false) : ?>
			<h3>Clubs and Societies</h3>
			<?php echo apply_filters('the_content', get_field('student_orgs')); ?>
		<?php endif; ?>
			
		<?php the_content(); ?>
	
		</div>

<?php endif;
}

function print_dept_faculty($dept) {
	$query_faculty = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'faculty',
		'department_shortname' => $dept,		//root out emeriti
		'posts_per_page' => 1000,));
		
	$post_counter = 0;	
	if($query_faculty->have_posts()): ?>
		<div class="faculty course-of-study">
			<h3>Faculty</h3>
			
			<?php while ($query_faculty->have_posts()) { 
				$query_faculty->the_post();
				$post_counter++;
				
				$name = get_the_title();
				$names = explode(", ", $name);
				$name = $names[1]." ".$names[0];
				
				echo $name;
				
				if( $post_counter < $query_faculty->post_count ) 
					echo ', ';
			} ?>
			
		</div>
	<?php endif; 
}

function print_dept_emeriti($dept) {
	$query_faculty = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'faculty',
		'department_shortname' => 'emeriti+'.$dept,
		'posts_per_page' => 1000,));
	
	$post_counter = 0;
	if($query_faculty->have_posts()): ?>
		<div class="emeriti course-of-study">
			<h3>Emeritus Faculty</h3>
			<?php while ($query_faculty->have_posts()) {
				$query_faculty->the_post();
				$post_counter++;
				
				$name = get_the_title();
				$names = explode(", ", $name);
				$name = $names[1]." ".$names[0];
				
				echo $name;
				
				if( $post_counter < $query_faculty->post_count ) 
					echo ', ';
			} ?>
		</div>
	<?php endif;
}

function print_contact($dept) { 
	$query_department = new WP_Query(array(
		'orderby' => 'title', 
		'order' => 'ASC',
		'post_type' => 'departments',
		'department_shortname' => $dept,
		'posts_per_page' => 1));

	if($query_department->have_posts()): $query_department->the_post(); ?>
	
		<div class="contact course-of-study">
			<?php echo apply_filters('the_content', get_field('contact')); ?>
		</div>

<?php endif;
}
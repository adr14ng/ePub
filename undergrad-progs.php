<?php

function print_ugp($pages) {
	//do undergraduate programs pages
	print_pages($pages);
	//do undergraduate policies
	undergrad_policies();
	//do undergraduate degree programs list
	degree_list();
}

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
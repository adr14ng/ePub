<?php

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
<?php

function print_gened() {
	//gen ed pages
	$pages = array('general-education', 'pattern-modifications',  'rules');
	print_pages($pages);
	//get gen ed by category
	categories();
	//get gen ed by ud
	upper_division();
	//get gen ed by ic
	info_competence();
}

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
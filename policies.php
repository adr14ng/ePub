<?php

//get policies by type

function print_policies() {
	$terms = get_terms('policy_categories'); ?>
	
	<div class="policies">
		<h1>Policies and Procedures</h1>
<?php 
		foreach($terms as $term) {
								
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
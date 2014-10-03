<?php

//loop through given pages

function print_pages($pages) {

	foreach($pages as $page_slug){
		$page = get_posts(array('name' => $page_slug, 'post_type' => 'page'));
		
		if($page)
		{
			echo '<div class="pages page">'
			echo '<h2>'.$page[0]->title.'</h2>';
			echo $page[0]->post_content;
			echo '</div>';
		}
	
	}
}
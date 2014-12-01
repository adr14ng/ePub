<?php
/** * * * * * * * * * * * * * * * * *
 * 
 *
 *
 *
 * * * * * * * * * * * * * * * * * * */
 
 class EpubOptions
 {
	
	/**
	 * Start Up
	 */
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_tool_page' ) );
	}
	
	/**
	 *
	 */
	public function add_tool_page()
	{
		//This page will be under "Tools"
		add_management_page(
			'EPUB Creation',
			'EPUB Creation',
			'manage_options',
			'epub-create',
			array($this, 'epub_page')
		);
	}
	
	/**
	 *
	 */
	public function epub_page()
	{
	?>
		<div class="wrap">
			<h2>EPUB Creation</h2>
			<form name="epub_options" action="<?php echo plugins_url().'/csun-epub/epub-process.php'; ?>" method="post" id="epub_options">
				<?php wp_nonce_field('update_review_status'); ?>
				<input type="hidden" id="referredby" name="referredby" value="<?php echo esc_url(wp_get_referer()); ?>" />
				<input type="hidden" name="return" value="<?php echo admin_url('tools.php?page=epub-create'); ?>" />
				<input type="hidden" name="action" value="epub-creation" />
				<input type="submit" name="default" value="default" class="btn btn-clear">
				
				<ul id="sortable">
					<li class="ui-state-default"><?php $this->options_inputs(); ?></li>
					<li class="ui-state-default"><?php $this->cover_inputs(); ?></li>
					<li class="ui-state-default"><?php $this->title_input('Table of Contents', 'toc'); ?></li>
					<li class="ui-state-default"><?php $this->page_inputs('CSUN', 'csun'); ?></li>
					<li class="ui-state-default"><?php $this->undergrad_inputs(); ?></li>
					<li class="ui-state-default"><?php $this->page_inputs('Student Services', 'student-services'); ?></li>
					<li class="ui-state-default"><?php $this->page_inputs('Special Programs', 'special-programs'); ?></li>
					<li class="ui-state-default"><?php $this->gened_inputs(); ?></li>
					<li class="ui-state-default"><?php $this->grad_inputs(); ?></li>
					<li class="ui-state-default"><?php $this->page_inputs('Credentials', 'credential'); ?></li>
					<li class="ui-state-default"><?php $this->courses_input(); ?></li>
					<li class="ui-state-default"><?php $this->policies_input(); ?></li>
					<li class="ui-state-default"><?php $this->title_input('Faculty', 'faculty'); ?></li>
					<li class="ui-state-default"><?php $this->title_input('Emeriti', 'emeriti'); ?></li>
				</ul>
				
				<input type="submit" name="submit" value="Submit" class="btn btn-clear">
			</form>
		</div>
		
	<?php
	}
	
	public function options_inputs()
	{ ?>
		<section class="options">
			<h3 class="options-title">ePub Options</h3>
			<div class="options-inside clearfix">
				<p><label for="options-title"> 
					<span>Title: </span>
					<input id="options-title" type="text" name="options[title]"/>
				</label></p>
				<p><label for="options-creator">
					<span>Author: </span>
					<input id="options-creator" type="text" name="options[creator]" />
				</label></p>
				<p><label for="options-language">
					<span>Language: </span>
					<input id="options-language" type="text" name="options[language]" value="en-US" />
				</label></p>
				<p><label for="options-rights">
					<span>Rights: </span>
					<input id="options-rights" type="text" name="options[rights]" />
				</label></p>
				<p><label for="options-publisher">
					<span>Publisher: </span>
					<input id="options-publisher" type="text" name="options[publisher]" />
				</label></p>
				<p><label for="options-bookid">
					<span>Unique Book ID: </span>
					<input id="options-bookid" type="text" name="options[bookid]" />
				</label></p>
			</div>
		</section>
<?php	
	}
	
	public function cover_inputs()
	{ ?>
		<section class="options">
			<h3 class="options-title">Cover</h3>
			<div class="options-inside clearfix">
				<p><label for="cover-title"> 
					<span>Title: </span>
					<input id="cover-title" type="text" name="content[cover][title]" />
				</label></p>
				<p><label for="cover-file"> 
					<span>File: </span>
					<input id="cover-file" type="file" name="content[cover][image]" />
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function page_inputs($title, $file)
	{ ?>
		<section class="options">
			<h3 class="options-title"><?php echo $title; ?></h3>
			<div class="options-inside clearfix">
				<p><label for="<?php echo $file; ?>-title"> 
					<span>Title: </span>
					<input id="<?php echo $file; ?>-title" type="text" name="content[<?php echo $file; ?>][title]" />
				</label></p>
				<p><label for="<?php echo $file; ?>-pages">
					<span class="list-box-title">Chosen Pages: </span>
					<span class="list-box-title">Available Pages: </span>
					<ul id="chose-<?php echo $file; ?>-page" class="chosen list-box <?php echo $file; ?>-page" name="content[<?php echo $file; ?>][pages][]">
					</ul>
					
					<ul id="unchosen-<?php echo $file; ?>-page" class="list-box <?php echo $file; ?>-page">
					<?php
						$pages = get_pages();
						
						foreach($pages as $page)
						{
							echo '<li class="list-option" value="'.$page->ID.'">'.$page->post_title.'</option></li>';
						}
					?>
					</ul>
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function undergrad_inputs()
	{ ?>
		<section class="options">
			<h3 class="options-title">Undergraduate Programs</h3>
			<div class="options-inside clearfix">
				<p><label for="undergrad-title"> 
					<span>Title: </span>
					<input id="undergrad-title" type="text" name="content[undergrad][title]" />
				</label></p>
				<p><label for="undergrad-pages">
					<span class="list-box-title">Chosen Pages: </span>
					<span class="list-box-title">Available Pages: </span>
					<ul id="chose-undergrad-page" class="chosen list-box undergrad-page" name="content[undergrad][pages][]">
					</ul>
					
					<ul id="unchosen-undergrad-page" class="list-box undergrad-page">
					<?php
						$pages = get_pages();
						
						foreach($pages as $page)
						{
							echo '<li class="list-option" value="'.$page->ID.'">'.$page->post_title.'</option></li>';
						}
					?>
					</ul>
				</label></p>
				<p><label for="undergrad-policies"> 
					<span>Undergraduate Policies: </span>
					<input id="undergrad-policies" type="checkbox" name="content[undergrad][policies]" />
				</label></p>
				<p><label for="undergrad-proglist"> 
					<span>List Programs: </span>
					<input id="proglist-title" type="checkbox" name="content[undergrad][proglist]" />
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function gened_inputs()
	{ ?>
		<section class="options">
			<h3 class="options-title">General Education</h3>
			<div class="options-inside clearfix">
				<p><label for="ge-title"> 
					<span>Title: </span>
					<input id="ge-title" type="text" name="content[gened][title]" />
				</label></p>
				<p><label for="ge-pages">
					<span class="list-box-title">Chosen Pages: </span>
					<span class="list-box-title">Available Pages: </span>
					<ul id="chose-gened-page" class="chosen list-box gened-page" name="content[gened][pages][]">
					</ul>
					
					<ul id="unchosen-gened-page" class="list-box gened-page">
					<?php
						$pages = get_pages();
						
						foreach($pages as $page)
						{
							echo '<li class="list-option" value="'.$page->ID.'">'.$page->post_title.'</option></li>';
						}
					?>
					</ul>
				</label></p>
				<p><label for="ge-category"> 
					<span>List Courses: </span>
					<input id="ge-category" type="checkbox" name="content[gened][category]" />
				</label></p>
				<p><label for="ge-upper"> 
					<span>Upper Division List: </span>
					<input id="ge-upper" type="checkbox" name="content[gened][upper]" />
				</label></p>
				<p><label for="ge-ic"> 
					<span>Information Competence List: </span>
					<input id="ge-ic" type="checkbox" name="content[gened][ic]" />
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function grad_inputs()
	{ ?>
		<section class="options">
			<h3 class="options-title">Graduate Studies</h3>
			<div class="options-inside clearfix">
				<p><label for="grad-title"> 
					<span>Title: </span>
					<input id="grad-title" type="text" name="content[grad][title]" />
				</label></p>
				<p><label for="grad-pages"> 
					<span class="list-box-title">Chosen Pages: </span>
					<span class="list-box-title">Available Pages: </span>
					<ul id="chose-grad-page" class="chosen list-box grad-page" name="content[grad][pages][]">
					</ul>
					
					<ul id="unchosen-grad-page" class="list-box grad-page">
					<?php
						$pages = get_pages();
						
						foreach($pages as $page)
						{
							echo '<li class="list-option" value="'.$page->ID.'">'.$page->post_title.'</option></li>';
						}
					?>
					</ul>
				</label></p>
				<p><label for="grad-prog"> 
					<span>List Programs: </span>
					<input id="grad-prog" type="checkbox" name="content[grad][proglist]" />
				</label></p>
				<p><label for="grad-cert"> 
					<span>List Certificates: </span>
					<input id="grad-cert" type="checkbox" name="content[grad][certlist]" />
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function courses_input()
	{ ?>
		<section class="options">
			<h3 class="options-title">Courses of Study</h3>
			<div class="options-inside clearfix">
				<p><label for="study-title"> 
					<span>Title: </span>
					<input id="study-title" type="text" name="content[study][title]" />
				</label></p>
				<p><label for="study-list"> 
					<span>Table of Content Title: </span>
					<input id= "study-list" type="text" name="content[study][listTitle]" />
				</label></p>
				<p><label for="study-policies"> 
					<span>Course Policies: </span>
					<input id="study-policies" type="checkbox" name="content[study][policies]" />
				</label></p>
				<p><label for="study-cats"> 
					<span class="list-box-title">Chosen Departments: </span>
					<span class="list-box-title">Available Departments: </span>
					<ul id="chose-dept-cat" class="chosen list-box dept-cats" name="content[study][categories][]">
					</ul>
					
					<ul id="unchosen-dept-cat" class="list-box dept-cats">
					<?php
						$terms = get_terms('department_shortname');
						
						foreach($terms as $term)
						{
							echo '<li class="list-option" value="'.$term->slug.'">'.$term->description.'</option></li>';
						}
					?>
					</ul>
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function policies_input()
	{ ?>
		<section class="options">
			<h3 class="options-title">Policies</h3>
			<div class="options-inside clearfix">
				<p><label for="policies-title">
					<span>Title: </span>
					<input id="policies-title" type="text" name="content[policies][title]" />
				</label></p>
				<p><label for="policies-cat">
					<span class="list-box-title">Chosen Policy Categories: </span>
					<span class="list-box-title">Available Policy Categories: </span>
					<ul id="chose-pol-cat" class="chosen list-box pol-cats" name="content[policies][categories][]">
					</ul>
					
					<ul id="unchosen-pol-cat" class="list-box pol-cats">
					<?php
						$terms = get_terms('policy_categories');
						
						foreach($terms as $term)
						{
							echo '<li class="list-option" value="'.$term->term_id.'">'.$term->name.'</option></li>';
						}
					?>
					</ul>
					
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function title_input($title, $type)
	{ ?>
		<section class="options">
			<h3 class="options-title"><?php echo $title; ?></h3>
			<div class="options-inside clearfix">
				<p><label for="<?php echo $type; ?>-title"> 
					<span>Title: </span>
					<input id="<?php echo $type; ?>-title" type="text" name="content[<?php echo $type; ?>][title]" />
				</label></p>
			</div>
		</section>
<?php
	}
 
}
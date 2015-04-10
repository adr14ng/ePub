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
		$order = get_option('epub_order');
	?>
		<div class="wrap clearfix">
			<h2>EPUB Creation</h2>
			<form enctype="multipart/form-data" name="epub_options" action="<?php echo plugins_url().'/csun-epub/epub-process.php'; ?>" method="post" id="epub_options">
			<div id="form-wrap" class="pull-left">
				<?php wp_nonce_field('epub-creation'); ?>
				<input type="hidden" id="referredby" name="referredby" value="<?php echo esc_url(wp_get_referer()); ?>" />
				<input type="hidden" name="return" value="<?php echo admin_url('tools.php?page=epub-create'); ?>" />
				<input type="hidden" name="action" value="epub-creation" />
				<ul id="sortable">
					<?php foreach($order as $item) : ?>
						<?php if($item === "options"): ?>
							<li class="ui-state-default"><?php $this->options_inputs(); ?></li>
						<?php elseif($item === "cover"): ?>
							<li class="ui-state-default"><?php $this->cover_inputs(); ?></li>
						<?php elseif($item === "undergrad"): ?>
							<li class="ui-state-default"><?php $this->undergrad_inputs(); ?></li>
						<?php elseif($item === "gened"): ?>
							<li class="ui-state-default"><?php $this->gened_inputs(); ?></li>
						<?php elseif($item === "grad"): ?>
							<li class="ui-state-default"><?php $this->grad_inputs(); ?></li>
						<?php elseif($item === "courses"): ?>
							<li class="ui-state-default"><?php $this->courses_input(); ?></li>
						<?php elseif($item === "policies"): ?>
							<li class="ui-state-default"><?php $this->policies_input(); ?></li>
						<?php elseif($item === "toc" || $item === "faculty" || $item === "emeriti"): ?>
							<li class="ui-state-default"><?php $this->title_input($item); ?></li>
						<?php elseif(strpos($item, 'group') !== FALSE) : ?>
							<li class="ui-state-default"><?php $this->groups_input($item); ?></li>
						<?php else: ?>
							<li class="ui-state-default"><?php $lists[] = $this->page_inputs($item); ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
			<div id="sumbit-box" class="left-section-box pull-left">
				<section class="options">
					<h3 class="options-title">Actions</h3>
					<div id="submit-inner" class="controls-inside">
						<span id="collapse-all" class="btn btn-clear">Collapse All</span>
						<span id="expand-all" class="btn btn-clear">Expand All</span>
						<br />
						<button type="submit" name="submit" value="submit" class="btn btn-clear">Create ePub</button>
						<button type="submit" name="submit" value="save" class="btn btn-clear">Save Changes</button>
						<button type="submit" name="submit" value="default" class="btn btn-clear">Restore Defaults</button>
					</div>
				</section>
			</div>
			</form>
			<div id="add-more" class="left-section-box pull-left">
				<section class="options">
					<h3 class="options-title">Add More</h3>
					<div class="option-controls">
						<span id="add-more-mini" class="dashicons dashicons-arrow-up-alt2"></span>
						<span id="add-more-max" class="dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
					</div>
					<div id="add-more-inner" class="controls-inside">
						<h4>As needed: </h4>
						<button id="add-page" class="btn btn-clear" value=0>Pages</button>
						<button id="add-group" class="btn btn-clear" value=0>Groups</button>
						<h4>Only one: </h4>
						<button class="btn btn-clear add-content" value="options">Options</button>
						<button class="btn btn-clear add-content" value="cover">Cover</button>
						<button class="btn btn-clear add-content" value="toc">Table of Contents</button>
						<button class="btn btn-clear add-content" value="undergrad">Undergraduate Studies</button>
						<button class="btn btn-clear add-content" value="gened">General Education</button>
						<button class="btn btn-clear add-content" value="grad">Courses of Study</button>
						<button class="btn btn-clear add-content" value="policies">Policies</button>
						<button class="btn btn-clear add-content" value="faculty">Faculty</button>
						<button class="btn btn-clear add-content" value="emeriti">Emeriti</button>
						<h4>Note:</h4>
						<p>When you first add an item, only the title field will be available. Save to choose other options.</p>
					</div>
				</section>
			</div>
		</div>
		<script>
			$j = jQuery.noConflict();
			<?php foreach($lists as $item) : ?>
				$j("#chose-<?php echo $item; ?>, #unchosen-<?php echo $item; ?>" ).sortable({
					connectWith: ".<?php echo $item; ?>"
				}).disableSelection();
			<?php endforeach; ?>
		</script>
		
	<?php
	}
	
	public function options_inputs()
	{ 
		$options = get_option('epub_general');
		$type = 'curr-options';
	?>
		<section id="<?php echo $type; ?>" class="options">
			<div class="options-drag-handle">
				<h3 class="options-title">ePub Options</h3>
				<div class="option-controls">
					<span id="<?php echo $type; ?>-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>
					<span id="<?php echo $type; ?>-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
				</div>
			</div>
			<div id="<?php echo $type; ?>-inner" class="options-inside clearfix">
				<p><label for="options-title"> 
					<span>Title: </span>
					<input id="options-title" type="text" name="options[title]" value="<?php echo $options['title']; ?>" />
				</label></p>
				<p><label for="options-creator">
					<span>Author: </span>
					<input id="options-creator" type="text" name="options[creator]" value="<?php echo $options['creator']; ?>" />
				</label></p>
				<p><label for="options-language">
					<span>Language: </span>
					<input id="options-language" type="text" name="options[language]" value="<?php echo $options['language']; ?>" />
				</label></p>
				<p><label for="options-rights">
					<span>Rights: </span>
					<input id="options-rights" type="text" name="options[rights]" value="<?php echo $options['rights']; ?>"/>
				</label></p>
				<p><label for="options-publisher">
					<span>Publisher: </span>
					<input id="options-publisher" type="text" name="options[publisher]" value="<?php echo $options['publisher']; ?>" />
				</label></p>
				<p><label for="options-bookid">
					<span>Unique Book ID: </span>
					<input id="options-bookid" type="text" name="options[bookid]" value="CSUN<?php echo mt_rand(); ?>" />
				</label></p>
			</div>
		</section>
<?php	
	}
	
	public function cover_inputs()
	{ 
		$options = get_option('epub_cover');
		$type = 'curr-cover';
	?>
		<section id="<?php echo $type; ?>" class="options">
			<div class="options-drag-handle">
				<h3 class="options-title">Cover</h3>
				<div class="option-controls">
					<span id="<?php echo $type; ?>-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>
					<span id="<?php echo $type; ?>-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
					<span id="<?php echo $type; ?>-close" class="option-close dashicons dashicons-no"></span>
				</div>
			</div>
			<div id="<?php echo $type; ?>-inner" class="options-inside clearfix">
				<p><label for="cover-title"> 
					<span>Title: </span>
					<input id="cover-title" type="text" name="content[cover][title]" value="<?php echo $options['title']; ?>" />
				</label></p>
				<p><label for="cover-file"> 
					<span>File: </span>
					<input id="cover-file" type="file" name="cover-image" />
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function page_inputs($file)
	{ 
		$options = get_option('epub_'.$file);
		$title = $options['title'];
		$pages = $options['pages'];
	?>
		<section id="<?php echo $file; ?>" class="options">
			<div class="options-drag-handle">
				<h3 class="options-title"><?php echo $title; ?></h3>
				<div class="option-controls">
					<span id="<?php echo $file; ?>-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>
					<span id="<?php echo $file; ?>-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
					<span id="<?php echo $file; ?>-close" class="option-close dashicons dashicons-no"></span>
				</div>
			</div>
			<div id="<?php echo $file; ?>-inner" class="options-inside clearfix">
				<p><label for="<?php echo $file; ?>-title"> 
					<span>Title: </span>
					<input id="<?php echo $file; ?>-title" type="text" name="content[<?php echo $file; ?>][title]" value="<?php echo $title; ?>" />
				</label></p>
				<p><label for="<?php echo $file; ?>-pages">
					<span class="list-box-title">Chosen Pages: </span>
					<span class="list-box-title">Available Pages: </span>
					<ul id="chose-<?php echo $file; ?>-page" class="chosen list-box <?php echo $file; ?>-page" name="content[<?php echo $file; ?>][pages][]">
					<?php
						if(!empty($pages)) : foreach($pages as $id) :
							$page = get_post($id);
							echo '<li class="list-option" id="'.$page->ID.'">'.$page->post_title.'</option></li>';
						endforeach; endif; 
					?>
					</ul>
					
					<ul id="unchosen-<?php echo $file; ?>-page" class="list-box <?php echo $file; ?>-page">
					<?php
						$args = array('exclude' => $pages, 'hierarchical' => 0,);
						$other_pages = get_pages($args);
						
						foreach($other_pages as $page)
						{
							echo '<li class="list-option" id="'.$page->ID.'">'.$page->post_title.'</option></li>';
						}
					?>
					</ul>
				</label></p>
			</div>
		</section>
<?php
		return $file.'-page';
	}
	
	public function undergrad_inputs()
	{ 
		$options = get_option('epub_undergrad');
		$df_pages = $options['pages'];
		$type = 'curr-undergrad';
	?>
		<section id="<?php echo $type; ?>" class="options">
			<div class="options-drag-handle">
				<h3 class="options-title">Undergraduate Programs</h3>
				<div class="option-controls">
					<span id="<?php echo $type; ?>-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>
					<span id="<?php echo $type; ?>-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
					<span id="<?php echo $type; ?>-close" class="option-close dashicons dashicons-no"></span>
				</div>
			</div>
			<div id="<?php echo $type; ?>-inner" class="options-inside clearfix">
				<p><label for="undergrad-title"> 
					<span>Title: </span>
					<input id="undergrad-title" type="text" name="content[undergrad][title]" value="<?php echo $options['title']; ?>" />
				</label></p>
				<p><label for="undergrad-admissionpol"> 
					<span>Undergraduate Admission Policies: </span>
					<input id="undergrad-admissionpol" type="checkbox" name="content[undergrad][admissionpol]" <? if(isset($options['admissionpol'])) echo 'checked'; ?> />
				</label></p>
				<p><label for="undergrad-pages">
					<span class="list-box-title">Chosen Pages: </span>
					<span class="list-box-title">Available Pages: </span>
					<ul id="chose-undergrad-page" class="chosen list-box undergrad-page" name="content[undergrad][pages][]">
					<?php
						if(!empty($df_pages)) :  foreach($df_pages as $id) :
							$page = get_post($id);
							echo '<li class="list-option" id="'.$page->ID.'">'.$page->post_title.'</option></li>';
						endforeach; endif;
					?>
					</ul>
					
					<ul id="unchosen-undergrad-page" class="list-box undergrad-page">
					<?php
						$args = array('exclude' => $df_pages, 'hierarchical' => 0,);
						$pages = get_pages($args);
						
						foreach($pages as $page)
						{
							echo '<li class="list-option" id="'.$page->ID.'">'.$page->post_title.'</option></li>';
						}
					?>
					</ul>
				</label></p>
				<p><label for="undergrad-policies"> 
					<span>Undergraduate Policies: </span>
					<input id="undergrad-policies" type="checkbox" name="content[undergrad][policies]" <? if(isset($options['policies'])) echo 'checked'; ?> />
				</label></p>
				<p><label for="undergrad-proglist"> 
					<span>List Programs: </span>
					<input id="proglist-title" type="checkbox" name="content[undergrad][proglist]" <? if(isset($options['proglist'])) echo 'checked'; ?> />
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function gened_inputs()
	{ 
		$options = get_option('epub_gened');
		$df_pages = $options['pages'];
		$type = 'curr-gened';
	?>
		<section id="<?php echo $type; ?>" class="options">
			<div class="options-drag-handle">
				<h3 class="options-title">General Education</h3>
				<div class="option-controls">
					<span id="<?php echo $type; ?>-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>
					<span id="<?php echo $type; ?>-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
					<span id="<?php echo $type; ?>-close" class="option-close dashicons dashicons-no"></span>
				</div>
			</div>
			<div id="<?php echo $type; ?>-inner" class="options-inside clearfix">
				<p><label for="ge-title"> 
					<span>Title: </span>
					<input id="ge-title" type="text" name="content[gened][title]" value="<?php echo $options['title']; ?>" />
				</label></p>
				<p><label for="ge-pages">
					<span class="list-box-title">Chosen Pages: </span>
					<span class="list-box-title">Available Pages: </span>
					<ul id="chose-gened-page" class="chosen list-box gened-page" name="content[gened][pages][]">
					<?php
						if(!empty($df_pages)) :  foreach($df_pages as $id) :
							$page = get_post($id);
							echo '<li class="list-option" id="'.$page->ID.'">'.$page->post_title.'</option></li>';
						endforeach; endif;
					?>
					</ul>
					
					<ul id="unchosen-gened-page" class="list-box gened-page">
					<?php
						$args = array('exclude' => $df_pages, 'hierarchical' => 0,);
						$pages = get_pages($args);
						
						foreach($pages as $page)
						{
							echo '<li class="list-option" id="'.$page->ID.'">'.$page->post_title.'</option></li>';
						}
					?>
					</ul>
				</label></p>
				<p><label for="ge-category"> 
					<span>List Courses: </span>
					<input id="ge-category" type="checkbox" name="content[gened][category]" <? if(isset($options['category'])) echo 'checked'; ?> />
				</label></p>
				<p><label for="ge-upper"> 
					<span>Upper Division List: </span>
					<input id="ge-upper" type="checkbox" name="content[gened][upper]" <? if(isset($options['upper'])) echo 'checked'; ?> />
				</label></p>
				<p><label for="ge-ic"> 
					<span>Information Competence List: </span>
					<input id="ge-ic" type="checkbox" name="content[gened][ic]" <? if(isset($options['ic'])) echo 'checked'; ?> />
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function grad_inputs()
	{ 
		$options = get_option('epub_grad');
		$df_pages = $options['pages'];
		$type = 'curr-grad';
	?>
		<section id="<?php echo $type; ?>" class="options">
			<div class="options-drag-handle">
				<h3 class="options-title">Graduate Studies</h3>
				<div class="option-controls">
					<span id="<?php echo $type; ?>-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>
					<span id="<?php echo $type; ?>-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
					<span id="<?php echo $type; ?>-close" class="option-close dashicons dashicons-no"></span>
				</div>
			</div>
			<div id="<?php echo $type; ?>-inner" class="options-inside clearfix">
				<p><label for="grad-title"> 
					<span>Title: </span>
					<input id="grad-title" type="text" name="content[grad][title]" value="<?php echo $options['title']; ?>" />
				</label></p>
				<p><label for="grad-pages"> 
					<span class="list-box-title">Chosen Pages: </span>
					<span class="list-box-title">Available Pages: </span>
					<ul id="chose-grad-page" class="chosen list-box grad-page" name="content[grad][pages][]">
					<?php
						if(!empty($df_pages)) :  foreach($df_pages as $id) :
							$page = get_post($id);
							echo '<li class="list-option" id="'.$page->ID.'">'.$page->post_title.'</option></li>';
						endforeach; endif;
					?>
					</ul>
					
					<ul id="unchosen-grad-page" class="list-box grad-page">
					<?php
						$args = array('exclude' => $df_pages, 'hierarchical' => 0,);
						$pages = get_pages($args);
						
						foreach($pages as $page)
						{
							echo '<li class="list-option" id="'.$page->ID.'">'.$page->post_title.'</option></li>';
						}
					?>
					</ul>
				</label></p>
				<p><label for="grad-prog"> 
					<span>List Programs: </span>
					<input id="grad-prog" type="checkbox" name="content[grad][proglist]" <? if(isset($options['proglist'])) echo 'checked'; ?> />
				</label></p>
				<p><label for="grad-cert"> 
					<span>List Certificates: </span>
					<input id="grad-cert" type="checkbox" name="content[grad][certlist]" <? if(isset($options['certlist'])) echo 'checked'; ?> />
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function courses_input()
	{ 
		$options = get_option('epub_courses');
		$depts = $options['categories'];
		$type = 'curr-courses';
	?>
		<section id="<?php echo $type; ?>" class="options">
			<div class="options-drag-handle">
				<h3 class="options-title">Courses of Study</h3>
				<div class="option-controls">
					<span id="<?php echo $type; ?>-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>
					<span id="<?php echo $type; ?>-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
					<span id="<?php echo $type; ?>-close" class="option-close dashicons dashicons-no"></span>
				</div>
			</div>
			<div id="<?php echo $type; ?>-inner" class="options-inside clearfix">
				<p><label for="courses-title"> 
					<span>Title: </span>
					<input id="courses-title" type="text" name="content[courses][title]" value="<?php echo $options['title']; ?>" />
				</label></p>
				<p><label for="courses-list"> 
					<span>Table of Content Title: </span>
					<input id= "courses-list" type="text" name="content[courses][listTitle]" value="Colleges, Departments and Programs" />
				</label></p>
				<p><label for="courses-policies"> 
					<span>Course Policies: </span>
					<input id="courses-policies" type="checkbox" name="content[courses][policies]" <? if(isset($options['policies'])) echo 'checked'; ?> />
				</label></p>
				<p><label for="courses-cats"> 
					<span class="list-box-title">Chosen Departments: </span>
					<span class="list-box-title">Available Departments: </span>
					<ul id="chose-dept-cat" class="chosen list-box dept-cats" name="content[courses][categories][]">
					<?php
						if(!empty($depts)) :  foreach($depts as $dept) :
							$term = get_term_by('slug', $dept, 'department_shortname');
							echo '<li class="list-option" id="'.$term->slug.'">'.$term->description.'</option></li>';
							
							$exclude[] = $term->term_id;
						endforeach; endif;
					?>
					</ul>
					
					<ul id="unchosen-dept-cat" class="list-box dept-cats">
					<?php
						$args = array('exclude' => $exclude, 'exclude_tree' => 511); 
						$terms = get_terms('department_shortname', $args);
						
						foreach($terms as $term)
						{
							echo '<li class="list-option" id="'.$term->slug.'">'.$term->description.'</option></li>';
						}
					?>
					</ul>
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function policies_input()
	{ 
		$options = get_option('epub_policies');
		$cats = $options['categories'];
		$type = 'curr-policies';
		
	?>
		<section id="<?php echo $type; ?>" class="options">
			<div class="options-drag-handle">
				<h3 class="options-title">Policies</h3>
				<div class="option-controls">
					<span id="<?php echo $type; ?>-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>
					<span id="<?php echo $type; ?>-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
					<span id="<?php echo $type; ?>-close" class="option-close dashicons dashicons-no"></span>
				</div>
			</div>
			<div id="<?php echo $type; ?>-inner" class="options-inside clearfix">
				<p><label for="policies-title">
					<span>Title: </span>
					<input id="policies-title" type="text" name="content[policies][title]" value="<?php echo $options['title']; ?>" />
				</label></p>
				<p><label for="policies-cat">
					<span class="list-box-title">Chosen Policy Categories: </span>
					<span class="list-box-title">Available Policy Categories: </span>
					<ul id="chose-pol-cat" class="chosen list-box pol-cats" name="content[policies][categories][]">
					<?php
						if(!empty($cats)) :  foreach($cats as $cat) :
							$term = get_term($cat, 'policy_categories');
							echo '<li class="list-option" id="'.$term->term_id.'">'.$term->name.'</option></li>';
						endforeach; endif;
					?>
					</ul>
					
					<ul id="unchosen-pol-cat" class="list-box pol-cats">
					<?php
						$args = array('exclude' => $cats, 'orderby' => 'title', 'order' => 'ASC',);
						$terms = get_terms('policy_categories', $args);
						
						foreach($terms as $term)
						{
							echo '<li class="list-option" id="'.$term->term_id.'">'.$term->name.'</option></li>';
						}
					?>
					</ul>
					
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function title_input($type)
	{ 
		$options = get_option('epub_'.$type);
		$title = $options['title'];
	?>
		<section id="<?php echo $type; ?>" class="options">
			<div class="options-drag-handle">
				<h3 class="options-title"><?php echo $title; ?></h3>
				<div class="option-controls">
					<span id="<?php echo $type; ?>-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>
					<span id="<?php echo $type; ?>-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
					<span id="<?php echo $type; ?>-close" class="option-close dashicons dashicons-no"></span>
				</div>
			</div>
			<div id="<?php echo $type; ?>-inner" class="options-inside clearfix">
				<p><label for="<?php echo $type; ?>-title"> 
					<span>Title: </span>
					<input id="<?php echo $type; ?>-title" type="text" name="content[<?php echo $type; ?>][title]" value="<?php echo $title; ?>" />
				</label></p>
			</div>
		</section>
<?php
	}
	
	public function groups_input($type)
	{ 
		$options = get_option('epub_'.$type);
		$cats = $options['categories'];
		$group = $cats[0];
		
	?>
		<section id="<?php echo $type; ?>" class="options">
			<div class="options-drag-handle">
				<h3 class="options-title">Groups - <?php echo $options['title']; ?></h3>
				<div class="option-controls">
					<span id="<?php echo $type; ?>-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>
					<span id="<?php echo $type; ?>-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>
					<span id="<?php echo $type; ?>-close" class="option-close dashicons dashicons-no"></span>
				</div>
			</div>
			<div id="<?php echo $type; ?>-inner" class="options-inside clearfix">
				<p><label for="<?php echo $type; ?>-title">
					<span>Title: </span>
					<input id="<?php echo $type; ?>-title" type="text" name="content[<?php echo $type; ?>][title]" value="<?php echo $options['title']; ?>" />
				</label></p>
				<p><label for="<?php echo $type; ?>-cat">
					<span>Group Category: </span>
					<ul id="<?php echo $type; ?>-cat" name="content[<?php echo $type; ?>][categories][]">
					<?php $terms = get_terms('group_type');
						if(!empty($terms)) : foreach($terms as $term) : ?>
							<li>
								<input type="radio" name="content[<?php echo $type; ?>][categories][]" value="<?php echo $term->term_id; ?>" <?php if($group == $term->term_id) echo 'checked'; ?>>
								<?php echo $term->name; ?>
							</li>
						<?php endforeach; endif; ?>
					</ul>
					
				</label></p>
			</div>
		</section>
<?php
	}
 
}
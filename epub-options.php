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
				
				<label for="title"> 
					<span>Title: </span>
					<input type="text" name="title" id="title" />
				</label>
				<label for="creator">
					<span>Author: </span>
					<input type="text" name="creator" id="creator" />
				</label>
				<label for="language">
					<span>Language: </span>
					<input type="text" name="language" id="language" value="en-US" />
				</label>
				<label for="rights">
					<span>Rights: </span>
					<input type="text" name="rights" id="rights" />
				</label>
				<label for="publisher">
					<span>Publisher: </span>
					<input type="text" name="publisher" id="publisher" />
				</label>
				<label for="bookid">
					<span>Unique Book ID: </span>
					<input type="text" name="bookid" id="bookid" />
				</label>
				
				<input type="submit" name="submit" value="Submit" class="btn btn-clear">
			</form>
		</div>
		
	<?php
	}
 
 }
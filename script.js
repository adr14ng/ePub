$j = jQuery.noConflict();

$j( document ).ready(function() {
    $j( "#sortable" ).sortable();
	$j( "#sortable" ).sortable("option", "handle", ".options-title");
    $j( "#sortable" ).disableSelection();
	
	$j("#chose-pol-cat, #unchosen-pol-cat" ).sortable({
		connectWith: ".pol-cats"
	}).disableSelection();
	
	$j("#chose-dept-cat, #unchosen-dept-cat" ).sortable({
		connectWith: ".dept-cats"
	}).disableSelection();
	
	$j("#chose-grad-page, #unchosen-grad-page" ).sortable({
		connectWith: ".grad-page"
	}).disableSelection();
	
	$j("#chose-gened-page, #unchosen-gened-page" ).sortable({
		connectWith: ".gened-page"
	}).disableSelection();
	
	$j("#chose-undergrad-page, #unchosen-undergrad-page" ).sortable({
		connectWith: ".undergrad-page"
	}).disableSelection();
	
	$j('#epub_options').submit(function() {
		
		$j('.list-option').each(function() {
			var value = $j(this).attr('id');
			var option = '<option selected value="'+value+'">'+this.innerHTML+'</option>';
			$j(this).replaceWith(option);
		});
		
		$j('.chosen').each(function() {
			var name = $j(this).attr('name');
			var form = '<select multiple name="'+name+'">'+this.innerHTML+'</select>';
			$j(this).replaceWith(form);
		});

		return true;
	});
	
  });
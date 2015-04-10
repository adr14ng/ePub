$j = jQuery.noConflict();

$j( document ).ready(function() {
    $j( "#sortable" ).sortable();
	$j( "#sortable" ).sortable("option", "handle", ".options-drag-handle");
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
	
	$j('.add-content').click(function() {
		var type = $j(this).attr("value");
		var content = '<li class="ui-state-default">'+
						'<section id="'+type+'" class="options">' +
							'<div class="options-drag-handle">' +
								'<h3 class="options-title">'+type+'</h3>' +
								'<div class="option-controls">' +
									'<span id="'+type+'-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>' +
									'<span id="'+type+'-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>' +
									'<span id="'+type+'-close" class="option-close dashicons dashicons-no"></span>' +
								'</div>' +
							'</div>' +
							'<div id="'+type+'-inner" class="options-inside clearfix">' +
								'<p><label for="'+type+'-title">'  +
									'<span>Title: </span>' +
									'<input id="'+type+'-title" type="text" name="content['+type+'][title]" value="" />' +
								'</label></p>' +
							'</div>' +
						'</section>' +
					  '</li>';
		$j('#sortable').prepend(content);
	});
	
	$j('#add-page').click(function() {
		var val = $j(this).val();
		var type = "new-page-"+val;
		val++;
		$j(this).val(val)
		var content = '<li class="ui-state-default">'+
						'<section id="'+type+'" class="options">' +
							'<div class="options-drag-handle">' +
								'<h3 class="options-title">New Pages</h3>' +
								'<div class="option-controls">' +
									'<span id="'+type+'-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>' +
									'<span id="'+type+'-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>' +
									'<span id="'+type+'-close" class="option-close dashicons dashicons-no"></span>' +
								'</div>' +
							'</div>' +
							'<div id="'+type+'-inner" class="options-inside clearfix">' +
								'<p><label for="'+type+'-title">'  +
									'<span>Title: </span>' +
									'<input id="'+type+'-title" type="text" name="content['+type+'][title]" value="" />' +
								'</label></p>' +
							'</div>' +
						'</section>' +
					  '</li>';
		$j('#sortable').prepend(content);
	});
	
	$j('#add-group').click(function() {
		var val = $j(this).val();
		var type = "new-group-"+val;
		val++;
		$j(this).val(val)
		var content = '<li class="ui-state-default">'+
						'<section id="'+type+'" class="options">' +
							'<div class="options-drag-handle">' +
								'<h3 class="options-title">New Groups</h3>' +
								'<div class="option-controls">' +
									'<span id="'+type+'-mini" class="option-hide dashicons dashicons-arrow-up-alt2"></span>' +
									'<span id="'+type+'-max" class="option-show dashicons dashicons-arrow-down-alt2" style="display: none;"></span>' +
									'<span id="'+type+'-close" class="option-close dashicons dashicons-no"></span>' +
								'</div>' +
							'</div>' +
							'<div id="'+type+'-inner" class="options-inside clearfix">' +
								'<p><label for="'+type+'-title">'  +
									'<span>Title: </span>' +
									'<input id="'+type+'-title" type="text" name="content['+type+'][title]" value="" />' +
								'</label></p>' +
							'</div>' +
						'</section>' +
					  '</li>';
		$j('#sortable').prepend(content);
	});
	
	$j("#sortable").delegate('.option-hide', "click", function() {
		var id = $j(this).attr('id');
		console.log("close");
		$j(this).hide();
		id = '#'+id.replace("mini", "max");
		$j(id).show();
		id = id.replace("max", "inner");
		$j(id).hide("slide", {direction:"up"}, 200);
	});
	
	$j("#sortable").delegate('.option-show', "click", function() {
		var id = $j(this).attr('id');
		$j(this).hide();
		id = '#'+id.replace("max", "mini");
		$j(id).show();
		id = id.replace("mini", "inner");
		$j(id).show("slide", {direction:"up"}, 200);
	});
	
	$j("#sortable").delegate('.option-close', "click", function() {
		var id = $j(this).attr('id');
		console.log("delete");
		id = '#'+id.replace("-close", "");
		$j(id).remove();
	});
	
	$j("#add-more-mini").click(function() {
		$j(this).hide();
		$j("#add-more-max").show();
		$j("#add-more-inner").hide("slide", {direction:"up"}, 200);
	});
	
	$j("#add-more-max").click(function() {
		$j(this).hide();
		$j("#add-more-mini").show();
		$j("#add-more-inner").show("slide", {direction:"up"}, 200);
	});
	
	$j("#collapse-all").click(function() {
		$j(".option-hide").hide();
		$j(".option-show").show();
		$j(".options-inside").hide("slide", {direction:"up"}, 200);
	});
	
	$j("#expand-all").click(function() {
		$j(".option-show").hide();
		$j(".option-hide").show();
		$j(".options-inside").show("slide", {direction:"up"}, 200);
	});
	
  });
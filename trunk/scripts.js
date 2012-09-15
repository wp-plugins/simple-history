
/**
 *  load history items via ajax
 */
var simple_history_current_page = 0;

// search on enter
jQuery(document).on("keyup", ".simple-history-filter-search input[type='text']", function(e) {
	// Key is enter
	if (e.keyCode == 13) {
		console.log("do search");
		jQuery(".simple-history-filter input[type='button']").trigger("click");
	}
});

jQuery(".simple-history-filter a, .simple-history-filter input[type='button']").live("click", function() {

	$t = jQuery(this);
	$t.closest("ul").find("li").removeClass("selected");
	$t.closest("li").addClass("selected");
	$ol = jQuery("ol.simple-history");
	
	jQuery(".simple-history-added-by-ajax").remove();
	
	var $wrapper = jQuery(".simple-history-ol-wrapper");
	$wrapper.height($wrapper.height()); // so dashboard widget does not collapse when loading new items

	jQuery(".simple-history-load-more").hide("fast");
	$ol.fadeOut("fast");
	jQuery(".simple-history-no-more-items").hide();

	var search = jQuery("p.simple-history-filter-search input[type='text']").val();

	simple_history_current_page = 0;
	var data = {
		"action": "simple_history_ajax",
		"type": jQuery("ul.simple-history-filter-type li.selected a").text(),
		"user": jQuery("ul.simple-history-filter-user li.selected a").text(),
		"search": search,
		"num_added": 0
	};
	jQuery.post(ajaxurl, data, function(data, textStatus, XMLHttpRequest){
		if (data == "simpleHistoryNoMoreItems") {
			jQuery(".simple-history-load-more,.simple-history-load-more-loading").hide();
			jQuery(".simple-history-no-more-items").show();
			jQuery(".simple-history-ol-wrapper").height("auto");
		} else {
			$ol.html(data);
			$ol.fadeIn("fast");
			$wrapper.height("auto");
			jQuery(".simple-history-load-more").fadeIn("fast");
		}
	});
	
	return false;
});

/**
 * Click on load more = load more items via AJAX
 */
jQuery(".simple-history-load-more a, .simple-history-load-more input[type='button']").live("click", function() {

	simple_history_current_page++;

	// the number of new history items to get
	var num_to_get = jQuery(this).prev("select").find(":selected").val();
	
	// the number of added li-items = the number of added history items
	var num_added = jQuery("ol.simple-history > li").length;

	jQuery(".simple-history-load-more,.simple-history-load-more-loading").toggle();
	
	var search = jQuery("p.simple-history-filter-search input[type='text']").val();
	
	$ol = jQuery("ol.simple-history:last");
	var data = {
		"action": "simple_history_ajax",
		"type": jQuery(".simple-history-filter-type li.selected a").text(),
		"user": jQuery(".simple-history-filter-user li.selected a").text(),
		"page": simple_history_current_page,
		"items": num_to_get,
		"num_added": num_added,
		"search": search
	};
	jQuery.post(ajaxurl, data, function(data, textStatus, XMLHttpRequest){
	
		// if data = simpleHistoryNoMoreItems then no more items found, so hide load-more-link
		if (data == "simpleHistoryNoMoreItems") {
			jQuery(".simple-history-load-more,.simple-history-load-more-loading").hide();
			jQuery(".simple-history-no-more-items").show();
		} else {
			//var $new_elm = jQuery("<ol class='simple-history simple-history-added-by-ajax'>" + data + "</ol>");
			var $new_lis = jQuery(data);
			$new_lis.hide();
			$ol.append($new_lis);
			$new_lis.fadeIn("slow");
			//$new_elm.hide();
			//$ol.after($new_elm);
			//$new_elm.show("slow");
			jQuery(".simple-history-load-more,.simple-history-load-more-loading").toggle();
		}

	});
	return false;
});

jQuery("ol.simple-history .when").live("mouseover", function() {
	jQuery(this).closest("li").find(".when_detail").fadeIn("fast");
});
jQuery("ol.simple-history .when").live("mouseout", function() {
	jQuery(this).closest("li").find(".when_detail").fadeOut("fast");
});

// show occasions
jQuery("a.simple-history-occasion-show").live("click", function() {
	jQuery(this).closest("li").find("ul.simple-history-occasions").toggle("fast");
	return false;
});

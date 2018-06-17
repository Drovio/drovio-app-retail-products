jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Listener to switch to contact details
	jq(document).on("listviewer.switchto.details", function() {
		jq(".productsListViewContainer .productsListView").addClass("details");
	});
	
	// Switch to list
	jq(document).on("click", ".productsListViewContainer .detailsContainer .wbutton.back", function() {
		jq(".productsListViewContainer .productsListView").removeClass("details");
	});
	
	
	// Search for products
	jq(document).on("keyup", ".productsListViewContainer .listContainer .searchContainer .searchInput", function() {
		var search = jq(this).val();
		if (search == "")
			return jq(".productsListViewContainer .listContainer .listItem").show();
			
		// Create the regular expression
		var regEx = new RegExp(jq.map(search.trim().split(' '), function(v) {
			return '(?=.*?' + v + ')';
		}).join(''), 'i');
		
		// Select all project boxes, hide and filter by the regex then show
		jq(".productsListViewContainer .listContainer .listItem").hide().find(".title").filter(function() {
			return regEx.exec(jq(this).text());
		}).each(function() {
			jq(this).closest(".listItem").show();
		});
	});
});
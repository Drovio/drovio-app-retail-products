jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Edit action
	jq(document).on("productinfo.edit", function() {
		jq(".productInfoContainer .productInfo").addClass("edit");
	});
	
	// Cancel edit action
	jq(document).on("productinfo.cancel_edit", function() {
		// Remove class
		jq(".productInfoContainer .productInfo").removeClass("edit");
		
		// Clear edit form container contents
		jq(".editFormContainer").html("");
	});
});
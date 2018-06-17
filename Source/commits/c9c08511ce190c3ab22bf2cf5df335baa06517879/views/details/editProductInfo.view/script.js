jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Edit action
	jq(document).on("click", ".editProductInfo .close_ico", function() {
		// Trigger to cancel edit
		jq(document).trigger("productinfo.cancel_edit");
	});
	
	// Add new row action
	jq(document).on("click", ".editGroup .ico.create_new", function() {
		// Get first new form row and clone
		var frow = jq(this).closest(".editGroup").find(".frow.new").first().clone(true);
		frow.find("input").val("");
		jq(this).closest(".editGroup").find(".frow.new").last().after(frow);
	});
	
	// Remove row
	jq(document).on("click", ".frow .ico.remove", function() {
		// Get row and remove
		jq(this).closest(".frow").remove();
	});
});
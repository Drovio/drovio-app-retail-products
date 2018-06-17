jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Reload contact info
	jq(document).on("productinfo.reload", function() {
		jq("#productInfoViewContainer").trigger("reload");
	});
});
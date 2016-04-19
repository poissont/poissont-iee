jQuery(window).load(function () {
	if (jQuery("#bii_preloader")) {
		var dt = jQuery("#bii_preloader").attr("data-timeout")*1;
		var df = jQuery("#bii_preloader").attr("data-fading")*1;

		setTimeout(function () {
			jQuery("#bii_preloader").fadeOut(df);
		}, dt);
	}
});
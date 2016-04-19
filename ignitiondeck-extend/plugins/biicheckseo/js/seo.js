jQuery(function () {
	checkSEO();
});
function checkSEO() {
	if (jQuery("title").length) {
		bii_CL("TITLE : "+jQuery("title").html());
	} else {
		bii_CL("NO title");
	}
	if (jQuery("h1").length) {
		bii_CL("H1 : "+jQuery("h1").text());
	} else {
		bii_CL("NO h1");
	}
	if (jQuery("meta[name='description']").length) {
		bii_CL("DESCRIPTION : "+jQuery("meta[name='description']").attr("content"));
	} else {
		bii_CL("NO metadescription");
	}
	if (jQuery("meta[name='og:description']").length) {
		bii_CL("OG:DESCRIPTION : "+jQuery("meta[name='og:description']").attr("content"));
	} else {
		bii_CL("NO meta og:description");
	}	
}
jQuery("img").each(function () {
	if ((typeof jQuery(this).attr("alt") === "undefined") || (jQuery(this).attr("alt") == "")) {
		//Alt par défaut
		var nomArray = jQuery(this).attr("src").split("/");
		jQuery(this).attr("alt", nomArray[nomArray.length - 1]);
	}

	if (typeof jQuery(this).attr("height") === "undefined" && jQuery(this).height() != 0) {
		//height par défaut
		var height = parseInt(jQuery(this).height())
			+ parseInt(jQuery(this).css("padding-top").replace("px", ""))
			+ parseInt(jQuery(this).css("padding-bottom").replace("px", ""));
		jQuery(this).attr("height", height);
	}
	if (typeof jQuery(this).attr("width") === "undefined" && jQuery(this).width() != 0) {
		//width par défaut
		var width = parseInt(jQuery(this).width())
			+ parseInt(jQuery(this).css("padding-left").replace("px", ""))
			+ parseInt(jQuery(this).css("padding-right").replace("px", ""));
		jQuery(this).attr("width", width);
	}
});

jQuery(function ($) {
	if (typeof bii_showlogs != 'undefined' && bii_showlogs) {

		$(".menu-toggle").on("click", function () {
			$("#bii-overlay").addClass("bii-menu-toogle");
			$(".bii-left-menu").addClass("bii-menu-toogle").animate({
				left: "0px"
			}, 900, "easeOutBounce");
//			$("#wrapper").css("overflow","hidden");
		});
		$("#bii-overlay, .close-toogle").on("click", function () {
			$(".bii-left-menu").animate({
				left: "-230px"
			}, {
				duration: 1000,
				easing: "easeOutBounce",
				complete: function () {
					
						$("#bii-overlay, .bii-left-menu").removeClass("bii-menu-toogle");
//					$("#wrapper").css("overflow-y","scroll");
				}
			});
		});
	}
});
jQuery(function ($) {
	$(".synchro-photo").click(function () {
		var $fa = $(this).find(".fa-refresh");
		$(this).addClass("btn-info").removeClass("btn-default");
		$fa.addClass("fa-spin");
		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_synchronize_photos'
			},
			dataType: 'html',
			success: function (reponse) {
				$fa.removeClass("fa-spin");
				$(this).addClass("btn-default").removeClass("btn-info");
			}
		});
	});

	$("#chooseinstance").on("change", function () {
		var val = $(this).val();
		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_change_instance',
				'newinstance': val
			},
			dataType: 'html',
			success: function (reponse) {
//				alert("ok");
				location.reload();
			}
		});
	});
	
	$(".bii_upval").on("click",function(){
		var val = $(this).attr("data-newval");
		var option = $(this).attr("data-option");
		var html = $(this).html();
		var fa = $(this).find(".fa");
		jQuery.ajax({
			url: ajaxurl,
			data: {
				'action': 'bii_change_wp_option',
				'option': option,
				'newval': val
			},
			dataType: 'html',
			success: function (reponse) {
//				alert(reponse);
				location.reload();
			}
		});
	});
	
	$(".publier").on("click",function(e){
		e.preventDefault();
		$("#poststuff").submit();
	});
	
	$(".hide-relative").on("click",function(){
		$(".hide-relative").removeClass("active");
		$(".bii_option").addClass("hidden");
		$(this).addClass("active");
		var dr = $(this).attr("data-relative");
		$("."+dr).removeClass('hidden');
		if($(this).hasClass("hide-publier")){
			$(".publier").addClass("hidden");
		}else{
			$(".publier").removeClass("hidden");
		}
	});
	
	$(".update-nag ").addClass("hidden");
});
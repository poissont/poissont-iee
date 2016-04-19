var currentYear = (new Date).getFullYear();
var yearstart = 2016;

jQuery(function ($) {
	if($(".md-requiredlogin.login.pageconnexion")){
		$("#containerwrapper").addClass("bii-connexion");
	}
	
	$("#project_fesubmit").hide();
	add_onscreen_to_checkifscroll();
	$(window).scroll(function () {
		add_onscreen_to_checkifscroll();
	});

	if ($(".id-widget-date").length) {
		$(".id-widget-date").each(function () {
			var $month = $(this).find(".id-widget-month");
			var $year = $(this).find(".id-widget-year");
			var $day = $(this).find(".id-widget-day");
			$(this).html("");
			$(this).append($day).append($month).append($year);
		});
	}
	if ($(".ign-project-end").length) {
		$(".ign-project-end").each(function () {
			var text = $(this).text();
			var mots = text.split(" ");
			var textrepl = "";
			var motreplace = "texttoreplace";
			$.each(mots, function (indexInArray, mot) {
				if (mot.indexOf("/") != -1) {
//					console.log(mot);
					motreplace = mot;
					var exp = mot.split("/");
					var mois = exp[0];
					var jour = exp[1];
					var annee = exp[2];
					textrepl = jour + " " + mois + " " + annee;
				}
			});
			$(this).text(text.replace(motreplace, textrepl));
		});
//		$date.html("");
	}
	if ($(".product-author-details").length) {
		$(".product-author-details").each(function () {
			var html = $(this).html();
			var exp = html.split("\n");
//			console.log(exp);
			var textrepl = "";
			var motreplace = "texttoreplace";
			$.each(exp, function (indexInArray, element) {
				for (i = yearstart; i <= currentYear; ++i) {
					if (element.indexOf(", " + i) != -1) {
						i = i.toString();
						motreplace = element;
//						console.log(motreplace);
						var mots = element.split(" ");
						var posyear = mots.indexOf(i);
						var mois = mots[posyear - 2];
						var jour = mots[posyear - 1].replace(",", "");
						var annee = i;
						var heure = mots[posyear + 2];
						textrepl = '<i class="fa fa-clock-o"></i> ' + jour + " " + mois + " " + annee + " à " + heure;
					}
				}
			});
			$(this).html(html.replace(motreplace, textrepl));
		});

	}

	if($("#sidebar").length){
		if(!bii_showlogs){
			$("#sidebar").hide();
		}
	}

	if ($("#loginform").length) {

		$("#loginform input[name='redirect_to']").val(bloginfourl + "/preinscription/");
	}


	if ($(".datepicker").length) {
		$(".datepicker").on("click change input load blur", function () {
			eventchangeElement($(this), $(this).val());
		});
		$(".datepicker").datepicker({
			firstDay: 1,
			closeText: 'Fermer',
			prevText: '',
			nextText: '',
			currentText: 'Aujourd\'hui',
			monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
			monthNamesShort: ['Janv.', 'Févr.', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'],
			dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
			dayNamesShort: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
			dayNamesMin: ['Dim.', 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.'],
			dateFormat: 'dd/mm/yy',
			defaultDate: new Date(),
			beforeShow: function () {
				$('#ui-datepicker-div').addClass("bii-datepicker");
			},
			onSelect: function (string) {
//				console.log(string);
				eventchangeElement($(this), string);
			}
		});
	}

	$('#CGU').on("click", function () {
		if ($(this).is(':checked')) {
			$("#project_fesubmit").show();
		} else {
			$("#project_fesubmit").hide();
		}
	});

	$("#fes").on("submit", function (e) {
		if (!$('#CGU').is(':checked')) {
			e.preventDefault();
		} else {
			var nbrequired = 0;
			var nbpass = 0;
			$(this).find(".required").each(function () {
				++nbrequired;
				var val = $(this).val().trim();
				if (val) {
					if ($(this).attr("data-pattern")) {
						var pattern = $(this).attr("data-pattern");
						var regex = new RegExp(pattern);
						if (regex.test(val)) {
							++nbpass;
							$(this).removeClass("invalid");
						}else{
							$(this).addClass("invalid");
						}
					}else{
						++nbpass;
						$(this).removeClass("invalid");
					}
				}else{
					$(this).addClass("invalid");
				}
			});
			if(nbrequired != nbpass){
				e.preventDefault();
				var nbnotpass = nbrequired-nbpass;
				var pluriel = "";
				var verbe = " n'est";
				if(nbnotpass > 1){
					pluriel = "s";
					verbe = " ne sont";
				}
				alert(nbnotpass+" champ"+pluriel+verbe+" pas correctement renseigné"+pluriel);
			}
		}
	});

	function eventchangeElement($element, string) {
		if ($element.attr("data-relative")) {
			var relative = $("#" + $element.attr("data-relative"));
			if (relative.length) {
				var val = string;
				if (string.indexOf("/")) {
					var exp = string.split("/");
					val = exp[1] + "/" + exp[0] + "/" + exp[2];
				}
				relative.val(val);
			}
		}
	}


	function add_onscreen_to_checkifscroll() {
		var zone = zoneFenetre();
		var yb = zone.ybottom;
		var yt = zone.ytop;
		var middle = (yb + yt) / 2;
		middle += middle / 4; //déclenchement au 5/8 screen
//		bii_CL(zone);
//		bii_CL(middle);
		$(".checkifscroll:not(.onscreen)").each(function () {
			var top = $(this).offset().top;
//			bii_CL(top);
//			var bottom = top + $(this).height();
			if (top < middle) {
//				bii_CL("trigger");
				$(this).addClass("onscreen");
			}
		});
	}

});
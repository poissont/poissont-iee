var formerzone = zoneFenetre();
var zonebottomplus = 10;
function bii_CL(value) {
	if (typeof bii_showlogs != 'undefined' && bii_showlogs) {
		console.log(value);
	}
}
function zoneFenetre() {
	var ytop = jQuery(window).scrollTop() * 1;
	var ybottom = jQuery(window).height() * 1 + ytop + zonebottomplus;
	var ret = {'ytop': ytop, 'ybottom': ybottom};
//	console.log(ret);
	return ret;
}
function directionScroll() {
	var zone = zoneFenetre();
//	console.log([formerzone, zone]);
	var direction = "stay";
	if (zone.ytop > formerzone.ytop) {
		direction = "down";
	}
	if (zone.ytop < formerzone.ytop) {
		direction = "up";
	}
	formerzone = zone;
	return direction;
}
function checkBrowser() {
	var ret = "undefined";
	var isIE = false;
	var isChrome = false;
	var isOpera = false;
	// Opera 8.0+
	if ((!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0) {
		ret = "Opera";
		isOpera = true;
	}
	// Firefox 1.0+
	if (typeof InstallTrigger !== 'undefined') {
		ret = "Firefox";
	}
	// At least Safari 3+: "[object HTMLElementConstructor]"
	if (Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0) {
		ret = "Safari";
	}
	// Internet Explorer 6-11
	if (!!document.documentMode) {
		ret = "IE";
		isIE = true;
	}
	// Edge 20+
	if (!isIE && !!window.StyleMedia) {
		ret = "Edge";
	}
	// Chrome 1+
	if (!!window.chrome && !!window.chrome.webstore) {
		ret = "Chrome";
		isChrome = true;
	}
	// Blink engine detection
	if ((isChrome || isOpera) && !!window.CSS) {
		ret = "Blink";
	}
	return ret;

}
function getWindowSize() {
	var windowsize = "";
	if (window.matchMedia("(max-width: 470px").matches) {
		windowsize = "xxs";
	} else if (window.matchMedia("(max-width: 767px").matches) {
		windowsize = "xs";
	} else if (window.matchMedia("(max-width: 992px").matches) {
		windowsize = "sm";
	} else if (window.matchMedia("(max-width: 1200px").matches) {
		windowsize = "md";
	} else {
		windowsize = "lg";
	}
	return windowsize;
}

function cleanArray(array) {
	var i, j, len = array.length, out = [], obj = {};
	for (i = 0; i < len; i++) {
		obj[array[i]] = 0;
	}
	for (j in obj) {
		out.push(j);
	}
	return out;
}



function makeid(length)
{
	if(!length){
		length = 5;
	}
	var text = "";
	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	for (var i = 0; i < length; i++)
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	return text;
}
// Si le navigateur ne prend pas en charge le placeholder
if (document.createElement('input').placeholder == undefined) {
	// Champ avec un attribut HTML5 placeholder
	jQuery('[placeholder]')
		// Au focus on clean si sa valeur équivaut à celle du placeholder
		.focus(function () {
			if (jQuery(this).val() == jQuery(this).attr('placeholder')) {
				jQuery(this).val('');
			}
		})
		// Au blur on remet le placeholder si le champ est laissé vide
		.blur(function () {
			if (jQuery(this).val() == '') {
				jQuery(this).val(jQuery(this).attr('placeholder'));
			}
		})
		// On déclenche un blur afin d'initialiser le champ
		.blur()
		// Au submit on clean pour éviter d'envoyer la valeur du placeholder
		.parents('form').submit(function () {
		jQuery(this).find('[placeholder]').each(function () {
			if (jQuery(this).val() == jQuery(this).attr('placeholder')) {
				jQuery(this).val('');
			}
		});
	});
}
//Fix matchmedia bug in ie < 10
window.matchMedia = window.matchMedia || (function (doc, undefined) {

	var docElem = doc.documentElement,
		refNode = docElem.firstElementChild || docElem.firstChild,
		// fakeBody required for <FF4 when executed in <head>
		fakeBody = doc.createElement('body'),
		div = doc.createElement('div');
	div.id = 'mq-test-1';
	div.style.cssText = "position:absolute;top:-100em";
	fakeBody.style.background = "none";
	fakeBody.appendChild(div);
	var mqRun = function (mq) {
		div.innerHTML = '&shy;<style media="' + mq + '"> #mq-test-1 { width: 42px; }</style>';
		docElem.insertBefore(fakeBody, refNode);
		bool = div.offsetWidth === 42;
		docElem.removeChild(fakeBody);
		return {matches: bool, media: mq};
	},
		getEmValue = function () {
			var ret,
				body = docElem.body,
				fakeUsed = false;
			div.style.cssText = "position:absolute;font-size:1em;width:1em";
			if (!body) {
				body = fakeUsed = doc.createElement("body");
				body.style.background = "none";
			}

			body.appendChild(div);
			docElem.insertBefore(body, docElem.firstChild);
			if (fakeUsed) {
				docElem.removeChild(body);
			} else {
				body.removeChild(div);
			}

			//also update eminpx before returning
			ret = eminpx = parseFloat(div.offsetWidth);
			return ret;
		},
		//cached container for 1em value, populated the first time it's needed 
		eminpx,
		// verify that we have support for a simple media query
		mqSupport = mqRun('(min-width: 0px)').matches;
	return function (mq) {
		if (mqSupport) {
			return mqRun(mq);
		} else {
			var min = mq.match(/\(min\-width[\s]*:[\s]*([\s]*[0-9\.]+)(px|em)[\s]*\)/) && parseFloat(RegExp.$1) + (RegExp.$2 || ""),
				max = mq.match(/\(max\-width[\s]*:[\s]*([\s]*[0-9\.]+)(px|em)[\s]*\)/) && parseFloat(RegExp.$1) + (RegExp.$2 || ""),
				minnull = min === null,
				maxnull = max === null,
				currWidth = doc.body.offsetWidth,
				em = 'em';
			if (!!min) {
				min = parseFloat(min) * (min.indexOf(em) > -1 ? (eminpx || getEmValue()) : 1);
			}
			if (!!max) {
				max = parseFloat(max) * (max.indexOf(em) > -1 ? (eminpx || getEmValue()) : 1);
			}

			bool = (!minnull || !maxnull) && (minnull || currWidth >= min) && (maxnull || currWidth <= max);
			return {matches: bool, media: mq};
		}
	};
}(document));

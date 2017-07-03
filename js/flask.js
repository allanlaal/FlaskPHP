
/**
 *
 *	 FlaskPHP
 *	 ------------
 *   2017 (c) Codelab Solutions OÜ <codelab@codelab.ee>
 *
 */

$(function(){

	// Set default jQuery ajax options
	$.ajaxSetup({
		type:     "post",
		dataType: "json"
	});

	// Init tooltips
	$('[data-toggle="tooltip"]').tooltip();

	// Tab switch
	var URL = document.location.toString();
	if (URL.match(/#/))
	{
		var anchor = URL.split('#')[1];
		if ($("#content_"+anchor).length)
		{
			Flask.Tab.select(anchor);
		}
	}

	// We can remove that
	$('noscript').remove();

	// Default focus
	if ($(".defaultfocus").length)
	{
		$(".defaultfocus")[0].focus();
	}

});


/*
 *   Helper functions
 *   ----------------
 */

function str_repeat(i,m)
{
	for (var o = []; m > 0; o[--m] = i);
	return o.join('');
}

function sprintf()
{
	var i = 0, a, f = arguments[i++], o = [], m, p, c, x, s = '';
	while (f) {
		if (m = /^[^\x25]+/.exec(f)) {
			o.push(m[0]);
		}
		else if (m = /^\x25{2}/.exec(f)) {
			o.push('%');
		}
		else if (m = /^\x25(?:(\d+)\$)?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(f)) {
			if (((a = arguments[m[1] || i++]) == null) || (a == undefined)) {
				throw('Too few arguments.');
			}
			if (/[^s]/.test(m[7]) && (typeof(a) != 'number')) {
				throw('Expecting number but found ' + typeof(a));
			}
			switch (m[7]) {
				case 'b': a = a.toString(2); break;
				case 'c': a = String.fromCharCode(a); break;
				case 'd': a = parseInt(a); break;
				case 'e': a = m[6] ? a.toExponential(m[6]) : a.toExponential(); break;
				case 'f': a = m[6] ? parseFloat(a).toFixed(m[6]) : parseFloat(a); break;
				case 'o': a = a.toString(8); break;
				case 's': a = ((a = String(a)) && m[6] ? a.substring(0, m[6]) : a); break;
				case 'u': a = Math.abs(a); break;
				case 'x': a = a.toString(16); break;
				case 'X': a = a.toString(16).toUpperCase(); break;
			}
			a = (/[def]/.test(m[7]) && m[2] && a >= 0 ? '+'+ a : a);
			c = m[3] ? m[3] == '0' ? '0' : m[3].charAt(1) : ' ';
			x = m[5] - String(a).length - s.length;
			p = m[5] ? str_repeat(c, x) : '';
			o.push(s + (m[4] ? a + p : p + a));
		}
		else {
			throw('Huh ?!');
		}
		f = f.substring(m[0].length);
	}
	return o.join('');
}

function sortObject(object)
{
	var newArr = new Array();
	var last = "";
	for (n in object)
	{
		last = "";
		for (i in object)
		{
			if ((last=='' || object[i] < last) && !newArr[i])
			{
				last = i;
			}
		}
		newArr[last] = object[last];
	}
	return newArr;
}

function var_dump(obj)
{
	var out = typeof(obj) + ": \n";
	for (var i in obj) {
		out += i + ": " + obj[i] + "\n";
	}
	return out;
}

function oneof()
{
	for (var i = 0; i < arguments.length; i++)
	{
		if (arguments[i]!=null && arguments[i]!='' && arguments[i]!=0) return arguments[i];
	}
	return arguments[arguments.length];
}

function htmlspecialchars(text)
{
  return text
	.toString()
	.replace(/&/g, "&amp;")
  .replace(/</g, "&lt;")
  .replace(/>/g, "&gt;")
  .replace(/"/g, "&quot;")
  .replace(/'/g, "&#039;");
}

Number.prototype.formatNumber = function(c, d, t)
{
	var n = this,
	c = isNaN(c = Math.abs(c)) ? 2 : c,
	d = d == undefined ? "," : d,
	t = t == undefined ? " " : t,
	s = n < 0 ? "-" : "",
	i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
	j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};


/*
 *   JavaScript hacks & additions
 *   ----------------------------
 */

Math.stdRound = Math.round;
Math.round = function(number, precision)
{
	if (isNaN(number)) return 0;
	if (precision!=null)
	{
		precision = Math.abs(parseInt(precision)) || 0;
		var coefficient = Math.pow(10, precision);
		return Math.stdRound(number*coefficient)/coefficient;
	}
	else
	{
		return Math.stdRound(number);
	}
};

Object.elementCount = function(obj)
{
	var size = 0, key;
	for (key in obj) {
		if (obj.hasOwnProperty(key)) size++;
	}
	return size;
};


/*
 *   Base64 encode/decode
 *   --------------------
 */

var Base64={
	_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
	encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},
	decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},
	_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},
	_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}
};


/*
 *   Init Flask object
 *   -----------------
 */

Flask = {};


/*
 *   Standard functions
 *   ------------------
 */

Flask.redirect = function( url )
{
	window.location.href=url;
};

Flask.reload = function()
{
	window.location.reload(true);
};


/*
 *   Login handler
 *   -------------
 */

Flask.Login = {

	// Handle e-mail field keypress
	emailKeypress: function(evt)
	{
		if ( evt.which == 13 )
		{
			$("#login_password")[0].focus();
			evt.stopPropagation();
		}
	},

	// Handle password field keypress
	passwordKeypress: function(evt)
	{
		if ( evt.which == 13 )
		{
			Flask.Login.doLogin();
			evt.stopPropagation();
		}
	},

	// Validate login
	validateLogin: function()
	{
		$("#login_message").hide();
		$("#login_form div").removeClass('has-danger');
		$("#login_form input").removeClass('form-control-danger');
		$("#login_form div.form-control-feedback").remove();

		var email=jQuery.trim($("#login_email").val());
		if (email=='')
		{
			$("#field_login_email").addClass('has-danger');
			$("#login_email").addClass('form-control-danger');
			$("#login_email").after('<div class="form-control-feedback">'+Locale.get('FLASK.USER.Login.Error.EmailEmpty')+'</div>');
			$("#login_email")[0].focus();
			return false;
		}
		else if (!email.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/))
		{
			$("#field_login_email").addClass('has-danger');
			$("#login_email").addClass('form-control-danger');
			$("#login_email").after('<div class="form-control-feedback">'+Locale.get('FLASK.USER.Login.Error.EmailInvalid')+'</div>');
			$("#login_email")[0].focus();
			return false;
		}

		var password=jQuery.trim($("#login_password").val());
		if (password=='')
		{
			$("#field_login_password").addClass('has-danger');
			$("#login_password").addClass('form-control-danger');
			$("#login_password").after('<div class="form-control-feedback">'+Locale.get('FLASK.USER.Login.Error.PasswordEmpty')+'</div>');
			$("#login_password")[0].focus();
			return false;
		}

		return true;
	},

	// Show errors
	showErrors: function( error )
	{
		if (typeof(error)=='object')
		{
			var general_error='';
			for (var fld in error)
			{
				if ($("#field_"+fld).length)
				{
					$("#field_"+fld).addClass('has-danger');
					$("#"+fld).addClass('form-control-danger');
					$("#"+fld).after('<div class="form-control-feedback">'+error[fld]+'</div>');
				}
				else
				{
					general_error=general_error+'<div>'+error[fld]+'</div>';
				}
			}
		}
		else
		{
			$("#login_message").text(error).show();
		}
	},

	// Progress start trigger
	progressStart: function()
	{
		$("#login_email").attr('readonly','readonly');
		$("#login_password").attr('readonly','readonly');
		$("#login_submit").attr('disabled','disabled');
		if ($("#login_submit").attr('data-title-progress')!=null && $("#login_submit").attr('data-title-progress')!='')
		{
			$("#login_submit").html($("#login_submit").attr('data-title-progress'));
		}
	},

	// Progress stop trigger
	progressStop: function()
	{
		$("#login_email").removeAttr('readonly');
		$("#login_password").removeAttr('readonly');
		$("#login_submit").removeAttr('disabled');
		if ($("#login_submit").attr('data-title')!=null && $("#login_submit").attr('data-title')!='')
		{
			$("#login_submit").html($("#login_submit").attr('data-title'));
		}
	},

	// Do login
	doLogin: function()
	{
		if (!Flask.Login.validateLogin()) return;
		Flask.Login.progressStart();
		$("#login_form").ajaxSubmit({
			type: 'post',
			data: { login: '1' },
			success: function( data )
			{
				Flask.Login.progressStop();
				if (data!=null && data.status=='1')
				{
					if (data.redirect!=null && data.redirect!='')
					{
						Flask.redirect(data.redirect);
					}
					else if (data.reload!=null && data.reload=='1')
					{
						Flask.reload();
					}
					else
					{
						if (data.submitsuccessaction)
						{
							eval(data.submitsuccessaction);
						}
					}
				}
				else
				{
					Flask.Login.showErrors(data.error);
				}
			},
			error: function()
			{
				Flask.Login.progressStop();
				Flask.Login.showErrors(Locale.get('FLASK.COMMON.InvalidResponse'));
			}
		});
	}

};


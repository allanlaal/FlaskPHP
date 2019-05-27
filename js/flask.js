
/**
 *
 *	 FlaskPHP
 *	 System JavaScript
 *	 -----------------
 *   2017 (c) Codelab Solutions OÜ <codelab@codelab.ee>
 *   Distributed under the MIT License: https://www.flaskphp.com/LICENSE
 *
 */

$(function(){

	// Set default jQuery ajax options
	$.ajaxSetup({
		type:     "post",
		dataType: "json"
	});

	// Tab switch
	var URL = document.location.toString();
	if (URL.match(/#/))
	{
		var anchor = URL.split('#')[1];
		if ($("#content_"+anchor).length)
		{
			Flask.Tab.selectTab(anchor);
		}
	}

	// Init elements
	Flask.initElements();

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
 *   Alert
 */

Flask.alert = function( alertMessage, alertTitle, reloadOnClose )
{
	var modalTag=Flask.Modal.createModal(alertTitle,alertMessage,null,{
		modalclass: 'small',
		reloadonclose: reloadOnClose
	});
	Flask.Modal.setButtons(modalTag,{
		ok: {
			title: Locale.get('FLASK.MODAL.Btn.OK'),
			class: 'primary',
			onclick: function(){
				if (reloadOnClose) {
					Flask.reload();
				}
				Flask.Modal.closeModal(modalTag);
			}
		}
	});
};


/*
 *   Base actions
 */

Flask.doAjaxAction = function( actionURL, actionData, param, confirmed )
{
	// Init param
	if (param==null) {
		param={};
	}

	// Confirm?
	if (param.confirm!=null && (confirmed==null || !confirmed)) {
		var modalTag=Flask.Modal.createModal(oneof(param.confirm_title,Locale.get('FLASK.MODAL.Confirm')),param.confirm,null,{
			modalclass: 'tiny'
		});
		Flask.Modal.setButtons(modalTag,{
			ok: {
				title: oneof(param.confirm_submit,Locale.get('FLASK.MODAL.Btn.OK')),
				class: 'primary',
				onclick: function(){
					Flask.Modal.closeModal(modalTag);
					Flask.doAjaxAction(actionURL,actionData,param,true);
				}
			},
			cancel: {
				title: Locale.get('FLASK.FORM.Btn.Cancel'),
				onclick: function(){
					Flask.Modal.closeModal(modalTag);
				}
			}
		});
		return;
	}

	// Progress message
	if (param.progressmessage==null || param.progressmessage!==false) {
		Flask.ProgressDialog.show(oneof(param.progressmessage,Locale.get('FLASK.MODAL.Progress')));
	}

	// Make Ajax call
	$.ajax({
		url: actionURL,
		data: actionData,
		success: function(data) {
			if (data!=null && data.status=='1') {
				if (data.redirect!=null) {
					if (data.redirect_data!=null) {
						Flask.doPostSubmit(data.redirect,data.redirect_data);
					}
					else {
						Flask.redirect(data.redirect);
					}
				}
				else if ((data.reload!=null && data.reload=='1') || (param.reload_on_success!=null && param.reload_on_success==true)) {
					Flask.reload();
				}
				else {
					Flask.ProgressDialog.hide();
					Flask.processResponse(data);
				}
			}
			else
			{
				Flask.ProgressDialog.hide();
				Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorSavingData')),Locale.get('FLASK.COMMON.Error'),param.reloadonclose);
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			Flask.ProgressDialog.hide();
			Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorOpeningModal')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
		}
	});
};

Flask.doPostSubmit = function( form_url, form_data, confirm_message, new_window )
{
	// Confirm?
	if (confirm_message!=null) {
		var modalTag=Flask.Modal.createModal(Locale.get('FLASK.MODAL.Confirm'),confirm_message,null,{
			modalclass: 'tiny'
		});
		Flask.Modal.setButtons(modalTag,{
			ok: {
				title: Locale.get('FLASK.MODAL.Btn.OK'),
				class: 'primary',
				onclick: function(){
					Flask.Modal.closeModal(modalTag);
					Flask.doPostSubmit(form_url,form_data,null,new_window);
				}
			},
			cancel: {
				title: Locale.get('FLASK.FORM.Btn.Cancel'),
				onclick: function(){
					Flask.Modal.closeModal(modalTag);
				}
			}
		});
		return;
	}

	// Post
	var form_tag=Math.floor(Math.random()*100000+1);
	if (new_window!=null && new_window=='1')
	{
		var target=' target="_blank"';
	}
	else
	{
		var target='';
	}
	$('body').append('<form id="form_'+form_tag+'" method="post" action="'+form_url+'"'+target+'></form>');
	for (var k in form_data)
	{
		$('#form_'+form_tag).append('<input type="hidden" name="'+k+'" value="'+htmlspecialchars(form_data[k])+'" />');
	}
	$('#form_'+form_tag)[0].submit();
};

Flask.processResponse = function( data, close_progress )
{
	// Redirect?
	if (data.redirect!=null) {
		if (data.redirect_data!=null) {
			Flask.doPostSubmit(data.redirect,data.redirect_data);
		}
		else {
			Flask.redirect(data.redirect);
		}
		return;
	}

	// Reload?
	if (data.reload!=null) {
		Flask.reload();
		return;
	}

	// Close progress
	if (close_progress!=null) {
		Flask.Form.progressStop();
	}

	// Replace content
	if (data.replacecontent!=null) {
		for (var k in data.replacecontent) {
			$(k).html(data.replacecontent[k]);
		}
	}

	// Hide elements
	if (data.hideelements!=null) {
		for (var k in data.hideelements) {
			$(data.hideelements[k]).hide();
		}
	}

	// Show elements
	if (data.showelements!=null) {
		for (var k in data.showelements) {
			$(data.showelements[k]).show();
		}
	}

	// Success triger
	if (data.successaction!=null && data.successaction.length>0) {
		eval(data.successaction);
	}
};


/*
 *   Init elements
 *   -------------
 */

Flask.initElements = function( base )
{
	// Base
	if (base==null || base=='')
	{
		base='';
	}
	else
	{
		base=base+' ';
	}

	// Init dropdowns
	$(base+'.ui.dropdown').dropdown({
		placeholder: false,
		selectOnKeydown: false,
		forceSelection: false,
		fullTextSearch: true,
		match: 'text'
	});

	// Init date fields
	$(base+'.ui.calendar').each(function(){
		var dateFormat=$('input',this).attr('data-date-format');
		$(this).calendar({
			type: 'date',
			text: {
				days: [
					Locale.get('FLASK.COMMON.DAY.Abbr.7'),
					Locale.get('FLASK.COMMON.DAY.Abbr.1'),
					Locale.get('FLASK.COMMON.DAY.Abbr.2'),
					Locale.get('FLASK.COMMON.DAY.Abbr.3'),
					Locale.get('FLASK.COMMON.DAY.Abbr.4'),
					Locale.get('FLASK.COMMON.DAY.Abbr.5'),
					Locale.get('FLASK.COMMON.DAY.Abbr.6')
				],
				months: [
					Locale.get('FLASK.COMMON.Month.1'),
					Locale.get('FLASK.COMMON.Month.2'),
					Locale.get('FLASK.COMMON.Month.3'),
					Locale.get('FLASK.COMMON.Month.4'),
					Locale.get('FLASK.COMMON.Month.5'),
					Locale.get('FLASK.COMMON.Month.6'),
					Locale.get('FLASK.COMMON.Month.7'),
					Locale.get('FLASK.COMMON.Month.8'),
					Locale.get('FLASK.COMMON.Month.9'),
					Locale.get('FLASK.COMMON.Month.10'),
					Locale.get('FLASK.COMMON.Month.11'),
					Locale.get('FLASK.COMMON.Month.12')
				],
				monthsShort: [
					Locale.get('FLASK.COMMON.Month.Short.1'),
					Locale.get('FLASK.COMMON.Month.Short.2'),
					Locale.get('FLASK.COMMON.Month.Short.3'),
					Locale.get('FLASK.COMMON.Month.Short.4'),
					Locale.get('FLASK.COMMON.Month.Short.5'),
					Locale.get('FLASK.COMMON.Month.Short.6'),
					Locale.get('FLASK.COMMON.Month.Short.7'),
					Locale.get('FLASK.COMMON.Month.Short.8'),
					Locale.get('FLASK.COMMON.Month.Short.9'),
					Locale.get('FLASK.COMMON.Month.Short.10'),
					Locale.get('FLASK.COMMON.Month.Short.11'),
					Locale.get('FLASK.COMMON.Month.Short.12')
				],
				today: Locale.get('FLASK.COMMON.Today'),
				now: Locale.get('FLASK.COMMON.Now'),
				am: 'AM',
				pm: 'PM'
			},
			parser: {
				date: function (date, settings) {
					if (!date || date=='') return null;
					if (date.match(/^([0-9]+)$/)) {
						return moment.unix(parseInt(date)).toDate();
					}
					return moment(date,[dateFormat]).toDate();
				}
			},
			formatter: {
				date: function (date, settings) {
					if (!date) return '';
					return moment(date).format(dateFormat);
				}
			}
		});
	});

	// Init file upload fields
	$(base+'.ui.fileupload').each(function(){
		var id=$("input[type=file]",this).attr('id');
		$("button",this).on('click',function(){
			$("#"+id).trigger('click');
		});
		$("input[type=text]",this).on('click',function(){
			$("#"+id).trigger('click');
		});
		$("#"+id).on('change',function(evt){
			var target=evt.currentTarget;
			var files=evt.currentTarget.files;
			if (files.length>0) {
				var filelist='';
				for (var i=0;i<files.length;++i) {
					filelist+=(filelist!=''?', ':'')+files[i].name;
				}
				$("#"+id+"_fdisp").val(filelist);
			}
			else {
				$("#"+id+"_fdisp").val('');
			}
		});
	});
	$(base+'.ui.fileupload-view').each(function(){
		$("input[type=hidden]").siblings('button').on('click',function(){
			$(this).siblings("input[type=hidden]").val('1');
			$(this).closest('.fileupload-view').find('.fileupload-fileinfo').html('<span class="text-muted">'+Locale.get('FLASK.FIELD.File.FileRemoved')+'</span>');
			$(this).closest('.fileupload-view').find('.fileupload-view-displayimage').remove();
		});
	});

	// Set masked inputs
	/*
	$(base+"input[data-mask!='']").each(function(){
		$(this).mask($(this).attr('data-mask'));
	});
	*/

	// Init checkboxes
	$(base+'.ui.checkbox').checkbox();

	// Init tooltips
	$(base+'[data-tooltip]').popup();
	$(base+'[data-html]').popup();
};


/*
 *   Copy text to clipboard
 *   ----------------------
 */

Flask.copyToClipboard = function( str, show_toast )
{
	const el=document.createElement('textarea');
  el.value=str;
  document.body.appendChild(el);
  el.select();
  document.execCommand('copy');
  document.body.removeChild(el);
  if (show_toast!=null && show_toast) {
		$('body')
		.toast({
			message: Locale.get('FLASK.COPYTOCLIPBOARD.Toast'),
			displayTime: 2000,
			class : 'green',
			className: {
				toast: 'ui message'
			}
		});
	}
};


/*
 *   Modal/dialog
 *   ------------
 */

Flask.Modal = {

	// Modal parameters
	param: [],

	// Create modal
	createModal: function( title, content, buttons, param )
	{
		// Init
		if (param==null) param={};
		var modalTag=oneof(param.modalTag,'modal_'+Math.floor(Math.random()*100000+1));
		if (param.modal_width!=null) param.modal_width=parseInt(param.modal_width);
		Flask.Modal.param[modalTag]=param;

		// Draw
		Flask.Modal.drawModal(modalTag,title,content,buttons,param);
		if (!(param.showoncreate!=null && param.showoncreate==false)) {
			Flask.Modal.showModal(modalTag,param);
		}

		// Return tag
		return modalTag;
	},

	// Draw modal
	drawModal: function( modalTag, title, content, buttons, param )
	{
		// Param
		var param=Flask.Modal.param[modalTag];
		var modalClass='ui modal'+(param.modalclass!=null?' '+param.modalclass:'');
		var modalStyle='';

		// Create html
		var modalHTML='<div id="'+modalTag+'" class="'+modalClass+'" tabindex="-1">';
		modalHTML+='<div class="scrolling content">';
		modalHTML+='</div>';
		modalHTML+='</div>';
		$('body').append(modalHTML);

		// Set content
		if (title!=null && title!=false) {
			Flask.Modal.setTitle(modalTag,title,param.noclosebtn);
		}
		if (content!=null && content!=false) {
			Flask.Modal.setContent(modalTag,content);
		}
		if (buttons!=null && buttons!=false) {
			Flask.Modal.setButtons(modalTag,buttons);
		}
	},

	// Show modal
	showModal: function( modalTag, param )
	{
		$('#'+modalTag).modal({
			observeChanges: true,
			allowMultiple: true,
			autofocus: false,
			closable: ((param.noclosebtn!=null && param.noclosebtn==true)?false:true),
			onVisible: function(){
				if ($("#"+modalTag+" .content .defaultfocus").length)
				{
					$("#"+modalTag+" .content .defaultfocus").focus();
				}
				else
				{
					if ($("#"+modalTag+" .content .ui.form input:visible").length)
					{
						$("#"+modalTag+" .content .ui.form input:visible").first().focus();
					}
				}
			},
			onHidden: function(){
				$('#'+modalTag).remove();
				if ($(".ui.dimmer.modals").length && $(".ui.dimmer.modals div").length==0) {
					$(".ui.dimmer.modals").remove();
				}
			}
		}).modal('show');
		if (param.noclosebtn==null) {
			$("#"+modalTag).on('keypress',function(evt){
				if (evt.which==27) {
					Flask.Modal.closeModal(modalTag);
					evt.stopPropagation()
				}
			});
		}
	},

	// Open form modal
	openModal: function( url, post_data, param )
	{
		// Init
		if (param==null) param={};
		if (param.modalclass==null) param.modalclass='tiny';

		// Get content
		$.ajax({
			url: url,
			data: post_data,
			success: function (data) {
				if (data!=null && data.status=='1') {
					delete data.status;
					var modalTag=Flask.Modal.createModal('','','',param);
					if (data.title!=null) {
						Flask.Modal.setTitle(modalTag,data.title);
						delete data.title;
					}
					if (data.content!=null) {
						Flask.Modal.setContent(modalTag,data.content);
						delete data.content;
					}
					if (data.displaytrigger!=null)
					{
						eval(data.displaytrigger);
					}
					var btns={};
					if (param.buttons!=null) {
						btns=param.buttons;
					}
					else if (data.buttons!=null) {
						btns=data.buttons;
						delete data.buttons;
					}
					if ((param.cancelbtn==null || param.cancelbtn==false) && (data.cancelbtn==null || data.cancelbtn==false)) {
						btns.cancel={
							title: (btns.length?Locale.get('FLASK.FORM.Btn.Cancel'):Locale.get('FLASK.FORM.Btn.Close')),
							onclick: function(){
								Flask.Modal.closeModal(modalTag);
							}
						}
					}
					Flask.Modal.setButtons(modalTag,btns);
					for (var k in data) {
						Flask.Modal.param[modalTag][k]=data[k];
					}
					Flask.Form.initElements(modalTag);
					$("#"+modalTag+" form :input").not('textarea').not('.noautosubmit').keypress(function(e){
						if (e.which==13) {
							$("#"+modalTag+" .actions button").first().trigger('click');
						}
					});
				}
				else {
					Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorOpeningModal')),Locale.get('FLASK.COMMON.Error'));
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorOpeningModal')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
			}
		});


	},

	// Set title
	setTitle: function( modalTag, title, noclosebtn )
	{
		// Create header wrapper if needed
		if ($("#"+modalTag+" .header").length==0) {
			var modalHeader='<div class="header">';
			modalHeader+='<div class="title"></div>';
			if (noclosebtn==null || !noclosebtn) {
				modalHeader+='<a class="close-btn"><i class="remove icon"></i></a>';
			}
			modalHeader+='</div>';
			$("#"+modalTag).prepend(modalHeader);
		}

		// Set title
		$("#"+modalTag+" .header .title").html(title);

		// Set close button action
		if (noclosebtn==null || !noclosebtn) {
			$("#"+modalTag+" .header .close-btn").on("click",function(){
				Flask.Modal.closeModal(modalTag);
			});
		}
	},

	// Set content
	setContent: function( modalTag, content )
	{
		$("#"+modalTag+" .content").html(content);
	},

	// Set content
	setButtons: function( modalTag, buttons )
	{
		// Create button container if needed
		if ($("#"+modalTag+" .actions").length==0) {
			var modalFooter='<div class="actions"></div>';
			$("#"+modalTag).append(modalFooter);
		}

		// Add buttons
		for (var k in buttons) {
			var btnHTML='<button id="'+modalTag+'_'+k+'" type="button" class="ui button '+oneof(buttons[k].class,'')+'">'+buttons[k].title+'</button>';
			$("#"+modalTag+" .actions").append(btnHTML);
			if (buttons[k].onclick!=null) {
				$("#"+modalTag+"_"+k).on('click',buttons[k].onclick);
			}
		}
	},

	// Close modal
	closeModal: function( modalTag )
	{
		if (modalTag!=null && modalTag!='undefined') {
			Flask.Modal.hideModal(modalTag);
		}
		else {
			$(".ui.drawer.visible").each(function(){
				Flask.Modal.hideModal($(this).attr('id'));
			});
		}
	},

	// Hide modal
	hideModal: function( modalTag )
	{
		$("#"+modalTag).modal('hide');
	},

};


/*
 *   Drawer
 *   ------
 */

Flask.Drawer = {

	// Drawer parameters
	param: [],

	// Create drawer
	createDrawer: function( title, content, buttons, param )
	{
		// Init
		if (param==null) param={};
		var drawerTag=oneof(param.drawerTag,'drawer_'+Math.floor(Math.random()*100000+1));
		if (param.drawer_width!=null) param.drawer_width=parseInt(param.drawer_width);
		Flask.Drawer.param[drawerTag]=param;

		// Draw
		Flask.Drawer.drawDrawer(drawerTag,title,content,buttons,param);
		if (!(param.showoncreate!=null && param.showoncreate==false)) {
			Flask.Drawer.showDrawer(drawerTag,param);
		}

		// Return tag
		return drawerTag;
	},

	// Draw drawer
	drawDrawer: function( drawerTag, title, content, buttons, param )
	{
		// Param
		var param=Flask.Drawer.param[drawerTag];
		var drawerClass='ui'+(param.drawerclass!=null?' '+param.drawerclass:'')+' drawer';
		var drawerStyle='';

		// Create html
		var drawerHTML='<div id="'+drawerTag+'" class="'+drawerClass+'" tabindex="-1">';
		drawerHTML+='<div class="wrapper">';
		drawerHTML+='<div class="content">';
		drawerHTML+='</div>';
		drawerHTML+='</div>';
		drawerHTML+='<div class="dimmer"></div>';
		drawerHTML+='</div>';
		$('body').append(drawerHTML);

		// Set content
		if (title!=null && title!=false) {
			Flask.Drawer.setTitle(drawerTag,title);
		}
		if (content!=null && content!=false) {
			Flask.Drawer.setContent(drawerTag,content);
		}
		if (buttons!=null && buttons!=false) {
			Flask.Drawer.setButtons(drawerTag,buttons);
		}
	},

	// Show drawer
	showDrawer: function( drawerTag, param )
	{
		$('body').addClass('overflow-hidden');
		$('#'+drawerTag).addClass('visible');
		$('#'+drawerTag+' .dimmer').on('click',function(){
			Flask.Drawer.hideDrawer(drawerTag);
		});
		if ($("#"+drawerTag+" .content .defaultfocus").length)
		{
			$("#"+drawerTag+" .content .defaultfocus").focus();
		}
		else
		{
			if ($("#"+drawerTag+" .content .ui.form input:visible").length)
			{
				$("#"+drawerTag+" .content .ui.form input:visible").first().focus();
			}
		}
		$("#"+drawerTag).on('keypress',function(evt){
			if (evt.which==27) {
				Flask.Drawer.closeDrawer(drawerTag);
				evt.stopPropagation()
			}
		});
	},

	// Open drawer with Ajax content
	openDrawer: function( url, post_data, param )
	{
		// Init
		if (param==null) param={};
		if (param.drawerclass==null) param.drawerclass='normal';
		param.showoncreate=false;

		// Get content
		$.ajax({
			url: url,
			data: post_data,
			success: function (data) {
				if (data!=null && data.status=='1') {
					delete data.status;
					var drawerTag=Flask.Drawer.createDrawer('','','',param);
					if (data.title!=null) {
						Flask.Drawer.setTitle(drawerTag,data.title);
						delete data.title;
					}
					if (data.content!=null) {
						Flask.Drawer.setContent(drawerTag,data.content);
						delete data.content;
					}
					if (data.displaytrigger!=null)
					{
						eval(data.displaytrigger);
					}
					var btns={};
					if (param.buttons!=null) {
						btns=param.buttons;
					}
					else if (data.buttons!=null) {
						btns=data.buttons;
						delete data.buttons;
					}
					if ((param.cancelbtn==null || param.cancelbtn==false) && (data.cancelbtn==null || data.cancelbtn==false)) {
						btns.cancel={
							title: (btns.length?Locale.get('FLASK.FORM.Btn.Cancel'):Locale.get('FLASK.FORM.Btn.Close')),
							onclick: function(){
								Flask.Drawer.closeDrawer(drawerTag);
							}
						}
					}
					Flask.Drawer.setButtons(drawerTag,btns);
					for (var k in data) {
						Flask.Drawer.param[drawerTag][k]=data[k];
					}
					Flask.Form.initElements(drawerTag);
					$("#"+drawerTag+" form :input").not('textarea').not('.noautosubmit').keypress(function(e){
						if (e.which==13) {
							$("#"+drawerTag+" .actions button").first().trigger('click');
						}
					});
					setTimeout(function(){
						Flask.Drawer.showDrawer(drawerTag,param);
					},25);
				}
				else {
					Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorOpeningModal')),Locale.get('FLASK.COMMON.Error'));
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorOpeningModal')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
			}
		});


	},

	// Set title
	setTitle: function( drawerTag, title, noclosebtn )
	{
		// Create header wrapper if needed
		if ($("#"+drawerTag+" .header").length==0) {
			var drawerHeader='<div class="header">';
			drawerHeader+='<div class="title"></div>';
			drawerHeader+='</div>';
			$("#"+drawerTag+" .wrapper").prepend(drawerHeader);
		}

		// Set title
		$("#"+drawerTag+" .header .title").html(title);
	},

	// Set content
	setContent: function( drawerTag, content )
	{
		$("#"+drawerTag+" .content").html(content);
	},

	// Set content
	setButtons: function( drawerTag, buttons )
	{
		// Create button container if needed
		if ($("#"+drawerTag+" .actions").length==0) {
			var drawerFooter='<div class="actions"></div>';
			$("#"+drawerTag+" .wrapper").append(drawerFooter);
		}

		// Add buttons
		for (var k in buttons) {
			var btnHTML='<button id="'+drawerTag+'_'+k+'" type="button" class="ui button '+oneof(buttons[k].class,'')+'">'+buttons[k].title+'</button>';
			$("#"+drawerTag+" .actions").append(btnHTML);
			if (buttons[k].onclick!=null) {
				$("#"+drawerTag+"_"+k).on('click',buttons[k].onclick);
			}
		}
	},

	// Close drawer
	closeDrawer: function( drawerTag )
	{
		if (drawerTag!=null && drawerTag!='undefined') {
			Flask.Drawer.hideDrawer(drawerTag);
		}
		else {
			$(".ui.drawer.visible").each(function(){
				Flask.Drawer.hideDrawer($(this).attr('id'));
			});
		}
	},

	// Hide modal
	hideDrawer: function( drawerTag )
	{
		$('body').removeClass('overflow-hidden');
		$('#'+drawerTag).removeClass('visible');
		setTimeout(function(){
			 $('#'+drawerTag).remove();
		},400);
	}

};


/*
 *   Form handler
 *   ------------
 */

Flask.Form = {

	// Open form modal
	openModal: function( url, post_data, param )
	{
		// Init
		if (param==null) param={};
		if (param.modalclass==null) param.modalclass='tiny';

		// Get content
		$.ajax({
			url: url,
			data: post_data,
			success: function (data) {
				if (data!=null && data.status=='1') {
					delete data.status;
					var modalTag=Flask.Modal.createModal('','','',param);
					if (data.title!=null) {
						Flask.Modal.setTitle(modalTag,data.title);
						delete data.title;
					}
					if (data.content!=null) {
						Flask.Modal.setContent(modalTag,data.content);
						delete data.content;
					}
					if (data.displaytrigger!=null)
					{
						eval(data.displaytrigger);
					}
					var btns={};
					if (data.buttons!=null) {
						btns=data.buttons;
						delete data.buttons;
					}
					else {
						btns.save={
							title: oneof(data.submitbuttontitle,Locale.get('FLASK.FORM.Btn.edit')),
							onclick: function(){
								Flask.Form.submitModal(modalTag,'submit_save');
							},
							class: 'primary'
						};
						if (data.submitbuttontitle!=null) {
							delete data.submitbuttontitle;
						}
					}
					btns.cancel={
						title: Locale.get('FLASK.FORM.Btn.Cancel'),
						onclick: function(){
							Flask.Modal.closeModal(modalTag);
						}
					}
					Flask.Modal.setButtons(modalTag,btns);
					for (var k in data) {
						Flask.Modal.param[modalTag][k]=data[k];
					}
					$("#"+modalTag+" form").first().attr('id','form_'+modalTag);
					Flask.Form.initElements(modalTag);
					$("#"+modalTag+" form :input").not('textarea').not('.noautosubmit').keypress(function(e){
						if (e.which==13) {
							$("#"+modalTag+" .actions button").first().trigger('click');
						}
					});
				}
				else {
					Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorOpeningModal')),Locale.get('FLASK.COMMON.Error'));
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorOpeningModal')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
			}
		});
	},

	// Open form drawer
	openDrawer: function( url, post_data, param )
	{
		// Init
		if (param==null) param={};
		if (param.drawerclass==null) param.drawerclass='normal';
		param.showoncreate=false;

		// Get content
		$.ajax({
			url: url,
			data: post_data,
			success: function (data) {
				if (data!=null && data.status=='1') {
					delete data.status;
					var drawerTag=Flask.Drawer.createDrawer('','','',param);
					if (data.title!=null) {
						Flask.Drawer.setTitle(drawerTag,data.title);
						delete data.title;
					}
					if (data.content!=null) {
						Flask.Drawer.setContent(drawerTag,data.content);
						delete data.content;
					}
					if (data.displaytrigger!=null)
					{
						eval(data.displaytrigger);
					}
					var btns={};
					if (data.buttons!=null) {
						btns=data.buttons;
						delete data.buttons;
					}
					else {
						btns.save={
							title: oneof(data.submitbuttontitle,Locale.get('FLASK.FORM.Btn.edit')),
							onclick: function(){
								Flask.Form.submitDrawer(drawerTag,'submit_save');
							},
							class: 'primary'
						};
						if (data.submitbuttontitle!=null) {
							delete data.submitbuttontitle;
						}
					}
					btns.cancel={
						title: Locale.get('FLASK.FORM.Btn.Cancel'),
						onclick: function(){
							Flask.Drawer.closeDrawer(drawerTag);
						}
					}
					Flask.Drawer.setButtons(drawerTag,btns);
					for (var k in data) {
						Flask.Drawer.param[drawerTag][k]=data[k];
					}
					$("#"+drawerTag+" form").first().attr('id','form_'+drawerTag);
					Flask.Form.initElements(drawerTag);
					$("#"+drawerTag+" form :input").not('textarea').not('.noautosubmit').keypress(function(e){
						if (e.which==13) {
							$("#"+drawerTag+" .actions button").first().trigger('click');
						}
					});
					setTimeout(function(){
						Flask.Drawer.showDrawer(drawerTag,param);
					},25);
				}
				else {
					Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorOpeningModal')),Locale.get('FLASK.COMMON.Error'));
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorOpeningModal')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
			}
		});
	},

	// Handle submit
	submit: function( submitAction, param, formID )
	{
		// Init
		if (submitAction==null) {
			var submitAction='save';
		}
		if (formID==null) {
			var formID='flask-form';
		}

		// Params
		if (param==null) {
			var param={};
		}

		// Check
		if ($("#"+formID)[0].action==null || $("#"+formID)[0].action=="") {
			Flask.alert(Locale.get('FLASK.FORM.Error.CouldNotFindForm'),LOCALE.get('FLASK.COMMON.Error'));
			return;
		}

		// Clear errors
		this.clearErrors(formID);

		// Progress message
		var progressmessage=oneof(param.progressmessage,Locale.get('FLASK.FORM.Msg.Saving'));
		this.progressStart(progressmessage);

		// Submit
		var submitdata={};
		submitdata.action=submitAction;
		submitdata[submitAction]=1;
		$("#"+formID).ajaxSubmit({
			data: submitdata,
			success: function( data ) {
				if (data!=null && data.status=='1') {
					if (param.success_callback!=null) {
						Flask.Form.progressStop();
						param.success_callback(data);
					}
					Flask.processResponse(data,true);
				}
				else
				{
					Flask.Form.progressStop();
					if (data!=null && data.error!=null) {
						if (typeof(data.error)=='object') {
							var globalerror=[];
							for(var fld_tag in data.error) {
								if ($("#field_"+fld_tag).length>0) {
									Flask.Form.showFieldError(fld_tag,data.error[fld_tag]);
								}
								else {
									globalerror.push(data.error[fld_tag]);
								}
							}
							if (globalerror.length) {
								Flask.Form.showErrors(formID,globalerror);
							}
						}
						else {
							Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorSavingData')),Locale.get('FLASK.COMMON.Error'));
						}
					}
					else {
						Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorSavingData'),Locale.get('FLASK.COMMON.Error'));
					}
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.Form.progressStop();
				Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorSavingData')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
			}
		});
	},

	// Submit modal
	submitModal: function ( modalTag, submitAction, param )
	{
		// Check
		if ($("#"+modalTag+" form").length==0) {
			Flask.alert(Locale.get('FLASK.FORM.Error.CouldNotFindForm'),Locale.get('FLASK.COMMON.Error'));
			return;
		}
		else {
			var formID=$("#"+modalTag+" form").first().attr('id');
		}

		// Param
		if (param!=null) {
			for (var k in param) {
				Flask.Modal.param[modalTag]=param[k];
			}
		}
		var param=Flask.Modal.param[modalTag];

		// Clear errors
		this.clearErrors(formID);

		// Progress message
		var progressmessage=oneof(param.progressmessage,Locale.get('FLASK.FORM.Msg.Saving'));
		this.progressStart(progressmessage);

		// Submit
		var submitdata={};
		submitdata.action=submitAction;
		submitdata[submitAction]=1;
		$("#"+formID).ajaxSubmit({
			data: submitdata,
			success: function( data ) {
				if (data!=null && data.status=='1') {
					if (param.success_callback!=null) {
						Flask.Form.progressStop();
						param.success_callback(data);
					}
					Flask.processResponse(data,true);
					Flask.Modal.closeModal(modalTag);
				}
				else
				{
					Flask.Form.progressStop();
					if (data!=null && data.error!=null) {
						if (typeof(data.error)=='object') {
							var globalerror=[];
							for(var fld_tag in data.error) {
								if ($("#field_"+fld_tag).length>0) {
									Flask.Form.showFieldError(fld_tag,data.error[fld_tag]);
								}
								else {
									globalerror.push(data.error[fld_tag]);
								}
							}
							if (globalerror.length) {
								Flask.Form.showErrors(formID,globalerror);
							}
						}
						else {
							Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorSavingData')),Locale.get('FLASK.COMMON.Error'));
						}
					}
					else {
						Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorSavingData'),Locale.get('FLASK.COMMON.Error'));
					}
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.Form.progressStop();
				Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorSavingData')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
			}
		});
	},

	// Submit modal
	submitDrawer: function ( drawerTag, submitAction, param )
	{
		// Check
		if ($("#"+drawerTag+" form").length==0) {
			Flask.alert(Locale.get('FLASK.FORM.Error.CouldNotFindForm'),Locale.get('FLASK.COMMON.Error'));
			return;
		}
		else {
			var formID=$("#"+drawerTag+" form").first().attr('id');
		}

		// Param
		if (param!=null) {
			for (var k in param) {
				Flask.Drawer.param[drawerTag]=param[k];
			}
		}
		var param=Flask.Drawer.param[drawerTag];

		// Clear errors
		this.clearErrors(formID);

		// Progress message
		var progressmessage=oneof(param.progressmessage,Locale.get('FLASK.FORM.Msg.Saving'));
		this.progressStart(progressmessage);

		// Submit
		var submitdata={};
		submitdata.action=submitAction;
		submitdata[submitAction]=1;
		$("#"+formID).ajaxSubmit({
			data: submitdata,
			success: function( data ) {
				if (data!=null && data.status=='1') {
					if (param.success_callback!=null) {
						Flask.Form.progressStop();
						param.success_callback(data);
					}
					Flask.processResponse(data,true);
					Flask.Drawer.closeDrawer(modalTag);
				}
				else
				{
					Flask.Form.progressStop();
					if (data!=null && data.error!=null) {
						if (typeof(data.error)=='object') {
							var globalerror=[];
							for(var fld_tag in data.error) {
								if ($("#field_"+fld_tag).length>0) {
									Flask.Form.showFieldError(fld_tag,data.error[fld_tag]);
								}
								else {
									globalerror.push(data.error[fld_tag]);
								}
							}
							if (globalerror.length) {
								Flask.Form.showErrors(formID,globalerror);
							}
						}
						else {
							Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorSavingData')),Locale.get('FLASK.COMMON.Error'));
						}
					}
					else {
						Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorSavingData'),Locale.get('FLASK.COMMON.Error'));
					}
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.Form.progressStop();
				Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorSavingData')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
			}
		});
	},

	// Update fields
	updateFields: function ( fieldTag, url, param )
	{
		// Check
		if ($("#"+fieldTag).closest("form").length==0) {
			Flask.alert(Locale.get('FLASK.FORM.Error.CouldNotFindForm'),Locale.get('FLASK.COMMON.Error'));
			return;
		}
		else {
			var formID=$("#"+fieldTag).closest("form").first().attr('id');
		}
		if (url==null) {
			if ($("#"+fieldTag).attr('data-update-url')!="") {
				var url=$("#"+fieldTag).attr('data-update-url');
			}
			else {
				Flask.alert('No URL and no data-update-url on update field',Locale.get('FLASK.COMMON.Error'));
				return;
			}
		}

		// Params
		if (param==null) {
			var param={};
		}

		// Progress message
		if (param.progressmessage!=null && param.progressmessage!='') {
			this.progressStart(progressmessage);
		}

		// Submit
		var submitdata={};
		submitdata.action='updatefields';
		submitdata.update_from=fieldTag;
		$("#"+formID).ajaxSubmit({
			data: submitdata,
			url: url,
			success: function( data ) {
				Flask.Form.progressStop();
				if (data!=null && data.status=='1') {
					if (data.values!=null) {
						Flask.Form.setValues(data.values);
					}
				}
				else
				{
					Flask.Form.progressStop();
					if (data!=null && data.error!=null) {
						if (typeof(data.error)=='object') {
							var globalerror=[];
							for(var fld_tag in data.error) {
								if ($("#field_"+fld_tag).length>0) {
									Flask.Form.showFieldError(fld_tag,data.error[fld_tag]);
								}
								else {
									globalerror.push(data.error[fld_tag]);
								}
							}
							if (globalerror.length) {
								Flask.Form.showErrors(formID,globalerror);
							}
						}
						else {
							Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorReadingData')),Locale.get('FLASK.COMMON.Error'));
						}
					}
					else {
						Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorReadingData'),Locale.get('FLASK.COMMON.Error'));
					}
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.Form.progressStop();
				Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorReadingData')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
			}
		});
	},

	// Update values
	setValues: function( values )
	{
		for (var fld in values) {
			if ($("select[name="+fld+"]").length) {
				var val=$("select[name="+fld+"]").val();
				if (values[fld]!=null && typeof(values[fld])=='object') {
					Flask.Form.setSelectOptions("select[name="+fld+"]",values[fld],$("select[name="+fld+"]").val());
				}
				else {
					$("#"+fld).val(values[fld]);
				}
				if (val!=values[fld]) {
					$("#"+fld).trigger('change');
				}
			}
			else if ($("#"+fld).parent(".ui.dropdown").length) {
				if (typeof(values[fld])==='object') {
					var valueHTML='';
					for (var k in values[fld]) {
						valueHTML=valueHTML+'<div class="item" data-value="'+k+'">'+values[fld][k]+'</div>';
					}
					$("#field_"+fld+" .menu").html(valueHTML);
				}
				else {
					$("#field_"+fld+" .menu").html(values[fld]);
				}
				$("#"+fld).parent(".ui.dropdown").dropdown('refresh');
				$("#"+fld).parent(".ui.dropdown").dropdown('set selected','');
			}
			else if ($("input[name="+fld+"]").attr('type')=='radio') {
				$("#"+fld+"_"+values[fld]).trigger("click");
			}
			else if ($("input[name="+fld+"]").attr('type')=='checkbox') {
				if (values[fld]=='1') {
					$("#"+fld).attr('checked','checked');
				}
				else {
					$("#"+fld).removeAttr('checked');
				}
			}
			else {
				$("#"+fld).val(values[fld]);
			}
		}
	},

	// Load dropdown options
	setSelectOptions: function( field, options, selectedValue )
	{
		if (!$(field).length) return;
		if ($(field).prop('tagName')=='SELECT') {
			var option='';
			for(var key in options) {
				var dkey=((key.indexOf("#_") > -1)?key.replace("#_",""):key).trim();
				option+='<option value="'+dkey+'"'+(dkey==selectedValue?' selected="selected"':'')+'>'+options[key]+'</option>';
			}
			$(field+' option').remove();
			$(field).html(option);
		}
		else if ($(field).parent(".ui.dropdown").length) {
			if (typeof(options)==='object') {
				var valueHTML='';
				for (var key in options) {
					var dkey=((key.indexOf("#_") > -1)?key.replace("#_",""):key).trim();
					valueHTML=valueHTML+'<div class="item" data-value="'+dkey+'">'+options[key]+'</div>';
				}
				$(".menu",$(field).parent(".ui.dropdown")).html(valueHTML);
			}
			else {
				$(".menu",$(field).parent(".ui.dropdown")).html(options);
			}
			$(field).parent(".ui.dropdown").dropdown('refresh');
			$(field).parent(".ui.dropdown").dropdown('set selected',selectedValue);
		}
	},

	// Init elements
	initElements: function( formID )
	{
		// Init UI elements
		Flask.initElements('#'+formID);

		// Add triggers to remove error message on form element change
		$("#"+formID+" input[type='text']").on('keypress',function(){
			$(this).closest('.field').find(".ui.red.label").remove();
		});
		$("#"+formID+" input[type='password']").on('keypress',function(){
			$(this).closest('.field').find(".ui.red.label").remove();
		});
		$("#"+formID+" textarea").on('keypress',function(){
			$(this).closest('.field').find(".ui.red.label").remove();
		});
		$("#"+formID+" select").on('change',function(){
			$(this).closest('.field').find(".ui.red.label").remove();
		});
		$("#"+formID+" input[type='checkbox']").on('change',function(){
			$(this).closest('.field').find(".ui.red.label").remove();
		});
		$("#"+formID+" input[type='radio']").on('change',function(){
			$(this).closest('.field').find(".ui.red.label").remove();
		});
	},

	// Clear errors
	clearErrors: function( formID )
	{
		$("#"+formID).removeClass('error');
		$("#"+formID+" .ui.red.label").remove();
		$("#"+formID+" .error.message").remove();
	},

	// Show errors
	showErrors: function( formID, error )
	{
		var html='<div class="ui error message">';
		for (var e in error) {
			html+='<div>'+error[e]+'</div>';
		}
		html+='</div>';
		$("#"+formID).addClass('error').append(html);
	},

	// Show field error
	showFieldError: function( field, error )
	{
		$("#"+field).closest('form').addClass('error');
		if ($("#"+field).parent().is('.ui.input') || $("#"+field).parent().is('.ui.dropdown')) {
			$("#"+field).parent().parent().append('<div class="ui basic red pointing prompt label transition visible">'+error+'</div>');
		}
		else if ($("#field_"+field+" .ui.radiochecklist.segment").length) {
			$("#field_"+field).append('<div class="ui basic red pointing prompt label transition visible">'+error+'</div>');
		}
		else {
			$("#"+field).closest('.field').append('<div class="ui basic red pointing prompt label transition visible">'+error+'</div>');
		}
		if ($(":input.error").length==1) {
			$("#"+field).focus();
		}
	},

	// Progress start trigger
	progressStart: function( progressmessage )
	{
		Flask.ProgressDialog.show(progressmessage);
	},

	// Progress stop trigger
	progressStop: function()
	{
		Flask.ProgressDialog.hide();
	},

	// Convert field value to uppercase
	toUpperCase: function ( fieldTag )
	{
		$(fieldTag).val($(fieldTag).val().toUpperCase());
	},

	// Convert field value to uppercase
	toLowerCase: function ( fieldTag )
	{
		$(fieldTag).val($(fieldTag).val().toLowerCase());
	},

	// Convert field value to "name case"
  toNameCase: function ( fieldTag )
	{
		var name=$(fieldTag).val();

		// Convert "Name Name"
		var pieces=name.split(" ");
		for (var i=0;i<pieces.length;i++) {
			var j = pieces[i].charAt(0).toUpperCase();
			pieces[i] = j + pieces[i].substr(1);
		}
		name=pieces.join(" ");

		// Convert "Name-Name"
		var pieces=name.split("-");
		for (var i=0;i<pieces.length;i++) {
			var j = pieces[i].charAt(0).toUpperCase();
			pieces[i] = j + pieces[i].substr(1);
		}
		name=pieces.join("-");

		$(fieldTag).val(name);
	},

	// Hide/show fields
	showFields: function( hideFields, showFields, field, paramAttr, init )
	{
		if (field!=null && $(field).length==0) return;
		if (hideFields!='') {
			$(hideFields).hide();
		}
		if (field!=null) {
			if (paramAttr!=null) {
				if ($(field).get(0).tagName=='SELECT') {
					showFields=showFields.replace('$val',$(field+' option:selected').attr(paramAttr));
					showFields=showFields.replace('$empty',(($(field).val().length==0 || $(field).val()=='0')?'empty':'notempty'));
				}
				else {
					showFields=showFields.replace('$val',$(field).attr(paramAttr));
					showFields=showFields.replace('$empty',(($(field).attr(paramAttr).length==0)?'empty':'notempty'));
				}
			}
			else 	{
				if ($(field).attr('type')=='checkbox') {
					showFields=showFields.replace('$val',($(field).is(':checked')?'1':'0'));
					showFields=showFields.replace('$empty',($(field).is(':checked')?'notempty':'empty'));
				}
				else if ($(field).attr('type')=='radio') {
					showFields=showFields.replace('$val',$(field+':checked').val());
					showFields=showFields.replace('$empty',($(field+':checked').length>0?'notempty':'empty'));
				}
				else {
					showFields=showFields.replace('$val',$(field).val());
					showFields=showFields.replace('$empty',(($(field).val()==null || $(field).val().length==0 || ($(field).get(0).tagName=='SELECT' && $(field).val()=='0'))?'empty':'notempty'));
				}
			}
		}
		if (showFields!='') {
			$(showFields).show();
		}
		if (hideFields!='') {
			$(hideFields + " input[type=text]").each(function(){
				if ($(this).closest("div.field").css('display')=='none' && $(this).attr('data-keephiddenvalue')!='1') {
					$(this).val($(this).attr('data-emptyformat'));
				}
			});
			$(hideFields + " textarea").each(function(){
				if ($(this).closest("div.field").css('display')=='none' && $(this).attr('data-keephiddenvalue')!='1') {
					$(this).val($(this).attr('data-emptyformat'));
				}
			});
			$(hideFields + " select").each(function(){
				if ($(this).closest("div.field").css('display')=='none' && $(this).attr('data-keephiddenvalue')!='1') {
					if ($(this).attr('data-emptyformat')!=null && $(this).attr('data-emptyformat')!='') {
						$(this).val($(this).attr('data-emptyformat'));
					}
					else {
						$(this).val($('option:first',this).attr('value'));
					}
				}
			});
			$(hideFields + " input[type=checkbox]").each(function(){
				if ($(this).closest("div.field").css('display')=='none' && $(this).attr('data-keephiddenvalue')!='1') {
					$(this).removeAttr('checked');
				}
			});
		}
		$(window).trigger('resize');
	}

};


/*
 *   List
 */

Flask.List = {

	// Update filters
	updateFilters: function( listID, listURL, param )
	{
		Flask.List.progressStart(Locale.get('FLASK.LIST.Progress'));
		if (param!=null) {
			var submitData=param;
		}
		else {
			var submitData={};
		}
		submitData.action='filter_submit';
		$("#list_"+listID+"_filter").ajaxSubmit({
			type: 'post',
			url: listURL,
			data: submitData,
			success: function( data ) {
				if (data!=null && data.status=='1') {
					if (data.reload!=null && data.reload=='1') {
						Flask.reload();
					}
					else if (data.redirect!=null && data.redirect.length>0) {
						if (data.redirect_data!=null) {
							Flask.doPostSubmit(data.redirect,data.redirect_data);
						}
						else {
							Flask.redirect(data.redirect);
						}
					}
					else {
						Flask.List.progressStop();
						if (data.filter!=null && data.filter!='') {
							$("#list_"+listID+"_filter").html(data.filter);
							Flask.List.initFilters(listID);
						}
						if (data.content!=null && data.content!='') {
							$("#list_"+listID).html(data.content);
						}
						if (data.scrolltop!=null && data.scrolltop=='1') {
							$(window).scrollTop(0);
						}
					}
				}
				else
				{
					Flask.List.progressStop();
					Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorReadingData')),Locale.get('FLASK.COMMON.Error'));
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.List.progressStop();
				Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorReadingData')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
			}
		});
	},

	// Get content
	getContent: function( listID, listURL, action, param )
	{
		Flask.List.progressStart(Locale.get('FLASK.LIST.Progress'));
		if (param!=null) {
			var submitData=param;
		}
		else {
			var submitData={};
		}
		submitData.action=action;
		$.ajax({
			type: 'post',
			url: listURL,
			data: submitData,
			success: function( data ) {
				if (data!=null && data.status=='1') {
					if (data.reload!=null && data.reload=='1') {
						Flask.reload();
					}
					else if (data.redirect!=null && data.redirect.length>0) {
						if (data.redirect_data!=null) {
							Flask.doPostSubmit(data.redirect,data.redirect_data);
						}
						else {
							Flask.redirect(data.redirect);
						}
					}
					else {
						Flask.List.progressStop();
						if (data.filter!=null && data.filter!='') {
							$("#list_"+listID+"_filter").html(data.filter);
							Flask.List.initFilters(listID);
						}
						if (data.content!=null && data.content!='') {
							$("#list_"+listID).html(data.content);
						}
						if (data.scrolltop!=null && data.scrolltop=='1') {
							$(window).scrollTop(0);
						}
					}
				}
				else
				{
					Flask.List.progressStop();
					Flask.alert(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorReadingData')),Locale.get('FLASK.COMMON.Error'));
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.List.progressStop();
				Flask.alert(Locale.get('FLASK.COMMON.Error.ErrorReadingData')+' - '+thrownError+' - '+xhr.responseText,Locale.get('FLASK.COMMON.Error'));
			}
		});
	},

	// Init filters
	initFilters: function( listID )
	{
		Flask.initElements("#list_"+listID+"_filter");
		$("#list_"+listID+"_filter :input").not('textarea').not('.noautosubmit').keypress(function(e){
			if (e.which==13) {
				$("#list_"+listID+"_filter .list-filter-submit button").first().trigger('click');
			}
		});
	},

	// Progress start trigger
	progressStart: function( progressmessage )
	{
		Flask.ProgressDialog.show(progressmessage);
	},

	// Progress stop trigger
	progressStop: function()
	{
		Flask.ProgressDialog.hide();
	}

};


/*
 *   Progress dialog
 */

Flask.ProgressDialog = {

	// Show
	show: function( message, container ) {
		if (container!=null) {
			var progressHTML='<div id="progressdimmer" class="ui active inverted dimmer"><div class="content"><div class="center"><div class="ui active centered inline large text loader">'+message+'</div></div></div></div>';
			$(container).append(progressHTML);
		}
		else {
			var progressHTML='<div id="progressdimmer" class="ui page active inverted dimmer"><div class="content"><div class="center"><div class="ui active centered inline large text loader">'+message+'</div></div></div></div>';
			$('body').append(progressHTML);
		}
		$("#progressdimmer").dimmer({
			closable: false
		}).dimmer('show');
	},

	// Hide
	hide: function () {
		if ($("#progressdimmer").length) {
			$("#progressdimmer").dimmer({
				onHide: function(){
					$("#progressdimmer").remove();
				}
			}).dimmer('hide');
		}
	}

};


/*
 *   Tabbed view
 *   -----------
 */

Flask.Tab = {

	loaded: [],

	// Select tab
	selectTab: function( tab, tabContentURL ) {
		// Show tab
		this.showTab(tab);

		// URL
		var URL=document.location.toString();
		if (URL.match('#'))
		{
			var URLbase=URL.split('#')[0];
			document.location.href=URLbase+'#'+tab;
		}
		else
		{
			document.location.href=URL+'#'+tab;
		}

		// Already loaded
		if (this.loaded[tab]!=null && this.loaded[tab]==1) {
			return;
		}

		// Load content
		if (tabContentURL==null && $("#content_"+tab).attr('rel')!='undefined' && $("#content_"+tab).attr('rel')!='')
		{
			tabContentURL=$("#content_"+tab).attr('rel');
		}
		if (tabContentURL!=null)
		{
			// Show loading progress
			this.progressStart(tab);

			// Get content
			$.ajax({
				method: 'GET',
				url: tabContentURL,
				success: function( data ) {
					Flask.Tab.progressStop(tab);
					if (data!=null && data.status=='1') {
						$("#content_"+tab).hide().html(data.content).fadeIn(200);
						Flask.Tab.loaded[tab]=1;

						// Init tooltips
						Flask.initElements("#content_"+tab);

						// Init tab
						Flask.Tab.initTab(tab);

						// Fire display trigger
						if ($("#content_"+tab).attr('data-displaytrigger').length)
						{
							eval($("#content_"+tab).attr('data-displaytrigger'));
						}
					}
					else
					{
						Flask.Tab.showError(oneof(data.error,Locale.get('FLASK.COMMON.Error.ErrorReadingData')));
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					Flask.Tab.progressStop(tab);
					Flask.Tab.showError(Locale.get('FLASK.COMMON.Error.ErrorReadingData')+' - '+thrownError+' - '+xhr.responseText);
				}
			});
		}
	},

	// Show tab
	showTab: function( tab ) {
		$(".tabbedview-tabcontent").hide();
		$("#content_"+tab).show();
		$(".tabbedview-tabs .item").removeClass('active');
		$("#tab_"+tab).addClass('active');
	},

	showError: function( tab, error ) {
		// This should be implemented in the layout extension.
		$("#content_"+tab).html('<div class="error">'+error+'</div>');
	},

	// Progress start trigger
	progressStart: function( tab ) {
		// This should be implemented in the layout extension.
	},

	// Progress stop trigger
	progressStop: function( tab ) {
		// This should be implemented in the layout extension.
	},

	// Init tab
	initTab: function ( tab ) {
		// This can be implemented in the application.
	}

};


/*
 *   Chooser field
 *   -------------
 */

Flask.Chooser = {

	// Open search modal
	openModal: function( fieldTag, param, data )
	{
		// Generate HTML
		html='<div class="chooser-modal">';
		html+='<div class="ui fluid action input">';
		html+='<input type="text" class="chooser-modal-searchinput" id="'+fieldTag+'_search" placeholder="'+param.search_placeholder+'" autocomplete="off" maxlength="255">';
		html+='<button class="ui button" id="'+fieldTag+'_submit"><i class="search icon"></i> '+Locale.get('FLASK.FIELD.Chooser.Search.Submit')+'</button>';
		if (param.addform!=null && param.addform==1) {
			html+='<button class="ui basic button ml-2" id="'+fieldTag+'_addform">'+param.addform_buttontitle+'</button>';
		}
		html+='</div>';
		html+='<div class="chooser-modal-searchresult" id="'+fieldTag+'_result"></div>';
		html+='</div>';

		// Open dialog
		var modalTag=Flask.Modal.createModal(param.search_title,html,null,{
			modalTag: 'searchmodal_'+fieldTag
		});
		Flask.Modal.showModal(modalTag,param);
		$("#"+fieldTag+'_search').focus();

		// Attach events
		$("#"+fieldTag+"_search").on('keypress',function(event){
			Flask.Chooser.searchKeyPress(event,fieldTag,param,data);
		});
		$("#"+fieldTag+"_submit").on('click',function(){
			Flask.Chooser.searchSubmit(fieldTag,param,data);
		});
		if (param.addform!=null && param.addform==1) {
			$("#"+fieldTag+"_addform").on('click',function(){
				Flask.Form.openModal(param.addform_url);
			});
		}
	},

	// Close search modal
	closeModal: function( fieldTag )
	{
		Flask.Modal.closeModal('searchmodal_'+fieldTag);
	},

	// Keypress event handler
	searchKeyPress: function( event, fieldTag, param, data )
	{
		if (event.which==13) {
			this.searchSubmit(fieldTag,param,data);
			event.stopPropagation();
		}
	},

	// Do search
	searchSubmit: function( fieldTag, param, data )
	{
		// Search value
		var search=$("#"+fieldTag+"_search").val().trim();
		if (search=='') return;

		// Submit data
		if (data==null) {
			var data={};
		}
		data.field=fieldTag;
		data.search=search;

		// Run query
		Flask.Chooser.progressStart(fieldTag);
		$.ajax({
			url: param.search_url,
			data: data,
			success: function( data ) {
				Flask.Chooser.progressStop(fieldTag);
				if (data!=null && data.status=='1')
				{
					Flask.Chooser.displaySearchResults(fieldTag,data.content);
				}
				else
				{
					Flask.Chooser.displayError(fieldTag,oneof(data.error,Locale.get('FLASK.FIELD.Chooser.Error')));
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				Flask.Chooser.progressStop(fieldTag);
				Flask.Chooser.displayError(Locale.get('FLASK.FIELD.Chooser.Error')+' - '+thrownError+' - '+xhr.responseText);
			}
		});
	},

	// Progress start trigger
	progressStart: function( fieldTag ) {
		$("#"+fieldTag+"_result").html('<span class="spinner"></span> '+Locale.get('FLASK.FIELD.Chooser.Progress'));
	},

	// Progress stop trigger
	progressStop: function( fieldTag ) {
		$("#"+fieldTag+"_result").html('');
	},

	// Display search results
	displaySearchResults: function( fieldTag, content )
	{
		$("#"+fieldTag+"_result").html(content);
	},

	// Display error
	displayError: function( fieldTag, error )
	{
		$("#"+fieldTag+"_result").html('<div class="p-3 bg-danger text-white">'+error+'</div>');
	},

	// Clear chooser value
	clearChooser: function( fieldTag, param )
	{
		$("#"+fieldTag).val('');
		$("#field_"+fieldTag+" .chooser-value").html('<div class="chooser-emptyvalue text-muted">'+param.emptyvalue+'</div>');
		$("#field_"+fieldTag+" .chooser-clearbtn").hide();
	},

	// Choose chooser value
	chooseValue: function( fieldTag, value, description )
	{
		// This can be overwritten in the layout class if needed
		$("#"+fieldTag).val(value).trigger('change');
		$("#field_"+fieldTag+" .chooser-value").html(description);
		$("#field_"+fieldTag+" .chooser-clearbtn").show();
		Flask.Chooser.closeModal(fieldTag);
	},

	// Clear chooser value
	addValue: function( fieldTag, value, description )
	{
		// TODO: finish
	}

};


/*
 *   Log data utilities
 *   ------------------
 */

Flask.LogData = {

	// Show log data
	showLogData: function( logData )
	{
		var decodedLogData=Base64.decode(logData);
		Flask.alert(decodedLogData,Locale.get('FLASK.LOG.Fld.LogData'));
	}

};



/*
 *   Password utilities
 *   ------------------
 */

Flask.Password = {

	// Suggest a secure password
	suggestPassword: function( fieldTag )
	{
		// Generate password
		while(true)
		{
			var password='';
			var symbols='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz987654321';
			for(var b=0;b<4;b++)
			{
				if (b>0) password+='-';
				for(var i=0;i<3;i++)
				{
					password+=symbols[(Math.floor(Math.random() * symbols.length))];
				}
			}
			if (password.match(/([987654321])+/)) break;
		}

		// Set
		$("#"+fieldTag).val(password);
		$("#"+fieldTag+"_repeat").val(password);

		// Show
		Flask.alert(Locale.get('FLASK.FIELD.Password.Suggest.Result.1')+': <b>'+password+'</b><br/>'+Locale.get('FLASK.FIELD.Password.Suggest.Result.2'),Locale.get('FLASK.FIELD.Password.Suggest.Title'));
	}

};


/*
 *   Login handler
 *   -------------
 */

Flask.Login = {

	// Handle e-mail field keypress
	emailKeypress: function(evt) {
		if ( evt.which == 13 )
		{
			$("#login_password")[0].focus();
			evt.stopPropagation();
		}
	},

	// Handle password field keypress
	passwordKeypress: function(evt) {
		if ( evt.which == 13 )
		{
			Flask.Login.doLogin();
			evt.stopPropagation();
		}
	},

	// Validate login
	validateLogin: function() {
		this.clearErrors();

		var email=jQuery.trim($("#login_email").val());
		if (email=='') {
			this.showFieldError('login_email',Locale.get('FLASK.USER.Login.Error.EmailEmpty'));
			return false;
		}
		else if (!email.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)) {
			this.showFieldError('login_email',Locale.get('FLASK.USER.Login.Error.EmailInvalid'));
			return false;
		}

		var password=jQuery.trim($("#login_password").val());
		if (password=='') {
			this.showFieldError('login_password',Locale.get('FLASK.USER.Login.Error.PasswordEmpty'));
			return false;
		}

		return true;
	},


	// Init elements
	initElements: function() {
		$("#login_form input[type='text'], #login_form input[type='password']").on('keypress',function(){
			$(this).closest('.field').find(".ui.red.label").remove();
		});
	},

	// Clear errors
	clearErrors: function() {
		$("#login_form").removeClass('error');
		$("#login_form *").removeClass('error');
		$("#login_form div.ui.red.label").remove();
		$("#login_message").html('');
	},

	// Show errors
	showErrors: function( error ) {
		if (typeof(error)=='object')
		{
			var generalError='';
			for (var fld in error) {
				if ($("#field_"+fld).length) {
					Flask.Login.showFieldError(fld,error[fld]);
				}
				else {
					generalError=generalError+'<div>'+error[fld]+'</div>';
				}
			}
			if (generalError!='') {
				$("#login_form").addClass('error');
				$("#login_message").html('<div class="ui error message">'+generalError+'</div>');
			}
		}
		else
		{
			$("#login_form").addClass('error');
			$("#login_message").html('<div class="ui error message">'+error+'</div>');
		}
	},

	// Show field error
	showFieldError: function( field, error ) {
		$("#login_form").addClass('error');
		$("#"+field).after('<div class="ui basic red pointing prompt label transition visible">'+error+'</div>');
	},

	// Progress start trigger
	progressStart: function() {
		$("#login_email").attr('readonly','readonly');
		$("#login_password").attr('readonly','readonly');
		$("#login_submit").attr('disabled','disabled');
		$("#login_submit").html('<div class="ui active inverted mini inline loader"></div>');
	},

	// Progress stop trigger
	progressStop: function() {
		$("#login_email").removeAttr('readonly');
		$("#login_password").removeAttr('readonly');
		$("#login_submit").removeAttr('disabled');
		$("#login_submit").html($("#login_submit").attr('data-title'));
	},

	// Do login
	doLogin: function() {
		if (!Flask.Login.validateLogin()) return;
		Flask.Login.progressStart();
		$("#login_form").ajaxSubmit({
			type: 'post',
			data: {
				login: '1'
			},
			success: function( data ) {
				Flask.Login.progressStop();
				if (data!=null && data.status=='1') {
					Flask.processResponse(data);
				}
				else
				{
					Flask.Login.showErrors(data.error);
				}
			},
			error: function() {
				Flask.Login.progressStop();
				Flask.Login.showErrors(Locale.get('FLASK.COMMON.InvalidResponse'));
			}
		});
	}

};


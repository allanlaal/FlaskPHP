
/**
 *
 *	 FlaskPHP
 *	 Layout JS extensions: Bootstrap 4
 *	 ---------------------------------
 *   2017 (c) Codelab Solutions OÜ <codelab@codelab.ee>
 *   Distributed under the MIT License: https://www.flaskphp.com/LICENSE
 *
 */



/*
 *   Init
 *   ----
 */

$(function(){

	// Init tooltips
  $('[data-toggle="tooltip"]').tooltip();

});



/*
 *   Modal/dialog
 *   ------------
 */

// Draw modal
Flask.Modal.drawModal = function( modalTag, title, content, buttons )
{
	// Param
	var param=Flask.Modal.param[modalTag];
	var modalClass='modal'+(param.modalclass!=null?' '+param.modalclass:'');
	var modalStyle='';
	if (param.width!=null) {
		modalStyle+='width: '+param.width+';max-width: 100%;';
	}
	else {
		modalStyle+='width: 750px;max-width: 100%;';
	}

	// Create html
	var modalHTML='<div id="'+modalTag+'" class="'+modalClass+'" tabindex="-1">';
	modalHTML+='<div class="modal-dialog" style="'+modalStyle+'">';
	modalHTML+='<div class="modal-content">';
	modalHTML+='<div class="modal-body"></div>';
	modalHTML+='</div>';
	modalHTML+='</div>';
	modalHTML+='</div>';
	$('body').append(modalHTML);

	// Set content
	if (title!=null && title!=false) {
		Flask.Modal.setTitle(modalTag,title);
	}
	if (content!=null && content!=false) {
		Flask.Modal.setContent(modalTag,content);
	}
	if (buttons!=null && buttons!=false) {
		Flask.Modal.setButtons(modalTag,buttons);
	}
};

// Show modal
Flask.Modal.showModal = function( modalTag )
{
	$('#'+modalTag).modal({
		backdrop: 'static',
		keyboard: true,
		show: true
	});
};

// Set title
Flask.Modal.setTitle = function( modalTag, title )
{
	if ($("#"+modalTag+" .modal-header").length==0) {
		var modalHeader='<div class="modal-header">';
		modalHeader+='<h5 class="modal-title"></h5>';
		modalHeader+='<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		modalHeader+='</div>';
		$("#"+modalTag+" .modal-content").prepend(modalHeader);
	}
	$("#"+modalTag+" .modal-content .modal-header .modal-title").html(title);
};

// Set content
Flask.Modal.setContent = function( modalTag, content )
{
	$("#"+modalTag+" .modal-content .modal-body").html(content);
};

// Set content
Flask.Modal.setButtons = function( modalTag, buttons )
{
	if ($("#"+modalTag+" .modal-footer").length==0) {
		var modalFooter='<div class="modal-footer"></div>';
		$("#"+modalTag+" .modal-content").append(modalFooter);
	}

	for (var k in buttons) {
		var btnHTML='<button id="'+modalTag+'_'+k+'" type="button" class="btn btn-'+oneof(buttons[k].class,'secondary')+'">'+buttons[k].title+'</button>';
		$("#"+modalTag+" .modal-content .modal-footer").append(btnHTML);
		if (buttons[k].onclick!=null) {
			$("#"+modalTag+"_"+k).on('click',buttons[k].onclick);
		}
	}
};

// Hide modal
Flask.Modal.hideModal = function( modalTag )
{
	$("#"+modalTag).modal('hide');
};

// De-draw modal
Flask.Modal.removeModal = function( modalTag )
{
	$("#"+modalTag).remove();
};



/*
 *   Form
 *   ----
 */

// Init UI elements
Flask.Form.initUIElements = function( formID )
{
	// Init tooltips
  $('#'+formID+' [data-toggle="tooltip"]').tooltip();
};

// Clear errors
Flask.Form.clearErrors = function( formID )
{
	$("#"+formID+" div.alert.alert-danger").remove();
	$("#"+formID+" :input").removeClass('is-invalid');
	$("#"+formID+" div.invalid-feedback").remove();
};

// Show errors
Flask.Form.showErrors = function( formID, error )
{
	var html='<div class="alert alert-danger" role="alert">';
	for (var e in error) {
		html+='<div>'+error[e]+'</div>';
	}
	html+='</div>';
	$("#"+formID).prepend(html);
};

// Show field error
Flask.Form.showFieldError = function( field, error )
{
	$("#field_"+field+" :input").addClass('is-invalid');
	$("#"+field).after('<div class="invalid-feedback">'+error+'</div>');
	if ($(":input.is-invalid").length==1) {
		$("#"+field).focus();
	}
};


/*
 *   Progress dialog
 */

// Show
Flask.ProgressDialog.show = function( message )
{
	var modalHTML='<div id="progressmodal" class="modal" tabindex="-1">';
	modalHTML+='<div class="modal-dialog">';
	modalHTML+='<div class="modal-content">';
	modalHTML+='<div class="modal-body"><h4 class="modal-body-progress mx-4 my-4 text-center"><span class="spinner"></span> '+message+'</h4></div>';
	modalHTML+='</div>';
	modalHTML+='</div>';
	modalHTML+='</div>';
	$('body').append(modalHTML);
	$('#progressmodal').modal({
		backdrop: 'static',
		keyboard: true,
		show: true
	});
};

// Hide
Flask.ProgressDialog.hide = function()
{
	if ($("#progressmodal").length) {
		$("#progressmodal").modal('hide');
		$("#progressmodal").remove();
	}
};


/*
 *   Login handler
 *   -------------
 */

// Clear errors
Flask.Login.clearErrors = function()
{
	$("#login_message").hide();
	$("#login_form :input").removeClass('is-invalid');
	$("#login_form input").removeClass('form-control-danger');
	$("#login_form div.invalid-feedback").remove();
};

// Show errors
Flask.Login.showErrors = function( error )
{
	if (typeof(error)=='object')
	{
		var generalError='';
		for (var fld in error)
		{
			if ($("#field_"+fld).length)
			{
				Flask.Login.showFieldError(fld,error[fld]);
			}
			else
			{
				generalError=generalError+'<div>'+error[fld]+'</div>';
			}
		}
	}
	else
	{
		$("#login_message").text(error).show();
	}
};

// Show field error
Flask.Login.showFieldError = function( field, error )
{
	$("#field_"+field+" :input").addClass('is-invalid');
	$("#"+field).after('<div class="invalid-feedback">'+error+'</div>');
	$("#"+field)[0].focus();
};

// Progress start trigger
Flask.Login.progressStart = function()
{
	$("#login_email").attr('readonly','readonly');
	$("#login_password").attr('readonly','readonly');
	$("#login_submit").attr('disabled','disabled');
	if ($("#login_submit").attr('data-title-progress')!=null && $("#login_submit").attr('data-title-progress')!='')
	{
		$("#login_submit").html($("#login_submit").attr('data-title-progress'));
	}
};

// Progress stop trigger
Flask.Login.progressStop = function()
{
	$("#login_email").removeAttr('readonly');
	$("#login_password").removeAttr('readonly');
	$("#login_submit").removeAttr('disabled');
	if ($("#login_submit").attr('data-title') != null && $("#login_submit").attr('data-title') != '')
	{
		$("#login_submit").html($("#login_submit").attr('data-title'));
	}
};

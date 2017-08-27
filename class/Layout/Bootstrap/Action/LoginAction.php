<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The form action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class LoginAction extends FlaskPHP\Action\LoginAction
	{


		/**
		 *
		 *   Set label width
		 *   ---------------
		 *   @access public
		 *   @param int $labelWidth Label width
		 *   @return LoginAction
		 *
		 */

		public function setLabelWidth( int $labelWidth )
		{
			$this->setParam('labelwidth',$labelWidth);
			return $this;
		}


		/**
		 *
		 *   Display login form
		 *   ------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayLoginForm()
		{
			// Init
			$labelWidth=oneof($this->getParam('labelwidth'),3);
			$fieldWidth=intval(12-$labelWidth);

			// Render
			$c='<div class="login-wrapper">';
			if (!empty($this->getParam('banner'))) $c.='<div class="login-banner">'.$this->getParam('banner').'</div>';
			$c.='<div class="login-form-wrapper">';
			$c.='<form role="form" id="login_form" class="login-form form-horizontal" method="post" action="'.Flask()->Request->getRequestURI().'" onsubmit="return false">';
			$c.='<fieldset>';

				// E-mail
				$c.='
					<div id="field_login_email" class="form-group row">
						<label for="login_email" class="col-md-'.$labelWidth.' col-form-label">'.oneof($this->getParam('label_email'),'[[ FLASK.COMMON.Email ]]').':</label>
						<div class="col-md-'.$fieldWidth.'">
							<input type="text" class="form-control" id="login_email" name="login_email" onkeydown="Flask.Login.emailKeypress(event)">
						</div>
					</div>
				';

				// Password
				$c.='
					<div id="field_login_password" class="form-group row">
						<label for="login_password" class="col-md-'.$labelWidth.' col-form-label">'.oneof($this->getParam('label_password'),'[[ FLASK.COMMON.Password ]]').':</label>
						<div class="col-md-'.$fieldWidth.'">
							<input type="password" class="form-control" id="login_password" name="login_password" onkeydown="Flask.Login.passwordKeypress(event)">
						</div>
					</div>
				';

				// Language selection
				if ($this->getParam('langsel'))
				{
					$languageList=$this->getLanguageList();
					$c.='
						<div id="field_login_lang" class="form-group row">
							<label for="login_lang" class="col-md-'.$labelWidth.' col-form-label">'.oneof($this->getParam('label_password'),'[[ FLASK.COMMON.Language ]]').':</label>
							<div class="col-md-'.$fieldWidth.'">
								<select class="form-control" id="login_lang" name="login_lang">'.FlaskPHP\Util::arrayToSelectOptions($languageList,Flask()->Request->requestLang).'</select>
							</div>
						</div>
					';
				}

				// Login message
				$c.='
					<div class="form-group row">
						<div id="login_message" class="login-message col-md-'.$fieldWidth.' offset-md-'.$labelWidth.' text-danger" style="display: none"></div>
					</div>
				';

				// Login submit
				$c.='
					<div class="form-group row">
						<div class="col-md-'.$fieldWidth.' offset-md-'.$labelWidth.'">
							<button type="button" id="login_submit" class="btn btn-primary" data-title="'.oneof($this->getParam('label_submit'),'[[ FLASK.USER.LOGIN.Login ]]').'" data-title-progress="<span class=spinner></span>" onclick="Flask.Login.doLogin()">'.oneof($this->getParam('label_submit'),'[[ FLASK.USER.LOGIN.Login ]]').'</button>
						</div>
					</div>
				';

			$c.='</fieldset>';
			$c.='</form>';
			$c.='</div>';
			$c.='</div>';

			$c.='<script language="JavaScript"> $(function(){ $("#login_email").focus(); }); </script>';
			return $c;
		}


	}


?>
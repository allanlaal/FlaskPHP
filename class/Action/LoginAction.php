<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The login action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class LoginAction extends ActionInterface
	{


		/**
		 *
		 *   Enable language selection
		 *   -------------------------
		 *   @access public
		 *   @param bool $languageSelect Language select
		 *   @return void
		 *
		 */

		public function enableLanguageSelect( bool $languageSelect )
		{
			$this->setParam('langsel',$languageSelect);
		}


		/**
		 *
		 *   Set language selection source list
		 *   ----------------------------------
		 *   @access public
		 *   @param array $languageSourceList Language source list
		 *   @return LoginAction
		 *
		 */

		public function setLanguageSourceList( array $languageSourceList )
		{
			$this->setParam('langsel_sourcelist',$languageSourceList);
			return $this;
		}


		/**
		 *
		 *   Set reload on success
		 *   ---------------------
		 *   @access public
		 *   @param bool $reload Reload on success
		 *   @return LoginAction
		 *
		 */

		public function setReload( bool $reload )
		{
			$this->setParam('reload',$reload);
			return $this;
		}


		/**
		 *
		 *   Set redirect URL
		 *   ----------------
		 *   @access public
		 *   @param string $redirectURL Redirect URL
		 *   @return LoginAction
		 *
		 */

		public function setRedirectURL( string $redirectURL )
		{
			$this->setParam('url_redirect',$redirectURL);
			return $this;
		}


		/**
		 *
		 *   Set template
		 *   ------------
		 *   @access public
		 *   @param string $template Template
		 *   @return LoginAction
		 *
		 */

		public function setTemplate( string $template )
		{
			$this->setParam('template',$template);
			return $this;
		}


		/**
		 *
		 *   Set login banner
		 *   ----------------
		 *   @access public
		 *   @param string $banner Login banner
		 *   @return LoginAction
		 *
		 */

		public function setBanner( string $banner )
		{
			$this->setParam('banner',$banner);
			return $this;
		}


		/**
		 *
		 *   Set e-mail label
		 *   ----------------
		 *   @access public
		 *   @param string $labelEmail E-mail label
		 *   @return LoginAction
		 *
		 */

		public function setEmailLabel( string $labelEmail )
		{
			$this->setParam('label_email',$labelEmail);
			return $this;
		}


		/**
		 *
		 *   Set password label
		 *   ------------------
		 *   @access public
		 *   @param string $labelPassword Password label
		 *   @return LoginAction
		 *
		 */

		public function setPasswordLabel( string $labelPassword )
		{
			$this->setParam('label_password',$labelPassword);
			return $this;
		}


		/**
		 *
		 *   Set language selection label
		 *   ----------------------------
		 *   @access public
		 *   @param string $labelLangSel Password label
		 *   @return LoginAction
		 *
		 */

		public function setLangSelLabel( string $labelLangSel )
		{
			$this->setParam('label_langsel',$labelLangSel);
			return $this;
		}


		/**
		 *
		 *   Set submit button label
		 *   -----------------------
		 *   @access public
		 *   @param string $labelSubmit Submit button label
		 *   @return LoginAction
		 *
		 */

		public function setSubmitLabel( string $labelSubmit )
		{
			$this->setParam('label_submit',$labelSubmit);
			return $this;
		}


		/**
		 *
		 *   Init login action
		 *   -----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initLogin()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Pre-validate login submit
		 *   -------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function loginPreValidate()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Do login
		 *   --------
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 *
		 */

		public function doLogin()
		{
			Flask()->User->doLogin(Flask()->Request->postVar('login_email'),Flask()->Request->postVar('login_password'));
		}


		/**
		 *
		 *   Login trigger / after-validate
		 *   ------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function loginTrigger()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Get language list
		 *   -----------------
		 *   @access public
		 *   @return array
		 *   @throws \Exception
		 */

		public function getLanguageList()
		{
			if ($this->getParam('langsel_sourcelist'))
			{
				$languageList=$this->getParam('langsel_sourcelist');
			}
			else
			{
				$languageList=array();
				foreach (Flask()->Locale->localeLanguageSet as $lang)
				{
					$languageList[$lang]=mb_strtolower(Flask()->Locale->getName($lang));
				}
			}
			return $languageList;
		}


		/**
		 *
		 *   Select language
		 *   ---------------
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 *
		 */

		public function selectLanguage()
		{
			if (!$this->getParam('langsel')) return;
			if (empty(Flask()->Request->postVar('login_lang'))) return;
			if (!empty($this->getParam('langsel_sourcelist')))
			{
				if (!array_key_exists(Flask()->Request->postVar('login_lang'),$this->getParam('langsel_sourcelist'))) throw new FlaskPHP\Exception\Exception('[[ FLASK.USER.LOGIN.Error.UnknownLanguage ]]');
			}
			else
			{
				if (!Flask()->Locale->localeExists(Flask()->Request->postVar('login_lang'))) throw new FlaskPHP\Exception\Exception('[[ FLASK.USER.LOGIN.Error.UnknownLanguage ]]');
			}
			Flask()->Session->set('LANG',Flask()->Request->postVar('login_lang'));
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
			// This should be implemented in the layout extension
			throw new FlaskPHP\Exception\NotImplementedException('Function displayLoginForm() should be implemented in the layout extension.');
		}


		/**
		 *
		 *   Run action and return response
		 *   ------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return FlaskPHP\Response\ResponseInterface
		 *
		 */

		public function runAction()
		{
			// AJAX?
			if (Flask()->Request->isXHR() || Flask()->Request->getRequestMethod()=='POST')
			{
				try
				{
					// Init
					$this->initLogin();

					// Do login
					if (!empty(Flask()->Request->postVar('login')) || !empty(Flask()->Request->postVar('ajaxsubmit')) || (mb_strlen(Flask()->Request->postVar('login_email')) && mb_strlen(Flask()->Request->postVar('login_password'))))
					{
						// Pre-validate
						$this->loginPreValidate();

						// Do login
						$this->doLogin();

						// Login trigger/validate
						$this->loginTrigger();

						// Select language
						$this->selectLanguage();

						// Response
						$response=new \stdClass();
						$response->status=1;
						if (mb_strlen(Flask()->Session->get('login.redirect')))
						{
							$response->redirect=Flask()->Session->get('login.redirect');
							Flask()->Session->set('login.redirect','');
						}
						elseif (!empty($this->getParam('url_redirect')))
						{
							$response->redirect=$this->getParam('url_redirect');
						}
						elseif (!empty($this->getParam('reload')))
						{
							$response->reload=1;
						}
						else
						{
							$response->redirect='/';
						}
						return new FlaskPHP\Response\JSONResponse($response);
					}

					// Return login form
					else
					{
						$response=new \stdClass();
						$response->status=1;
						$response->content=$this->displayLoginForm();
						return new FlaskPHP\Response\JSONResponse($response);
					}
				}
				catch (FlaskPHP\Exception\ValidateException $e)
				{
					// Log out user if we logged it in
					if (Flask()->User->isLoggedIn())
					{
						Flask()->User->doLogout();
					}

					// Return error
					$response=new \stdClass();
					$response->status=2;
					$response->error=join('<br>',$e->getErrors());
					return new FlaskPHP\Response\JSONResponse($response);
				}
				catch (\Exception $e)
				{
					// Log out user if we logged it in
					if (Flask()->User->isLoggedIn())
					{
						Flask()->User->doLogout();
					}

					// Return error
					$response=new \stdClass();
					$response->status=2;
					$response->error=$e->getMessage();
					return new FlaskPHP\Response\JSONResponse($response);
				}
			}

			// Get
			else
			{
				// Init
				$this->initLogin();

				// Render
				$response=new FlaskPHP\Response\HTMLResponse();
				$response->setTemplate(oneof($this->getParam('template'),'login'));
				$response->setContent($this->displayLoginForm());

				// Return login form
				return $response;
			}
		}


	}


?>
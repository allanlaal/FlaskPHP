<?php


	/**
	 *
	 *   FlaskPHP
	 *   The logout action
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class LogoutAction extends ActionInterface
	{


		/**
		 *   Set reload
		 *   @access public
		 *   @param bool $reload Reload on success
		 *   @return LogoutAction
		 */

		public function setReload( bool $reload )
		{
			$this->setParam('reload',$reload);
			return $this;
		}


		/**
		 *   Set redirect URL
		 *   @access public
		 *   @param string $redirectURL Redirect URL
		 *   @return LogoutAction
		 */

		public function setRedirectURL( string $redirectURL )
		{
			$this->setParam('url_redirect',$redirectURL);
			return $this;
		}


		/**
		 *   Init logout action
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 */

		public function initLogout()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *   Run action and return response
		 *   @access public
		 *   @throws \Exception
		 *   @return FlaskPHP\Response\ResponseInterface
		 */

		public function runAction()
		{
			// AJAX?
			if (Flask()->Request->isXHR())
			{
				try
				{
					// Init
					$this->initLogout();

					// Do logout
					Flask()->User->doLogout();

					// Response
					$response=new \stdClass();
					$response->status=1;
					if (!empty($this->getParam('url_redirect')))
					{
						$response->redirect=$this->getParam('url_redirect');
					}
					else
					{
						if (!empty($this->getParam('reload'))) $response->reload=1;
					}
					return new FlaskPHP\Response\JSONResponse($response);
				}
				catch (\Exception $e)
				{
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
				$this->initLogout();

				// Do logout
				Flask()->User->doLogout();

				// Render
				$response=new FlaskPHP\Response\RedirectResponse();
				$response->setRedirect(oneof($this->getParam('url_redirect'),'/login'));

				// Return login form
				return $response;
			}
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The form action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class ModalFormAction extends FormAction
	{


		/**
		 *
		 *   Set reload
		 *   ----------
		 *   @access public
		 *   @param bool $reload Reload on success
		 *   @return DialogFormAction
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
		 *   @return DialogFormAction
		 *
		 */

		public function setRedirectURL( string $redirectURL )
		{
			$this->setParam('url_redirect',$redirectURL);
			return $this;
		}


		/**
		 *
		 *   Set display trigger
		 *   -------------------
		 *   @access public
		 *   @param string $displayTrigger Display trigger
		 *   @param bool $add Add to existing?
		 *   @return DialogFormAction
		 *
		 */

		public function setDisplayTrigger( string $displayTrigger, bool $add=true )
		{
			if ($add && $this->getParam('displaytrigger'))
			{
				$displayTrigger=$this->getParam('displaytrigger').$displayTrigger;
			}
			$this->setParam('displaytrigger',$displayTrigger);
			return $this;
		}


		/**
		 *
		 *   Set submit success action
		 *   -------------------------
		 *   @access public
		 *   @param string $submitSuccessAction Submit success action
		 *   @param bool $add Add to existing?
		 *   @return DialogFormAction
		 *
		 */

		public function setSubmitSuccessAction( string $submitSuccessAction, bool $add=true )
		{
			if ($add && $this->getParam('submitsuccessaction'))
			{
				$submitSuccessAction=$this->getParam('submitsuccessaction').$submitSuccessAction;
			}
			$this->setParam('submitsuccessaction',$submitSuccessAction);
			return $this;
		}


		/**
		 *
		 *   Display form
		 *   ------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayForm()
		{
			// Init
			$this->template=new FlaskPHP\Template\Template('form.modalform');
			$this->template->set('form','');

			// Render parts
			$c=$this->displayTitle();
			$c.=$this->displayFormBegin();
			$c.=$this->displayFormContents();
			$c.=$this->displayFormSubmit();
			$c.=$this->displayFormEnd();
			$c.=$this->displayNotes();
			$c.=$this->displayJS();
			$this->template->set('content',$c);

			// Compose response
			$response=new \stdClass();
			$response->status=1;
			$response->title=$this->template->get('title');
			$response->content=$this->template->render();
			return $response;
		}


		/**
		 *
		 *   Display form submit
		 *   -------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayFormSubmit()
		{
			// This is not needed in dialog form
		}


		/**
		 *
		 *   Display form submit
		 *   -------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function getSubmitButtons()
		{
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
			try
			{
				// Set defaults
				$this->setDefaults();

				// Init form
				$this->initForm();

				// Check
				if (!is_object($this->model)) throw new FlaskPHP\Exception\InvalidParameterException('No model defined.');

				// Determine operation
				$this->operation=oneof(
					$this->getParam('operation'),
					(Flask()->Request->postVar($this->model->getParam('idfield'))!==null?'edit':'add')
				);

				// Load data
				if ($this->operation=='edit') $this->dataLoad();

				// Are we submitting?
				$this->doSubmit=$this->checkSubmit();

				// Init fields
				$this->initFields();

				// Handle submit
				if ($this->doSubmit)
				{
					try
					{
						// Init
						$errors=array();

						// Pre-validate
						try
						{
							$this->dataPreValidate();
						}
						catch (FlaskPHP\Exception\ValidateException $validateException)
						{
							$response=new \stdClass();
							$response->status=2;
							$response->error=$validateException->getErrors();
							return new FlaskPHP\Response\JSONResponse($response);
						}

						// Validate fields
						try
						{
							$this->validateFields();
						}
						catch (FlaskPHP\Exception\ValidateException $validateException)
						{
							$errors=array_merge($errors,$validateException->getErrors());
						}

						// Do global error checking
						try
						{
							$this->dataValidate();
						}
						catch (FlaskPHP\Exception\ValidateException $validateException)
						{
							$errors=array_merge($errors,$validateException->getErrors());
						}

						// Errors?
						if (sizeof($errors))
						{
							$response=new \stdClass();
							$response->status=2;
							$response->error=$errors;
							return new FlaskPHP\Response\JSONResponse($response);
						}

						// Save data
						$this->dataSave();

						// Return response
						$response=new \stdClass();
						$response->status=1;
						if ($this->getParam('url_redirect'))
						{
							$response->redirect=$this->getParam('url_redirect');
						}
						elseif ($this->getParam('reload'))
						{
							$response->reload=1;
						}
						if ($this->getParam('submitsuccessaction'))
						{
							$response->submitsuccessaction=$this->getParam('submitsuccessaction');
						}
						return new FlaskPHP\Response\JSONResponse($response);
					}
					catch (\Exception $e)
					{
						$response=new \stdClass();
						$response->status=2;
						$response->error=$e->getMessage();
						return new FlaskPHP\Response\JSONResponse($response);
					}
				}

				// Display form
				$response=$this->displayForm();
				if (empty($response->status)) $response->status=1;
				return new FlaskPHP\Response\JSONResponse($response);
			}
			catch (\Exception $e)
			{
				$response=new \stdClass();
				$response->status=2;
				$response->error=$e->getMessage();
				return new FlaskPHP\Response\JSONResponse($response);
			}
		}


	}


?>
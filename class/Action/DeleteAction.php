<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The delete item action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class DeleteAction extends ActionInterface
	{


		/**
		 *   Include standard Ajax action parameters
		 */

		use FlaskPHP\Traits\AjaxActionParameters;


		/**
		 *
		 *   Init delete action
		 *   ------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initDelete()
		{
			// This can be implemented in the subclass. By default, we just reload on success
			$this->setReload(true);
		}


		/**
		 *
		 *   Set log message
		 *   ---------------
		 *   @access public
		 *   @param string|bool $logMessage (Message or false for no log entry)
		 *   @return DeleteAction
		 *
		 */

		public function setLogMessage( $logMessage )
		{
			$this->setParam('log_message',$logMessage);
			return $this;
		}


		/**
		 *
		 *   Set log ref OID
		 *   ---------------
		 *   @access public
		 *   @param string|int $logRefOID (Log ref OID (string / field name or int / value))
		 *   @return DeleteAction
		 *
		 */

		public function setLogRefOID( $logRefOID )
		{
			$this->setParam('log_refoid',$logRefOID);
			return $this;
		}


		/**
		 *
		 *   Set skip validation
		 *   -------------------
		 *   @access public
		 *   @param bool $skipValidation Skip validation?
		 *   @return DeleteAction
		 *
		 */

		public function setSkipValiation( bool $skipValidation )
		{
			$this->setParam('skipvalidation',$skipValidation);
			return $this;
		}


		/**
		 *
		 *   Trigger: pre-delete
		 *   -------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function triggerPreDelete()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Trigger: post-delete
		 *   --------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function triggerPostDelete()
		{
			// This can be implemented in the subclass.
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

				// Init delete
				$this->initDelete();

				// Check
				if (!is_object($this->model)) throw new FlaskPHP\Exception\InvalidParameterException('No model defined.');
				if (!Flask()->Request->postVar($this->model->getParam('idfield'))) throw new FlaskPHP\Exception\InvalidParameterException('[[ FLASK.COMMON.Error.InvalidRequest ]]');
				if (!intval(Flask()->Request->postVar($this->model->getParam('idfield')))) throw new FlaskPHP\Exception\InvalidParameterException('[[ FLASK.COMMON.Error.InvalidRequest ]]');

				// Load
				$Object=$this->model=$this->model::getObject(intval(Flask()->Request->postVar($this->model->getParam('idfield'))));

				// Pre-delete trigger
				$this->triggerPreDelete();

				// Delete
				$Object->delete(
					$this->getParam('log_message'),
					null,
					$this->getParam('log_refoid'),
					$this->getParam('log_op'),
					($this->getParam('skipvalidation')?true:false)
				);

				// Post-delete trigger
				$this->triggerPostDelete();

				// Response
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
				if ($this->getParam('successaction'))
				{
					$response->successaction=$this->getParam('successaction');
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


	}


?>
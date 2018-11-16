<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The item swap order action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class SwapAction extends ActionInterface
	{


		/**
		 *   Include standard Ajax action parameters
		 */

		use FlaskPHP\Traits\AjaxActionParameters;


		/**
		 *   The object swapped with
		 *   @var FlaskPHP\Model\ModelInterface
		 */

		var $swapWith = null;


		/**
		 *
		 *   Init swap action
		 *   ----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initSwap()
		{
			// This can be implemented in the subclass. By default, we just reload on success, and don't log this operation
			$this->setReload(true);
			$this->setLogMessage(false);
		}


		/**
		 *
		 *   Set log message
		 *   ---------------
		 *   @access public
		 *   @param string|bool $logMessage (Message or false for no log entry)
		 *   @return SwapAction
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
		 *   @return SwapAction
		 *
		 */

		public function setLogRefOID( $logRefOID )
		{
			$this->setParam('log_refoid',$logRefOID);
			return $this;
		}


		/**
		 *
		 *   Trigger: pre-swap
		 *   -----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function triggerPreSwap()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Trigger: post-swap
		 *   ------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function triggerPostSwap()
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
				$this->initSwap();

				// Check
				if (!is_object($this->model)) throw new FlaskPHP\Exception\InvalidParameterException('No model defined.');
				if (!Flask()->Request->postVar($this->model->getParam('idfield'))) throw new FlaskPHP\Exception\InvalidParameterException('[[ FLASK.COMMON.Error.InvalidRequest ]]');
				if (!intval(Flask()->Request->postVar($this->model->getParam('idfield')))) throw new FlaskPHP\Exception\InvalidParameterException('[[ FLASK.COMMON.Error.InvalidRequest ]]');
				if (!Flask()->Request->postVar('with')) throw new FlaskPHP\Exception\InvalidParameterException('[[ FLASK.COMMON.Error.InvalidRequest ]]');
				if (!intval(Flask()->Request->postVar('with'))) throw new FlaskPHP\Exception\InvalidParameterException('[[ FLASK.COMMON.Error.InvalidRequest ]]');

				// Load
				$Object=$this->model=$this->model::getObject(intval(Flask()->Request->postVar($this->model->getParam('idfield'))));
				$SwapWith=$this->swapWith=$this->model::getObject(intval(Flask()->Request->postVar('with')));

				// Some more checks
				if (get_class($Object)!=get_class($SwapWith)) throw new FlaskPHP\Exception\InvalidParameterException('[[ FLASK.COMMON.Error.InvalidRequest ]]');
				if (!$Object->getParam('setord')) throw new FlaskPHP\Exception\InvalidParameterException('[[ FLASK.COMMON.Error.InvalidRequest ]]');

				// Pre-swap trigger
				$this->triggerPreSwap();

				// Swap
				try
				{
					// Start TX
					Flask()->DB->startTransaction();

					// Get vars
					$ord1=$Object->ord;
					$ord2=$SwapWith->ord;

					// Swap #1
					$Object->ord=$ord2;
					$Object->save(
						Flask()->DB->getQueryBuilder()->addField('ord'),
						$this->getParam('log_message'),
						null,
						$this->getParam('log_refoid'),
						$this->getParam('log_op'),
						true
					);

					// Swap #2
					$SwapWith->ord=$ord1;
					$SwapWith->save(
						Flask()->DB->getQueryBuilder()->addField('ord'),
						$this->getParam('log_message'),
						null,
						$this->getParam('log_refoid'),
						$this->getParam('log_op'),
						true
					);

					// Commit
					Flask()->DB->doCommit();
				}
				catch (\Exception $e)
				{
					// Rollback and rethrow
					Flask()->DB->doRollback();
					throw $e;
				}

				// Post-swap trigger
				$this->triggerPostSwap();

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
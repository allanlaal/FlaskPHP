<?php


	/**
	 *
	 *   FlaskPHP
	 *   The action interface class
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class ActionInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Model
		 *   @var FlaskPHP\Model\ModelInterface
		 *   @access public
		 */

		public $model = null;


		/**
		 *   Backreference to controller instance
		 *   @access public
		 *   @var FlaskPHP\Controller\ControllerInterface
		 */

		public $controllerObject = null;


		/**
		 *   Inherit parameters from the controller
		 *   @access public
		 *   @param array $param Parameters
		 *   @param FlaskPHP\Model\ModelInterface $model Model
		 *   @return void
		 *   @throws \Exception
		 */

		public function inheritParameters( array $param=null, FlaskPHP\Model\ModelInterface $model=null )
		{
			// Inherit parameters
			$this->setParameters($param,false);

			// Inherit model
			if ($model!==null)
			{
				$this->model=$model;
			}
		}


		/**
		 *   Set unset parameters to default values
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 */

		public function setDefaults()
		{
			// Template
			if ($this->getParam('template')===null) $this->setParam('template','default');

			// URL
			if ($this->getParam('baseurl')===null) $this->setParam('baseurl',Flask()->Request->requestURI);
		}


		/**
		 *   Set base URL
		 *   @access public
		 *   @param string $baseURL Base URL
		 *   @return void
		 *   @throws \Exception
		 */

		public function setBaseURL( string $baseURL )
		{
			$this->setParam('baseurl',$baseURL);
		}


		/**
		 *  Set template
		 *  @access public
		 *  @param string $template Template tag
		 *  @return void
		 */

		public function setTemplate( string $template )
		{
			$this->setParam('template',$template);
		}


		/**
		 *   Set log message
		 *   @access public
		 *   @param string|bool $logMessage Log message
		 *   @return void
		 */

		public function setLogMessage( $logMessage )
		{
			$this->setParam('logmessage',$logMessage);
		}


		/**
		 *  Set log ref OID
		 *  @access public
		 *  @param integer|string $logRefOID Log ref OID (value or field name)
		 *  @return void
		 */

		public function setLogRefOID ( $logRefOID )
		{
			$this->setParam('logrefoid',$logRefOID);
		}


		/**
		 *   Run action and return response
		 *   @access public
		 *   @throws \Exception
		 *   @return FlaskPHP\Response\ResponseInterface
		 */

		public function runAction()
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function runAction() not implemented in the action class.');
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   The controller action class
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Controller;
	use Codelab\FlaskPHP as FlaskPHP;


	class ControllerAction
	{


		/**
		 *   Action request method(s)
		 *   @access public
		 *   @var array
		 */

		public $actionRequestMethod = null;


		/**
		 *   File
		 *   @access public
		 *   @var string
		 */

		public $actionFile = null;


		/**
		 *   Action handler
		 *   @access public
		 *   @var string
		 */

		public $actionHandler = null;


		/**
		 *   Action class
		 *   @access public
		 *   @var string
		 */

		public $actionClass = null;


		/**
		 *   Action login required
		 *   @access public
		 *   @var bool
		 */

		public $actionLoginRequired = null;


		/**
		 *   Action login URL
		 *   @access public
		 *   @var string
		 */

		public $actionLoginURL = null;


		/**
		 *   Action roles required
		 *   @access public
		 *   @var array
		 */

		public $actionRoleRequired = null;


		/**
		 *   Set action include file
		 *   @access public
		 *   @var string $fileName File name
		 *   @throws \Exception
		 *   @return ControllerAction
		 */

		public function setFile( string $fileName )
		{
			$this->actionFile=$fileName;
			return $this;
		}


		/**
		 *   Set action handler class
		 *   @access public
		 *   @var string $fileName
		 *   @throws \Exception
		 *   @return ControllerAction
		 */

		public function setClass( string $className )
		{
			$this->actionClass=$className;
			return $this;
		}


		/**
		 *   Set action handler object
		 *   @access public
		 *   @var string $handlerVariable Handler object variable name
		 *   @throws \Exception
		 *   @return ControllerAction
		 */

		public function setHandler( string $handlerVariable )
		{
			$this->actionHandler=$handlerVariable;
			return $this;
		}


		/**
		 *   Set login required
		 *   @access public
		 *   @var bool $loginRequired Login is required
		 *   @throws \Exception
		 *   @return ControllerAction
		 */

		public function setLoginRequired( bool $loginRequired )
		{
			$this->actionLoginRequired=$loginRequired;
			return $this;
		}


		/**
		 *   Set login URL
		 *   @access public
		 *   @var string $loginURL Login URL
		 *   @throws \Exception
		 *   @return ControllerAction
		 */

		public function setLoginURL( string $loginURL )
		{
			$this->actionLoginURL=$loginURL;
			return $this;
		}


		/**
		 *   Set login required
		 *   @access public
		 *   @var string|array $roleRequired
		 *   @throws \Exception
		 *   @return ControllerAction
		 */

		public function setRoleRequired( $roleRequired )
		{
			// Validate
			if (!is_string($roleRequired) && !is_array($roleRequired)) throw new FlaskPHP\Exception\Exception('setRoleRequired(): $roleRequired must be a string or an array.');

			// Init
			$roleList=str_array($roleRequired);
			if (!sizeof($roleList)) return $this;
			if (empty($this->actionRoleRequired)) $this->actionRoleRequired=array();

			// Add roles
			foreach ($roleList as $role)
			{
				if (!in_array($role,$this->actionRoleRequired))
				{
					$this->actionRoleRequired[]=$role;
				}
			}

			// Return self
			return $this;
		}


	}


?>
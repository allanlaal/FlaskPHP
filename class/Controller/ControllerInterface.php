<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The controller interface
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Controller;
	use Codelab\FlaskPHP as FlaskPHP;


	class ControllerInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Controller URL tag
		 *   @var string
		 *   @access public
		 */

		public $tag = null;


		/**
		 *   Model
		 *   @var FlaskPHP\Model\ModelInterface
		 *   @access public
		 */

		public $model = null;


		/**
		 *   Actions
		 *   @var array
		 *   @access public
		 */

		public $actionData = array();


		/**
		 *   Login required?
		 *   @var bool
		 *   @access public
		 */

		public $controllerLoginRequired = true;


		/**
		 *   Login URL
		 *   @var string
		 *   @access public
		 */

		public $controllerLoginURL = '/login';


		/**
		 *   Authorized roles
		 *   @var array
		 *   @access public
		 */

		public $controllerRoleRequired = null;


		/**
		 *   Is login required?
		 *   @access public
		 *   @return bool
		 *   @throws \Exception
		 */

		public function isLoginRequired()
		{
			return $this->controllerLoginRequired;
		}


		/**
		 *   Set login requirement
		 *   @access public
		 *   @var bool $loginRequired Login is required?
		 *   @return void
		 *   @throws \Exception
		 */

		public function setLoginRequired( bool $loginRequired )
		{
			$this->controllerLoginRequired=$loginRequired;
		}


		/**
		 *   Get list of roles required
		 *   @access public
		 *   @return array
		 *   @throws \Exception
		 */

		public function getRoleRequired()
		{
			return null;
		}


		/**
		 *   Init controller
		 *   @access public
		 *   @var string|array $roleRequired Role required (string or array)
		 *   @return void
		 *   @throws \Exception
		 */

		public function setRoleRequired( $roleRequired )
		{
			// Validate
			if (!is_string($roleRequired) && !is_array($roleRequired)) throw new FlaskPHP\Exception\Exception('setRoleRequired(): $roleRequired must be a string or an array.');

			// Init
			$roleList=str_array($roleRequired);
			if (!sizeof($roleList)) return;
			if (empty($this->controllerRoleRequired)) $this->controllerRoleRequired=array();

			// Add roles
			foreach ($roleList as $role)
			{
				if (!in_array($role,$this->controllerRoleRequired))
				{
					$this->controllerRoleRequired[]=$role;
				}
			}
		}


		/**
		 *   Get login URL
		 *   @access public
		 *   @return string
		 *   @throws \Exception
		 */

		public function getLoginURL()
		{
			return $this->controllerLoginURL;
		}


		/**
		 *   Init controller
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 */

		public function initController()
		{
			// This function should be implemented on the controller level.
		}


		/**
		 *   Init actions
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 */

		public function initActions()
		{
			// This function should be implemented on the controller level.
		}


		/**
		 *   Run controller
		 *   @access public
		 *   @param string $forceAction Force action
		 *   @throws \Exception
		 *   @return mixed
		 */

		public function runController( string $forceAction=null )
		{
			// Init actions
			$this->initActions();

			// See if there are any actions
			if (!sizeof($this->actionData)) throw new FlaskPHP\Exception\ControllerException('No actions defined.');

			// Get action
			$actionTag=$urlActionTag=oneof($forceAction,Flask()->Request->requestUriVarByPos[1]['name']);

			// No action? Will use default
			if (empty($actionTag))
			{
				reset($this->actionData);
				$actionUse=key($this->actionData);
			}

			// See if the action exists
			else
			{
				$actionFound=FALSE;
				foreach ($this->actionData as $aTag => $aObject)
				{
					if ($actionTag==$aTag)
					{
						$actionUse=$aTag;
						$actionFound=true;
						break;
					}
				}

				// Still not found? Let's revert to default
				if (!$actionFound)
				{
					reset($this->actionData);
					$urlActionTag='';
					$actionUse=key($this->actionData);
				}
			}

			// Get action object
			$actionObject=$this->actionData[$actionUse];

			// Check action-specific login requirement
			if ($actionObject->actionLoginRequired!==null)
			{
				if ($actionObject->actionLoginRequired===true)
				{
					if (!Flask()->User->isLoggedIn())
					{
						Flask()->Session->set('login.redirect',$_SERVER['REQUEST_URI']);
						return new FlaskPHP\Response\RedirectResponse(oneof($actionObject->actionLoginURL,$this->controllerLoginURL,'/login'));
					}
					if ($actionObject->actionRoleRequired!==null)
					{
						if (!Flask()->User->checkRole($actionObject->actionRoleRequired))
						{
							Flask()->Response->setStatus(401);
							return 'Access denied.';
						}
					}
				}
			}

			// Check global login requirement
			else
			{
				if ($this->controllerLoginRequired===true)
				{
					if (!Flask()->User->isLoggedIn())
					{
						Flask()->Session->set('login.redirect',$_SERVER['REQUEST_URI']);
						return new FlaskPHP\Response\RedirectResponse(oneof($this->controllerLoginURL,'/login'));
					}
					if ($this->controllerRoleRequired!==null)
					{
						if (!Flask()->User->checkRole($this->controllerRoleRequired))
						{
							throw new FlaskPHP\Exception\Exception('Access denied',401);
						}
					}
				}
			}

			// Get declared classes before including controller
			$declaredClassesBefore=get_declared_classes();

			// Include
			if (!empty($actionObject->actionFile))
			{
				$actionInclude=str_array($actionObject->actionFile);
				foreach ($actionInclude as $inc)
				{
					$resolvedInc=Flask()->resolvePath($inc);
					if (empty($resolvedInc)) throw new FlaskPHP\Exception\ControllerException('Action include not found: '.$inc);
					include $resolvedInc;
				}
			}

			// Get declared classes after including controller
			$declaredClassesAfter=get_declared_classes();

			// Action handler
			if (!empty($actionObject->actionHandler))
			{
				$actionHandler=&${$actionObject->actionHandler};
				if (!is_object($actionHandler)) throw new FlaskPHP\Exception\ControllerException('Action handler not found: '.$actionObject->actionHandler);
			}

			// Action class
			elseif (!empty($actionObject->actionClass))
			{
				$cName=$actionObject->actionClass;
				$actionHandler=new $cName();
			}

			// See if we have an action instance or class
			else
			{
				$foundDisplayObjects=0;
				foreach (array_keys(get_defined_vars()) as $varName)
				{
					if ($$varName instanceof FlaskPHP\Action\ActionInterface)
					{
						$foundDisplayObjects++;
						$actionHandler=new $varName();
					}
				}
				if ($foundDisplayObjects>1)
				{
					throw new FlaskPHP\Exception\ControllerException('Found more than one ActionInterface class. Please specify the class or handler in the controller.');
				}
				elseif ($foundDisplayObjects<1)
				{
					// Get diff, eliminate non-ControllerInterface classes
					$classList=array_diff($declaredClassesAfter,$declaredClassesBefore);
					foreach ($classList as $c => $className)
					{
						if (!is_subclass_of($className,'Codelab\FlaskPHP\Action\ActionInterface'))
						{
							unset($classList[$c]);
						}
					}

					// Nothing?
					if (!sizeof($classList))
					{
						throw new FlaskPHP\Exception\ControllerException('No action instance found in action file'.(sizeof($actionInclude)>1?'s':'').'.',500);
					}

					// Create instance and return
					foreach ($classList as $className)
					{
						$actionHandler=new $className();
					}
				}
			}

			// Set backreference
			$actionHandler->controllerObject=$this;

			// Set baseURL
			// $actionObject->setBaseURL('/'.Flask()->Request->requestController.(!empty($urlActionTag)?'/'.$urlActionTag:''));

			// Pass the inherited parameters
			$actionHandler->inheritParameters($this->_param, $this->model);

			// Run the action and return contents
			return $actionHandler->runAction();
		}


		/**
		 *   Add an action
		 *   @access public
		 *   @param string $actionTag Action tag
		 *   @param \Codelab\FlaskPHP\Controller\ControllerAction $actionObject Action object
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Controller\ControllerAction
		 */

		public function addAction( string $actionTag, FlaskPHP\Controller\ControllerAction $actionObject=null )
		{
			// Check
			if (array_key_exists($actionTag,$this->actionData)) throw new FlaskPHP\Exception\Exception('Action '.$actionTag.' already exists.');

			// Action passed
			if ($actionObject instanceof \Codelab\FlaskPHP\Controller\ControllerAction)
			{
				$this->actionData[$actionTag]=$actionObject;
			}

			// Create blank action
			else
			{
				$this->actionData[$actionTag]=new \Codelab\FlaskPHP\Controller\ControllerAction();
			}

			// Create backreference
			$this->actionData[$actionTag]->controllerObject=$this;

			// Return reference to action
			return $this->actionData[$actionTag];
		}


	}


?>
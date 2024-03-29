<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The parameter traits
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Traits;
	use Codelab\FlaskPHP as FlaskPHP;


	trait Parameters
	{


		/**
		 *   Parameters
		 *   @var array
		 *   @access public
		 */

		protected $_param = array();


		/**
		 *   Get parameter
		 *   @param string $paramName Parameter name
		 *   @param bool $throwExceptionOnNonExisting Throw exception on non-existing parameter
		 *   @param mixed $defaultValue Default value when parameter does not exist and no exception thrown
		 *   @throws \Exception
		 *   @return mixed
		 */

		public function getParam( string $paramName, bool $throwExceptionOnNonExisting=false, $defaultValue=null )
		{
			// Check if parameter exists
			if (!array_key_exists($paramName,$this->_param))
			{
				if ($throwExceptionOnNonExisting) throw new FlaskPHP\Exception\NotFoundException('Parameter '.$paramName.' not defined.');
				return $defaultValue;
			}

			// Return
			return $this->_param[$paramName];
		}


		/**
		 *   Check if a parameter is set
		 *   @param string $paramName Parameter name
		 *   @throws \Exception
		 *   @return bool
		 */

		public function hasParam( string $paramName )
		{
			// Return
			return array_key_exists($paramName,$this->_param);
		}


		/**
		 *   Set parameter
		 *   @param string $paramName Parameter name
		 *   @param mixed $paramValue Parameter value
		 *   @throws \Exception
		 *   @return object
		 */

		public function setParam( string $paramName, $paramValue )
		{
			// Unset if setting to null
			if ($paramValue===null)
			{
				unset($this->_param[$paramName]);
				return $this;
			}

			// Set
			$this->_param[$paramName]=$paramValue;
			return $this;
		}


		/**
		 *   Unset parameter
		 *   @param string $paramName Parameter name
		 *   @throws \Exception
		 *   @return object
		 */

		public function unsetParam( string $paramName )
		{
			// Unset
			unset($this->_param[$paramName]);
			return $this;
		}


		/**
		 *   Set parameters
		 *   @param array $param Array of parameters
		 *   @param bool $overwrite Overwrite if parameter already set
		 *   @throws \Exception
		 *   @return object
		 */

		public function setParameters( array $param=null, bool $overwrite=true )
		{
			// Check
			if ($param===null) return;
			if (!is_array($param)) throw new FlaskPHP\Exception\Exception('$param must be an array.');

			// Set
			foreach ($param as $k => $v)
			{
				if (!array_key_exists($k,$this->_param) || $overwrite)
				{
					$this->_param[$k]=$v;
				}
			}

			// Return self for chaining
			return $this;
		}


	}


?>
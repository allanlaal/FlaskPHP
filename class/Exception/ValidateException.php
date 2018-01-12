<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The FlaskPHP validate exception
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Exception;


	class ValidateException extends Exception
	{


		/**
		 *   Validate errors
		 *   @var array
		 */

		public $validateErrors = array();


		/**
		 *   Constructor
		 *   -----------
		 *   @access public
		 *   @param array $validateErrors
		 *   @param int $code Code
		 *   @param \Throwable $previous Previous exception
		 *   @return ValidateException
		 *
		 */

		public function __construct( array $validateErrors, $code=0, \Throwable $previous = null)
		{
			$this->validateErrors=$validateErrors;
			$this->message=sizeof($validateErrors).' validation error'.(sizeof($validateErrors)>1?'s':'').', retrieve with getErrors().';
		}


		/**
		 *
		 *   Format for display
		 *   ------------------
		 *   @access public
		 *   @return string
		 *
		 */

		public function __toString()
		{
			$retval=array();
			foreach ($this->validateErrors as $k => $v)
			{
				$retval[]=$k.': '.$v;
			}
			return join(' / ',$retval);
		}


		/**
		 *
		 *   Get errors
		 *   ----------
		 *   @access public
		 *   @return array
		 *
		 */

		public function getErrors()
		{
			return $this->validateErrors;
		}


	}


?>
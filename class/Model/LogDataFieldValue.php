<?php


	/**
	 *
	 *   FlaskPHP
	 *   Model change log data field value
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Model;
	use Codelab\FlaskPHP as FlaskPHP;


	class LogDataFieldValue
	{


		/**
		 *   Value
		 *   @var mixed
		 *   @access public
		 */

		public $value = null;


		/**
		 *   Display value
		 *   @var string
		 *   @access public
		 */

		public $displayValue = null;


		/**
		 *   Init log data
		 *   @access public
		 *   @param mixed $value Value
		 *   @param string $displayValue Display value
		 *   @return \Codelab\FlaskPHP\Model\LogDataFieldValue
		 */

		public function __construct( $value=null, string $displayValue=null )
		{
			$this->value=$value;
			$this->displayValue=$displayValue;
		}


	}


?>
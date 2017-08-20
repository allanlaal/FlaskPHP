<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Model change log data
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Model;
	use Codelab\FlaskPHP as FlaskPHP;


	class LogData
	{


		/**
		 *   Operation
		 *   @var string
		 *   @access public
		 */

		public $operation = null;


		/**
		 *   Log data
		 *   @var string
		 *   @access public
		 */

		public $data = array();


		/**
		 *   Fields
		 *   @var string
		 *   @access public
		 */

		public $_field = array();


		/**
		 *
		 *   Init log data class
		 *   -------------------
		 *   @access public
		 *   @param string $operation Log operation
		 *   @param array $fields Changed fields that need to be logged
		 *   @return \Codelab\FlaskPHP\Model\LogData
		 *
		 */

		public function __construct( string $operation=null, array $fields=array() )
		{
			$this->operation=$operation;
			$this->_field=$fields;
		}


		/**
		 *
		 *   Add log data
		 *   ------------
		 *   @access public
		 *   @param string $tag Log entry tag
		 *   @param object $logData Log data
		 *   @return void
		 *
		 */

		public function addData( string $tag, \stdClass $logData )
		{
			$this->data[$tag]=$logData;
		}


		/**
		 *
		 *   Create log data object
		 *   ----------------------
		 *   @access public
		 *   @param array $logData Log data
		 *   @return object
		 *
		 */

		public function createLogObject( array $logData )
		{
			$logObject=new \stdClass();
			foreach ($logData as $k => $v)
			{
				if ($v===null) continue;
				$logObject->{$k}=$v;
			}
			return $logObject;
		}


		/**
		 *
		 *   Mark field as handled
		 *   ---------------------
		 *   @access public
		 *   @param string $field Field tag
		 *   @return void
		 *
		 */

		public function setHandled( string $field )
		{
			unset($this->_field[$field]);
		}


		/**
		 *
		 *   Get log data JSON
		 *   -----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function getLogJSON()
		{
			$jsonObject=new \stdClass();
			$jsonObject->operation=$this->operation;
			$jsonObject->data=$this->data;
			return json_encode($jsonObject,JSON_FORCE_OBJECT|JSON_HEX_QUOT);
		}


	}


?>
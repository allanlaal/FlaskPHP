<?php


	/**
	 *
	 *   FlaskPHP
	 *   Model change log data
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
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
		 *   Field data
		 *   @var array()
		 *   @access public
		 */

		public $field = array();


		/**
		 *   Init log data
		 *   @access public
		 *   @param string $operation Log operation
		 *   @return \Codelab\FlaskPHP\Model\LogData
		 */

		public function __construct( string $operation=null )
		{
			$this->operation=$operation;
		}


		/**
		 *   Add a field and return class instance
		 *   @access public
		 *   @param string $fieldTag Field tag
		 *   @param string $fieldName Field name
		 *   @return \Codelab\FlaskPHP\Model\LogDataField
		 */

		public function addField( string $fieldTag=null, string $fieldName=null )
		{
			$logDataField=new LogDataField($fieldTag,$fieldName);
			$this->field[]=$logDataField;
			return $logDataField;
		}


		/**
		 *   Get serialized log data
		 *   @access public
		 *   @return string
		 */

		public function getSerializedData()
		{
			return serialize($this);
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   Model change log data field
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Model;
	use Codelab\FlaskPHP as FlaskPHP;


	class LogDataField
	{


		/**
		 *   Field tag / ID
		 *   @var string
		 *   @access public
		 */

		public $tag = null;


		/**
		 *   Display name
		 *   @var string
		 *   @access public
		 */

		public $displayName = null;


		/**
		 *   Value
		 *   @var LogDataFieldValue
		 *   @access public
		 */

		public $value = null;


		/**
		 *   Old name
		 *   @var LogDataFieldValue
		 *   @access public
		 */

		public $oldValue = null;


		/**
		 *   New name
		 *   @var LogDataFieldValue
		 *   @access public
		 */

		public $newValue = null;


		/**
		 *   Init log data field
		 *   @access public
		 *   @param string $fieldTag Field tag
		 *   @param string $fieldName Field name
		 *   @return \Codelab\FlaskPHP\Model\LogDataField
		 */

		public function __construct( string $fieldTag=null, string $fieldName=null )
		{
			$this->tag=$fieldTag;
			$this->displayName=$fieldName;
		}


		/**
		 *   Set value
		 *   @access public
		 *   @param mixed $value
		 *   @param mixed $displayValue
		 *   @return \Codelab\FlaskPHP\Model\LogDataFieldValue
		 */

		public function setValue( $value=null, $displayValue=null )
		{
			$this->value=new LogDataFieldValue($value,$displayValue);
			return $this->value;
		}


		/**
		 *   Set value
		 *   @access public
		 *   @param mixed $value
		 *   @param mixed $displayValue
		 *   @return \Codelab\FlaskPHP\Model\LogDataFieldValue
		 */

		public function setOldValue( $value=null, $displayValue=null )
		{
			$this->oldValue=new LogDataFieldValue($value,$displayValue);
			return $this->oldValue;
		}


		/**
		 *   Set value
		 *   @access public
		 *   @param mixed $value
		 *   @param mixed $displayValue
		 *   @return \Codelab\FlaskPHP\Model\LogDataFieldValue
		 */

		public function setNewValue( $value=null, $displayValue=null )
		{
			$this->newValue=new LogDataFieldValue($value,$displayValue);
			return $this->newValue;
		}


	}


?>
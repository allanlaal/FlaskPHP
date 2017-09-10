<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The subtitle form field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class SubTitleField extends FieldInterface
	{


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @access public
		 *   @param string $tag Field tag
		 *   @return \Codelab\FlaskPHP\Field\SubTitleField
		 *
		 */

		public function __construct( string $tag=null )
		{
			parent::__construct($tag);
			$this->setNoSave(true);
		}


		/**
		 *
		 *   Render form field: beginning block
		 *   ----------------------------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param int $row Row (for multi-row fields)
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFormLabel( $value, int $row=null )
		{
			return '<h3 class="form-subtitle mt-4 mb-2">'.$this->getTitle().'</h3>';
		}


		/**
		 *
		 *   Render form field: element
		 *   --------------------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param int $row Row (for multi-row fields)
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFormElement( $value, int $row=null )
		{
			return '';
		}


	}


?>
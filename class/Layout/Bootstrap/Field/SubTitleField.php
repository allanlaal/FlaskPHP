<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The subtitle field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class SubTitleField extends FlaskPHP\Field\SubTitleField
	{


		/**
		 *   Bootstrap standard functions
		 */

		use BootstrapField;


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
			$c='<div class="col-md-12 mt-2">';
			$c.='<h5 class="text-center">'.$this->getTitle().'</h5>';
			$c.='</div>';
			return $c;
		}


	}


?>
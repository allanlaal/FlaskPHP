<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The text list filter
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class TextListFilter extends FlaskPHP\Action\TextListFilter
	{


		/**
		 *   Bootstrap standard layout functions
		 */

		use BootstrapListFilter;


		/**
		 *
		 *   Render filter: element
		 *   ----------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilterElement()
		{
			// We just need the form-control class
			if ($this->hasParam('fieldclass'))
			{
				$this->setParam('fieldclass',$this->getParam('fieldclass').' form-control');
			}
			else
			{
				$this->setParam('fieldclass','form-control');
			}

			// Render
			return parent::renderFilterElement();
		}



	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Controller: CSS tools
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	use Codelab\FlaskPHP as FlaskPHP;

	class JSController extends FlaskPHP\Controller\ControllerInterface
	{


		/**
		 *   Init controller
		 */

		function initController()
		{
			$this->setLoginRequired(false);
		}


		/**
		 *   Init actions
		 */

		function initActions()
		{
			// Serve JS asset bundle
			$a=$this->addAction('bundle');
			$a->setFile(Flask()->getFlaskPath().'/action/js.bundle.php');

			// Locale JS
			$a=$this->addAction('locale');
			$a->setFile(Flask()->getFlaskPath().'/action/js.locale.php');
		}

	}


?>
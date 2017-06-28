<?php


	/**
	 *
	 *   Flask PHP
	 *   Controller: CSS tools
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	use Codelab\FlaskPHP as FlaskPHP;

	class CssController extends FlaskPHP\Controller\ControllerInterface
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
			// Serve CSS asset bundle
			$a=$this->addAction('bundle');
			$a->setFile(Flask()->getFlaskPath().'/action/css.bundle.php');
		}

	}


?>
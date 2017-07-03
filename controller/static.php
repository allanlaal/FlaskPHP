<?php


	/**
	 *
	 *   Flask PHP
	 *   Controller: static passthrough
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	use Codelab\FlaskPHP as FlaskPHP;

	class StaticController extends FlaskPHP\Controller\ControllerInterface
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
			// Static passthru action
			$a=$this->addAction('passthru');
			$a->setFile(Flask()->getFlaskPath().'/action/static.passthru.php');
		}

	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   The default app request controller mapper
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Request;
	use Codelab\FlaskPHP as FlaskPHP;


	class AppRequestControllerMapper extends RequestControllerMapperInterface
	{


		/**
		 *    Run the controller mapper
		 *    @access public
		 *    @param string $uriElement Controller URI element
		 *    @param array $uriArray The remaining URI array
		 *    @throws \Exception
		 *    @return FlaskPHP\Controller\ControllerInterface
		 */

		public function runControllerMapper( string $uriElement, array &$uriArray )
		{
			// Validate
			$controllerName=$uriElement;
			if (!preg_match("/^[A-Za-z0-9\.\_\-]+$/",$controllerName)) throw new FlaskPHP\Exception\ControllerMapperException('Invalid request',400);

			// Check if the controller exists
			$controllerFilename=Flask()->resolvePath('controller/'.$controllerName.'.php',false);
			if (empty($controllerFilename) || !is_readable($controllerFilename)) return null;

			// Init controller
			return $this->getControllerObject($controllerFilename);
		}


	}


?>
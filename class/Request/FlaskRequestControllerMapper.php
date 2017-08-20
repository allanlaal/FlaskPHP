<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The request controller mapper for Flask system actions
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Request;
	use Codelab\FlaskPHP as FlaskPHP;


	class FlaskRequestControllerMapper extends RequestControllerMapperInterface
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
			// Not Flask
			if (mb_strtolower($uriElement!='flask')) return null;

			// Check that we have a controller
			if (!sizeof($uriArray)) throw new FlaskPHP\Exception\ControllerMapperException('Invalid request',400);

			// Get and validate controller
			$controllerName=array_shift($uriArray);
			if (!preg_match("/^[A-Za-z0-9]+$/",$controllerName)) throw new FlaskPHP\Exception\ControllerMapperException('Invalid request',400);

			// See if the controller exists
			$controllerFilename=Flask()->getFlaskPath().'/controller/'.$controllerName.'.php';
			if (!is_readable($controllerFilename)) throw new FlaskPHP\Exception\ControllerMapperException('Invalid request',400);

			// Init controller
			return $this->getControllerObject($controllerFilename);
		}


	}


?>
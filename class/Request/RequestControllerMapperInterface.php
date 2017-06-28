<?php


	/**
	 *
	 *   FlaskPHP
	 *   The request controller mapper interface
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Request;
	use Codelab\FlaskPHP as FlaskPHP;


	class RequestControllerMapperInterface
	{


		/**
		 *   Get controller object from file
		 *   @access public
		 *   @param string $filename File name
		 *   @throws \Exception
		 *   @return FlaskPHP\Controller\ControllerInterface
		 */

		public function getControllerObject( string $filename )
		{
			// Check if file exists
			if (!is_readable($filename))
			{
				throw new FlaskPHP\Exception\ControllerMapperException('Controller file not readable.',401);
			}

			// Get declared classes before including controller
			$declaredClassesBefore=get_declared_classes();

			// Read
			$retVal=require($filename);

			// Object instance returned?
			if (is_object($retVal))
			{
				if (!($retVal instanceof FlaskPHP\Controller\ControllerInterface)) throw new FlaskPHP\Exception\ControllerMapperException('Wrong object returned in controller file.',500);
				return $retVal;
			}

			// Get declared classes after including controller
			$declaredClassesAfter=get_declared_classes();

			// Get diff, eliminate non-ControllerInterface classes
			$classList=array_diff($declaredClassesAfter,$declaredClassesBefore);
			foreach ($classList as $c => $className)
			{
				if (!is_subclass_of($className,'Codelab\FlaskPHP\Controller\ControllerInterface'))
				{
					unset($classList[$c]);
				}
			}

			// Multiple matches?
			if (sizeof($classList)>1)
			{
				throw new FlaskPHP\Exception\ControllerMapperException('Found multiple controller instances in controller file.',500);
			}

			// Nothing?
			if (!sizeof($classList))
			{
				throw new FlaskPHP\Exception\ControllerMapperException('No controller instance found in controller file.',500);
			}

			// Create instance and return
			foreach ($classList as $className)
			{
				$controllerObject=new $className();
				return $controllerObject;
			}
		}


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
			// This needs to be implemented in the mapper class
			throw new FlaskPHP\Exception\NotImplementedException('The function runControllerMapper() is not implemented in the '.get_called_class().' class.');
		}


	}


?>
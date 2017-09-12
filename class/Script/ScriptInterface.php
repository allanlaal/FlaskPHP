<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The command-line script interface
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Script;
	use Codelab\FlaskPHP as FlaskPHP;


	class ScriptInterface
	{


		/**
		 *
		 *   Run script
		 *   ----------
		 *   @access public
		 *   @throws \Exception
		 *   @return int
		 *
		 */

		public function runScript()
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function runScript() not implemented in the script class.');
		}


	}


?>
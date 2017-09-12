<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The script session simulator class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Session;
	use Codelab\FlaskPHP as FlaskPHP;


	class ScriptSessionSimulator extends SessionInterface
	{


		/**
		 *   Load the session
		 *   @access public
		 *   @return void
		 */

		public function loadSession()
		{
			// Nothing need to be done here
			$this->sessionLoaded=true;
		}



		/**
		 *   Save the session
		 *   @access public
		 *   @return void
		 */

		public function saveSession()
		{
			// Nothing need to be done here
		}



		/**
		 *   Destroy the session
		 *   @access public
		 *   @param bool $destroyData Destroy the data as well (false by default)
		 *   @return void
		 */

		public function destroySession( bool $destroyData=false )
		{
			// Nothing need to be done here
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The base session interface
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Session;
	use Codelab\FlaskPHP as FlaskPHP;


	class SessionInterface
	{


		/**
		 *   Session data
		 *   @var array
		 *   @access public
		 */

		public $sessionData = array();


		/**
		 *   Is session loaded
		 *   @var boolean
		 *   @access public
		 */

		public $sessionLoaded = false;


		/**
		 *   Is session updated
		 *   @var boolean
		 *   @access public
		 */

		public $sessionUpdated  = false;



		/**
		 *   Load the session
		 *   @access public
		 *   @return void
		 */

		public function loadSession()
		{
			// This should be implemented in the session class
		}



		/**
		 *   Save the session
		 *   @access public
		 *   @return void
		 */

		public function saveSession()
		{
			// This should be implemented in the session class
		}



		/**
		 *   Destroy the session
		 *   @access public
		 *   @param bool $destroyData Destroy the data as well (false by default)
		 *   @return void
		 */

		public function destroySession( bool $destroyData=false )
		{
			// This should be implemented in the session class
		}



		/**
		 *   Regenerate session ID
		 *   @access public
		 *   @return void
		 */

		public function regenerateSessionID()
		{
			// This should be implemented in the session class
		}



		/**
		 *   Set a session variable
		 *   @access public
		 *   @param string $varName Variable name
		 *   @param mixed $varValue Value
		 *   @return void
		 */

		public function set( string $varName, $varValue )
		{
			traverse_set($varName,$this->sessionData,$varValue);
			$this->sessionUpdated=true;
		}



		/**
		 *   Get a session variable
		 *   @access public
		 *   @param string $varName Variable name
		 *   @return mixed
		 */

		public function get( $varName )
		{
			return traverse_get($varName,$this->sessionData);
		}


	}


?>
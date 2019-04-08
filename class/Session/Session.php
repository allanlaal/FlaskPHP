<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The default session class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Session;
	use Codelab\FlaskPHP as FlaskPHP;


	class Session extends SessionInterface
	{


		/**
		 *   Load the session
		 *   @access public
		 *   @return void
		 */

		public function loadSession()
		{
			// Hack: make sure gc_maxlifetime is not smaller than cookie lifetime
			if (intval(Flask()->Config->get('session.cookie_lifetime')))
			{
				if (ini_get('session.gc_maxlifetime')<intval(Flask()->Config->get('session.cookie_lifetime')))
				{
					ini_set('session.gc_maxlifetime',intval(Flask()->Config->get('session.cookie_lifetime')));
				}
			}

			// Start session
			if (intval(Flask()->Config->get('session.cookie_lifetime')))
			{
				session_start([
					'cookie_lifetime' => intval(Flask()->Config->get('session.cookie_lifetime'))
				]);
			}
			else
			{
				session_start();
			}

			// Finish up
			$this->sessionData=&$_SESSION;
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
			if ($destroyData)
			{
				$this->sessionData=array();
			}
			session_write_close();
			$this->sessionLoaded=false;
		}


	}


?>
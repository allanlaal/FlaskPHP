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

			// Session parameters
			$sessionParam=array();
			if (Flask()->Config->get('session.cookie_lifetime')!==null)
			{
				$sessionParam['cookie_lifetime']=intval(Flask()->Config->get('session.cookie_lifetime'));
			}
			if (Flask()->Config->get('session.cookie_httponly')!==null)
			{
				$sessionParam['cookie_httponly']=Flask()->Config->get('session.cookie_httponly');
			}
			else
			{
				$sessionParam['cookie_httponly']=true;
			}
			if (Flask()->Config->get('session.cookie_secure')!==null)
			{
				$sessionParam['cookie_secure']=Flask()->Config->get('session.cookie_secure');
			}
			elseif (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')
			{
				$sessionParam['cookie_secure']=true;
			}
			if (Flask()->Config->get('session.cookie_samesite')!==null)
			{
				$sessionParam['cookie_samesite']=Flask()->Config->get('session.cookie_samesite');
			}
			else
			{
				$sessionParam['cookie_samesite']='lax';
			}
			if (Flask()->Config->get('session.cookie_path')!==null)
			{
				$sessionParam['cookie_path']=Flask()->Config->get('session.cookie_path');
			}
			if (Flask()->Config->get('session.cookie_domain')!==null)
			{
				$sessionParam['cookie_domain']=Flask()->Config->get('session.cookie_domain');
			}

			// Start session
			session_start($sessionParam);

			// Finish up
			$appID=Flask()->Config->get('app.id');
			if (!array_key_exists($appID,$_SESSION) || !is_array($_SESSION[$appID]))
			{
				$_SESSION[$appID]=array();
			}
			$this->sessionData=&$_SESSION[$appID];
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



		/**
		 *   Regenerate session ID
		 *   @access public
		 *   @return void
		 */

		public function regenerateSessionID()
		{
			session_regenerate_id(true);
		}


	}


?>
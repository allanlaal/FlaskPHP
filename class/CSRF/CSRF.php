<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The CSRF protection functionality class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\CSRF;
	use Codelab\FlaskPHP;


	class CSRF
	{


		/**
		 *
		 *   Get CSRF token
		 *   --------------
		 *   @access public
		 *   @static
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public static function getCSRFToken()
		{
			// Already exists?
			$sessionCSRFToken=Flask()->Session->get('csrf.token');

			// If more than 3 hours old, regenerate
			if (mb_strlen($sessionCSRFToken))
			{
				$sessionCSRFTokenGenerated=Flask()->Session->get('csrf.tstamp');
				if (time()-$sessionCSRFTokenGenerated>10800) $sessionCSRFToken=null;
			}

			// Return if valid
			if (mb_strlen($sessionCSRFToken)) return $sessionCSRFToken;

			// Generate
			$sessionCSRFToken=sha1(Flask()->Config->get('app.id').time().uniqid());
			Flask()->Session->set('csrf.token',$sessionCSRFToken);
			Flask()->Session->set('csrf.tstamp',time());

			// Return
			return $sessionCSRFToken;
		}


		/**
		 *
		 *   Validate CSRF token
		 *   -------------------
		 *   @access public
		 *   @static
		 *   @param string $submittedToken Submitted token
		 *   @throws FlaskPHP\Exception\ValidateException
		 *   @return void
		 *
		 */

		public static function validateCSRFToken( $submittedToken )
		{
			// Get token from session
			$sessionCSRFToken=Flask()->Session->get('csrf.token');

			// No token - nothing to validate against
			if (!mb_strlen($sessionCSRFToken)) return;

			// Check submitted token
			if (!mb_strlen($submittedToken)) throw new FlaskPHP\Exception\ValidateException([
				'csrf' => '[[ FLASK.FORM.Error.MissingCSRFToken ]]'
			]);
			if ($submittedToken!=$sessionCSRFToken) throw new FlaskPHP\Exception\ValidateException([
				'csrf' => '[[ FLASK.FORM.Error.InvalidCSRFToken ]]'
			]);
		}


		/**
		 *
		 *   Set CSRF cookie
		 *   ---------------
		 *   @access public
		 *   @static
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public static function setCSRFCookie()
		{
			// Already exists?
			$sessionCSRFToken=static::getCSRFToken();

			// Set cookie using new syntax on 7.3.0+
			if (version_compare(phpversion(),'7.3.0', '>='))
			{
				$cookieParam=[];
				$cookieParam['expires']=0;
				$cookieParam['path']='/';
				$cookieParam['domain']='';
				$cookieParam['httponly']=true;
				if (Flask()->Request->isHTTPS())
				{
					$cookieParam['secure']=true;
					$cookieParam['samesite']='Strict';
				}
				setcookie('CSRF-Token',$sessionCSRFToken,$cookieParam);
			}

			// Set using old syntax
			else
			{
				setcookie('CSRF-Token',$sessionCSRFToken,0,'/','',Flask()->Request->isHTTPS(),true);
			}
		}


	}


?>
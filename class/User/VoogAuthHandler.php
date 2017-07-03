<?php


	/**
	 *
	 *   FlaskPHP
	 *   The auth handler interface for user
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\User;
	use Codelab\FlaskPHP as FlaskPHP;


	class VoogAuthHandler extends AuthHandlerInterface
	{


		/**
		 *   Enable default Flask auth?
		 *   @access public
		 *   @var bool
		 */

		public $enableFlaskAuth = true;


		/**
		 *   Voog URL
		 *   @access public
		 *   @var bool
		 */

		public $voogURL = null;


		/**
		 *   Init
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function initAuthHandler()
		{
			$this->voogURL=Flask()->Config->get('voog.url');
			if ($this->voogURL===null) throw new FlaskPHP\Exception\ConfigException('Voog URL not specified in config.');
		}


		/**
		 *   Auth
		 *   @access public
		 *   @param string $email E-mail address
		 *   @param string $password Password
		 *   @param bool $throwExceptionOnError Throw exception on error
		 *   @throws \Exception
		 *   @return UserInterface
		 */

		public function doLogin( string $email, string $password, $throwExceptionOnError=true )
		{
			try
			{
				// Check input
				if (!mb_strlen($email)) throw new FlaskPHP\Exception\Exception(Flask()->Locale->get('FLASK.USER.Login.Error.EmailEmpty'));
				if (!mb_strlen($password)) throw new FlaskPHP\Exception\Exception(Flask()->Locale->get('FLASK.USER.Login.Error.PasswordEmpty'));

				// Make request
				$HTTP=new FlaskPHP\Http\HttpRequest();
				$HTTP->setURL($this->voogURL.'/admin/login');
				$HTTP->setRequestMethod('POST');
				$HTTP->setOption('newcookiesession',true);
				$HTTP->setOption('followlocation',false);
				$HTTP->setRequestHeaders(array(
					'User-Agent' => 'FlaskPHP by Codelab, Voog user auth agent (curlHTTPRequest)',
					'Accept' => '',
					'Accept-Encoding' => ''
				));
				$HTTP->setPostFields(array(
					'utf8' => '✓',
					'language' => 'en',
					'email' => $email,
					'password' => $password
				));
				$HTTP->send();

				// 302 - login OK
				if ($HTTP->getResponseCode()!=302)
				{
					throw new FlaskPHP\Exception\LoginFailedException('Login failed, non-302 HTTP response: '.$HTTP->getResponseCode());
				}

				// See if the user exists
				$queryParam=Flask()->DB->getQueryBuilder();
				$queryParam->addWhere(Flask()->User->getParam('loginfield_email')."='".addslashes($email)."'");
				$User=Flask()->User::getObjectByQuery($queryParam,false);

				// If not, create
				if ($User===null)
				{
					// Create new user
					$userClass=get_class(Flask()->User);
					$User=new $userClass();
					$User->{Flask()->User->getParam('loginfield_email')}=$email;
					$User->save(null,'User created from VoogAuthHandler');
				}

				// Return
				return $User;
			}
			catch (\Exception $e)
			{
				if ($throwExceptionOnError)
				{
					$exceptionType=get_class($e);
					throw new $exceptionType('VoogAuthHandler: '.$e->getMessage());
				}
				return null;
			}
		}


		/**
		 *   Do logout
		 *   @access public
		 *   @throws \Exception
		 *   @return null
		 */

		public function doLogout()
		{
			// Nothing to do here.
		}


	}


?>
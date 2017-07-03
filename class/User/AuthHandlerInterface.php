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


	class AuthHandlerInterface
	{


		/**
		 *   Enable default Flask auth?
		 *   @access public
		 *   @var bool
		 */

		public $enableFlaskAuth = true;


		/**
		 *   Init
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function initAuthHandler()
		{
			// This can be implemented in the subclass if necessary.
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
			// This should be implemented in the subclass.
			throw new FlaskPHP\Exception\NotImplementedException('The function doLogin() is not implemented in the '.get_called_class().' class.');
		}


		/**
		 *   Do logout
		 *   @access public
		 *   @throws \Exception
		 *   @return null
		 */

		public function doLogout()
		{
			// This should be implemented in the subclass.
			throw new FlaskPHP\Exception\NotImplementedException('The function doLogout() is not implemented in the '.get_called_class().' class.');
		}


	}


?>
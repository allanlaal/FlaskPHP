<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   EE Mobile-ID auth response class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Auth;
	use Codelab\FlaskPHP;


	class EEMobileIDAuthResponse
	{


		/**
		 *   Response status
		 *   @var string
		 *     pending  -  request pending
		 *     success  -  successfully authenticated
		 *     error    -  error
		 */

		public $status = null;


		/**
		 *   Error message
		 *   @var string
		 *   @access public
		 */

		public $error = null;


		/**
		 *   First name
		 *   @var string
		 *   @access public
		 */

		public $firstName = null;


		/**
		 *   Last name
		 *   @var string
		 *   @access public
		 */

		public $lastName = null;


		/**
		 *   ID code
		 *   @var string
		 *   @access public
		 */

		public $idCode = null;


	}


?>
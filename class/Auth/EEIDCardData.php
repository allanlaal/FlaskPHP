<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   EE ID card data class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Auth;
	use Codelab\FlaskPHP;


	class EEIDCardData
	{


		/**
		 *   Full CN
		 *   @var string
		 *   @access public
		 */

		public $CN = null;


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
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Auth provider: Smart-ID
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Auth;
	use Codelab\FlaskPHP;


	class SmartID
	{


		/**
		 *
		 *   Constructor
		 *   -----------
		 *   @access public
		 *   @param bool $forceDev Force dev environment
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Auth\SmartID
		 *
		 */

		public function __construct( bool $forceDev=false )
		{
			$this->initSmartID($forceDev);
		}


		/**
		 *
		 *   Init the provider
		 *   -----------------
		 *   @access public
		 *   @param bool $forceDev Force dev environment
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initSmartID( bool $forceDev=false )
		{
			// This can be extended in the subclass if necessary.
		}


	}


?>
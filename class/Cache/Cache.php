<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The cache provider class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Cache;
	use Codelab\FlaskPHP;


	class Cache
	{


		/**
		 *   Model cache
		 *   @access public
		 *   @var array
		 */

		public $modelCache = array();


		/**
		 *   Country data cache
		 *   @access public
		 *   @var object
		 */

		public $countryData = null;


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Response CSS: CSS bundle item
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Response;
	use Codelab\FlaskPHP as FlaskPHP;


	class ResponseCSSItem
	{


		/**
		 *   Type
		 *   @access public
		 *   @var string
		 */

		public $itemType = null;


		/**
		 *   Filename
		 *   @access public
		 *   @var string
		 */

		public $itemFilename = null;


		/**
		 *   Source
		 *   @access public
		 *   @var string
		 */

		public $itemSource = null;


		/**
		 *   URL
		 *   @access public
		 *   @var string
		 */

		public $itemURL = null;


		/**
		 *   Modified timestamp
		 *   @access public
		 *   @var int
		 */

		public $itemModifiedTimestamp = null;


		/**
		 *   Priority
		 *   @access public
		 *   @var int
		 */

		public $itemPriority = null;


	}


?>
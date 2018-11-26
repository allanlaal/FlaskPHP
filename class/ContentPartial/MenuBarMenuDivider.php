<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The menu bar partial menu divider
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\ContentPartial;
	use Codelab\FlaskPHP as FlaskPHP;


	class MenuBarMenuDivider
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Menu tag
		 *   @access public
		 *   @var string
		 */

		public $menuTag=null;


		/**
		 *   Menu title
		 *   @access public
		 *   @var string
		 */

		public $menuTitle=null;


		/**
		 *   Menu URL
		 *   @access public
		 *   @var string
		 */

		public $menuURL=null;


		/**
		 *   Menu JS action
		 *   @access public
		 *   @var string
		 */

		public $menuAction=null;


		/**
		 *   Subitems
		 *   @access public
		 *   @var string
		 */

		public $subItems=null;


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @return MenuBarMenuDivider
		 *
		 */

		public function __construct()
		{
		}


		/**
		 *
		 *   Render item
		 *   -----------
		 *   @access public
		 *   @return string
		 *
		 */

		public function renderItem()
		{
			$c='<div class="divider"></div>';
			return $c;
		}


	}


?>
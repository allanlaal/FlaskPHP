<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The menu bar partial menu header
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\ContentPartial;
	use Codelab\FlaskPHP as FlaskPHP;


	class MenuBarMenuHeader
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Menu header label
		 *   @access public
		 *   @var string
		 */

		public $menuHeader=null;


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @param string $menuHeader Menu header
		 *   @return MenuBarMenuHeader
		 *
		 */

		public function __construct( string $menuHeader )
		{
			$this->menuHeader=$menuHeader;
		}


		/**
		 *
		 *   Render item
		 *   -----------
		 *   @access public
		 *   @param bool $isSubItem Is subitem?
		 *   @return string
		 *
		 */

		public function renderItem( bool $isSubItem=false )
		{
			$c='<div class="header">';
			$c.=$this->menuHeader;
			$c.='</div>';
			return $c;
		}


	}


?>
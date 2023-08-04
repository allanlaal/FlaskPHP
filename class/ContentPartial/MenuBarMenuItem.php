<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The menu bar partial menu item
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\ContentPartial;
	use Codelab\FlaskPHP as FlaskPHP;


	class MenuBarMenuItem
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
		 *   @var array
		 */

		public $subItems=null;


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @param string $menuTitle Menu item title
		 *   @param string $menuTag Menu item tag
		 *   @param string $menuURL Menu item URL/link
		 *   @param string $menuAction Menu item JS action
		 *   @param array $subItems Subitems
		 *   @return MenuBarMenuItem
		 *
		 */

		public function __construct( string $menuTitle, string $menuTag, string $menuURL=null, string $menuAction=null, array $subItems=null )
		{
			$this->menuTag=$menuTag;
			$this->menuTitle=$menuTitle;
			$this->menuURL=$menuURL;
			$this->menuAction=$menuAction;
			if ($subItems!==null) $this->subItems=$subItems;
		}


		/**
		 *
		 *   Add subitem
		 *   -----------
		 *   @param string $menuTitle Menu item title
		 *   @param string $menuURL Menu item URL/link
		 *   @param string $menuAction Menu item JS action
		 *   @return MenuBarMenuItem
		 *
		 */

		public function addSubItem( string $menuTitle, string $menuURL=null, string $menuAction=null )
		{
			$menuTag=FlaskPHP\Util::stringToURL($menuTitle);
			$this->subItems[$menuTag]=new MenuBarMenuItem($menuTitle,$menuTag,$menuURL,$menuAction);
			return $this->subItems[$menuTag];
		}


		/**
		 *
		 *   Add subheader
		 *   -------------
		 *   @param string $menuHeader
		 *   @return MenuBarMenuHeader
		 *
		 */

		public function addSubHeader( string $menuHeader )
		{
			$menuTag=uniqid();
			$this->subItems[$menuTag]=new MenuBarMenuHeader($menuHeader);
			return $this->subItems[$menuTag];
		}


		/**
		 *
		 *   Add divider
		 *   -----------
		 *   @param string $menuHeader
		 *   @return MenuBarMenuDivider
		 *
		 */

		public function addSubDivider()
		{
			$menuTag=uniqid();
			$this->subItems[$menuTag]=new MenuBarMenuDivider();
			return $this->subItems[$menuTag];
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
			$c='';
			if (!$isSubItem && is_array($this->subItems) && sizeof($this->subItems))
			{
				$c.='<div class="ui dropdown item">';
					$c.=$this->menuTitle.' <i class="dropdown icon"></i>';
					$c.='<div class="menu">';
						foreach ($this->subItems as $SubItem)
						{
							$c.=$SubItem->renderItem(true);
						}
					$c.='</div>';
				$c.='</div>';
			}
			else
			{
				$c.='<a ';
					$c.=' class="item"';
					if (!empty($this->menuURL)) $c.=' href="'.$this->menuURL.'"';
					if (!empty($this->menuAction)) $c.=' onclick="'.$this->menuAction.'"';
				$c.='>';
				$c.=$this->menuTitle;
				$c.='</a>';
			}
			return $c;
		}


	}


?>
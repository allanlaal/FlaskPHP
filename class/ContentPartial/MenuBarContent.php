<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The menu bar content partial
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\ContentPartial;
	use Codelab\FlaskPHP as FlaskPHP;


	class MenuBarContent extends ContentPartialInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Logo
		 *   @var array
		 *   @access public
		 */

		public $logo=null;


		/**
		 *   Menu items
		 *   @var array
		 *   @access public
		 */

		public $menuItems=array();


		/**
		 *   Right side items
		 *   @var array
		 *   @access public
		 */

		public $rightSideItems=array();


		/**
		 *   Additional content
		 *   @var string
		 *   @access public
		 */

		public $additionalContent='';


		/**
		 *
		 *   Set menu wrapper class
		 *   ----------------------
		 *   @param string $class Menu wrapper class
		 *   @return MenuBarContent
		 *
		 */

		public function setWrapperClass( string $class )
		{
			$this->setParam('class',$class);
			return $this;
		}


		/**
		 *
		 *   Set logo
		 *   --------
		 *   @param string $logo Logo
		 *   @param string $logoWrapperClass Logo wrapper class
		 *   @return MenuBarContent
		 *
		 */

		public function setLogo( string $logo, string $logoWrapperClass=null )
		{
			$this->logo=$logo;
			if ($logoWrapperClass!==null) $this->setParam('logowrapperclass',$logoWrapperClass);
			return $this;
		}


		/**
		 *
		 *   Add menu item
		 *   -------------
		 *   @param string $menuTitle Menu item title
		 *   @param string $menuTag Menu item tag
		 *   @param string $menuURL Menu item URL/link
		 *   @param string $menuAction Menu item JS action
		 *   @param array $subItems Subitems
		 *   @return MenuBarMenuItem
		 *
		 */

		public function addMenuItem( string $menuTitle, string $menuTag=null, string $menuURL=null, string $menuAction=null, array $subItems=null )
		{
			if ($menuTag==null) $menuTag=FlaskPHP\Util::stringToURL($menuTitle);
			$this->menuItems[$menuTag]=new MenuBarMenuItem($menuTitle,$menuTag,$menuURL,$menuAction,$subItems);
			return $this->menuItems[$menuTag];
		}


		/**
		 *
		 *   Add right side item
		 *   -------------------
		 *   @param string $itemContent Item content
		 *   @param string $wrapperTag Wrapper tag
		 *   @param string $wrapperClass Wrapper class
		 *   @return MenuBarMenuItem
		 *
		 */

		public function addRightSideItem( string $itemContent, string $wrapperTag='div', string $wrapperClass=null )
		{
			$RightSideItem=new MenuBarRightSideItem($itemContent,$wrapperTag,$wrapperClass);
			$this->rightSideItems[]=$RightSideItem;
			return $RightSideItem;
		}


		/**
		 *
		 *   Add menu item
		 *   -------------
		 *   @param string $menuTitle Menu item title
		 *   @param string $menuTag Menu item tag
		 *   @param string $menuURL Menu item URL/link
		 *   @param string $menuAction Menu item JS action
		 *   @param array $subItems Subitems
		 *   @return MenuBarMenuItem
		 *
		 */

		public function addRightSideMenuItem( string $menuTitle, string $menuTag=null, string $menuURL=null, string $menuAction=null, array $subItems=null )
		{
			if ($menuTag==null) $menuTag=FlaskPHP\Util::stringToURL($menuTitle);
			$this->rightSideItems[$menuTag]=new MenuBarMenuItem($menuTitle,$menuTag,$menuURL,$menuAction,$subItems);
			return $this->rightSideItems[$menuTag];
		}


		/**
		 *
		 *   Add additional content
		 *   ----------------------
		 *   @param string $additionalContent Content
		 *   @return MenuBarContent
		 *
		 */

		public function addAdditionalContent( string $additionalContent )
		{
			$this->additionalContent.=$additionalContent;
			return $this;
		}


		/**
		 *
		 *   Render content
		 *   --------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContent()
		{
			// Wrapper begins
			$wrapperClass='ui top inverted borderless menu';
			if ($this->getParam('class')) $wrapperClass.=' '.$this->getParam('class');
			$c='<div class="'.$wrapperClass.'">';

				// Logo
				if ($this->logo!==null)
				{
					$logoWrapperClass='item';
					if ($this->getParam('logowrapperclass')) $logoWrapperClass.=' '.$this->getParam('logowrapperclass');
					$c.='<div class="'.$logoWrapperClass.'">';
					$c.=$this->logo;
					$c.='</div>';
				}

				// Menu
				foreach ($this->menuItems as $MenuItem)
				{
					$c.=$MenuItem->renderItem();
				}

				// Right side
				if (sizeof($this->rightSideItems))
				{
					$c.='<div class="right menu">';
					foreach ($this->rightSideItems as $RightSideItem)
					{
						$c.=$RightSideItem->renderItem();
					}
					$c.='</div>';
				}

				// Additional content
				if (mb_strlen($this->additionalContent))
				{
					$c.=$this->additionalContent;
				}

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


		/**
		 *
		 *   To string: wrapper for render content
		 *   -------------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function __toString()
		{
			return $this->renderContent();
		}


	}


?>
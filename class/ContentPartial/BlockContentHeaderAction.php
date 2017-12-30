<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The block content partial header action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\ContentPartial;
	use Codelab\FlaskPHP as FlaskPHP;


	class BlockContentHeaderAction extends BlockContentHeaderItem
	{


		/**
		 *
		 *   Init action
		 *   -----------
		 *   @access public
		 *   @param string $title Title
		 *   @param string $action Action
		 *   @param string $itemID Item ID
		 *   @param string $itemClass Item class
		 *   @throws \Exception
		 *   @return BlockContentHeaderAction
		 *
		 */

		public function __construct( string $title=null, string $action=null, string $itemID=null, string $itemClass=null )
		{
			$this->setParam('title',$title);
			$this->setParam('action',$action);
			$this->setParam('id',$itemID);
			$this->setParam('class',$itemClass);
			parent::__construct();
		}


		/**
		 *
		 *   Set title
		 *   ---------
		 *   @param string $title Title
		 *   @return BlockContentHeaderAction
		 *
		 */

		public function setTitle( string $title )
		{
			$this->setParam('title',$title);
			return $this;
		}


		/**
		 *
		 *   Set icon
		 *   --------
		 *   @param string $icon Icon
		 *   @return BlockContentHeaderAction
		 *
		 */

		public function setIcon( string $icon )
		{
			$this->setParam('icon',$icon);
			return $this;
		}


		/**
		 *
		 *   Set action
		 *   ----------
		 *   @param string $action Action
		 *   @return BlockContentHeaderAction
		 *
		 */

		public function setAction( string $action )
		{
			$this->setParam('action',$action);
			return $this;
		}


		/**
		 *
		 *   Set link
		 *   --------
		 *   @param string $link Link
		 *   @return BlockContentHeaderAction
		 *
		 */

		public function setLink( string $link )
		{
			$this->setParam('link',$link);
			return $this;
		}


		/**
		 *
		 *   Add a subitem
		 *   -------------
		 *   @param BlockContentHeaderItem $item
		 *   @return BlockContentHeaderAction
		 *
		 */

		public function addSubItem( BlockContentHeaderItem $item )
		{
			if (!is_array($this->getParam('subitems')))
			{
				$this->setParam('subitems',array());
			}
			$this->_param['subitems'][]=$item;
			return $this;
		}


		/**
		 *
		 *   Set subitems
		 *   ------------
		 *   @param array $items Items
		 *   @throws \Exception
		 *   @return BlockContentHeaderAction
		 *
		 */

		public function setSubItems( array $items )
		{
			foreach ($items as $k => $item)
			{
				if (!($item instanceof BlockContentHeaderItem))
				{
					throw new FlaskPHP\Exception\InvalidParameterException('Member '.$k.' of $actions is not an instance of BlockContentHeaderItem.');
				}
			}
			$this->setParam('subitems',$items);
			return $this;
		}


		/**
		 *
		 *   Render item
		 *   -----------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderItem()
		{
			// Class
			$itemClass=oneof($this->getParam('class'),array());
			$itemClass[]='ui dropdown item';
			if ($this->getParam('icon')) $itemClass[]='icon';

			// Content
			$itemContent='';
			if ($this->getParam('link'))
			{
				$itemContent.='<a href="'.$this->getParam('link').'" class="'.join(' ',$itemClass).'" '.($this->getParam('id')?' id="'.$this->getParam('id').'"':'').'>';
				if ($this->getParam('icon')) $itemContent.=$this->getParam('icon');
				elseif ($this->getParam('title')) $itemContent.=$this->getParam('title');
				$itemContent.='</a>';
			}
			else
			{
				$itemContent.='<div onclick="'.$this->getParam('action').'" class="'.join(' ',$itemClass).'" '.($this->getParam('id')?' id="'.$this->getParam('id').'"':'').'>';
				if ($this->getParam('icon')) $itemContent.=$this->getParam('icon');
				elseif ($this->getParam('title')) $itemContent.=$this->getParam('title');
				if ($this->getParam('subitems'))
				{
					$itemContent.='<div class="menu">';
					foreach ($this->getParam('subitems') as $subItem)
					{
						$itemContent.=$subItem->renderItem();
					}
					$itemContent.='</div>';
				}
				$itemContent.='</div>';
			}
			return $itemContent;
		}


	}


?>
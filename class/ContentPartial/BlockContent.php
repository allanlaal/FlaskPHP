<?php


	/**
	 *
	 *   FlaskPHP
	 *   -------------------------
	 *   The block content partial
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\ContentPartial;
	use Codelab\FlaskPHP as FlaskPHP;


	class BlockContent extends ContentPartialInterface
	{


		/**
		 *
		 *   Init content partial
		 *   --------------------
		 *   @access public
		 *   @param string|FlaskPHP\ContentPartial\ContentPartialInterface $content Content
		 *   @param string $title Title
		 *   @param array $items Header items/actions
		 *   @param string $blockID Block ID
		 *   @param string $blockClass Block class
		 *   @throws \Exception
		 *   @return BlockContent
		 *
		 */

		public function __construct( $content=null, string $title=null, array $items=null, string $blockID=null, string $blockClass=null )
		{
			$this->setParam('content',$content);
			$this->setParam('title',$title);
			$this->setParam('headeritems',$items);
			$this->setParam('id',$blockID);
			$this->setParam('class',$blockClass);
			parent::__construct();
		}


		/**
		 *
		 *   Set content
		 *   -----------
		 *   @param string|FlaskPHP\ContentPartial\ContentPartialInterface $content Content
		 *   @return BlockContent
		 *
		 */

		public function setContent( $content )
		{
			$this->setParam('content',$content);
			return $this;
		}


		/**
		 *
		 *   Set title
		 *   ---------
		 *   @param string $title Title
		 *   @return BlockContent
		 *
		 */

		public function setTitle( string $title )
		{
			$this->setParam('title',$title);
			return $this;
		}


		/**
		 *
		 *   Add an action
		 *   -------------
		 *   @param BlockContentHeaderItem $item
		 *   @return BlockContent
		 *
		 */

		public function addHeaderItem( BlockContentHeaderItem $item )
		{
			if (!is_array($this->getParam('headeritems')))
			{
				$this->setParam('headeritems',array());
			}
			$this->_param['headeritems'][]=$item;
			return $this;
		}


		/**
		 *
		 *   Set header items
		 *   ----------------
		 *   @param array $items Header items
		 *   @throws \Exception
		 *   @return BlockContent
		 *
		 */

		public function setHeaderItems( array $items )
		{
			foreach ($items as $k => $item)
			{
				if (!($item instanceof BlockContentHeaderItem))
				{
					throw new FlaskPHP\Exception\InvalidParameterException('Member '.$k.' of $actions is not an instance of BlockContentHeaderItem.');
				}
			}
			$this->setParam('headeritems',$items);
			return $this;
		}


		/**
		 *
		 *   Set block ID
		 *   ------------
		 *   @param string $blockID Block ID
		 *   @return BlockContent
		 *
		 */

		public function setBlockID( string $blockID )
		{
			$this->setParam('id',$blockID);
			return $this;
		}


		/**
		 *
		 *   Set block class
		 *   ---------------
		 *   @param string $blockClass Block class
		 *   @return BlockContent
		 *
		 */

		public function setBlockClass( string $blockClass )
		{
			$this->setParam('class',$blockClass);
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
			// Card begins
			$blockContent='';

				// Header
				if ($this->getParam('title') || $this->getParam('headeritems'))
				{
					$blockContent.='<div class="ui top attached secondary segment">';

						if ($this->getParam('headeritems'))
						{
							$blockContent.='<div class="flask blockcontent header wrapper">';
						}

						if ($this->getParam('title'))
						{
							$blockContent.='<div class="title"><h4>'.$this->getParam('title').'</h4></div>';
						}

						if ($this->getParam('headeritems'))
						{
							$blockContent.='<div class="headeritems">';
							foreach ($this->getParam('headeritems') as $k => $actionItem)
							{
								if (!($actionItem instanceof BlockContentHeaderItem)) throw new FlaskPHP\Exception\InvalidParameterException('Member '.$k.' of $actions is not an instance of BlockContentHeaderItem.');
								$blockContent.=$actionItem->renderItem();
							}
							$blockContent.='</div>';
							$blockContent.='</div>';
						}

					$blockContent.='</div>';
				}

				/*
				if ($this->getParam('headeritems'))
				{
					$blockContent.='<div class="ui top attached menu'.($this->getParam('class')?' '.$this->getParam('class'):'').'"'.($this->getParam('id')?' id="'.$this->getParam('id').'_title"':'').'>';
					if ($this->getParam('title'))
					{
						$blockContent.='<h4 class="borderless header item">';
						$blockContent.=$this->getParam('title');
						$blockContent.='</h4>';
					}
					$blockContent.='<div class="right menu">';
					$blockContent.='</div>';
					$blockContent.='</div>';
				}
				elseif ($this->getParam('title'))
				{
					$blockContent.='<div class="ui top attached padded header'.($this->getParam('class')?' '.$this->getParam('class'):'').'"'.($this->getParam('id')?' id="'.$this->getParam('id').'_title"':'').'>';
					$blockContent.=$this->getParam('title');
					$blockContent.='</div>';
				}
				*/

				// Content
				$contentBody=$this->getParam('content');
				$blockContent.='<div class="ui bottom attached padded segment'.($this->getParam('class')?' '.$this->getParam('class'):'').'"'.($this->getParam('id')?' id="'.$this->getParam('id').'_content"':'').'>';
				if (is_object($contentBody))
				{
					if ($contentBody instanceof FlaskPHP\ContentPartial\ContentPartialInterface)
					{
						$blockContent.=$contentBody->renderContent();
					}
					else
					{
						throw new FlaskPHP\Exception\Exception('If object, content must be an instance of ContentPartialInterface.');
					}
				}
				else
				{
					$blockContent.=$contentBody;
				}
				$blockContent.='</div>';

			// Card ends
			return $blockContent;
		}


	}


?>
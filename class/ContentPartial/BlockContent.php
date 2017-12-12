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
		 *   @param array $actions Actions
		 *   @param string $blockID Block ID
		 *   @param string $blockClass Block class
		 *   @throws \Exception
		 *   @return BlockContent
		 *
		 */

		public function __construct( $content=null, string $title=null, array $actions=null, string $blockID=null, string $blockClass=null )
		{
			$this->setParam('content',$content);
			$this->setParam('title',$title);
			$this->setParam('actions',$actions);
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
		 *   Set actions
		 *   -----------
		 *   @param array $actions Actions
		 *   @return BlockContent
		 *
		 */

		public function setActions( array $actions )
		{
			$this->setParam('actions',$actions);
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

				// Title
				if ($this->getParam('title') || $this->getParam('actions'))
				{
					$blockContent.='<div class="ui top attached padded header'.($this->getParam('class')?' '.$this->getParam('class'):'').'"'.($this->getParam('id')?' id="'.$this->getParam('id').'_title"':'').'>';
					$blockContent.='<div class="d-flex justify-content-between">';
					if ($this->getParam('title'))
					{
						if ($this->getParam('actions')) $blockContent.='<div class="float-left">';
						$blockContent.=$this->getParam('title');
						if ($this->getParam('actions')) $blockContent.='</div>';
					}
					if ($this->getParam('actions'))
					{
						if ($this->getParam('title')) $blockContent.='<div class="float-right"">';
						foreach ($this->getParam('actions') as $action)
						{
							$blockContent.='<small class="block action margin-l-1">'.$action.'</small>';
						}
						if ($this->getParam('title')) $blockContent.='</div>';
					}
					$blockContent.='</div>';
					$blockContent.='</div>';
				}

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
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
		 *   @throws \Exception
		 *   @return BlockContent
		 *
		 */

		public function __construct( $content=null, string $title=null, array $actions=null )
		{
			$this->setParam('content',$content);
			$this->setParam('title',$title);
			$this->setParam('actions',$actions);
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
					$blockContent.='<div class="ui top attached padded header">';
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
				$blockContent.='<div class="ui bottom attached padded segment">';
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
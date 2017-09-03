<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The card content partial
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class CardContent extends FlaskPHP\Action\ContentPartialInterface
	{


		/**
		 *
		 *   Init content partial
		 *   --------------------
		 *   @access public
		 *   @param string|FlaskPHP\Action\ContentPartialInterface $content Content
		 *   @param string $title Title
		 *   @param array $actions Actions
		 *   @throws \Exception
		 *   @return CardContent
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
			$cardContent='<div class="card my-4">';

				// Title
				if ($this->getParam('title') || $this->getParam('actions'))
				{
					$cardContent.='<div class="card-header bg-light">';
					if ($this->getParam('title'))
					{
						if ($this->getParam('actions')) $cardContent.='<div class="float-left">';
						$cardContent.=$this->getParam('title');
						if ($this->getParam('actions')) $cardContent.='</div>';
					}
					if ($this->getParam('actions'))
					{
						if ($this->getParam('title')) $cardContent.='<div class="float-right">';
						foreach ($this->getParam('actions') as $action)
						{
							$cardContent.='<small class="card-action d-inline ml-2">'.$action.'</small>';
						}
						if ($this->getParam('title')) $cardContent.='</div>';
					}
					$cardContent.='</div>';
				}

				// Content
				$contentBody=$this->getParam('content');
				$cardContent.='<div class="card-body">';
				if (is_object($contentBody))
				{
					if ($contentBody instanceof FlaskPHP\Action\ContentPartialInterface)
					{
						$cardContent.=$contentBody->renderContent();
					}
					else
					{
						throw new FlaskPHP\Exception\Exception('If object, content must be an instance of ContentPartialInterface.');
					}
				}
				else
				{
					$cardContent.=$contentBody;
				}
				$cardContent.='</div>';

			// Card ends
			$cardContent.='</div>';
			return $cardContent;
		}


	}


?>
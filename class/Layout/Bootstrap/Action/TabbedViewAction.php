<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The tabbed view action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class TabbedViewAction extends FlaskPHP\Action\TabbedViewAction
	{


		/**
		 *
		 *   Render tab bar
		 *   --------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderTabBar()
		{
			$tabBar='<div class="tabbedview-tabbar my-4"><ul class="tabbedview-tabs nav nav-tabs">';
			$t=0;
			foreach ($this->tab as $tabTag => $tabObject)
			{
				$tabBar.='<li id="tab_'.$tabTag.'" class="tabbedview-tab nav-item"><a class="nav-link'.($t==0?' active':'').'" onclick="Flask.Tab.selectTab(\''.htmlspecialchars($tabTag).'\');">'.$tabObject->getTitle().'</a></li>';
				$t++;
			}
			$tabBar.='</ul></div>';
			return $tabBar;
		}


		/**
		 *
		 *   Render return link
		 *   ------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderReturnLink()
		{
			$returnLink='';
			if ($this->hasParam('returnlink'))
			{
				$returnLink='<div class="returnlink mt-4 text-center"><a href="'.$this->getParam('returnlink').'"><span class="icon-back"></span> '.oneof($this->getParam('returnlink_title'),'[[ FLASK.LIST.Return ]]').'</a></div>';
			}
			return $returnLink;
		}


	}


?>
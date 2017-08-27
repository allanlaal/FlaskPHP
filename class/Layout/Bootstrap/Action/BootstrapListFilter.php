<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The Bootstrap list filter trait
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	trait BootstrapListFilter
	{


		/**
		 *
		 *   Render filter: beginning block
		 *   ------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilterBeginningBlock()
		{
			$filterWidth=oneof($this->listObject->getParam('filterwidth'),3);
			$filterBeginningBlock='<div class="filter-item col-12 col-md-'.$filterWidth.'" id="filter_'.$this->tag.'">';
			return $filterBeginningBlock;
		}


		/**
		 *
		 *   Render filter: label
		 *   --------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilterLabel()
		{
			$filterLabel='<label class="filter-item-label" for="filter_'.$this->tag.'">';
			$filterLabel.='<small>'.$this->getParam('title').':</small>';
			$filterLabel.='</label>';
			return $filterLabel;
		}


	}


?>
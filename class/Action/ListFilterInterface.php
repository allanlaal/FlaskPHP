<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The list filter interface
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class ListFilterInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Filter tag
		 *   @var string
		 *   @access public
		 */

		public $tag = null;


		/**
		 *   Back-reference to list object
		 *   @var FlaskPHP\Action\ListAction
		 *   @access public
		 */

		public $listObject = null;


		/**
		 *
		 *   Set title
		 *   ---------
		 *   @access public
		 *   @param string $title Filter title
		 *   @return \Codelab\FlaskPHP\Action\ListFilterInterface
		 *
		 */

		public function setTitle( string $title )
		{
			$this->setParam('title',$title);
			return $this;
		}


		/**
		 *
		 *   Trigger: pre-display
		 *   --------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function triggerPreDisplay()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Render filter
		 *   -------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilter()
		{
			// Pre-display trigger
			$this->triggerPreDisplay();

			// Render
			$filter=$this->renderFilterBeginningBlock();
			$filter.=$this->renderFilterLabel();
			$filter.=$this->renderFilterElement();
			$filter.=$this->renderFilterEndingBlock();
			return $filter;
		}


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
			$filterBeginningBlock='<div class="filter-item" id="filter_'.$this->tag.'">';
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
			$filterLabel.=$this->getParam('title');
			$filterLabel.='</label>';
			return $filterLabel;
		}


		/**
		 *
		 *   Render filter: element
		 *   ----------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilterElement()
		{
			// This should be implemented in the filter class
			throw new FlaskPHP\Exception\NotImplementedException('Function renderFilterElement() not implemented in the filter class.');
		}


		/**
		 *
		 *   Render filter: ending block
		 *   ---------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilterEndingBlock()
		{
			$filterEndingBlock='</div>';
			return $filterEndingBlock;
		}


		/**
		 *
		 *   Get filter submit value
		 *   -----------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function getSubmitValue()
		{
			return Flask()->Request->postVar('filter_'.$this->tag);
		}


		/**
		 *
		 *   Get filter value
		 *   ----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function getValue()
		{
			// Not connected to a list?
			if (!is_object($this->listObject)) return null;

			// Return value
			return (Flask()->Session->get('list.'.$this->listObject->getParam('id').'.filter.'.$this->tag));
		}


		/**
		 *
		 *   Apply filter to load list query
		 *   -------------------------------
		 *   @access public
		 *   @param FlaskPHP\DB\QueryBuilderInterface $loadListParam List load parameters
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function applyFilter( FlaskPHP\DB\QueryBuilderInterface $loadListParam )
		{
			// Empty?
			$value=$this->getValue();
			if ($value==null) return;

			// Mark filter applied
			$this->listObject->filterApplied=true;

			// TODO: finish
			if ($this->getParam('exact') || !is_string($value))
			{
				$loadListParam->addWhere($this->tag."=".$loadListParam::colValue($value));
			}
			else
			{
				$loadListParam->addWhere($this->tag." like ".$loadListParam::colValue('%'.strval($value).'%'));
			}
		}


	}


?>
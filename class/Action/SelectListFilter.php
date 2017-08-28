<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   List filter: select filter
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class SelectListFilter extends ListFilterInterface
	{


		/**
		 *
		 *   Set option grouping
		 *   -------------------
		 *   @access public
		 *   @param bool $grouping Grouping enabled
		 *   @return \Codelab\FlaskPHP\Action\ListFilterInterface
		 *
		 */

		public function setGrouping( bool $grouping )
		{
			$this->setParam('optgroup',$grouping);
			return $this;
		}


		/**
		 *
		 *   Set "all" value
		 *   ---------------
		 *   @access public
		 *   @param string|bool $select Empty select value (or bool for none)
		 *   @return \Codelab\FlaskPHP\Action\ListFilterInterface
		 *
		 */

		public function setSelect( $select )
		{
			$this->setParam('select',$select);
			return $this;
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
			// Get options
			$options=$this->getOptions();

			// Value
			$value=$this->getValue();

			// Style
			$style=array();
			if ($this->getParam('fieldstyle')) $class[]=$this->getParam('fieldstyle');

			// Class
			$class=array();
			if (!empty($this->getParam('fieldclass'))) $class[]=$this->getParam('fieldclass');

			// Wrapper
			$filterElement='<div class="filter-item-element">';

			// Field
			$filterElement.='<select';
				$filterElement.=' type="text"';
				$filterElement.=' id="filter_'.$this->tag.'"';
				$filterElement.=' name="filter_'.$this->tag.'"';
				$filterElement.=' value="'.$value.'"';
				$filterElement.=' autocomplete="off"';
				$filterElement.=' class="'.join(' ',$class).'"';
				if (!empty($style)) $filterElement.=' style="'.join('; ',$style).'"';
				if ($this->getParam('maxlength')) $filterElement.=' maxlength="'.$this->getParam('maxlength').'"';
				if ($this->getParam('readonly') || $this->getParam('form_readonly')) $filterElement.=' readonly="readonly"';
				if ($this->getParam('disabled') || $this->getParam('form_disabled')) $filterElement.=' disabled="disabled"';
				if ($this->getParam('placeholder')) $filterElement.=' placeholder="'.htmlspecialchars($this->getParam('placeholder')).'"';
				if ($this->getParam('event'))
				{
					foreach ($this->getParam('form_event') as $eventType => $eventContent) $filterElement.=' '.$eventType.'="'.$eventContent.'"';
				}
				if ($this->getParam('data'))
				{
					foreach ($this->getParam('data') as $dataKey => $dataValue) $filterElement.=' data-'.$dataKey.'="'.htmlspecialchars($dataValue).'"';
				}
			$filterElement.='>';
			if ($this->getParam('select')!==false)
			{
				$filterElement.='<option value="">--- '.oneof($this->getParam('select'),'[[ FLASK.LIST.Filter.ShowAll ]]').' ---</option>';
			}
			if ($this->getParam('optgroup'))
			{
				foreach ($options as $optGroupName => $optGroupOptions)
				{
					$filterElement.='<optgroup label="'.htmlspecialchars($optGroupName).'">';
					$filterElement.=FlaskPHP\Util::arrayToSelectOptions($optGroupOptions,$value);
					$filterElement.='</optgroup>';
				}
			}
			else
			{
				$filterElement.=FlaskPHP\Util::arrayToSelectOptions($options,$value);
			}
			$filterElement.='</select>';
			
			// Wrapper ends
			$filterElement.='</div>';
			return $filterElement;
		}


	}


?>
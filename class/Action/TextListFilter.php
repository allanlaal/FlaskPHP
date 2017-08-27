<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   List filter: text filter
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class TextListFilter extends ListFilterInterface
	{


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
			// Value
			$value=$this->getValue();
			$value=htmlspecialchars($value);
			$value=str_replace('{','&#123;',$value);
			$value=str_replace('}','&#125;',$value);
			$value=str_replace('[','&#091;',$value);
			$value=str_replace(']','&#093;',$value);

			// Style
			$style=array();
			if ($this->getParam('fieldstyle')) $class[]=$this->getParam('fieldstyle');

			// Class
			$class=array();
			if (!empty($this->getParam('fieldclass'))) $class[]=$this->getParam('fieldclass');

			// Wrapper
			$filterElement='<div class="filter-item-element">';

			// Field
			$filterElement.='<input';
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
			
			// Wrapper ends
			$filterElement.='</div>';
			return $filterElement;
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   List filter: date range filter
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class DateRangeListFilter extends ListFilterInterface
	{


		/**
		 *
		 *   Set datetime field
		 *   ------------------
		 *   @access public
		 *   @param bool $dateTimeField Is datetime field
		 *   @return \Codelab\FlaskPHP\Action\DateRangeListFilter
		 *
		 */

		public function setDateTimeField( bool $dateTimeField )
		{
			$this->setParam('datetime',$dateTimeField);
			return $this;
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
			$submitValue=array();
			$submitValue['from']=Flask()->Request->postVar('filter_'.$this->tag.'_from');
			if (mb_strlen($submitValue['from'])) $submitValue['from']=Flask()->I18n->toYMD($submitValue['from']);
			$submitValue['to']=Flask()->Request->postVar('filter_'.$this->tag.'_to');
			if (mb_strlen($submitValue['to'])) $submitValue['to']=Flask()->I18n->toYMD($submitValue['to']);
			return $submitValue;
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
			// Values
			if (is_object($this->listObject))
			{
				$valueFrom=$this->escapeValue(Flask()->Session->get('list.'.$this->listObject->getParam('id').'.filter.'.$this->tag.'.from'));
				$valueTo=$this->escapeValue(Flask()->Session->get('list.'.$this->listObject->getParam('id').'.filter.'.$this->tag.'.to'));
				if (!mb_strlen($valueFrom) && !mb_strlen($valueTo)) return;
			}
			else
			{
				return;
			}

			// Mark filter applied
			$this->listObject->filterApplied=true;

			// Field list
			if ($this->hasParam('field'))
			{
				$fieldList=str_array($this->getParam('field'));
			}
			else
			{
				$fieldList=array($this->tag);
			}

			// Build criteria
			$whereList=array();
			foreach ($fieldList as $field)
			{
				$field=(mb_strpos($field,'.')===false?$this->listObject->model->getParam('table').'.':'').$field;
				if (!empty($valueFrom) && !empty($valueTo))
				{
					if ($this->getParam('datetime'))
					{
						$whereList[]="(".$field." between '".$valueFrom." 00:00:00' and '".$valueTo." 23:59:59')";
					}
					else
					{
						$whereList[]="(".$field." between '".$valueFrom."' and '".$valueTo."')";
					}
				}
				elseif (!empty($valueFrom))
				{
					if ($this->getParam('datetime'))
					{
						$whereList[]=$field.">='".$valueFrom." 00:00:00'";
					}
					else
					{
						$whereList[]=$field.">='".$valueFrom."'";
					}
				}
				else
				{
					if ($this->getParam('datetime'))
					{
						$whereList[]=$field."<='".$valueTo." 23:59:59'";
					}
					else
					{
						$whereList[]=$field."<='".$valueTo."'";
					}
				}
			}
			$loadListParam->addWhere('('.join(') or (',$whereList).')');
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
			// Values
			if (is_object($this->listObject))
			{
				$valueFrom=$this->escapeValue(Flask()->I18n->formatDate(Flask()->Session->get('list.'.$this->listObject->getParam('id').'.filter.'.$this->tag.'.from')));
				$valueTo=$this->escapeValue(Flask()->I18n->formatDate(Flask()->Session->get('list.'.$this->listObject->getParam('id').'.filter.'.$this->tag.'.to')));
			}
			else
			{
				$valueFrom=$valueTo=null;
			}

			// Style
			$style=array();
			if ($this->getParam('fieldstyle')) $class[]=$this->getParam('fieldstyle');

			// Class
			$class=array();
			if (!empty($this->getParam('fieldclass'))) $class[]=$this->getParam('fieldclass');

			// Wrapper
			$filterElement='<div class="filter-item-element" style="display: flex; align-items: center">';

			// From field
			$filterElement.='<div style="flex: 1 1 auto">';
			$filterElement.='<div class="ui right labeled fluid input calendar">';
			$filterElement.='<input';
				$filterElement.=' type="text"';
				$filterElement.=' id="filter_'.$this->tag.'_from"';
				$filterElement.=' name="filter_'.$this->tag.'_from"';
				$filterElement.=' value="'.$valueFrom.'"';
				$filterElement.=' autocomplete="off"';
				$filterElement.=' class="'.join(' ',$class).'"';
				$filterElement.=' data-mask="'.Flask()->I18n->getDateFormat('mask').'"';
				$filterElement.=' data-date-format="'.Flask()->I18n->getDateFormat('datepicker').'"';
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
			$filterElement.='<a class="ui tag label" onclick="$(this).parent(\'.ui.calendar\').calendar(\'popup\',\'show\')"><i class="fitted calendar icon"></i></a>';
			$filterElement.='</div>';
			$filterElement.='</div>';

			// Divider
			$filterElement.='<div style="flex: 0 0 auto; text-align: center; padding: 0px 10px 0px 10px; white-space: nowrap">-</div>';

			// To field
			$filterElement.='<div style="flex: 1 1 auto">';
			$filterElement.='<div class="ui right labeled fluid input calendar">';
			$filterElement.='<input';
				$filterElement.=' type="text"';
				$filterElement.=' id="filter_'.$this->tag.'_to"';
				$filterElement.=' name="filter_'.$this->tag.'_to"';
				$filterElement.=' value="'.$valueTo.'"';
				$filterElement.=' autocomplete="off"';
				$filterElement.=' class="'.join(' ',$class).'"';
				$filterElement.=' data-mask="'.Flask()->I18n->getDateFormat('mask').'"';
				$filterElement.=' data-date-format="'.Flask()->I18n->getDateFormat('datepicker').'"';
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
			$filterElement.='<a class="ui tag label" onclick="$(this).parent(\'.ui.calendar\').calendar(\'popup\',\'show\')"><i class="fitted calendar icon"></i></a>';
			$filterElement.='</div>';
			$filterElement.='</div>';

			// Wrapper ends
			$filterElement.='</div>';
			return $filterElement;
		}


	}


?>
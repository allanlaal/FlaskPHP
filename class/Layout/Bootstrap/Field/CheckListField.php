<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The check list field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class CheckListField extends FlaskPHP\Field\CheckListField
	{


		/**
		 *   Bootstrap standard functions
		 */

		use BootstrapField;


		/**
		 *
		 *   Render form field: element
		 *   --------------------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param int $row Row (for multi-row fields)
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFormElement( $value, int $row=null )
		{
			// Calculate widths
			$labelWidth=oneof($this->getParam('form_labelwidth'),3);
			$elementWidth=round(12-$labelWidth);

			// Options
			$options=$this->getOptions();
			if (!$this->getParam('optgroup'))
			{
				$options=array(''=>$options);
			}

			// Convert value to array
			$value=str_array($value,"\t");

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');

			// Class
			$class=array();
			$class[]='form-check-input';
			if ($this->getParam('form_fieldclass')) $class[]=$this->getParam('form_fieldclass');

			// Items per row
			$checkboxClass='';
			if ($this->getParam('itemsperrow'))
			{
				$checkboxClass='form-check-inline col-md-'.round(12/$this->getParam('itemsperrow'));
			}

			// Wrapper
			$c='<div class="col-md-'.$elementWidth.'">';

			// Iterate through groups
			foreach ($options as $optGroupName => $optGroupOptions)
			{
				// Title
				if (mb_strlen($optGroupName))
				{
					$c.='<p>'.$optGroupName.'</p>';
				}

				// Checkboxes
				if ($this->getParam('itemsperrow'))
				{
					$c.='<div class="row">';
				}
				foreach ($optGroupOptions as $optionVal => $optionDescription)
				{
					if ($this->getParam('separateitems'))
					{
						$checkboxID=$optionVal;
						$checkboxName=$optionVal;
						$checkboxValue='1';
						$checkboxChecked=(!empty($this->formObject->model->{$optionVal})?true:false);
					}
					else
					{
						$checkboxID=$this->tag.'_'.$optionVal;
						$checkboxName=$this->tag.'[]';
						$checkboxValue=$optionVal;
						$checkboxChecked=(in_array($optionVal,$value)?true:false);
					}

					$c.='<div class="form-check '.$checkboxClass.'">';
					$c.='<label class="form-check-label">';
					$c.='<input';
					$c.=' type="checkbox"';
					$c.=' id="'.$checkboxID.'"';
					$c.=' name="'.$checkboxName.'"';
					$c.=' value="'.htmlspecialchars($checkboxValue).'"';
					if ($checkboxChecked) $c.=' checked="checked"';
					$c.=' class="'.join(' ',$class).'"';
					if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
					if ($this->getParam('readonly') || $this->getParam('form_readonly')) $c.=' readonly="readonly"';
					if ($this->getParam('disabled') || $this->getParam('form_disabled')) $c.=' disabled="disabled"';
					if ($this->getParam('form_keephiddenvalue')) $c.=' data-keephiddenvalue="1"';
					if ($this->getParam('form_event'))
					{
						foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
					}
					if ($this->getParam('form_data'))
					{
						foreach ($this->getParam('form_data') as $dataKey => $dataValue) $c.=' data-'.$dataKey.'="'.htmlspecialchars($dataValue).'"';
					}
					$c.='>'.htmlspecialchars($optionDescription);
					$c.='</label>';
					$c.='</div>';
				}
			}
			if ($this->getParam('itemsperrow'))
			{
				$c.='</div>';
			}

			// Comment
			$c.=$this->renderComment();

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


	}


?>
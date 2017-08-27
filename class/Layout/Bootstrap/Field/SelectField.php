<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The select field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class SelectField extends FlaskPHP\Field\SelectField
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
			// Element widths
			$labelWidth=oneof($this->getParam('form_labelwidth'),3);
			$elementWidth=round(12-$labelWidth);

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');

			// Class
			$class=array();
			$class[]='form-control';
			if ($this->getParam('form_fieldclass')) $class[]=$this->getParam('form_fieldclass');

			// Options
			$options=$this->getOptions();

			// Wrapper
			$c='<div class="col-md-'.$elementWidth.'">';

				// Dropdown
				$c.='<select';
				$c.=' id="'.$this->tag.'"';
				$c.=' name="'.$this->tag.'"';
				$c.=' class="'.join(' ',$class).'"';
				if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
				$c.=' data-originalvalue="'.htmlspecialchars($value).'"';
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
				$c.='>';

					// Select
					if ($this->getParam('select')) $c.='<option value="">--- '.$this->getParam('select').' ---</option>';

					// Options
					if ($this->getParam('optgroup'))
					{
						foreach ($options as $optGroupName => $optGroupOptions)
						{
							$c.='<optgroup label="'.htmlspecialchars($optGroupName).'">';
							foreach ($optGroupOptions as $val => $description)
							{
								$c.='<option';
								$c.=' value="'.htmlspecialchars($val).'"';
								$c.=($val==$value?' selected="selected"':'');
								if ($this->hasParam('form_optiondata') && is_array($this->getParam('form_optiondata')[$val]))
								{
									foreach ($this->getParam('form_optiondata')[$val] as $k => $v)
									{
										$c.=' data-'.$k.'="'.htmlspecialchars($v).'"';
									}
								}
								$c.='>';
								$c.=htmlspecialchars($description);
								$c.='</option>';
							}
							$c.='</optgroup>';
						}
					}
					else
					{
						foreach ($options as $val => $description)
						{
							$c.='<option';
							$c.=' value="'.htmlspecialchars($val).'"';
							$c.=($val==$value?' selected="selected"':'');
							if ($this->hasParam('form_optiondata') && is_array($this->getParam('form_optiondata')[$val]))
							{
								foreach ($this->getParam('form_optiondata')[$val] as $k => $v)
								{
									$c.=' data-'.$k.'="'.htmlspecialchars($v).'"';
								}
							}
							$c.='>';
							$c.=htmlspecialchars($description);
							$c.='</option>';
						}
					}

				// Dropdown ends
				$c.='</select>';

			// Comment
			$c.=$this->renderComment();

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


	}


?>
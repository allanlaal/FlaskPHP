<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The select/dropdown field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class SelectField extends FieldInterface
	{


		/**
		 *   Validate value
		 *   @var bool
		 *   @access public
		 */

		public $validateValueExists = false;


		/**
		 *
		 *   Set empty select value
		 *   ----------------------
		 *   @access public
		 *   @param string $select Empty select value
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setSelect( string $select )
		{
			$this->setParam('select',$select);
			return $this;
		}


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
			// Options
			$options=$this->getOptions();

			// Style
			$style=array();
			if ($this->getParam('fieldstyle')) $style[]=$this->getParam('fieldstyle');
			if ($this->getParam('form_comment')) $style[]='width: 70%; display: inline-block';

			// Class
			$class=array();
			if ($this->getParam('form_fieldclass')) $class[]=$this->getParam('form_fieldclass');

			// Wrapper
			$c='<div class="element">';

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

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


	}


?>
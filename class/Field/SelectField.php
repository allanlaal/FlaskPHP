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
		 *   Set option grouping
		 *   -------------------
		 *   @access public
		 *   @param bool $grouping Grouping enabled
		 *   @return \Codelab\FlaskPHP\Field\SelectField
		 *
		 */

		public function setGrouping( bool $grouping )
		{
			$this->setParam('optgroup',$grouping);
			return $this;
		}


		/**
		 *
		 *   Set empty select value
		 *   ----------------------
		 *   @access public
		 *   @param string $select Empty select value
		 *   @return \Codelab\FlaskPHP\Field\SelectField
		 *
		 */

		public function setSelect( string $select )
		{
			$this->setParam('select',$select);
			return $this;
		}


		/**
		 *
		 *   Get log data
		 *   ------------
		 *   @access public
		 *   @param FlaskPHP\Model\LogData $logData Log data object
		 *   @param FlaskPHP\Model\ModelInterface $model Model object
		 *   @param FlaskPHP\Action\FormAction $form Form object
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function getLogData( FlaskPHP\Model\LogData $logData, FlaskPHP\Model\ModelInterface $model, FlaskPHP\Action\FormAction $form=null )
		{
			// Unset from log values
			$logData->setHandled($this->tag);

			// TODO: finish
		}


		/**
		 *
		 *   Get list value
		 *   --------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param array $row Row
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function listValue( $value, array &$row )
		{
			$options=$this->getOptions();
			return htmlspecialchars($options[$value]);
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
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');
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
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The checkbox field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class CheckboxField extends FieldInterface
	{


		/**
		 *
		 *   Set checkbox value
		 *   ------------------
		 *   @access public
		 *   @param mixed $checkboxValue Save value for selected checkbox
		 *   @return \Codelab\FlaskPHP\Field\CheckboxField
		 */

		public function setCheckboxValue( $checkboxValue )
		{
			$this->setParam('checkboxvalue',$checkboxValue);
			return $this;
		}


		/**
		 *
		 *   Set checkbox empty value
		 *   ------------------------
		 *   @access public
		 *   @param mixed $checkboxEmptyValue Save value for non-selected checkbox
		 *   @return \Codelab\FlaskPHP\Field\CheckboxField
		 */

		public function setCheckboxEmptyValue( $checkboxEmptyValue )
		{
			$this->setParam('checkboxemptyvalue',$checkboxEmptyValue);
			return $this;
		}


		/**
		 *
		 *   Set checkbox title
		 *   ------------------
		 *   @access public
		 *   @param string $checboxTitle Checkbox title
		 *   @return \Codelab\FlaskPHP\Field\CheckboxField
		 */

		public function setCheckboxTitle( string $checboxTitle )
		{
			$this->setParam('checkboxtitle',$checboxTitle);
			return $this;
		}


		/**
		 *
		 *   Set list checkbox title
		 *   -----------------------
		 *   @access public
		 *   @param string $checboxTitle Checkbox title
		 *   @return \Codelab\FlaskPHP\Field\CheckboxField
		 */

		public function setListCheckboxTitle( string $checboxTitle )
		{
			$this->setParam('list_checkboxtitle',$checboxTitle);
			return $this;
		}


		/**
		 *
		 *   Validate field value
		 *   --------------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param array|object $data Full dataset
		 *   @param FlaskPHP\Action\FormAction $formObject Form object
		 *   @return void
		 *   @throws \Exception
		 *
		 */

		public function validate( $value, $data=null, $formObject=null )
		{
			// Required and empty?
			if ($this->required())
			{
				$checkboxValue=oneof($this->getParam('checkboxvalue'),'1');
				if ($value!=$checkboxValue)
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => oneof($this->getParam('required_message'),'[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]')
					]);
				}
			}
		}


		/**
		 *
		 *   Get field form save value
		 *   -------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function saveValue()
		{
			$value=$this->getValue();
			$checkboxValue=($this->hasParam('checkboxvalue')?$this->getParam('checkboxvalue'):1);
			if ($value==$checkboxValue)
			{
				return $checkboxValue;
			}
			else
			{
				return ($this->hasParam('checkboxemptyvalue')?$this->getParam('checkboxemptyvalue'):0);
			}
		}


		/**
		 *
		 *   Get displayable value
		 *   ---------------------
		 *   @access public
		 *   @param bool $encodeContent Encode content
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function displayValue( bool $encodeContent=true )
		{
			// Get value
			$value=parent::displayValue();

			// Show
			$checkboxValue=oneof($this->getParam('checkboxvalue'),'1');
			if ($value==$checkboxValue)
			{
				$displayValue=oneof(
					$this->getParam('list_checkboxtitle'),
					$this->getParam('checkboxtitle'),
					'[[ FLASK.COMMON.Yes | tolower ]]'
				);
				return $displayValue;
			}
			else
			{
				return '';
			}
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
			$checkboxValue=oneof($this->getParam('checkboxvalue'),'1');
			if ($value==$checkboxValue)
			{
				$displayValue=oneof(
					$this->getParam('list_checkboxtitle'),
					$this->getParam('checkboxtitle'),
					'<i class="check square icon"></i>'
				);
				return $displayValue;
			}
			else
			{
				return '';
			}
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
			// Value and state
			$checkboxValue=oneof($this->getParam('checkboxvalue'),'1');
			$checked=($value==$checkboxValue?true:false);

			// Title
			$checkboxTitle=oneof($this->getParam('checkboxtitle'),$this->getTitle());

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');

			// Class
			$class=array();
			if (!empty($this->getParam('form_fieldclass'))) $class[]=$this->getParam('form_fieldclass');

			// Init
			$c='';
			$fieldWrapper=null;

			// Wrapper
			$c='<div class="ui segment">';
			$c.='<div class="ui toggle checkbox '.join(' ',$class).'">';

			// Field
			$c.='<input';
				$c.=' type="checkbox"';
				$c.=' id="'.$this->tag.'"';
				$c.=' name="'.$this->tag.'"';
				$c.=' value="'.$checkboxValue.'"';
				$c.=' data-originalvalue="'.($checked?$checkboxValue:'').'"';
				if ($checked) $c.=' checked="checked"';
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

			// Label
			$c.='<label>'.$checkboxTitle.'</label>';

			// Wrapper
			$c.='</div>';
			$c.='</div>';

			// Comment
			$c.=$this->renderComment();

			// Return
			return $c;
		}


	}


?>
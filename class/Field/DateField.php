<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The date field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class DateField extends FieldInterface
	{


		/**
		 *
		 *   Set minimum allowed date
		 *   ------------------------
		 *   @access public
		 *   @param string $minDate Minimum allowed date
		 *   @return \Codelab\FlaskPHP\Field\DateField
		 *
		 */

		public function setMinDate( string $minDate )
		{
			$this->setParam('mindate',$minDate);
			return $this;
		}


		/**
		 *
		 *   Set maximum allowed date
		 *   ------------------------
		 *   @access public
		 *   @param string $maxDate Maximum allowed date
		 *   @return \Codelab\FlaskPHP\Field\DateField
		 *
		 */

		public function setMaxDate( string $maxDate )
		{
			$this->setParam('maxdate',$maxDate);
			return $this;
		}


		/**
		 *
		 *   Show time in list?
		 *   ------------------
		 *   @access public
		 *   @param bool $showTime Show time (hh:mm)
		 *   @param bool $showSeconds Show seconds
		 *   @return \Codelab\FlaskPHP\Field\DateField
		 *
		 */

		public function setListShowTime( bool $showTime, bool $showSeconds=null )
		{
			$this->setParam('list_showtime',$showTime);
			$this->setParam('list_showtimesec',$showSeconds);
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
				if (!mb_strlen($value))
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => '[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]'
					]);
				}
			}

			// No point in checking further if empty
			if (!mb_strlen($value)) return;

			// Validate format
			if (!Flask()->I18n->validateDateInput($value))
			{
				throw new FlaskPHP\Exception\ValidateException([
					$this->tag => '[[ FLASK.FIELD.Error.InvalidDate ]]'
				]);
			}

			// Validate min/max values
			if ($this->getParam('mindate'))
			{
				if (Flask()->I18n->toTimestamp($value)<strtotime($this->getParam('mindate').' midnight'))
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => '[[ FLASK.FIELD.Error.MinValue : minvalue='.Flask()->I18n->formatDate($this->getParam('mindate')).' ]]'
					]);
				}
			}
			if ($this->getParam('maxdate'))
			{
				if (Flask()->I18n->toTimestamp($value)>strtotime($this->getParam('maxdate').' midnight'))
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => '[[ FLASK.FIELD.Error.MaxValue : maxvalue='.Flask()->I18n->formatDate($this->getParam('maxdate')).' ]]'
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
			if (empty($value) || $value=='0000-00-00') return '0000-00-00';
			if ($value=='0000-00-00 00:00:00') return '0000-00-00 00:00:00';
			if (!preg_match("/^(\d\d\d\d-\d\d-\d\d)$/",$value))
			{
				$value=Flask()->I18n->toYMD($value);
			}
			return $value;
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
			$value=$this->getValue();
			return Flask()->I18n->formatDate($value,($this->getParam('list_showtime')?true:false),($this->getParam('list_showtimesec')?true:false));
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
			// Value
			if ($value=='0000-00-00' || $value=='0000-00-00 00:00:00') $value='';
			if ($value!='') $value=Flask()->I18n->formatDate($value);

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');

			// Class
			$class=array();
			if (!empty($this->getParam('form_fieldclass'))) $class[]=$this->getParam('form_fieldclass');

			// Wrapper
			$c='<div class="ui right labeled input calendar">';

			// Field
			$c.='<input';
				$c.=' type="text"';
				$c.=' id="'.$this->tag.'"';
				$c.=' name="'.$this->tag.'"';
				$c.=' value="'.$value.'"';
				$c.=' data-originalvalue="'.$value.'"';
				$c.=' data-datefield="1"';
				$c.=' data-mask="'.Flask()->I18n->getDateFormat('mask').'"';
				$c.=' data-date-format="'.Flask()->I18n->getDateFormat('datepicker').'"';
				$c.=' autocomplete="off"';
				$c.=' class="'.join(' ',$class).'"';
				if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
				if ($this->getParam('maxlength')) $c.=' maxlength="'.$this->getParam('maxlength').'"';
				if ($this->getParam('readonly') || $this->getParam('form_readonly')) $c.=' readonly="readonly"';
				if ($this->getParam('disabled') || $this->getParam('form_disabled')) $c.=' disabled="disabled"';
				if ($this->getParam('form_placeholder')) $c.=' placeholder="'.htmlspecialchars($this->getParam('form_placeholder')).'"';
				if ($this->getParam('form_emptyformat')) $c.=' data-emptyformat="'.htmlspecialchars($this->getParam('form_emptyformat')).'"';
				elseif ($this->getParam('emptyformat')) $c.=' data-emptyformat="'.htmlspecialchars($this->getParam('emptyformat')).'"';
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

			// Activator
			$c.='<a class="ui tag label" onclick="$(this).parent(\'.ui.calendar\').calendar(\'popup\',\'show\')"><span class="icon-calendar"></span></a>';

			// Wrapper ends
			$c.='</div>';

			// Comment
			$c.=$this->renderComment();

			// Return
			return $c;
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The password field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class PasswordField extends FlaskPHP\Field\PasswordField
	{


		/**
		 *   Bootstrap standard functions
		 */

		use BootstrapField;


		/**
		 *
		 *   Render form field: beginning block
		 *   ----------------------------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param int $row Row (for multi-row fields)
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFormBeginningBlock( $value, int $row=null )
		{
			// Default simple wrapper
			return '<div id="field_'.$this->tag.($row==2?'_repeat':'').'" class="form-group row">';
		}


		/**
		 *
		 *   Render form field: beginning block
		 *   ----------------------------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param int $row Row (for multi-row fields)
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFormLabel( $value, int $row=null )
		{
			$labelWidth=oneof($this->getParam('form_labelwidth'),3);
			$c='<label for="'.$this->tag.'" class="col-md-'.$labelWidth.' col-form-label text-right">';
			switch ($row)
			{
				case 2:
					$c.=$this->getParam('title_repeat').':';
					break;
				default:
					$c.=$this->getParam('title').':';
					break;
			}
			$c.='</label>';
			return $c;
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
			// Calculate widths
			$labelWidth=oneof($this->getParam('form_labelwidth'),3);
			$elementWidth=round(12-$labelWidth);

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');
			if ($row==1 && $this->getParam('form_comment')) $style[]='width: 70%; display: inline-block';
			if ($row==2 && $this->getParam('form_comment_repeat')) $style[]='width: 70%; display: inline-block';

			// Class
			$class=array();
			$class[]='form-control';
			if (!empty($this->getParam('form_fieldclass'))) $class[]=$this->getParam('form_fieldclass');

			// Wrapper
			$c='<div class="col-md-'.$elementWidth.'">';

			// Password suggest
			if ($row==1 && $this->getParam('form_suggest'))
			{
				$c.='<div class="input-group">';
			}

			// Field
			$c.='<input';
				$c.=' type="password"';
				$c.=' id="'.$this->tag.($row==2?'_repeat':'').'"';
				$c.=' name="'.$this->tag.($row==2?'_repeat':'').'"';
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

			// Password suggest
			if ($row==1 && $this->getParam('form_suggest'))
			{
				$c.='<span class="input-group-addon" data-toggle="tooltip" title="[[ FLASK.FORM.Password.Suggest.FormComment ]]"><a onclick="Flask.Password.suggestPassword(\''.$this->tag.'\')"><span class="icon-bulb"></span></a></span>';
				$c.='</div>';
			}

			// Comment
			if ($row==1)
			{
				$c.=$this->renderComment();
			}

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


	}


?>
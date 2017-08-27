<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The text field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class TextField extends FieldInterface
	{


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
			$value=htmlspecialchars($value);
			$value=str_replace('{','&#123;',$value);
			$value=str_replace('}','&#125;',$value);
			$value=str_replace('[','&#091;',$value);
			$value=str_replace(']','&#093;',$value);

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');
			if ($this->getParam('form_comment')) $style[]='width: 70%; display: inline-block';

			// Class
			$class=array();
			if (!empty($this->getParam('form_autocomplete'))) $class[]='autocomplete';
			if (!empty($this->getParam('form_fieldclass'))) $class[]=$this->getParam('form_fieldclass');

			// Wrapper
			$c='<div class="element">';

			// Field
			$c.='<input';
				$c.=' type="text"';
				$c.=' id="'.$this->tag.'"';
				$c.=' name="'.$this->tag.'"';
				$c.=' value="'.$value.'"';
				$c.=' data-originalvalue="'.$value.'"';
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
				if ($this->getParam('form_editmask')) $c.=' data-mask="'.$this->getParam('form_editmask').'"';
				if ($this->getParam('form_autocomplete'))
				{
					$c.='data-autocomplete-minlength="'.intval($this->getParam("form_autocomplete_minlength")).'"';
					$c.='data-autocomplete-sourceurl="'.htmlspecialchars($this->getParam("form_autocomplete_sourceurl")).'"';
					$c.='data-autocomplete-sourcelist="'.htmlspecialchars($this->getParam("form_autocomplete_sourcelist")).'"';
					$c.='data-autocomplete-keyvalue="'.($this->getParam("form_autocomplete_keyvalue")?'1':'0').'"';
				}
				if ($this->getParam('form_event'))
				{
					foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
				}
				if ($this->getParam('form_data'))
				{
					foreach ($this->getParam('form_data') as $dataKey => $dataValue) $c.=' data-'.$dataKey.'="'.htmlspecialchars($dataValue).'"';
				}
			$c.='>';

			// Comment
			$c.=$this->renderComment();

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


	}


?>
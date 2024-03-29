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
		 *   Set field type
		 *   --------------
		 *   @access public
		 *   @param string $type Field type
		 *   @return \Codelab\FlaskPHP\Field\TextField
		 *
		 */

		public function setFieldType( string $type )
		{
			$this->setParam('fieldtype',$type);
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
			// Value
			$value=$this->escapeValue($value);

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');

			// Class
			$class=array();
			if (!empty($this->getParam('form_fieldclass'))) $class[]=$this->getParam('form_fieldclass');

			// Init
			$c='';
			$fieldWrapper=null;

			// Wrapper if needed
			if ($this->getParam('form_suffixlabel') || $this->getParam('form_suffixdropdown'))
			{
				$c.='<div class="ui right labeled input">';
				$fieldWrapper=true;
			}
			elseif ($this->getParam('form_prefixlabel') || $this->getParam('form_prefixdropdown'))
			{
				$c.='<div class="ui labeled input">';
				$fieldWrapper=true;
			}

			// Prefix dropdown
			if ($this->getParam('form_prefixdropdown'))
			{
				$prefixDropdownValue=$this->model->{$this->getParam('form_prefixdropdown_field')};
				$c.='<select id="'.$this->getParam('form_prefixdropdown_field').'" name="'.$this->getParam('form_prefixdropdown_field').'" class="ui compact dropdown label'.($this->getParam('form_prefixdropdown_type')?' '.$this->getParam('form_prefixdropdown_type'):'').'">';
				$c.=FlaskPHP\Util::arrayToSelectOptions($this->getParam('form_prefixdropdown'),$prefixDropdownValue);
				$c.='</select>';
			}

			// Prefix label
			elseif ($this->getParam('form_prefixlabel'))
			{
				$c.='<div class="ui label'.($this->getParam('form_prefixlabel_type')?' '.$this->getParam('form_prefixlabel_type'):'').'">';
				$c.=$this->getParam('form_prefixlabel');
				$c.='</div>';
			}

			// Field
			$c.='<input';
				$c.=' type="'.oneof($this->getParam('fieldtype'),'text').'"';
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
				if ($this->getParam('form_event'))
				{
					foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
				}
				if ($this->getParam('form_data'))
				{
					foreach ($this->getParam('form_data') as $dataKey => $dataValue) $c.=' data-'.$dataKey.'="'.htmlspecialchars($dataValue).'"';
				}
			$c.='>';

			// Suffix dropdown
			if ($this->getParam('form_suffixdropdown'))
			{
				$suffixDropdownValue=$this->model->{$this->getParam('form_suffixdropdown_field')};
				$c.='<select id="'.$this->getParam('form_suffixdropdown_field').'" name="'.$this->getParam('form_suffixdropdown_field').'" class="ui compact dropdown label'.($this->getParam('form_suffixdropdown_type')?' '.$this->getParam('form_suffixdropdown_type'):'').'">';
				$c.=FlaskPHP\Util::arrayToSelectOptions($this->getParam('form_suffixdropdown'),$suffixDropdownValue);
				$c.='</select>';
			}

			// Suffix label
			elseif ($this->getParam('form_suffixlabel'))
			{
				$c.='<div class="ui label'.($this->getParam('form_suffixlabel_type')?' '.$this->getParam('form_suffixlabel_type'):'').'">';
				$c.=$this->getParam('form_suffixlabel');
				$c.='</div>';
			}

			// Wrapper
			if ($fieldWrapper)
			{
				$c.='</div>';
			}

			// Comment
			$c.=$this->renderComment();

			// Return
			return $c;
		}


	}


?>
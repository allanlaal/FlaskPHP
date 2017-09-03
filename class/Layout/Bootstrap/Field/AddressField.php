<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The address field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class AddressField extends FlaskPHP\Field\AddressField
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
			// Loaded data
			if (is_object($this->formObject) && is_object($this->formObject->model) && $this->formObject->model->_loaded)
			{
				$value=$this->escapeValue($this->formObject->model->{$this->tag});
				$valueCity=$this->escapeValue($this->formObject->model->{$this->tag.'_city'});
				$valueZIP=$this->escapeValue($this->formObject->model->{$this->tag.'_zip'});
				$valueState=$this->escapeValue($this->formObject->model->{$this->tag.'_state'});
				$valueCountry=$this->escapeValue($this->formObject->model->{$this->tag.'_country'});
			}

			// Default
			else
			{
				$value=$this->getParam('default');
				$valueCity=$this->getParam('default_city');
				$valueZIP=$this->getParam('default_zip');
				$valueState=$this->getParam('default_state');
				$valueCountry=$this->getParam('default_country');
			}

			// Calculate widths
			$labelWidth=oneof($this->getParam('form_labelwidth'),3);
			$elementWidth=round(12-$labelWidth);

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');
			if ($this->getParam('form_comment')) $style[]='width: 70%; display: inline-block';

			// Class
			$class=array();
			$class[]='form-control';
			if (!empty($this->getParam('form_autocomplete'))) $class[]='autocomplete';
			if (!empty($this->getParam('form_fieldclass'))) $class[]=$this->getParam('form_fieldclass');

			// Wrapper
			$c='<div class="col-md-'.$elementWidth.'">';

			// Row 1
			$c.='<div>';

				// Field
				$c.='<input';
					$c.=' type="text"';
					$c.=' id="'.$this->tag.'"';
					$c.=' name="'.$this->tag.'"';
					$c.=' value="'.$value.'"';
					$c.=' data-originalvalue="'.$value.'"';
					$c.=' placeholder="[[ FLASK.COMMON.Address.Street ]]"';
					$c.=' autocomplete="off"';
					$c.=' class="'.join(' ',$class).'"';
					if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
					if ($this->getParam('maxlength')) $c.=' maxlength="'.$this->getParam('maxlength').'"';
					if ($this->getParam('readonly') || $this->getParam('form_readonly')) $c.=' readonly="readonly"';
					if ($this->getParam('disabled') || $this->getParam('form_disabled')) $c.=' disabled="disabled"';
					if ($this->getParam('form_keephiddenvalue')) $c.=' data-keephiddenvalue="1"';
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

			// Row 2
			$c.='</div>';
			$c.='<div class="mt-2">';

				// Widths
				if ($this->getParam('state') && $this->getParam('country')!==false)
				{
					$widthCity='45%';
					$widthState='25%';
					$widthZIP='15%';
					$widthCountry='25%';
				}
				elseif ($this->getParam('state') && $this->getParam('country')===false)
				{
					$widthCity='55%';
					$widthState='25%';
					$widthZIP='20%';
				}
				else
				{
					$widthCity='50%';
					$widthZIP='20%';
					$widthCountry='30%';
				}

				// City
				$c.='<div style="display: inline-block; width: '.$widthCity.'">';
				$c.='<input';
					$c.=' type="text"';
					$c.=' id="'.$this->tag.'_city"';
					$c.=' name="'.$this->tag.'_city"';
					$c.=' value="'.$valueCity.'"';
					$c.=' data-originalvalue="'.$valueCity.'"';
					$c.=' placeholder="[[ FLASK.COMMON.Address.City ]]"';
					$c.=' autocomplete="off"';
					$c.=' class="'.join(' ',$class).'"';
					$c.=' maxlength="255"';
					if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
					if ($this->getParam('readonly') || $this->getParam('form_readonly')) $c.=' readonly="readonly"';
					if ($this->getParam('disabled') || $this->getParam('form_disabled')) $c.=' disabled="disabled"';
					if ($this->getParam('form_keephiddenvalue')) $c.=' data-keephiddenvalue="1"';
					if ($this->getParam('form_event'))
					{
						foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
					}
				$c.='>';
				$c.='</div>';

				// State
				if ($this->getParam('state'))
				{
					$c.='<div style="display: inline-block; width: '.$widthState.'; padding-left: 10px">';
					$c.='<input';
						$c.=' type="text"';
						$c.=' id="'.$this->tag.'_state"';
						$c.=' name="'.$this->tag.'_state"';
						$c.=' value="'.$valueState.'"';
						$c.=' data-originalvalue="'.$valueState.'"';
						$c.=' placeholder="[[ FLASK.COMMON.Address.State ]]"';
						$c.=' autocomplete="off"';
						$c.=' class="'.join(' ',$class).'"';
						$c.=' maxlength="255"';
						if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
						if ($this->getParam('readonly') || $this->getParam('form_readonly')) $c.=' readonly="readonly"';
						if ($this->getParam('disabled') || $this->getParam('form_disabled')) $c.=' disabled="disabled"';
						if ($this->getParam('form_keephiddenvalue')) $c.=' data-keephiddenvalue="1"';
						if ($this->getParam('form_event'))
						{
							foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
						}
					$c.='>';
					$c.='</div>';
				}

				// ZIP
				$c.='<div style="display: inline-block; width: '.$widthZIP.'; padding-left: 10px">';
				$c.='<input';
					$c.=' type="text"';
					$c.=' id="'.$this->tag.'_zip"';
					$c.=' name="'.$this->tag.'_zip"';
					$c.=' value="'.$valueZIP.'"';
					$c.=' data-originalvalue="'.$valueZIP.'"';
					$c.=' placeholder="[[ FLASK.COMMON.Address.ZIP ]]"';
					$c.=' autocomplete="off"';
					$c.=' class="'.join(' ',$class).'"';
					$c.=' maxlength="20"';
					if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
					if ($this->getParam('maxlength')) $c.=' maxlength="'.$this->getParam('maxlength').'"';
					if ($this->getParam('readonly') || $this->getParam('form_readonly')) $c.=' readonly="readonly"';
					if ($this->getParam('disabled') || $this->getParam('form_disabled')) $c.=' disabled="disabled"';
					if ($this->getParam('form_keephiddenvalue')) $c.=' data-keephiddenvalue="1"';
					if ($this->getParam('form_event'))
					{
						foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
					}
				$c.='>';
				$c.='</div>';

				// Country
				if ($this->getParam('country')!==false)
				{
					$c.='<div style="display: inline-block; width: '.$widthCountry.'; padding-left: 10px">';
					$c.='<select';
						$c.=' id="'.$this->tag.'_country"';
						$c.=' name="'.$this->tag.'_country"';
						$c.=' data-originalvalue="'.$valueCountry.'"';
						$c.=' autocomplete="off"';
						$c.=' class="'.join(' ',$class).'"';
						if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
						if ($this->getParam('maxlength')) $c.=' maxlength="'.$this->getParam('maxlength').'"';
						if ($this->getParam('readonly') || $this->getParam('form_readonly')) $c.=' readonly="readonly"';
						if ($this->getParam('disabled') || $this->getParam('form_disabled')) $c.=' disabled="disabled"';
						if ($this->getParam('form_keephiddenvalue')) $c.=' data-keephiddenvalue="1"';
						if ($this->getParam('form_event'))
						{
							foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
						}
					$c.='>';
					$c.='<option value="">---</option>';
					$c.=FlaskPHP\Util::arrayToSelectOptions(oneof($this->getParam('country_list'),FlaskPHP\I18n\CountryData::getCountryList()),$valueCountry);
					$c.='</select>';
					$c.='</div>';
				}

			// Row 2 ends
			$c.='</div>';

			// Comment
			$c.=$this->renderComment();

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The address field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class AddressField extends FieldInterface
	{


		/**
		 *
		 *   Show state field
		 *   ----------------
		 *   @access public
		 *   @param bool $state Show state field
		 *   @return AddressField
		 *
		 */

		public function setEnableState( bool $state )
		{
			$this->setParam('state',$state);
			return $this;
		}


		/**
		 *
		 *   Show country field
		 *   ------------------
		 *   @access public
		 *   @param bool $country SHow country field
		 *   @return AddressField
		 *
		 */

		public function setEnableCountry( bool $country )
		{
			$this->setParam('country',$country);
			return $this;
		}


		/**
		 *
		 *   Set default city value
		 *   ----------------------
		 *   @access public
		 *   @param string $defaultCity Default city value
		 *   @return AddressField
		 *
		 */

		public function setDefaultCity( string $defaultCity )
		{
			$this->setParam('default_city',$defaultCity);
			return $this;
		}


		/**
		 *
		 *   Set default state value
		 *   -----------------------
		 *   @access public
		 *   @param string $defaultState Default state value
		 *   @return AddressField
		 *
		 */

		public function setDefaultState( string $defaultState )
		{
			$this->setParam('default_state',$defaultState);
			return $this;
		}


		/**
		 *
		 *   Set default ZIP code value
		 *   --------------------------
		 *   @access public
		 *   @param string $defaultZIP Default state value
		 *   @return AddressField
		 *
		 */

		public function setDefaultZIP( string $defaultZIP )
		{
			$this->setParam('default_zip',$defaultZIP);
			return $this;
		}


		/**
		 *
		 *   Set default country value
		 *   -------------------------
		 *   @access public
		 *   @param string $defaultCountry Default country value
		 *   @return AddressField
		 *
		 */

		public function setDefaultCountry( string $defaultCountry )
		{
			$this->setParam('default_country',$defaultCountry);
			return $this;
		}


		/**
		 *
		 *   Set display separator
		 *   ---------------------
		 *   @access public
		 *   @param string $separator Separator (false for returning an array)
		 *   @return AddressField
		 *
		 */

		public function setDisplaySeparator( string $separator )
		{
			$this->setParam('separator',$separator);
			return $this;
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
			// Values
			$value=$valueCity=$valueZIP=$valueState=$valueCountry=null;
			if (is_object($this->formObject) && is_object($this->formObject->model) && $this->formObject->model->_loaded)
			{
				$value=$this->escapeValue($this->formObject->model->{$this->tag});
				$valueCity=$this->escapeValue($this->formObject->model->{$this->tag.'_city'});
				$valueZIP=$this->escapeValue($this->formObject->model->{$this->tag.'_zip'});
				$valueState=$this->escapeValue($this->formObject->model->{$this->tag.'_state'});
				$valueCountry=$this->escapeValue($this->formObject->model->{$this->tag.'_country'});
			}
			elseif (!is_object($this->formObject) && is_object($this->modelObject) && $this->modelObject->_loaded)
			{
				$value=$this->escapeValue($this->modelObject->{$this->tag});
				$valueCity=$this->escapeValue($this->modelObject->{$this->tag.'_city'});
				$valueZIP=$this->escapeValue($this->modelObject->{$this->tag.'_zip'});
				$valueState=$this->escapeValue($this->modelObject->{$this->tag.'_state'});
				$valueCountry=$this->escapeValue($this->modelObject->{$this->tag.'_country'});
			}

			// Init
			$address=array();

			// Line 1: street
			if ($value)
			{
				$address[]=$value;
			}

			// Line 2:
			$addressLine2=array();
			if ($valueCity) $addressLine2[]=$valueCity;
			if ($this->getParam('state') && $valueState) $addressLine2[]=$valueState;
			if ($valueZIP) $addressLine2[]=$valueZIP;
			if ($this->getParam('country')!==false && $valueCountry) $addressLine2[]=FlaskPHP\I18n\CountryData::getName($valueCountry);
			if (sizeof($addressLine2))
			{
				$address[]=join(', ',$addressLine2);
			}

			// Return
			$separator=oneof($this->getParam('separator'),'<br>');
			return join($separator,$address);
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
			$saveValue=array();
			$saveValue[$this->tag]=Flask()->Request->postVar($this->tag);
			$saveValue[$this->tag.'_city']=Flask()->Request->postVar($this->tag.'_city');
			$saveValue[$this->tag.'_zip']=Flask()->Request->postVar($this->tag.'_zip');
			if ($this->getParam('state')) $saveValue[$this->tag.'_state']=Flask()->Request->postVar($this->tag.'_state');
			if ($this->getParam('country')!==false) $saveValue[$this->tag.'_country']=Flask()->Request->postVar($this->tag.'_country');
			return $saveValue;
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

		public function renderFormBeginningBlock( $value, int $row=null )
		{
			// Default simple wrapper
			return '<div id="fieldgroup_'.$this->tag.'" class="grouped fields '.$this->getParam('form_wrapperclass').'">';
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

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');

			// Class
			$class=array();
			if (!empty($this->getParam('form_fieldclass'))) $class[]=$this->getParam('form_fieldclass');

			// Fields
			$fieldList=array();
			$fieldList[$this->tag.'_city']=$valueCity;
			if ($this->getParam('state')) $fieldList[$this->tag.'_state']=$valueState;
			$fieldList[$this->tag.'_zip']=$valueZIP;

			// Placeholders
			$fieldPlaceholder=array();
			$fieldPlaceholder[$this->tag.'_city']='[[ FLASK.COMMON.Address.City ]]';
			if ($this->getParam('state')) $fieldPlaceholder[$this->tag.'_state']='[[ FLASK.COMMON.Address.State ]]';;
			$fieldPlaceholder[$this->tag.'_zip']='[[ FLASK.COMMON.Address.ZIP ]]';

			// Widths
			if ($this->getParam('state'))
			{
				$fieldWidth=array();
				$fieldWidth[$this->tag.'_city']='eight';
				$fieldWidth[$this->tag.'_state']='four';
				$fieldWidth[$this->tag.'_zip']='four';
			}
			else
			{
				$fieldWidth=array();
				$fieldWidth[$this->tag.'_city']='twelve';
				$fieldWidth[$this->tag.'_zip']='four';
			}

			// Main field
			$c='<div class="field">';

				$c.='<input';
				$c.=' type="text"';
				$c.=' autocomplete="off"';
				$c.=' placeholder="[[ FLASK.COMMON.Address.Street ]]"';
				$c.=' value="'.$value.'"';
				$c.=' id="'.$this->tag.'"';
				$c.=' name="'.$this->tag.'"';
				$c.=' data-originalvalue="'.$value.'"';
				$c.=' class="'.join(' ',$class).'"';
				if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
				if ($this->getParam('maxlength')) $c.=' maxlength="'.$this->getParam('maxlength').'"';
				if ($this->getParam('readonly') || $this->getParam('form_readonly')) $c.=' readonly="readonly"';
				if ($this->getParam('disabled') || $this->getParam('form_disabled')) $c.=' disabled="disabled"';
				if ($this->getParam('form_keephiddenvalue')) $c.=' data-keephiddenvalue="1"';
				if ($this->getParam('form_data'))
				{
					foreach ($this->getParam('form_data') as $dataKey => $dataValue) $c.=' data-'.$dataKey.'="'.htmlspecialchars($dataValue).'"';
				}
				if ($this->getParam('form_event'))
				{
					foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
				}

			$c.='</div>';

			// Other fields
			$c.='<div class="fields mb-0">';
			foreach ($fieldList as $fieldTag => $fieldValue)
			{
				$c.='<div class="'.$fieldWidth[$fieldTag].' wide field">';
				$c.='<input';
				$c.=' type="text"';
				$c.=' autocomplete="off"';
				$c.=' placeholder="'.$fieldPlaceholder[$fieldTag].'"';
				$c.=' value="'.$fieldValue.'"';
				$c.=' class="'.join(' ',$class).'"';
				$c.=' id="'.$fieldTag.'"';
				$c.=' name="'.$fieldTag.'"';
				$c.=' data-originalvalue="'.$fieldValue.'"';
				if ($this->getParam('form_event'))
				{
					foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
				}
				$c.='>';
				$c.='</div>';
			}
			$c.='</div>';

			// Country field
			if ($this->getParam('country')!==false)
			{
				$c.='<select';
				$c.=' class="ui dropdown '.join(' ',$class).'"';
				$c.=' id="'.$this->tag.'_country"';
				$c.=' name="'.$this->tag.'_country"';
				$c.=' data-originalvalue="'.$valueCountry.'"';
				if ($this->getParam('form_event'))
				{
					foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
				}
				$c.='>';
				$c.='<option value="">--- [[ FLASK.COMMON.Address.Country ]] ---</option>';
				$c.=FlaskPHP\Util::arrayToSelectOptions(oneof($this->getParam('country_list'),FlaskPHP\I18n\CountryData::getCountryList()),$valueCountry);
				$c.='</select>';
			}

			// Comment
			$c.=$this->renderComment();

			// Return
			$c.='</div>';
			return $c;
		}


	}


?>
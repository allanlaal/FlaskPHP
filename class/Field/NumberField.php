<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The number field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class NumberField extends TextField
	{


		/**
		 *
		 *   Set minimum field value
		 *   -----------------------
		 *   @access public
		 *   @param int $minValue Minimum value
		 *   @return \Codelab\FlaskPHP\Field\NumberField
		 *
		 */

		public function setMinimumLength( int $minValue )
		{
			$this->setParam('minvalue',$minValue);
			return $this;
		}


		/**
		 *
		 *   Set maximum field length
		 *   ------------------------
		 *   @access public
		 *   @param int $maxValue Maximum value
		 *   @return \Codelab\FlaskPHP\Field\NumberField
		 */

		public function setMaximumValue( int $maxValue )
		{
			$this->setParam('maxvalue',$maxValue);
			return $this;
		}


		/**
		 *
		 *   Set precision
		 *   -------------
		 *   @access public
		 *   @param int $precision Precision
		 *   @return \Codelab\FlaskPHP\Field\NumberField
		 */

		public function setPrecision( int $precision )
		{
			$this->setParam('precision',$precision);
			return $this;
		}


		/**
		 *
		 *   Allow negative value?
		 *   ---------------------
		 *   @access public
		 *   @param bool $allowNegative Allow negative value
		 *   @return \Codelab\FlaskPHP\Field\NumberField
		 */

		public function setAllowNegative( bool $allowNegative )
		{
			$this->setParam('allownegative',$allowNegative);
			return $this;
		}


		/**
		 *
		 *   Show zero value?
		 *   ----------------
		 *   @access public
		 *   @param bool $showZeroValue Show zero value
		 *   @return \Codelab\FlaskPHP\Field\NumberField
		 */

		public function setShowZeroValue( bool $showZeroValue )
		{
			$this->setParam('showzerovalue',$showZeroValue);
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
			// Change , -> . and eliminate whitespace
			$value=trim(preg_replace("/\s+/",'',str_replace(',','.',$value)));

			// Check for format
			if (mb_strlen($value))
			{
				if ($this->getParam('allownegative') && $this->getParam('precision')>0)
				{
					$filter='/^([\-]){0,1}[0-9]+(\.([0-9]){1,'.intval($this->getParam('precision')).'}){0,1}$/';
				}
				elseif ($this->getParam('allownegative'))
				{
					$filter='/^([\-]){0,1}[0-9]+$/';
				}
				elseif ($this->getParam('precision')>0)
				{
					$filter='/^[0-9]+(\.([0-9]){1,'.intval($this->getParam('precision')).'}){0,1}$/';
				}
				else
				{
					$filter='/^[0-9]+$/';
				}
				if (!preg_match($filter,$value))
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => '[[ FLASK.FIELD.Error.NumberFld.Format ]]'
					]);
				}
			}

			// Required and empty?
			if ($this->required())
			{
				if (!floatval($value))
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => '[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]'
					]);
				}
			}

			// No point in checking further if empty
			if (!floatval($value)) return;

			// Under minimum value
			if ($this->getParam('minvalue') && round($value,intval($this->getParam('precision')))<round($this->getParam('minvalue'),intval($this->getParam('precision'))))
			{
				throw new FlaskPHP\Exception\ValidateException([
					$this->tag => '[[ FLASK.FIELD.Error.MinValue : minvalue='.intval($this->getParam('minvalue')).' ]]'
				]);
			}

			// Over maximum value
			if ($this->getParam('maxvalue') && round($value,intval($this->getParam('precision')))>round($this->getParam('maxvalue'),intval($this->getParam('precision'))))
			{
				throw new FlaskPHP\Exception\ValidateException([
					$this->tag => '[[ FLASK.FIELD.Error.MaxValue: maxvalue='.intval($this->getParam('maxvalue')).' ]]'
				]);
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
			$value=$this->getValue();
			if (!floatval($value) && !$this->getParam('showzerovalue')) return '';
			if ($this->getParam('precision'))
			{
				return sprintf("%.0".$this->getParam('precision')."f",$value);
			}
			else
			{
				return intval($value);
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
			// Format value
			if (floatval($value) || $this->getParam('showzerovalue'))
			{
				if ($this->getParam('precision'))
				{
					$value=sprintf("%.0".$this->getParam('precision')."f",$value);
				}
				else
				{
					$value=intval($value);
				}
			}
			else
			{
				$value='';
			}

			// Return
			return parent::listValue($value,$row);
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The Bootstrap fields trait
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	trait BootstrapField
	{


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
			return '<div id="field_'.$this->tag.'" class="form-group row">';
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
			if ($this->getParam('required')=='always' || (is_object($this->formObject) && $this->getParam('required')==$this->formObject->operation) || $this->getParam('required_if'))
			{
				$c.='<span class="icon-asterisk text-muted"></span> ';
			}
			$c.=$this->getTitle().':';
			$c.='</label>';
			return $c;
		}


		/**
		 *
		 *   Render form field: comment
		 *   --------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderComment()
		{
			$c='';
			if ($this->getParam('form_comment'))
			{
				$c='<small class="form-text text-muted">'.$this->getParam('form_comment').'</small>';
			}
			return $c;
		}


		/**
		 *
		 *   Render form field: ending block
		 *   -------------------------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param int $row Row (for multi-row fields)
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFormEndingBlock( $value, int $row=null )
		{
			// Default simple wrapper
			return '</div>';
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The chooser form field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class ChooserField extends FieldInterface
	{


		/**
		 *
		 *   Allow multiple options
		 *   ----------------------
		 *   @access public
		 *   @param bool $multiple Allow multiple options
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setAllowMultiple( bool $multiple )
		{
			$this->setParam('multiple',$multiple);
			return $this;
		}


		/**
		 *
		 *   Set chooser search URL
		 *   ----------------------
		 *   @access public
		 *   @param string $chooserURL Chooser search URL
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserURL( string $chooserURL )
		{
			$this->setParam('chooser_url',$chooserURL);
			return $this;
		}


		/**
		 *
		 *   Set chooser dialog title
		 *   ------------------------
		 *   @access public
		 *   @param string $chooserTitle Search title
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserTitle( string $chooserTitle )
		{
			$this->setParam('chooser_title',$chooserTitle);
			return $this;
		}


		/**
		 *
		 *   Set chooser dialog search placeholder
		 *   -------------------------------------
		 *   @access public
		 *   @param string $chooserPlaceholder Search placeholder
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserPlaceholder( string $chooserPlaceholder )
		{
			$this->setParam('chooser_placeholder',$chooserPlaceholder);
			return $this;
		}


		/**
		 *
		 *   Set chooser empty value label
		 *   -----------------------------
		 *   @access public
		 *   @param string $chooserEmptyValue Empty value label
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserEmptyValue( string $chooserEmptyValue )
		{
			$this->setParam('chooser_emptyvalue',$chooserEmptyValue);
			return $this;
		}


		/**
		 *
		 *   Set allow clearing of value
		 *   ---------------------------
		 *   @access public
		 *   @param string $chooserClear Clear value button label
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserClear( string $chooserClear )
		{
			$this->setParam('chooser_clear',$chooserClear);
			return $this;
		}


		/**
		 *
		 *   Set choose button title
		 *   -----------------------
		 *   @access public
		 *   @param string $chooseButtonTitle Choose button title
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooseButtonTitle( string $chooseButtonTitle )
		{
			$this->setParam('chooser_choosebuttontitle',$chooseButtonTitle);
			return $this;
		}


		/**
		 *
		 *   Set chooser query data
		 *   ----------------------
		 *   @access public
		 *   @param array $chooserData Search query data
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserData( array $chooserData )
		{
			$this->setParam('chooser_data',$chooserData);
			return $this;
		}


		/**
		 *
		 *   Set choose button title
		 *   -----------------------
		 *   @access public
		 *   @param string $chooserAddFormButtonTitle Add form button title
		 *   @param string $chooserAddFormURL Add form URL
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserAddForm( string $chooserAddFormButtonTitle, string $chooserAddFormURL )
		{
			$this->setParam('chooser_addform_buttontitle',$chooserAddFormButtonTitle);
			$this->setParam('chooser_addform_url',$chooserAddFormURL);
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
			return htmlspecialchars($this->getValue());
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
			// TODO: finish layoutless mock
		}


	}


?>
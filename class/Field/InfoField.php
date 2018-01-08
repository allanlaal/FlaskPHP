<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The info field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class InfoField extends FieldInterface
	{


		/**
		 *
		 *   Set checkbox title
		 *   ------------------
		 *   @access public
		 *   @param string $checboxTitle Checkbox title
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Field\InfoField
		 *
		 */

		public function setContent( string $content, bool $encodeContent=false )
		{
			$this->setParam('content',$content);
			$this->setParam('encodecontent',$encodeContent);
			return $this;
		}


		/**
		 *
		 *   No save?
		 *   --------
		 *   @access public
		 *   @return boolean
		 *
		 */

		public function noSave()
		{
			return true;
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
			return $this->getParam('content');
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
			return $this->getParam('content');
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
			// Wrapper
			$c='<div class="ui segment '.$this->getParam('form_fieldclass').'">';

			// Content
			if ($this->getParam('encodecontent'))
			{
				$c.=htmlspecialchars($this->getParam('content'));
			}
			else
			{
				$c.=$this->getParam('content');
			}

			// Wrapper
			$c.='</div>';

			// Return
			return $c;
		}


	}


?>
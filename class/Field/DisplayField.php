<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The display field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class DisplayField extends FieldInterface
	{


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @access public
		 *   @param string $tag Field tag
		 *   @return \Codelab\FlaskPHP\Field\DisplayField
		 *
		 */

		public function __construct( string $tag=null )
		{
			parent::__construct($tag);
			$this->setNoSave(true);
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

			// Field
			$c='<input';
				$c.=' type="hidden"';
				$c.=' id="'.$this->tag.'"';
				$c.=' name="'.$this->tag.'"';
				$c.=' value="'.$value.'"';
				$c.=' data-originalvalue="'.$value.'"';
				$c.=' data-keephiddenvalue="1"';
				if ($this->getParam('form_event'))
				{
					foreach ($this->getParam('form_event') as $eventType => $eventContent) $c.=' '.$eventType.'="'.$eventContent.'"';
				}
				if ($this->getParam('form_data'))
				{
					foreach ($this->getParam('form_data') as $dataKey => $dataValue) $c.=' data-'.$dataKey.'="'.htmlspecialchars($dataValue).'"';
				}
			$c.='>';

			$c.=$value;

			// Comment
			$c.=$this->renderComment();

			// Return
			return $c;
		}


	}


?>
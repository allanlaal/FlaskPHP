<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The chooser field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class ChooserField extends FlaskPHP\Field\ChooserField
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
			// Calculate widths
			$labelWidth=oneof($this->getParam('form_labelwidth'),3);
			$elementWidth=round(12-$labelWidth);

			// Wrapper
			$c='<div class="col-md-'.$elementWidth.'">';

			// Field
			$c.='<input';
				$c.=' type="hidden"';
				$c.=' id="'.$this->tag.'"';
				$c.=' name="'.$this->tag.'"';
				$c.=' value="'.$value.'"';
				$c.=' data-originalvalue="'.$value.'"';
				$c.=' class="form-control"';
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

			// Value
			$c.='<div class="clearfix">';
			$c.='<div class="float-left col-form-label chooser-value">';
				if (!empty($value))
				{
					$c.=$this->displayValue();
				}
				else
				{
					$c.='<div class="chooser-emptyvalue text-muted">'.oneof($this->getParam('chooser_emptyvalue'),'[[ FLASK.COMMON.NotSet ]]').'</div>';
				}
			$c.='</div>';

			// Actions
			$c.='<div class="float-right chooser-actions">';

				// Clear
				if ($this->getParam('chooser_clear'))
				{
					$param=new \stdClass();
					$param->emptyvalue=oneof($this->getParam('chooser_emptyvalue'),'[[ FLASK.COMMON.NotSet ]]');
					$param=FlaskPHP\Util::htmlJSON($param);
					$c.='<button type="button" class="btn btn-secondary" onclick="Flask.Chooser.clearChooser(\''.$this->tag.'\','.$param.')">'.$this->getParam('chooser_clear').'</button>';
				}

				// Search
				$param=new \stdClass();
				$param->search_url=$this->getParam('chooser_url');
				$param->search_title=oneof($this->getParam('chooser_title'),'[[ FLASK.FIELD.Chooser.Search.Title ]]');
				$param->search_placeholder=oneof($this->getParam('chooser_title'),'[[ FLASK.FIELD.Chooser.Search.Placeholder ]]');
				$param->search_multiple=($this->getParam('multiple')?true:false);
				$param=FlaskPHP\Util::htmlJSON($param);
				if ($this->getParam('chooser_data'))
				{
					$data=FlaskPHP\Util::htmlJSON($this->getParam('chooser_data'));
				}
				else
				{
					$data='{}';
				}
				$c.='<button type="button" class="btn btn-secondary" onclick="Flask.Chooser.openModal(\''.$this->tag.'\','.$param.','.$data.')">'.oneof($this->getParam('chooser_choosebuttontitle'),'<span class="icon-search"></span>').'</button>';

			$c.='</div>';
			$c.='</div>';

			// Comment
			$c.=$this->renderComment();

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


	}


?>
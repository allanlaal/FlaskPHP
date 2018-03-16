<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The radiobutton list field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class RadioListField extends FieldInterface
	{


		/**
		 *   Validate value
		 *   @var bool
		 *   @access public
		 */

		public $validateValueExists = false;


		/**
		 *
		 *   Get log data
		 *   ------------
		 *   @access public
		 *   @param FlaskPHP\Model\LogData $logData Log data object
		 *   @param FlaskPHP\Model\ModelInterface $model Model object
		 *   @param FlaskPHP\Action\FormAction $form Form object
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function getLogData( FlaskPHP\Model\LogData $logData, FlaskPHP\Model\ModelInterface $model, FlaskPHP\Action\FormAction $form=null )
		{
			// Unset from log values
			$logData->setHandled($this->tag);

			// Get options
			$options=$this->getOptions();

			// Check if we need to log
			if ($logData->operation!='edit' && !mb_strlen($model->{$this->tag})) return;
			if ($logData->operation=='edit')
			{
				if ($model->{$this->tag}==$model->_in[$this->tag]) return;
				if ($model->{$this->tag}=='' && $model->_in[$this->tag]=='0') return;
				if ($model->{$this->tag}=='0' && $model->_in[$this->tag]=='') return;
			}

			// Compose log entry
			$fieldLogData=new \stdClass();
			$fieldLogData->id=$this->tag;
			$fieldLogData->name=$this->getTitle();
			if (in_array($logData->operation,array('add','edit')))
			{
				if ($logData->operation!='add' && !$this->getParam('forcevalue'))
				{
					$fieldLogData->old_value=$model->_in[$this->tag];
					if (!empty($options[$model->_in[$this->tag]])) $fieldLogData->old_description=$options[$model->_in[$this->tag]];
				}
				$fieldLogData->new_value=$model->{$this->tag};
				if (!empty($options[$model->{$this->tag}])) $fieldLogData->new_description=$options[$model->{$this->tag}];
			}
			else
			{
				$fieldLogData->value=$model->{$this->tag};
				if (!empty($options[$model->{$this->tag}])) $fieldLogData->description=$options[$model->{$this->tag}];
			}

			// Add log entry
			$logData->addData($this->tag,$fieldLogData);
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
			$options=$this->getOptions();
			return htmlspecialchars($options[$value]);
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
			$options=$this->getOptions();
			$value=$options[$this->getValue()];
			if ($encodeContent) htmlspecialchars($value);
			return $value;
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
			return '<div id="field_'.$this->tag.'" class="grouped fields '.$this->getParam('form_wrapperclass').'">';
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
			// Options
			$options=$this->getOptions();

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');

			// Class
			$class=array();
			if ($this->getParam('form_fieldclass')) $class[]=$this->getParam('form_fieldclass');

			// Display options
			$c='<div class="ui radiochecklist segment">';
			foreach ($options as $val => $description)
			{

				$c.='<div class="field" style="'.$style.'">';
				$c.='<div class="ui radio checkbox">';
				$c.='<input';
				$c.=' type="radio"';
				$c.=' id="'.$this->tag.'_'.$val.'"';
				$c.=' name="'.$this->tag.'"';
				$c.=' value="'.htmlspecialchars($val).'"';
				if ($val==$value) $c.=' checked="checked"';
				if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
				if ($this->getParam('readonly') || $this->getParam('form_readonly')) $c.=' readonly="readonly"';
				if ($this->getParam('disabled') || $this->getParam('form_disabled')) $c.=' disabled="disabled"';
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
				$c.='<label for="'.$this->tag.'_'.$val.'">'.htmlspecialchars($description).'</label>';
				$c.='</div>';
				$c.='</div>';
			}
			$c.='</div>';

			// Field ends
			return $c;
		}


	}


?>
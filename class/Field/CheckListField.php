<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The checklist field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class CheckListField extends FieldInterface
	{


		/**
		 *   Validate value
		 *   @var bool
		 *   @access public
		 */

		public $validateValueExists = false;


		/**
		 *
		 *   Set option grouping
		 *   -------------------
		 *   @access public
		 *   @param bool $grouping Grouping enabled
		 *   @return \Codelab\FlaskPHP\Field\CheckListField
		 *
		 */

		public function setGrouping( bool $grouping )
		{
			$this->setParam('optgroup',$grouping);
			return $this;
		}


		/**
		 *
		 *   Set separate items
		 *   ------------------
		 *   @access public
		 *   @param bool $separateItems Load/save as separate items
		 *   @return \Codelab\FlaskPHP\Field\CheckListField
		 *
		 */

		public function setSeparateItems( bool $separateItems )
		{
			$this->setParam('separateitems',$separateItems);
			return $this;
		}


		/**
		 *
		 *   Set list separator
		 *   ------------------
		 *   @access public
		 *   @param string $listSeparator
		 *   @return \Codelab\FlaskPHP\Field\CheckListField
		 *
		 */

		public function setListSeparator( string $listSeparator )
		{
			$this->setParam('list_separator',$listSeparator);
			return $this;
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
			// Init
			$options=$this->getOptions();
			if (!$this->getParam('optgroup'))
			{
				$options=array(''=>$options);
			}
			$value=str_array($value,"\t");
			$valueArray=array();
			$listSeparator=oneof($this->getParam('list_separator'),', ');

			// Compose value array
			foreach ($options as $optGroup)
			{
				foreach ($optGroup as $optVal => $optDesc)
				{
					if ($this->getParam('separateitems'))
					{
						if (!empty($row[$optVal])) $valueArray[]=$optDesc;
					}
					else
					{
						if (in_array($optVal,$value)) $valueArray[]=$optDesc;
					}
				}
			}

			// Return
			return join($listSeparator,$valueArray);
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
			// Separate items - scan
			if (!empty($this->getParam('separateitems')))
			{
				$retval=array();
				$options=$this->getOptions();
				if (!$this->getParam('optgroup'))
				{
					$options=array(''=>$options);
				}
				foreach ($options as $optGroup)
				{
					foreach ($optGroup as $optVal => $optDesc)
					{
						$retval[$optVal]=Flask()->Request->postVar($optVal);
					}
				}
				return $retval;
			}

			// Just glue to tab-separated string
			else
			{
				return join("\t",Flask()->Request->postVar($this->tag));
			}
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
			// Not required
			if (!$this->required()) return;

			// Separate items - scan to value
			if (!empty($this->getParam('separateitems')))
			{
				$value=array();
				$options=$this->getOptions();
				if (!$this->getParam('optgroup'))
				{
					$options=array(''=>$options);
				}
				foreach ($options as $optGroup)
				{
					foreach ($optGroup as $optVal => $optDesc)
					{
						if (Flask()->Request->postVar($optVal))
						{
							$value[]=$optVal;
						}
					}
				}
			}

			// Just glue to tab-separated string
			if (!is_array($value) || !sizeof($value))
			{
				throw new FlaskPHP\Exception\ValidateException([
					$this->tag => '[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]'
				]);
			}
		}


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

			// TODO: finish
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
			if (!$this->getParam('optgroup'))
			{
				$options=array(''=>$options);
			}

			// Convert value to array
			$value=str_array($value,"\t");

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');

			// Items per row
			$checkboxStyle='';
			if (!empty($this->getParam('itemsperrow')))
			{
				$checkboxStyle='float: left; width: '.floor(100/$this->param['itemsperrow']).'%';
			}

			// Iterate through groups
			$c='';
			foreach ($options as $optGroupName => $optGroupOptions)
			{
				// Title
				if (mb_strlen($optGroupName))
				{
					$c.='<div class="checklist-group">'.$optGroupName.'</div>';
				}

				// Checkboxes
				foreach ($optGroupOptions as $optionVal => $optionDescription)
				{
					if ($this->getParam('separateitems'))
					{
						$checkboxID=$optionVal;
						$checkboxName=$optionVal;
						$checkboxValue='1';
						$checkboxChecked=(!empty($this->formObject->model->{$optionVal})?true:false);
					}
					else
					{
						$checkboxID=$this->tag.'_'.$optionVal;
						$checkboxName=$this->tag.'[]';
						$checkboxValue=$optionVal;
						$checkboxChecked=(in_array($optionVal,$value)?true:false);
					}

					$c.='<div class="field" style="'.$checkboxStyle.'">';
					$c.='<div class="ui checkbox">';
					$c.='<input';
					$c.=' type="checkbox"';
					$c.=' id="'.$checkboxID.'"';
					$c.=' name="'.$checkboxName.'"';
					$c.=' value="'.htmlspecialchars($checkboxValue).'"';
					if ($checkboxChecked) $c.=' checked="checked"';
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
					$c.='<label for="'.$checkboxID.'">'.htmlspecialchars($optionDescription).'</label>';
					$c.='</div>';
				}
			}

			// Comment
			$c.=$this->renderComment();

			// Return
			return $c;
		}


	}


?>
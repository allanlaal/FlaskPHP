<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The select/dropdown field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class SelectField extends FieldInterface
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
		 *   @return \Codelab\FlaskPHP\Field\SelectField
		 *
		 */

		public function setGrouping( bool $grouping )
		{
			$this->setParam('optgroup',$grouping);
			return $this;
		}


		/**
		 *
		 *   Set form field searchable
		 *   -------------------------
		 *   @access public
		 *   @param bool $grouping Grouping enabled
		 *   @return \Codelab\FlaskPHP\Field\SelectField
		 *
		 */

		public function setFormSearchable( bool $formSearchable )
		{
			$this->setParam('form_searchable',$formSearchable);
			return $this;
		}


		/**
		 *
		 *   Set multiple select
		 *   -------------------
		 *   @access public
		 *   @param bool $grouping Grouping enabled
		 *   @return \Codelab\FlaskPHP\Field\SelectField
		 *
		 */

		public function setMultiple( bool $multiple )
		{
			$this->setParam('multiple',$multiple);
			return $this;
		}


		/**
		 *
		 *   Set HTML values
		 *   ---------------
		 *   @access public
		 *   @param bool $htmlValues HTML values?
		 *   @return \Codelab\FlaskPHP\Field\SelectField
		 *
		 */

		public function setHTMLValues( bool $htmlValues )
		{
			$this->setParam('htmlvalues',$htmlValues);
			return $this;
		}


		/**
		 *
		 *   Set empty select value
		 *   ----------------------
		 *   @access public
		 *   @param string $select Empty select value
		 *   @return \Codelab\FlaskPHP\Field\SelectField
		 *
		 */

		public function setSelect( string $select )
		{
			$this->setParam('select',$select);
			return $this;
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

			// Check if we need to log
			if ($logData->operation!='edit' && !mb_strlen($model->{$this->tag})) return;
			if ($logData->operation=='edit')
			{
				if ($model->{$this->tag}==$model->_in[$this->tag]) return;
				if ($model->{$this->tag}=='' && $model->_in[$this->tag]=='0') return;
				if ($model->{$this->tag}=='0' && $model->_in[$this->tag]=='') return;
			}

			// Get options
			$options=$this->getOptions();

			// Compose log entry
			$fieldLogData=new \stdClass();
			$fieldLogData->id=$this->tag;
			$fieldLogData->name=$this->getTitle();
			if (in_array($logData->operation,array('add','edit')))
			{
				if ($logData->operation!='add' && !$this->getParam('forcevalue'))
				{
					$fieldLogData->old_value=$model->_in[$this->tag];
					$fieldLogData->old_description=$options[$fieldLogData->old_value];
				}
				$fieldLogData->new_value=$model->{$this->tag};
				$fieldLogData->new_description=$options[$fieldLogData->new_value];
			}
			else
			{
				$fieldLogData->value=$model->{$this->tag};
				$fieldLogData->description=$options[$fieldLogData->value];
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
			if (mb_strlen($options[$value]))
			{
				return htmlspecialchars($options[$value]);
			}
			else
			{
				if (empty($value) && $this->hasParam('list_emptyvalue'))
				{
					return $this->getParam('list_emptyvalue');
				}
				return '';
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
			$options=$this->getOptions();
			$value=$options[$this->getValue()];
			if ($encodeContent) htmlspecialchars($value);
			return $value;
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

			// Selected value
			if (strval($value)=='' && !$this->getParam('select'))
			{
				if ($this->getParam('optgroup'))
				{
					foreach ($options as $optGroupName => $optGroupOptions)
					{
						$value=key($optGroupOptions);
						break;
					}
				}
				else
				{
					$value=key($options);
				}
			}

			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');

			// Class
			$class=array('ui fluid selection dropdown');
			if ($this->getParam('form_searchable')) $class[]='search';
			if ($this->getParam('form_fieldclass')) $class[]=$this->getParam('form_fieldclass');

			// Wrapper
			$c='<div class="'.join(' ',$class).'">';

				// Input element
				$c.='<input';
				$c.=' type="hidden"';
				$c.=' id="'.$this->tag.'"';
				$c.=' name="'.$this->tag.'"';
				if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
				$c.=' value="'.htmlspecialchars($value).'"';
				$c.=' data-originalvalue="'.htmlspecialchars($value).'"';
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

				// Selected item
				$c.='<div class="text">';
				if (!empty($value))
				{
					if ($this->getParam('optgroup'))
					{
						foreach ($options as $optGroupName=>$optGroupOptions)
						{
							if (array_key_exists($value,$optGroupOptions))
							{
								$c.=$this->renderItem($value,$optGroupOptions[$value],false);
								break;
							}
						}
					}
					else
					{
						$c.=$this->renderItem($value,strval($options[$value]),false);
					}
				}
				elseif ($this->getParam('select'))
				{
					$c.=$this->renderItem('','--- '.$this->getParam('select').' ---',false);
				}
				$c.='</div>';

				// Dropdown icon
				$c.='<i class="dropdown icon"></i>';

				// Options wrapper
				$c.='<div class="menu">';

					// Select
					if ($this->getParam('select'))
					{
						$c.=$this->renderItem('','--- '.$this->getParam('select').' ---');
					}

					// Options
					if ($this->getParam('optgroup'))
					{
						$o=0;
						foreach ($options as $optGroupName => $optGroupOptions)
						{
							if (!sizeof($optGroupOptions)) continue;
							if ($o)
							{
								$c.='<div class="divider"></div>';
							}
							$c.='<div class="header">'.$optGroupName.'</div>';
							$o++;
							foreach ($optGroupOptions as $val => $description)
							{
								$c.=$this->renderItem($val,$description);
							}
						}
					}
					else
					{
						foreach ($options as $val => $description)
						{
							$c.=$this->renderItem($val,$description);
						}
					}

				// Options wrapper
				$c.='</div>';

			// Dropdown ends
			$c.='</div>';

			// Field ends
			return $c;
		}


		/**
		 *
		 *   Render item
		 *   -----------
		 *   @access private
		 *   @param mixed $value Value
		 *   @param string $description Description
		 *   @param bool $wrapper Render wrapper div
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		private function renderItem( $value, string $description, bool $wrapper=true )
		{
			$c='';
			if ($wrapper)
			{
				$c.='<div class="item"';
				$c.=' data-value="'.htmlspecialchars(strval($value)).'"';
				if (!empty($value) && $this->hasParam('form_optiondata') && is_array($this->getParam('form_optiondata')[$value]))
				{
					foreach ($this->getParam('form_optiondata')[$value] as $k => $v)
					{
						$c.=' data-'.$k.'="'.htmlspecialchars($v).'"';
					}
				}
				$c.='>';
			}
			if ($this->getParam('htmlvalues'))
			{
				$c.=$description;
			}
			else
			{
				$c.=htmlspecialchars($description);
			}
			if ($wrapper)
			{
				$c.='</div>';
			}
			return $c;
		}


	}


?>
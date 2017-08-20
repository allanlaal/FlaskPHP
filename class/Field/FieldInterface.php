<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The data field interface
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class FieldInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Field tag
		 *   @var string
		 *   @access public
		 */

		public $tag = null;


		/**
		 *   Back-reference to model object
		 *   @var FlaskPHP\Model\ModelInterface
		 *   @access public
		 */

		public $modelObject = null;


		/**
		 *   Back-reference to form object
		 *   @var FlaskPHP\Action\FormAction
		 *   @access public
		 */

		public $formObject = null;


		/**
		 *   Back-reference to list object
		 *   @var FlaskPHP\Action\ListAction
		 *   @access public
		 */

		public $listObject = null;


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @access public
		 *   @param string $tag Field tag
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function __construct( string $tag=null )
		{
			if (!empty($tag)) $this->tag=$tag;
		}


		/**
		 *
		 *   Set title
		 *   ---------
		 *   @access public
		 *   @param string $title Field title
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setTitle( string $title )
		{
			$this->setParam('title',$title);
			return $this;
		}


		/**
		 *
		 *   Set required
		 *   ------------
		 *   @access public
		 *   @param string $required Required (no, always, add, edit, if)
		 *   @param string $requiredCondition Required condition
		 *   @throws FlaskPHP\Exception\InvalidParameterException
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 */

		public function setRequired( string $required, string $requiredCondition=null )
		{
			switch($required)
			{
				case 'no':
					$this->unsetParam('required');
					$this->unsetParam('required_cond');
					break;
				case 'always':
				case 'add':
				case 'edit':
					$this->setParam('required',$required);
					$this->unsetParam('required_cond');
					break;
				case 'if':
					if (empty($requiredCondition)) throw new FlaskPHP\Exception\InvalidParameterException('Condition required if type is "if"');
					$this->setParam('required',$required);
					$this->setParam('required_cond',$requiredCondition);
					break;
				default:
					throw new FlaskPHP\Exception\InvalidParameterException('Unknown required value: '.$required);
			}
			return $this;
		}


		/**
		 *
		 *   Set minimum field length
		 *   ------------------------
		 *   @access public
		 *   @param int $minLength Minimum length
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setMinimumLength( int $minLength )
		{
			$this->setParam('minlength',$minLength);
			return $this;
		}


		/**
		 *
		 *   Set maximum field length
		 *   ------------------------
		 *   @access public
		 *   @param int $maxLength Maximum length
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 */

		public function setMaximumLength( int $maxLength )
		{
			$this->setParam('maxlength',$maxLength);
			return $this;
		}


		/**
		 *
		 *   Set unique requirement
		 *   ----------------------
		 *   @access public
		 *   @param bool $unique Should be unique?
		 *   @param FlaskPHP\DB\QueryBuilderInterface $uniqueParam Unique query parameters
		 *   @param string $uniqueMessage Error message on constraint violation
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 */

		public function setUnique( bool $unique, FlaskPHP\DB\QueryBuilderInterface $uniqueParam=null, string $uniqueMessage=null )
		{
			if ($unique)
			{
				$this->setParam('unique',true);
				$this->setParam('unique_param',$uniqueParam);
				$this->setParam('unique_message',$uniqueMessage);
			}
			else
			{
				$this->unsetParam('unique');
				$this->unsetParam('unique_param');
				$this->unsetParam('unique_message');
			}
			return $this;
		}


		/**
		 *
		 *   Set filter
		 *   ----------
		 *   @access public
		 *   @param string $filter Filter (regexp)
		 *   @param string $filterMessage Filter message on violation
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setFilter( string $filter, string $filterMessage=null )
		{
			$this->setParam('filter',$filter);
			$this->setParam('filter_message',$filterMessage);
			return $this;
		}


		/**
		 *
		 *   Set field options source
		 *   ------------------------
		 *   @access public
		 *   @param string $source Source type
		 *   @throws FlaskPHP\Exception\InvalidParameterException
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setSource( string $source )
		{
			switch($source)
			{
				case 'list':
				case 'query':
				case 'model':
				case 'func':
					$this->setParam('source',$source);
					break;
				default:
					throw new FlaskPHP\Exception\InvalidParameterException('Unknown source: '.$source);
			}
			return $this;
		}


		/**
		 *
		 *   Set source list
		 *   ---------------
		 *   @access public
		 *   @param array $sourceList Source list
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setSourceList( array $sourceList )
		{
			$this->setParam('source_list',$sourceList);
			return $this;
		}


		/**
		 *
		 *   Set source model
		 *   ----------------
		 *   @access public
		 *   @param string|FlaskPHP\Model\ModelInterface $sourceModel source data object
		 *   @param string $keyField Key field
		 *   @param string $valueField Value field
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Additional load parameters
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setSourceModel( $sourceModel, string $keyField, string $valueField, FlaskPHP\DB\QueryBuilderInterface $param=null )
		{
			$this->setParam('source','model');
			$this->setParam('source_model',$sourceModel);
			$this->setParam('source_keyfield',$keyField);
			$this->setParam('source_valuefield',$valueField);
			$this->setParam('source_param',$param);
			return $this;
		}


		/**
		 *
		 *   Set source list
		 *   ---------------
		 *   @access public
		 *   @param string $sourceQuery Source query
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setSourceQuery( $sourceQuery )
		{
			$this->setParam('source_query',$sourceQuery);
			return $this;
		}


		/**
		 *
		 *   Set additional option data
		 *   --------------------------
		 *   @access public
		 *   @param array $optionData Option data
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setFormOptionData( array $optionData )
		{
			$this->setParam('form_optiondata',$optionData);
			return $this;
		}


		/**
		 *
		 *   Set additional field element data
		 *   ---------------------------------
		 *   @access public
		 *   @param array $data Field element data data
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setFormData( array $data )
		{
			$this->setParam('form_data',$data);
			return $this;
		}


		/**
		 *
		 *   Set default value
		 *   -----------------
		 *   @access public
		 *   @param mixed $default Default value
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setDefault( $default )
		{
			$this->setParam('default',$default);
			return $this;
		}


		/**
		 *
		 *   Set value
		 *   ---------
		 *   @access public
		 *   @param mixed $value Value
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setValue( $value )
		{
			$this->setParam('value',$value);
			return $this;
		}


		/**
		 *
		 *   Set no save
		 *   -----------
		 *   @access public
		 *   @param bool $noSave No save
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setNoSave( bool $noSave )
		{
			$this->setParam('nosave',$noSave);
			return $this;
		}


		/**
		 *
		 *   Set no log
		 *   ----------
		 *   @access public
		 *   @param bool $noLog No log
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setNoLog( bool $noLog )
		{
			$this->setParam('nolog',$noLog);
			return $this;
		}


		/**
		 *
		 *   Set field to disabled
		 *   ---------------------
		 *   @access public
		 *   @param bool $disabled Is disabled?
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setDisabled( bool $disabled )
		{
			$this->setParam('disabled',$disabled);
			return $this;
		}


		/**
		 *
		 *   Set read-only
		 *   -------------
		 *   @access public
		 *   @param bool $readOnly Is read-only?
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setReadOnly( bool $readOnly )
		{
			$this->setParam('readonly',$readOnly);
			return $this;
		}


		/**
		 *
		 *   Set form comment
		 *   ----------------
		 *   @access public
		 *   @param string $comment Comment
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setFormComment( string $comment )
		{
			$this->setParam('form_comment',$comment);
			return $this;
		}


		/**
		 *
		 *   Set form field placeholder text
		 *   -------------------------------
		 *   @access public
		 *   @param string $placeHolder Placeholder text
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setPlaceholder( $placeHolder )
		{
			$this->setParam('form_placeholder',$placeHolder);
			return $this;
		}


		/**
		 *
		 *   Add form event
		 *   --------------
		 *   @access public
		 *   @param string $eventType Event type
		 *   @param string $eventAction Event action
		 *   @param bool $runOnStartup Run event on startup?
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function addFormEvent( string $eventType, string $eventAction, bool $runOnStartup=false )
		{
			// Add
			if ($this->hasParam('form_event') && array_key_exists($eventType,$this->_param['form_event']))
			{
				$this->_param['form_event'][$eventType].=$eventAction;
			}

			// Set
			else
			{
				$this->_param['form_event'][$eventType]=$eventAction;
			}

			// Run on startup
			if ($runOnStartup && is_object($this->formObject))
			{
				if ($this->formObject instanceof FlaskPHP\Action\ModalFormAction)
				{
					$this->formObject->setDisplayTrigger($eventAction);
				}
				elseif ($this->formObject instanceof FlaskPHP\Action\FormAction)
				{
					$this->formObject->addJS($eventAction);
				}
			}
			return $this;
		}


		/**
		 *
		 *   Get title
		 *   ---------
		 *   @access public
		 *   @return string
		 *
		 */

		public function getTitle()
		{
			return $this->getParam('title');
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
			return ($this->getParam('nosave')?true:false);
		}


		/**
		 *
		 *   No log?
		 *   -------
		 *   @access public
		 *   @return boolean
		 *
		 */

		public function noLog()
		{
			return ($this->getParam('nolog')?true:false);
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
			// Required and empty?
			if ($this->required())
			{
				if (!mb_strlen($value))
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => '[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]'
					]);
				}
				if ((is_int($value) || is_float($value) || is_numeric($value)) && empty($value))
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => '[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]'
					]);
				}
			}

			// No point in checking further if empty
			if (empty($value)) return;

			// Minimum length not met
			if ($this->getParam('minlength') && mb_strlen($value)<$this->getParam('minlength'))
			{
				throw new FlaskPHP\Exception\ValidateException([
					$this->tag => '[[ FLASK.FIELD.Error.MinLength : minlength='.intval($this->getParam('minlength')).' ]]'
				]);
			}

			// Over maximum length
			if ($this->getParam('maxlength') && mb_strlen($value)>$this->getParam('maxlength'))
			{
				throw new FlaskPHP\Exception\ValidateException([
					$this->tag => '[[ FLASK.FIELD.Error.MaxLength : maxlength='.intval($this->getParam('maxlength')).' ]]'
				]);
			}

			// Filter
			if ($this->getParam('filter') && !preg_match($this->getParam('filter'),$value))
			{
				throw new FlaskPHP\Exception\ValidateException([
					$this->tag => oneof($this->getParam('filter_message'),'[[ FLASK.FIELD.Error.Filter ]]')
				]);
			}

			// Unique
			if ($this->getParam('unique'))
			{
				$model=oneof($this->formObject->model,$this->modelObject);
				$param=oneof($this->getParam('unique_param'),Flask()->DB->getQueryBuilder());
				$param->setModel($model);
				if (!$model->isUnique($this->tag,$value,$param))
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => oneof($this->getParam('unique_message'),'[[ FLASK.FIELD.Error.Unique ]]')
					]);
				}
			}

			// If we got here, it validates.
			return;
		}


		/**
		 *
		 *   Is this field required?
		 *   -----------------------
		 *   @access public
		 *   @return bool
		 *
		 */

		public function required()
		{
			// Get
			$required=$this->getParam('required');

			// Not required
			if (!$required) return false;

			// Always required
			if ($required=='always') return true;

			// Required on insert
			if ($required=='add' && is_object($this->formObject) && $this->formObject->operation=='add') return true;

			// Required on edit
			if ($required=='edit' && is_object($this->formObject) && $this->formObject->operation=='edit') return true;

			// Required_if
			if ($required=='if')
			{
				$reqd=null;
				$requiredCond=$this->getParam('required_cond');
				if (is_object($this->formObject))
				{
					$fieldSet=$this->formObject->field;
					$keys=array_map('strlen', array_keys($fieldSet));
					array_multisort($keys, SORT_DESC, $fieldSet);
					foreach ($fieldSet as $fieldTag => $fieldObject)
					{
						$requiredCond=str_replace('$'.$fieldTag,'$this->formObject->field["'.$fieldTag.'"]->getValue()',$requiredCond);
					}
				}
				eval('$reqd=('.$requiredCond.')?true:false;');
				return ($reqd?true:false);
			}

			// If we got here, it isn't required
			return false;
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

			// Compose log entry
			$fieldLogData=new \stdClass();
			$fieldLogData->id=$this->tag;
			$fieldLogData->name=$this->getTitle();
			if (in_array($logData->operation,array('add','edit')))
			{
				if ($logData->operation!='add' && !$this->getParam('forcevalue')) $fieldLogData->old_value=$model->_in[$this->tag];
				$fieldLogData->new_value=$model->{$this->tag};
			}
			else
			{
				$fieldLogData->value=$model->{$this->tag};
			}

			// Add log entry
			$logData->addData($this->tag,$fieldLogData);
		}


		/**
		 *
		 *   Trigger: form load
		 *   ------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function triggerFormLoad()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Trigger: form pre-display
		 *   -------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function triggerFormPreDisplay()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Trigger: form save
		 *   ------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function triggerFormSave()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Zero hack
		 *   ---------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param string $operation Operation (add/remove)
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function zeroHack( $value, string $operation )
		{
			// Don't zero-hack these
			if (is_array($value) || is_object($value)) return $value;

			// Do we need it?
			if (!$this->getParam('zerohack')) return $value;

			// Apply zero-hack
			if ($operation=='add')
			{
				return 'val_'.strval($value);
			}
			else
			{
				return preg_replace("/^val_/","",strval($value));
			}
		}


		/**
		 *
		 *   Get field value
		 *   ---------------
		 *   @access public
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function getValue()
		{
			// Fixed value?
			if ($this->hasParam('value'))
			{
				return $this->zeroHack($this->getParam('value'),'add');
			}

			// Submitting?
			if (is_object($this->formObject) && $this->formObject->doSubmit)
			{
				return Flask()->Request->postVar($this->tag);
			}

			// Loaded data
			if (is_object($this->formObject) && is_object($this->formObject->model) && $this->formObject->model->_loaded)
			{
				return $this->zeroHack($this->formObject->model->{$this->tag},'add');
			}

			// Default
			if ($this->hasParam('default'))
			{
				return $this->zeroHack($this->hasParam('default'),'add');
			}

			// Nothing
			return null;
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
			return $this->zeroHack($this->getValue(),'remove');
		}


		/**
		 *
		 *   Get options
		 *   -----------
		 *   @access public
		 *   @throws \Exception
		 *   @return array
		 *
		 */

		public function getOptions()
		{
			// Init
			$options=array();

			// Get options
			switch($this->getParam('source'))
			{
				// Query
				case 'query':
					$dataset=Flask()->DB->querySelectSQL($this->getParam('source_query'));
					foreach ($dataset as $row)
					{
						$options[$row['value']]=$row['description'];
					}
					break;

				// Model
				case 'model':
					$model=$this->getParam('source_model');
					$query=oneof($this->getParam('source_param'),Flask()->DB->getQueryBuilder('SELECT'));
					$query->setModel($model);
					$query->addField($this->getParam('source_keyfield').' as value');
					$query->addField($this->getParam('source_valuefield').' as description');
					$query->addOrderBy($model->getParam('setord')?'ord':'value');
					$dataset=$model->getList($query);
					foreach ($dataset as $row)
					{
						$options[$row['value']]=$row['description'];
					}
					break;

				// Function
				case 'func':
					$options=call_user_func($this->getParam('source_func'),$this);
					break;

				// Default: source array/list
				default:
					$options=(array)$this->getParam('source_list');
					break;
			}

			// Apply zero-hack if necessary
			if ($this->getParam('zerohack'))
			{
				$origoptions=$options;
				$options=array();
				foreach ($origoptions as $k => $v)
				{
					$options['val_'.$k]=$v;
				}
			}

			// Return
			return $options;
		}


		/**
		 *
		 *   Render form field
		 *   -----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFormField()
		{
			// Pre-display trigger
			$this->triggerFormPreDisplay();

			// Multi-row field
			if ($this->getParam('form_multirow')>1)
			{
				$c='';
				for ($i=1;$i<=$this->$this->getParam('form_multirow');++$i)
				{
					$c.=$this->renderFormBeginningBlock($this->getValue(),$i);
					$c.=$this->renderFormLabel($this->getValue(),$i);
					$c.=$this->renderFormElement($this->getValue(),$i);
					$c.=$this->renderFormEndingBlock($this->getValue(),$i);
				}
			}

			// Regular field
			else
			{
				$c =$this->renderFormBeginningBlock($this->getValue());
				$c.=$this->renderFormLabel($this->getValue());
				$c.=$this->renderFormElement($this->getValue());
				$c.=$this->renderFormEndingBlock($this->getValue());
			}

			// Render
			return $c;
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
			return '<div id="field_'.$this->tag.'" class="field">';
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
			return '<label>'.$this->getTitle().'</label>';
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
			// This should be implemented in the subclass
			throw new FlaskPHP\Exception\NotImplementedException('Function renderFormElement() not implemented in the '.get_called_class().' class');
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
				$c='<div class="comment">'.$this->getParam('form_comment').'</div>';
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
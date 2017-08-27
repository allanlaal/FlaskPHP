<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The password field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class PasswordField extends FieldInterface
	{


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @access public
		 *   @param string $tag Field tag
		 *   @return \Codelab\FlaskPHP\Field\PasswordField
		 *
		 */

		public function __construct( string $tag=null )
		{
			parent::__construct($tag);
			$this->setParam('form_multirow',2);
		}


		/**
		 *
		 *   Set title for repeat
		 *   --------------------
		 *   @access public
		 *   @param string $titleRepeat Repeat title
		 *   @return \Codelab\FlaskPHP\Field\PasswordField
		 *
		 */

		public function setTitleRepeat( string $titleRepeat )
		{
			$this->setParam('title_repeat',$titleRepeat);
			return $this;
		}


		/**
		 *
		 *   Set required password strength
		 *   ------------------------------
		 *   @access public
		 *   @param int $passwordStrength Password strength
		 *   @return \Codelab\FlaskPHP\Field\PasswordField
		 *
		 */

		public function setPasswordStrength( int $passwordStrength )
		{
			$this->setParam('passwordstrength',$passwordStrength);
			return $this;
		}


		/**
		 *
		 *   Set form password suggest
		 *   -------------------------
		 *   @access public
		 *   @param bool $suggest Suggest a secure password in form
		 *   @return \Codelab\FlaskPHP\Field\PasswordField
		 *
		 */

		public function setFormSuggest( bool $suggest )
		{
			$this->setParam('form_suggest',$suggest);
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
			// No save
			if ($this->getParam('nosave')) return true;

			// No value: no need to update
			if (is_object($this->formObject) && $this->formObject->doSubmit && !mb_strlen(Flask()->Request->postVar($this->tag))) return true;

			// Otherwise: guess we can
			return false;
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
			// Init
			$errors=array();

			// Required, but not set
			if ($this->required() && !mb_strlen($value))
			{
				$errors[$this->tag]='[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]';
			}
			if ($this->required() && !mb_strlen(Flask()->Request->postVar($this->tag.'_repeat')))
			{
				$errors[$this->tag.'_repeat']='[[ FLASK.FIELD.Error.PasswordRepeatEmpty ]]';
			}

			// No point in checking further if empty
			if (!mb_strlen($value))
			{
				if (sizeof($errors)) throw new FlaskPHP\Exception\ValidateException($errors);
				return;
			}

			// Minimum length not met
			if (empty($errors[$this->tag]) && $this->getParam('minlength') && mb_strlen($value)<$this->getParam('minlength'))
			{
				$errors[$this->tag]='[[ FLASK.FIELD.Error.MinLength : minlength='.intval($this->getParam('minlength')).' ]]';
			}

			// Over maximum length
			if (empty($errors[$this->tag]) && $this->getParam('maxlength') && mb_strlen($value)>$this->getParam('maxlength'))
			{
				$errors[$this->tag]='[[ FLASK.FIELD.Error.MaxLength : maxlength='.intval($this->getParam('maxlength')).' ]]';
			}

			// Check strength
			if (empty($errors[$this->tag]) && $this->getParam('passwordstrength'))
			{
				switch(intval($this->getParam('passwordstrength')))
				{
					case 1:
						if (!preg_match('/[A-Za-z]/',$value)) $errors[$this->tag]='[[ FLASK.FIELD.Error.PasswordWeak1 ]]';
						if (!preg_match('/[0-9]/',$value)) $errors[$this->tag]='[[ FLASK.FIELD.Error.PasswordWeak1 ]]';
						break;
					case 2:
						if (!preg_match('/[A-Z]/',$value)) $errors[$this->tag]='[[ FLASK.FIELD.Error.PasswordWeak2 ]]';
						if (!preg_match('/[a-z]/',$value)) $errors[$this->tag]='[[ FLASK.FIELD.Error.PasswordWeak2 ]]';
						if (!preg_match('/[0-9]/',$value)) $errors[$this->tag]='[[ FLASK.FIELD.Error.PasswordWeak2 ]]';
						if (!preg_match('/[^A-Za-z0-9]/',$value)) $errors[$this->tag]='[[ FLASK.FIELD.Error.PasswordWeak2 ]]';
						break;
					default:
						break;
				}
			}

			// Repeat doesn't match
			if (empty($errors[$this->tag.'_repeat']) && $value!=Flask()->Request->postVar($this->tag.'_repeat'))
			{
				$errors[$this->tag.'_repeat']='[[ FLASK.FIELD.Error.PasswordRepeatMismatch ]]';
			}

			// Errors?
			if (sizeof($errors)) throw new FlaskPHP\Exception\ValidateException($errors);
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
				if ($logData->operation!='add' && !$this->getParam('forcevalue')) $fieldLogData->old_value='*** old ***';
				$fieldLogData->new_value='*** new ***';
			}
			else
			{
				$fieldLogData->value='*** new ***';
			}

			// Add log entry
			$logData->addData($this->tag,$fieldLogData);
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
			// Get password
			$password=$this->getValue();

			// Empty?
			if (!mb_strlen($password)) return '';

			// Encrypt
			return Flask()->User->getPasswordHash($password);
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
			if (mb_strlen($value)) return '***';
			return '';
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
			return '<div id="field_'.$this->tag.($row==2?'_repeat':'').'" class="field">';
		}


		/**
		 *
		 *   Render form field: label
		 *   ------------------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param int $row Row (for multi-row fields)
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFormLabel( $value, int $row=null )
		{
			$c='<label>';
			switch ($row)
			{
				case 2:
					$c.=$this->getParam('title_repeat').':';
					break;
				default:
					$c.=$this->getParam('title').':';
					break;
			}
			$c.='</label>';
			return $c;
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
			// Style
			$style=array();
			if ($this->getParam('form_fieldstyle')) $style[]=$this->getParam('form_fieldstyle');
			if ($row==1 && $this->getParam('form_comment')) $style[]='width: 70%; display: inline-block';
			if ($row==2 && $this->getParam('form_comment_repeat')) $style[]='width: 70%; display: inline-block';

			// Class
			$class=array();
			if (!empty($this->getParam('form_fieldclass'))) $class[]=$this->getParam('form_fieldclass');

			// Wrapper
			$c='<div class="element">';

			// Field
			$c.='<input';
				$c.=' type="password"';
				$c.=' id="'.$this->tag.($row==2?'_repeat':'').'"';
				$c.=' name="'.$this->tag.($row==2?'_repeat':'').'"';
				$c.=' autocomplete="off"';
				$c.=' class="'.join(' ',$class).'"';
				if (!empty($style)) $c.=' style="'.join('; ',$style).'"';
				if ($this->getParam('maxlength')) $c.=' maxlength="'.$this->getParam('maxlength').'"';
				if ($this->getParam('readonly') || $this->getParam('form_readonly')) $c.=' readonly="readonly"';
				if ($this->getParam('disabled') || $this->getParam('form_disabled')) $c.=' disabled="disabled"';
				if ($this->getParam('form_placeholder')) $c.=' placeholder="'.htmlspecialchars($this->getParam('form_placeholder')).'"';
				if ($this->getParam('form_emptyformat')) $c.=' data-emptyformat="'.htmlspecialchars($this->getParam('form_emptyformat')).'"';
				elseif ($this->getParam('emptyformat')) $c.=' data-emptyformat="'.htmlspecialchars($this->getParam('emptyformat')).'"';
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

			// Password suggest
			if ($row==1 && $this->getParam('form_suggest'))
			{
				$passwordSuggest='<a onclick="Flask.Password.suggestPassword(\''.$this->tag.'\')"><span class="icon-bulb"></span> [[ FLASK.FORM.Password.Suggest.FormComment ]]</a>';
				if ($this->getParam('form_comment'))
				{
					$this->setParam('form_comment','<div>'.$passwordSuggest.'</div><div>'.$this->getParam('form_comment').'</div>');
				}
				else
				{
					$this->setParam('form_comment',$passwordSuggest);
				}
			}

			// Comment
			if ($row==1)
			{
				$c.=$this->renderComment();
			}

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The form action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class FormAction extends ActionInterface
	{


		/**
		 *   Operation
		 *   @var string
		 *   @access public
		 */

		public $operation = null;


		/**
		 *   Template
		 *   @var FlaskPHP\Template\Template
		 *   @access public
		 */

		public $template = null;


		/**
		 *   Fields/columns
		 *   @var array
		 *   @access public
		 */

		public $field = array();


		/**
		 *  Custom submit actions
		 *  @var array
		 *  @access public
		 */

		public $submit = array();


		/**
		 *  Are we submitting?
		 *  @var boolean
		 *  @access public
		 */

		public $doSubmit = false;


		/**
		 *  Do we have file upload?
		 *  @var boolean
		 *  @access public
		 */

		public $fileUpload = false;


		/**
		 *  Notes
		 *  @var array
		 *  @access public
		 */

		public $notes = array();


		/**
		 *  Form JavaScript
		 *  @var string
		 *  @access public
		 */

		public $js = '';


		/**
		 *
		 *   Init form
		 *   ---------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initForm()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Init fields
		 *   -----------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initFields()
		{
			// Check model
			if (!is_object($this->model)) throw new FlaskPHP\Exception\InvalidParameterException('No model defined.');
			if (!($this->model instanceof FlaskPHP\Model\ModelInterface)) throw new Exception('Model not an instance of ModelInterface.');

			// Add fields
			foreach ($this->model->_field as $fieldTag => $fieldObject)
			{
				if ($fieldObject->getParam('noedit')) continue;
				$this->addField($fieldTag,$fieldObject);
			}
		}


		/**
		 *
		 *   Load data
		 *   ---------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function dataLoad()
		{
			// Set defaults
			$this->setDefaults();

			// Load model
			$this->model->load(Flask()->Request->postVar($this->model->getParam('idfield')),$this->getParam('loadparam'));

			// Fire field form load triggers
			foreach ($this->field as $fieldTag => &$fieldObject)
			{
				$fieldObject->triggerFormLoad();
			}
		}


		/**
		 *
		 *   Save data
		 *   ---------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function dataSave()
		{
			try
			{
				// Init parameters
				$param=oneof($this->getParam('form_saveparam'),Flask()->DB->getQueryBuilder());

				// Gather columns and save values
				foreach ($this->field as $fieldTag => $fieldObject)
				{
					if ($fieldObject->getParam('nosave')) continue;
					if ($fieldObject->noSave()) continue;
					$saveValue=$fieldObject->saveValue();
					if (is_array($saveValue))
					{
						foreach ($saveValue as $svKey => $svVal)
						{
							$param->addField($svKey);
							$this->model->{$svKey}=$svVal;
						}
					}
					elseif ($saveValue!==false)
					{
						$param->addField($fieldTag);
						$this->model->{$fieldTag}=$saveValue;
					}
				}

				// Save data
				$logMessage=($this->hasParam('logmessage')?$this->getParam('logmessage'):null);
				$logData=null;
				$refOID=($this->hasParam('logrefoid')?(preg_match("/^[0-9]+$/",$this->getParam('logrefoid'))?$this->getParam('logrefoid'):$this->field[$this->getParam('logrefoid')]->saveValue()):null);
				$this->model->save($param,$logMessage,$logData,$refOID,$this->operation,$this);

				// Fire save triggers
				foreach ($this->field as $fieldTag => $fieldObject)
				{
					$fieldObject->triggerFormSave();
				}
			}
			catch (\Exception $e)
			{
				// Get error
				$err=$e->getMessage();

				// Check for common errors
				if (strpos($err,'MySQL query error: Duplicate entry')!==false)
				{
					$err='[[ FLASK.FORM.Error.DuplicateKey ]]';
				}

				// Return error
				throw new FlaskPHP\Exception\Exception('[[ FLASK.COMMON.Error.ErrorSavingData ]]'.(!empty($err)?': '.$err:''));
			}
		}


		/**
		 *
		 *   Are we submitting?
		 *   ------------------
		 *   @access public
		 *   @return bool
		 *
		 */

		public function checkSubmit()
		{
			// Inline submit
			if (sizeof($this->submit))
			{
				foreach ($this->submit as $submitTag => $submitLabel)
				{
					if (Flask()->Request->postVar('submit_'.$submitTag)!==null) return true;
				}
			}
			else
			{
				if (Flask()->Request->postVar('submit_save')!==null) return true;
			}
		}


		/**
		 *
		 *   Data pre-field validation
		 *   -------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function dataPreValidate()
		{
			// This can be overwritten in the subclass if necessary.
		}


		/**
		 *
		 *   Data post-field validation
		 *   --------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function dataValidate()
		{
			// This can be overwritten in the subclass if necessary.
		}


		/**
		 *
		 *   Validate field input
		 *   --------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function validateFields()
		{
			// Init
			$errors=array();

			// Validate fields
			foreach ($this->field as $fieldTag => $fieldObject)
			{
				$fieldObject->triggerFormPreDisplay();
				try
				{
					$fieldObject->validate(Flask()->Request->postVar($fieldTag),$this->model,$this);
				}
				catch (FlaskPHP\Exception\ValidateException $validateException)
				{
					$errors=array_merge($errors,$validateException->getErrors());
				}
			}

			// Errors?
			if (sizeof($errors))
			{
				throw new FlaskPHP\Exception\ValidateException($errors);
			}
		}


		/**
		 *
		 *   Add a field
		 *   -----------
		 *   @access public
		 *   @param string $fieldTag Field tag
		 *   @param FlaskPHP\Field\FieldInterface $fieldObject Field object
		 *   @throws \Exception
		 *   @return FlaskPHP\Field\FieldInterface Field reference
		 *
		 */

		public function addField( string $fieldTag, FlaskPHP\Field\FieldInterface $fieldObject=null )
		{
			// Check if the action already exists
			if (array_key_exists($fieldTag,$this->field)) throw new FlaskPHP\Exception\InvalidParameterException('Field '.$fieldTag.' already exists.');

			// Field object passed
			if (is_object($fieldObject))
			{
				$this->field[$fieldTag]=$fieldObject;
			}

			// Otherwise - data objekti olemasolev field
			else
			{
				// Do we have a data object?
				if (!is_object($this->model)) throw new FlaskPHP\Exception\Exception('No model defined.');
				if (!($this->model instanceof FlaskPHP\Model\ModelInterface)) throw new Exception('Model not a ModelInterface instance.');

				// Do we have this field?
				if (!array_key_exists($fieldTag,$this->model->_field)) throw new FlaskPHP\Exception\InvalidParameterException($fieldTag.': no such field defined in model.');

				// Link
				$this->field[$fieldTag]=&$this->model->_field[$fieldTag];
			}

			// Set tag/column
			if (empty($this->field[$fieldTag]->tag)) $this->field[$fieldTag]->tag=$fieldTag;

			// Create backreference to the field
			$this->field[$fieldTag]->formObject=$this;

			// Return reference
			return $this->field[$fieldTag];
		}


		/**
		 *
		 *   Remove a field
		 *   --------------
		 *   @access public
		 *   @param string $fieldTag Field tag
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function removeField( string $fieldTag )
		{
			// Check if the field exists
			if (!array_key_exists($fieldTag,$this->field)) throw new FlaskPHP\Exception\InvalidParameterException('Field '.$fieldTag.' does not exist.');

			// Remove
			unset($this->field[$fieldTag]);
		}


		/**
		 *
		 *   Set title
		 *   ---------
		 *   @access public
		 *   @param string $title Form title
		 *   @return \Codelab\FlaskPHP\Action\FormAction
		 *
		 */

		public function setTitle( string $title )
		{
			$this->setParam('title',$title);
			return $this;
		}


		/**
		 *
		 *   Set add title
		 *   -------------
		 *   @access public
		 *   @param string $title Add form title
		 *   @return \Codelab\FlaskPHP\Action\FormAction
		 *
		 */

		public function setAddTitle( string $title )
		{
			$this->setParam('title_add',$title);
			return $this;
		}


		/**
		 *
		 *   Set edit title
		 *   --------------
		 *   @access public
		 *   @param string $title Edit form title
		 *   @return \Codelab\FlaskPHP\Action\FormAction
		 *
		 */

		public function setEditTitle( string $title )
		{
			$this->setParam('title_edit',$title);
			return $this;
		}


		/**
		 *
		 *   Force operation type
		 *   --------------------
		 *   @access public
		 *   @param string $operation Operation
		 *   @return \Codelab\FlaskPHP\Action\FormAction
		 *
		 */

		public function setOperation( string $operation )
		{
			$this->setParam('operation',$operation);
			return $this;
		}


		/**
		 *
		 *   Set progress message
		 *   --------------------
		 *   @access public
		 *   @param string $progressMessage Progress message
		 *   @return \Codelab\FlaskPHP\Action\FormAction
		 *
		 */

		public function setProgressMessage( string $progressMessage )
		{
			$this->setParam('progressmessage',$progressMessage);
			return $this;
		}


		/**
		 *
		 *   Add JavaScript
		 *   --------------
		 *   @access public
		 *   @param string $js JavaScript
		 *   @return \Codelab\FlaskPHP\Action\FormAction
		 *
		 */

		public function addJS( string $js )
		{
			$this->js.=$js;
			return $this;
		}


		/**
		 *
		 *   Display form
		 *   ------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayForm()
		{
			// Init
			$this->template->set('form','');

			// Render parts
			$c=$this->displayTitle();
			$c.=$this->displayFormBegin();
			$c.=$this->displayFormContents();
			$c.=$this->displayFormSubmit();
			$c.=$this->displayFormEnd();
			$c.=$this->displayNotes();
			$c.=$this->displayJS();
			$this->template->set('content',$c);

			// Return
			return $this->template->render();
		}


		/**
		 *
		 *   Display title
		 *   -------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayTitle()
		{
			// Title
			$title=oneof(
				$this->getParam('title_'.$this->operation),
				$this->getParam('title'),
				'[[ FLASK.FORM.Title.'.$this->operation.' ]]'
			);
			if (mb_strlen($title))
			{
				$this->template->set('title',$title);
			}

			// Alerts/warnings
			if ($this->getParam('alert'))
			{
				$this->template->set('alert',$this->getParam('alert'));
			}

			// Info
			if ($this->getParam('info'))
			{
				$this->template->set('info',$this->getParam('info'));
			}
		}


		/**
		 *
		 *   Display form begin
		 *   ------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayFormBegin()
		{
			// The form begins
			$form_begin='<form id="flask-form" class="ui form" method="post" action="'.$this->buildURL($this->getParam('url')).'" onsubmit="return false">';

			// ID field if it's not in the fieldset
			if ($this->operation=='edit' && !is_object($this->field[$this->model->getParam('idfield')]))
			{
				$form_begin.='<input type="hidden" id="'.htmlspecialchars($this->model->getParam('idfield')).'" name="'.htmlspecialchars($this->model->getParam('idfield')).'" value="'.htmlspecialchars(Flask()->Request->postVar($this->model->getParam('idfield'))).'">';
			}

			// Set template items
			$this->template->set('url_submit',$this->buildURL($this->getParam('url')));
			$this->template->set('form_begin',$form_begin);
			$this->template->templateVar['form'].=$form_begin;

			// Return
			return $form_begin;
		}


		/**
		 *
		 *   Display form contents
		 *   ---------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayFormContents()
		{
			// Check
			if (!sizeof($this->field)) throw new FlaskPHP\Exception\Exception('No fields defined.');

			// Display the form elements
			$formField='';
			$formFieldSet=array();
			foreach ($this->field as $fieldTag => $fieldObject)
			{
				$formFieldSet[$fieldTag]=$fieldTag;
				$fld=$fieldObject->renderFormField();
				$formField.=$fld;
				$this->template->set('field_'.$fieldTag,$fld);
			}
			$this->template->set('form_field',$formField);
			$this->template->set('form_fieldset',$formFieldSet);
			$this->template->templateVar['form'].=$formField;

			// Return
			return $formField;
		}


		/**
		 *
		 *   Display form submit
		 *   -------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayFormSubmit()
		{
			// Start
			$form_submit='<div class="submit">';

			// Submit param
			$submitParam=new \stdClass();
			if ($this->getParam('progressmessage')) $submitParam->progressmessage=$this->getParam('progressmessage');
			$submitParam=FlaskPHP\Util::htmlJSON($submitParam);

			// Custom submit buttons
			if (sizeof($this->submit))
			{
				// Traverse
				foreach ($this->submit as $submitTag => $submitTitle)
				{
					$submit='<button type="button" id="submit_'.$submitTag.'" name="submit_'.$submitTag.'" onclick="Flask.Form.submit(\''.$submitTag.'\','.$submitParam.')">'.$submitTitle.'</button>';
					$this->template->set('submit_'.$submitTag,$submit);
					$form_submit.=$submit;
				}
			}

			// Standard submit
			else
			{
				$submit='<button type="button" id="submit_save" name="submit_save" onclick="Flask.Form.submit(\'submit_save\','.$submitParam.')">'.oneof($this->getParam('submitbuttontitle'),'[[ FLASK.FORM.Btn.'.$this->operation.' ]]').'</button>';
				$this->template->set('submit_'.$this->operation,$submit);
				$form_submit.=$submit;
			}

			// Cancel
			$cancelParam=new \stdClass();
			if ($this->getParam('progressmessage')) $cancelParam->progressmessage=$this->getParam('progressmessage');
			$cancelParam=FlaskPHP\Util::htmlJSON($cancelParam);
			$submit='<button type="button" id="submit_cancel" name="submit_cancel" onclick="Flask.Form.submit(\'submit_cancel\','.$cancelParam.')">[[ FLASK.FORM.Btn.Cancel ]]</button>';
			$this->template->set('submit_cancel',$submit);
			$form_submit.=$submit;

			// Close
			$form_submit.='</div>';
			$this->template->set('form_submit',$form_submit);
			$this->template->templateVar['form'].=$form_submit;

			// Return
			return $form_submit;
		}


		/**
		 *
		 *   Display form end
		 *   ----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayFormEnd()
		{
			$form_end='</form>';
			$this->template->set('form_end',$form_end);
			$this->template->templateVar['form'].=$form_end;
			return $form_end;
		}


		/**
		 *
		 *   Display form notes
		 *   ------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayNotes()
		{
			if (!empty($this->notes))
			{
				$notes='<ul class="notes">';
				foreach ($this->notes as $note)
				{
					$notes.='<li>'.$note.'</li>';
				}
				$notes.='</ul>';
				$this->template->set('notes',$notes);
				return $notes;
			}
			return '';
		}


		/**
		 *
		 *   Display form JS
		 *   ---------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function displayJS()
		{
			if (mb_strlen($this->js))
			{
				$this->template->set('js',$this->js);
				return $this->js;
			}
			return '';
		}


		/**
		 *
		 *   Run action and return response
		 *   ------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return FlaskPHP\Response\ResponseInterface
		 *
		 */

		public function runAction()
		{
			try
			{
				// Set defaults
				$this->setDefaults();

				// Cancel?
				if (Flask()->Request->postVar('submit_cancel')!==null)
				{
					$response=new \stdClass();
					$response->status=1;
					$response->redirect=$this->buildURL(oneof($this->getParam('url_cancel'),$this->getParam('url_return')));
					return new FlaskPHP\Response\JSONResponse($response);
				}

				// Init form
				$this->initForm();

				// Check
				if (!is_object($this->model)) throw new FlaskPHP\Exception\InvalidParameterException('No model defined.');

				// Init template
				$this->template=new FlaskPHP\Template\Template('form.'.$this->getParam('template'));

				// Determine operation
				$this->operation=oneof(
					$this->getParam('operation'),
					(Flask()->Request->postVar($this->model->getParam('idfield'))!==null?'edit':'add')
				);
				$this->template->set('operation',$this->operation);

				// Load data
				if ($this->operation=='edit') $this->dataLoad();

				// Are we submitting?
				$this->doSubmit=$this->checkSubmit();

				// Init fields
				$this->initFields();

				// Handle submit
				if ($this->doSubmit)
				{
					try
					{
						// Init
						$errors=array();

						// Pre-validate
						try
						{
							$this->dataPreValidate();
						}
						catch (FlaskPHP\Exception\ValidateException $validateException)
						{
							$response=new \stdClass();
							$response->status=2;
							$response->error=$validateException->getErrors();
							return new FlaskPHP\Response\JSONResponse($response);
						}

						// Validate fields
						try
						{
							$this->validateFields();
						}
						catch (FlaskPHP\Exception\ValidateException $validateException)
						{
							$errors=array_merge($errors,$validateException->getErrors());
						}

						// Do global error checking
						try
						{
							$this->dataValidate();
						}
						catch (FlaskPHP\Exception\ValidateException $validateException)
						{
							$errors=array_merge($errors,$validateException->getErrors());
						}

						// Errors?
						if (sizeof($errors))
						{
							$response=new \stdClass();
							$response->status=2;
							$response->error=$errors;
							return new FlaskPHP\Response\JSONResponse($response);
						}

						// Save data
						$this->dataSave();

						// Return response
						$response=new \stdClass();
						$response->status=1;
						if ($this->getParam('reload'))
						{
							$response->reload=1;
						}
						else
						{
							$response->redirect=$this->buildURL(oneof($this->getParam('url_cancel'),$this->getParam('url_return'),$this->buildURL()));
						}
						return new FlaskPHP\Response\JSONResponse($response);
					}
					catch (\Exception $e)
					{
						$response=new \stdClass();
						$response->status=2;
						$response->error=$e->getMessage();
						return new FlaskPHP\Response\JSONResponse($response);
					}
				}

				// Display form
				$response=new FlaskPHP\Response\HTMLResponse();
				$response->setContent($this->displayForm());
				return $response;
			}
			catch (\Exception $e)
			{
				// Ajax error
				if (Flask()->Request->isXHR())
				{
					$response=new \stdClass();
					$response->status=2;
					$response->error=$e->getMessage();
					return new FlaskPHP\Response\JSONResponse($response);
				}

				// HTML
				else
				{
					$response=new FlaskPHP\Response\HTMLResponse();
					$response->setContent('<h1>[[ FLASK.COMMON.Error ]]</h1><div class="error">'.htmlspecialchars($e->getMessage()).'</div>');
					return $response;
				}
			}
		}


	}


?>
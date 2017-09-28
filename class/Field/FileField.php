<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The file field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class FileField extends FieldInterface
	{


		/**
		 *
		 *   Set file type
		 *   -------------
		 *   @access public
		 *   @param string $fileType File type
		 *   @return \Codelab\FlaskPHP\Field\FileField
		 */

		public function setFileType( string $fileType )
		{
			$this->setParam('filetype',$fileType);
			return $this;
		}


		/**
		 *
		 *   Set allow multiple file uploads
		 *   -------------------------------
		 *   @access public
		 *   @param bool $allowMultiple Allow multi-file upload
		 *   @return \Codelab\FlaskPHP\Field\FileField
		 */

		public function setAllowMultiple( bool $allowMultiple )
		{
			$this->setParam('multiple',$allowMultiple);
			return $this;
		}


		/**
		 *
		 *   Set allow edit/reupload of file
		 *   -------------------------------
		 *   @access public
		 *   @param bool $allowEdit Allow edit/reupload
		 *   @return \Codelab\FlaskPHP\Field\FileField
		 */

		public function setFormAllowEdit( bool $allowEdit )
		{
			$this->setParam('form_allowedit',$allowEdit);
			return $this;
		}


		/**
		 *
		 *   Set allow removing of file
		 *   ---------------------------
		 *   @access public
		 *   @param bool $allowClear Allow remove
		 *   @return \Codelab\FlaskPHP\Field\FileField
		 */

		public function setFormAllowClear( bool $allowClear )
		{
			$this->setParam('form_allowclear',$allowClear);
			return $this;
		}


		/**
		 *
		 *   Set display list image
		 *   ----------------------
		 *   @access public
		 *   @param string $imageLink URL to show image
		 *   @return \Codelab\FlaskPHP\Field\FileField
		 */

		public function setListDisplayImage( string $imageLink )
		{
			$this->setParam('list_displayimage',$imageLink);
			return $this;
		}


		/**
		 *
		 *   Set display form image
		 *   ----------------------
		 *   @access public
		 *   @param string $imageLink URL to show image
		 *   @return \Codelab\FlaskPHP\Field\FileField
		 */

		public function setFormDisplayImage( string $imageLink )
		{
			$this->setParam('form_displayimage',$imageLink);
			return $this;
		}


		/**
		 *
		 *   Get file type
		 *   -------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 */

		public function getFileType()
		{
			if ($this->getParam('filetype'))
			{
				return $this->getParam('filetype');
			}
			else
			{
				if (is_object($this->model))
				{
					$model=$this->model;
				}
				elseif (is_object($this->formObject) && is_object($this->formObject->model))
				{
					$model=$this->formObject->model;
				}
				elseif (is_object($this->listObject) && is_object($this->listObject->model))
				{
					$model=$this->listObject->model;
				}
				if (!($model instanceof FlaskPHP\Model\ModelInterface)) throw new FlaskPHP\Exception\Exception('Could not identify model.');
				return $model->getParam('table');
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
			// Required and empty?
			if ($this->required())
			{
				if ($this->getParam('multiple'))
				{
					if (!intval($_FILES[$this->tag]['size'][0]))
					{
						throw new FlaskPHP\Exception\ValidateException([
							$this->tag => '[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]'
						]);
					}
				}
				else
				{
					if (!intval($_FILES[$this->tag]['size']))
					{
						throw new FlaskPHP\Exception\ValidateException([
							$this->tag => '[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]'
						]);
					}
				}
				if (Flask()->Request->postVar($this->tag.'_clear'))
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => '[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]'
					]);
				}
			}
			if (is_object($this->formObject) && $this->formObject->operation=='edit' && $this->getParam('required')=='add' && Flask()->Request->postVar($this->tag.'_clear'))
			{
				throw new FlaskPHP\Exception\ValidateException([
					$this->tag => '[[ FLASK.FIELD.Error.RequiredFieldEmpty ]]'
				]);
			}

			// No point in checking further if no files
			if ($this->getParam('multiple') && empty($_FILES[$this->tag]['size'][0])) return;
			if (!$this->getParam('multiple') && empty($_FILES[$this->tag]['size'])) return;

			// Filter
			if (!empty($this->getParam('filter')))
			{
				$allowedFileTypes=str_array($this->getParam('filter'));
				if ($this->getParam('multiple'))
				{
					foreach ($_FILES[$this->tag]['type'] as $f => $ftype)
					{
						$allowed=false;
						foreach ($allowedFileTypes as $aft)
						{
							if (preg_match('/^'.str_replace('/','\/',$aft).'$/',$ftype)) $allowed=true;
						}
						if (!$allowed)
						{
							throw new FlaskPHP\Exception\ValidateException([
								$this->tag => $_FILES[$this->tag]['name'][$f].': '.oneof($this->getParam('filter_message'),'[[ FLASK.FIELD.Error.FileTypeNotAllowed ]]')
							]);
						}
					}
				}
				else
				{
					$allowed=false;
					foreach ($allowedFileTypes as $aft)
					{
						if (preg_match('/^'.str_replace('/','\/',$aft).'$/',$_FILES[$this->tag]['type'])) $allowed=true;
					}
					if (!$allowed)
					{
						throw new FlaskPHP\Exception\ValidateException([
							$this->tag => oneof($this->getParam('filter_message'),'[[ FLASK.FIELD.Error.FileTypeNotAllowed ]]')
						]);
					}
				}
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
			// Values
			$valueContentType=$valueFileName=$valueFileSize=null;
			if (is_object($this->formObject) && is_object($this->formObject->model) && $this->formObject->model->_loaded)
			{
				$valueContentType=$this->formObject->model->{$this->tag.'_ctype'};
				$valueFileName=$this->formObject->model->{$this->tag.'_fname'};
				$valueFileSize=$this->formObject->model->{$this->tag.'_fsize'};
			}
			elseif (!is_object($this->formObject) && is_object($this->modelObject) && $this->modelObject->_loaded)
			{
				$valueContentType=$this->modelObject->{$this->tag.'_ctype'};
				$valueFileName=$this->modelObject->{$this->tag.'_fname'};
				$valueFileSize=$this->modelObject->{$this->tag.'_fsize'};
			}

			// No file?
			if (!$valueFileSize) return '';

			// Return filename + size
			return $valueFileName.' ('.FlaskPHP\File\File::niceFileSize($valueFileSize).')';
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
			// Init
			$saveValue=array();

			// This function cannot handle multiple file uploads, this needs to be implemented on the form level
			if ($this->getParam('multiple')) return $saveValue;

			// Clear file?
			if (Flask()->Request->postVar($this->tag.'_clear'))
			{
				if (FlaskPHP\File\File::dbStorage()) $saveValue[$this->tag]='';
				$saveValue[$this->tag.'_ctype']='';
				$saveValue[$this->tag.'_fname']='';
				$saveValue[$this->tag.'_fsize']='';
				return $saveValue;
			}

			// No file
			if (!intval($_FILES[$this->tag]['size'])) return $saveValue;

			// Save value
			if (FlaskPHP\File\File::dbStorage()) $saveValue[$this->tag]=file_get_contents($_FILES[$this->tag]['tmp_name']);
			$saveValue[$this->tag.'_ctype']=$_FILES[$this->tag]['type'];
			$saveValue[$this->tag.'_fname']=$_FILES[$this->tag]['name'];
			$saveValue[$this->tag.'_fsize']=$_FILES[$this->tag]['size'];
			return $saveValue;
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
			// Nothing needs to be done on multiple files and DB storage
			if ($this->getParam('multiple')) return;
			if (FlaskPHP\File\File::dbStorage()) return;

			// Delete
			if (Flask()->Request->postVar($this->tag.'_clear'))
			{
				FlaskPHP\File\File::deleteFile($this->formObject->model->_oid,$this->getFileType());
				return;
			}

			// No file?
			if (empty($_FILES[$this->tag]['tmp_name'])) return;

			// Delete previous files
			if (is_object($this->formObject) && $this->formObject->operation=='edit')
			{
				FlaskPHP\File\File::deleteFile($this->formObject->model->_oid,$this->getFileType());
			}

			// Save file
			FlaskPHP\File\File::moveUploadedFile($_FILES[$this->tag]['tmp_name'],$this->formObject->model->_oid,$this->getFileType());
		}


		/**
		 *
		 *   Get list query parameters
		 *   -------------------------
		 *   @access public
		 *   @param FlaskPHP\DB\QueryBuilderInterface $loadListParam List load parameters
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function getListQuery( FlaskPHP\DB\QueryBuilderInterface $loadListParam )
		{
			$loadListParam->addField([
				$this->tag.'_ctype',
				$this->tag.'_fname',
				$this->tag.'_fsize'
			]);
		}


		/**
		 *
		 *   Get list total query parameters
		 *   -------------------------------
		 *   @access public
		 *   @param FlaskPHP\DB\QueryBuilderInterface $loadListParam List load parameters
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function getListTotalQuery( FlaskPHP\DB\QueryBuilderInterface $loadListParam )
		{
			$loadListParam->addField(array('sum('.$this->tag.'_fsize) as '.$this->tag));
		}


		/**
		 *
		 *   Get list sort criteria
		 *   ----------------------
		 *   @access public
		 *   @param string $sortOrder Sort order
		 *   @param FlaskPHP\DB\QueryBuilderInterface $loadListParam List load parameters
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function getListSortCriteria( string $sortOrder, FlaskPHP\DB\QueryBuilderInterface $loadListParam )
		{
			$loadListParam->addOrderBy($this->tag.'_fname '.$sortOrder);
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
			// No file?
			if (!$row[$this->tag.'_fsize'])
			{
				if ($this->getParam('list_emptyvalue')) return $this->getParam('list_emptyvalue');
				return '';
			}

			// Init
			$listValue='';

			// Link
			$listLink=$this->listValueLink($value,$row);
			if ($listLink) $listValue.=$listLink;

			// Image
			if ($this->getParam('list_displayimage'))
			{
				$listValue.='<img src="'.FlaskPHP\Template\Template::parseSimpleVariables($this->getParam('list_displayimage'),$row).'">';
			}

			// Just filename and size
			else
			{
				$listValue.=htmlspecialchars($row[$this->tag.'_fname']).' ('.FlaskPHP\File\File::niceFileSize($row[$this->tag.'_fsize']).')';
			}

			if (!mb_strlen($value) && $this->hasParam('list_emptyvalue'))
			{
				$value=$this->getParam('list_emptyvalue');
			}
			if ($this->hasParam('list_format'))
			{
				$value=sprintf($this->getParam('list_format'),$value);
			}
			$listValue.=htmlspecialchars($value);

			// Link ends
			if ($listLink) $listValue.='</a>';

			// Return value
			return $listValue;
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
			// Values
			if (is_object($this->formObject) && is_object($this->formObject->model) && $this->formObject->model->_loaded)
			{
				$valueContentType=$this->formObject->model->{$this->tag.'_ctype'};
				$valueFileName=$this->formObject->model->{$this->tag.'_fname'};
				$valueFileSize=$this->formObject->model->{$this->tag.'_fsize'};
			}
			else
			{
				$valueContentType=$valueFileName=$valueFileSize=null;
			}

			// Class
			$class=array();
			if (!empty($this->getParam('form_fieldclass'))) $class[]=$this->getParam('form_fieldclass');

			// Init
			$c='';

			// Display current file
			if ($valueFileSize)
			{
				$c.='<div class="ui segment fileupload-view">';

				// Image
				if ($this->getParam('form_displayimage'))
				{
					$c.='<div class="fileupload-view-displayimage">';
					$c.='<img src="'.FlaskPHP\Template\Template::parseSimpleVariables($this->getParam('form_displayimage'),$this->formObject->model).'">';
					$c.='</div>';
				}

				// Value & remove
				$c.='<div class="fileupload-info">';

					// Value
					$c.='<div class="fileupload-fileinfo">'.$valueFileName.' ('.FlaskPHP\File\File::niceFileSize($valueFileSize).')</div>';

					// Remove button
					if ($this->getParam('form_allowclear'))
					{
						$c.='<div class="fileupload-clear">';
						$c.='<input type="hidden" id="'.$this->tag.'_clear" name="'.$this->tag.'_clear" value="">';
						$c.='<button id="'.$this->tag.'_fclear" class="ui icon button" data-tooltip="[[ FLASK.FIELD.File.Remove ]]" data-inverted=""><i class="delete icon"></i></button>';
						$c.='</div>';
					}

				$c.='</div>';

				$c.='</div>';
			}

			// Show field
			if (!$valueFileSize || $this->getParam('form_allowedit'))
			{
				// Wrapper
				$c.='<div class="ui fluid action fileupload '.($this->getParam('form_prefixlabel')?'labeled':'').' input">';

				// Input field
				$c.='<input';
					$c.=' type="file"';
					$c.=' id="'.$this->tag.'"';
					$c.=' name="'.$this->tag.'"';
					if ($this->getParam('multiple')) $c.=' multiple';
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

				// Prefix label
				if ($this->getParam('form_prefixlabel'))
				{
					$c.='<div class="ui label'.($this->getParam('form_prefixlabel_type')?' '.$this->getParam('form_prefixlabel_type'):'').'">';
					$c.=$this->getParam('form_prefixlabel');
					$c.='</div>';
				}

				// Display field
				$c.='<input';
					$c.=' type="text"';
					$c.=' id="'.$this->tag.'_fdisp"';
					$c.=' readonly="readonly"';
					$c.=' placeholder="[[ FLASK.FIELD.File.NoFile ]]"';
					$c.=' class="'.join(' ',$class).'"';
					if ($this->getParam('form_keephiddenvalue')) $c.=' data-keephiddenvalue="1"';
				$c.='>';

				// Browse btn
				$c.='<button class="ui right labeled icon button"><i class="file icon"></i>[[ FLASK.FIELD.File.Browse ]]</button>';

				// Wrapper
				$c.='</div>';

				// Comment
				$c.=$this->renderComment();
			}

			// Return
			return $c;
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The chooser form field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class ChooserField extends FieldInterface
	{


		/**
		 *
		 *   Allow multiple options
		 *   ----------------------
		 *   @access public
		 *   @param bool $multiple Allow multiple options
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setAllowMultiple( bool $multiple )
		{
			$this->setParam('multiple',$multiple);
			return $this;
		}


		/**
		 *
		 *   Set chooser search URL
		 *   ----------------------
		 *   @access public
		 *   @param string $chooserURL Chooser search URL
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserURL( string $chooserURL )
		{
			$this->setParam('chooser_url',$chooserURL);
			return $this;
		}


		/**
		 *
		 *   Set chooser dialog title
		 *   ------------------------
		 *   @access public
		 *   @param string $chooserTitle Search title
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserTitle( string $chooserTitle )
		{
			$this->setParam('chooser_title',$chooserTitle);
			return $this;
		}


		/**
		 *
		 *   Set chooser dialog search placeholder
		 *   -------------------------------------
		 *   @access public
		 *   @param string $chooserPlaceholder Search placeholder
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserPlaceholder( string $chooserPlaceholder )
		{
			$this->setParam('chooser_placeholder',$chooserPlaceholder);
			return $this;
		}


		/**
		 *
		 *   Set chooser empty value label
		 *   -----------------------------
		 *   @access public
		 *   @param string $chooserEmptyValue Empty value label
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserEmptyValue( string $chooserEmptyValue )
		{
			$this->setParam('chooser_emptyvalue',$chooserEmptyValue);
			return $this;
		}


		/**
		 *
		 *   Set allow clearing of value
		 *   ---------------------------
		 *   @access public
		 *   @param string $chooserClear Clear value button label
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserClear( string $chooserClear )
		{
			$this->setParam('chooser_clear',$chooserClear);
			return $this;
		}


		/**
		 *
		 *   Set choose button title
		 *   -----------------------
		 *   @access public
		 *   @param string $chooseButtonTitle Choose button title
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooseButtonTitle( string $chooseButtonTitle )
		{
			$this->setParam('chooser_choosebuttontitle',$chooseButtonTitle);
			return $this;
		}


		/**
		 *
		 *   Set chooser query data
		 *   ----------------------
		 *   @access public
		 *   @param array $chooserData Search query data
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserData( array $chooserData )
		{
			$this->setParam('chooser_data',$chooserData);
			return $this;
		}


		/**
		 *
		 *   Set chooser query data function
		 *   -------------------------------
		 *   @access public
		 *   @param array $chooserData Search query data
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserDataFunction( string $chooserDataFunction )
		{
			$this->setParam('chooser_datafunction',$chooserDataFunction);
			return $this;
		}


		/**
		 *
		 *   Set choose button title
		 *   -----------------------
		 *   @access public
		 *   @param string $chooserAddFormURL Add form URL
		 *   @param string $chooserAddFormButtonTitle Add form button title
		 *   @return \Codelab\FlaskPHP\Field\ChooserField
		 *
		 */

		public function setChooserAddForm( string $chooserAddFormURL, string $chooserAddFormButtonTitle=null )
		{
			$this->setParam('chooser_addform',true);
			$this->setParam('chooser_addform_url',$chooserAddFormURL);
			$this->setParam('chooser_addform_buttontitle',$chooserAddFormButtonTitle);
			return $this;
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
			return htmlspecialchars($this->getValue());
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
			// Calculate widths
			$labelWidth=oneof($this->getParam('form_labelwidth'),3);
			$elementWidth=round(12-$labelWidth);

			// Wrapper
			$c='<div class="ui chooserfield">';

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
			$c.='<div class="chooser-value">';
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
			$c.='<div class="chooser-actions">';

				// Clear
				if ($this->getParam('chooser_clear'))
				{
					$param=new \stdClass();
					$param->emptyvalue=oneof($this->getParam('chooser_emptyvalue'),'[[ FLASK.COMMON.NotSet ]]');
					$param=FlaskPHP\Util::htmlJSON($param);
					$c.='<button type="button" class="ui basic button chooser-clearbtn" style="'.(empty($value)?'display: none':'').'" onclick="Flask.Chooser.clearChooser(\''.$this->tag.'\','.$param.')">'.$this->getParam('chooser_clear').'</button>';
				}

				// Search
				$param=new \stdClass();
				$param->search_url=$this->getParam('chooser_url');
				$param->search_title=oneof($this->getParam('chooser_title'),'[[ FLASK.FIELD.Chooser.Search.Title ]]');
				$param->search_placeholder=oneof($this->getParam('chooser_title'),'[[ FLASK.FIELD.Chooser.Search.Placeholder ]]');
				$param->search_multiple=($this->getParam('multiple')?true:false);
				if ($this->getParam('chooser_addform'))
				{
					$param->addform=1;
					$param->addform_url=$this->getParam('chooser_addform_url');
					$param->addform_buttontitle=oneof($this->getParam('chooser_addform_buttontitle'),'<i class="add icon"></i> [[ FLASK.FIELD.Chooser.AddNew.Submit ]]');
				}
				$param=FlaskPHP\Util::htmlJSON($param);
				if ($this->getParam('chooser_datafunction'))
				{
					$data=$this->getParam('chooser_datafunction');
				}
				elseif ($this->getParam('chooser_data'))
				{
					$data=FlaskPHP\Util::htmlJSON($this->getParam('chooser_data'));
				}
				else
				{
					$data='{}';
				}
				$c.='<button type="button" class="ui button" onclick="Flask.Chooser.openModal(\''.$this->tag.'\','.$param.','.$data.')">'.oneof($this->getParam('chooser_choosebuttontitle'),'<i class="search icon"></i>').'</button>';

			$c.='</div>';

			// Comment
			$c.=$this->renderComment();

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The login action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class FormAction extends FlaskPHP\Action\FormAction
	{


		/**
		 *
		 *   Set label width
		 *   ---------------
		 *   @access public
		 *   @param int $labelWidth Label width
		 *   @return FormAction
		 *
		 */

		public function setLabelWidth( int $labelWidth )
		{
			$this->setParam('labelwidth',$labelWidth);
			return $this;
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
			$form_begin='<form id="flask-form" class="form-horizontal" method="post" action="'.$this->buildURL($this->getParam('url')).'" onsubmit="return false">';

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
			$submitParam=json_encode($submitParam,JSON_FORCE_OBJECT|JSON_HEX_QUOT);

			// Custom submit buttons
			if (sizeof($this->submit))
			{
				// Traverse
				foreach ($this->submit as $submitTag => $submitTitle)
				{
					$submit='<button type="button" id="submit_'.$submitTag.'" "btn btn-primary" name="submit_'.$submitTag.'" onclick="Flask.Form.submit(\''.$submitTag.'\','.$submitParam.')">'.$submitTitle.'</button>';
					$this->template->set('submit_'.$submitTag,$submit);
					$form_submit.=$submit;
				}
			}

			// Standard submit
			else
			{
				$submit='<button type="button" id="submit_save" class="btn btn-primary" name="submit_save" onclick="Flask.Form.submit(\'submit_save\','.$submitParam.')">'.oneof($this->getParam('submitbuttontitle'),'[[ FLASK.FORM.Btn.'.$this->operation.' ]]').'</button>';
				$this->template->set('submit_'.$this->operation,$submit);
				$form_submit.=$submit;
			}

			// Cancel
			$cancelParam=new \stdClass();
			if ($this->getParam('progressmessage')) $cancelParam->progressmessage=$this->getParam('progressmessage');
			$cancelParam=json_encode($cancelParam,JSON_FORCE_OBJECT|JSON_HEX_QUOT);
			$submit='<button type="button" id="submit_cancel" "btn btn-secondary" name="submit_cancel" onclick="Flask.Form.submit(\'submit_cancel\','.$cancelParam.')">[[ FLASK.FORM.Btn.Cancel ]]</button>';
			$this->template->set('submit_cancel',$submit);
			$form_submit.=$submit;

			// Close
			$form_submit.='</div>';
			$this->template->set('form_submit',$form_submit);
			$this->template->templateVar['form'].=$form_submit;

			// Return
			return $form_submit;
		}


	}


?>
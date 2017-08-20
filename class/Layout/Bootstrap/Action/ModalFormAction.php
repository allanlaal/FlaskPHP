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


	class ModalFormAction extends FlaskPHP\Action\ModalFormAction
	{


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


	}


?>
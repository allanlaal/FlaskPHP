<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The e-mail field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class EmailField extends TextField
	{


		/**
		 *
		 *   Allow multiple e-mail addresses?
		 *   --------------------------------
		 *   @access public
		 *   @param bool $allowMultiple Allow multiple e-mails
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 *
		 */

		public function setAllowMultiple( bool $allowMultiple )
		{
			$this->setParam('allowmultiple',$allowMultiple);
			return $this;
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

			// Validate e-mail(s)
			if ($this->getParam('allowmultiple'))
			{
				$emailList=str_array($value);
			}
			else
			{
				$emailList=array($value);
			}
			foreach ($emailList as $email)
			{
				if (!FlaskPHP\Util::isValidEmail($email))
				{
					throw new FlaskPHP\Exception\ValidateException([
						$this->tag => '[[ FLASK.FIELD.Error.InvalidEmail ]]'.(sizeof($emailList)>1?' ('.$email.')':'')
					]);
				}
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


	}


?>
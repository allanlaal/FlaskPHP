<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The Ajax action parameters trait
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Traits;
	use Codelab\FlaskPHP as FlaskPHP;


	trait AjaxActionParameters
	{


		/**
		 *
		 *   Set reload
		 *   ----------
		 *   @access public
		 *   @param bool $reload Reload on success
		 *   @return FlaskPHP\Action\ActionInterface
		 *
		 */

		public function setReload( bool $reload )
		{
			$this->setParam('reload',$reload);
			return $this;
		}


		/**
		 *
		 *   Set redirect URL
		 *   ----------------
		 *   @access public
		 *   @param string $redirectURL Redirect URL
		 *   @return FlaskPHP\Action\ActionInterface
		 *
		 */

		public function setRedirectURL( string $redirectURL )
		{
			$this->setParam('url_redirect',$redirectURL);
			return $this;
		}


		/**
		 *
		 *   Set success action
		 *   ------------------
		 *   @access public
		 *   @param string $successAction Success action
		 *   @param bool $add Add to existing?
		 *   @return FlaskPHP\Action\ActionInterface
		 *
		 */

		public function setSuccessAction( string $successAction, bool $add=true )
		{
			if ($add && $this->getParam('successaction'))
			{
				$successAction=$this->getParam('successaction').$successAction;
			}
			$this->setParam('successaction',$successAction);
			return $this;
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The list action interface
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class ListActionInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Action tag
		 *   @var string
		 *   @access public
		 */

		public $tag = null;


		/**
		 *   Back-reference to list object
		 *   @var FlaskPHP\Action\ListAction
		 *   @access public
		 */

		public $listObject = null;


		/**
		 *
		 *   Set title
		 *   ---------
		 *   @access public
		 *   @param string $title List title
		 *   @return \Codelab\FlaskPHP\Action\ListActionInterface
		 *
		 */

		public function setTitle( string $title )
		{
			$this->setParam('title',$title);
			return $this;
		}


		/**
		 *
		 *   Set action
		 *   ----------
		 *   @access public
		 *   @param string $action Action
		 *   @return \Codelab\FlaskPHP\Action\ListActionInterface
		 *
		 */

		public function setAction( string $action )
		{
			$this->setParam('action',$action);
			return $this;
		}


		/**
		 *
		 *   Set enabled condition
		 *   ---------------------
		 *   @access public
		 *   @param string $enabledIf Enabled condition
		 *   @return \Codelab\FlaskPHP\Action\ListActionInterface
		 *
		 */

		public function setEnabledIf( string $enabledIf )
		{
			$this->setParam('enabled_if',$enabledIf);
			return $this;
		}


		/**
		 *
		 *   Set disabled title
		 *   ------------------
		 *   @access public
		 *   @param string $disabledTitle Disabled title
		 *   @return \Codelab\FlaskPHP\Action\ListActionInterface
		 *
		 */

		public function setDisabledTitle( string $disabledTitle )
		{
			$this->setParam('title_disabled',$disabledTitle);
			return $this;
		}


		/**
		 *
		 *   Set URL
		 *   -------
		 *   @access public
		 *   @param string $url URL
		 *   @return \Codelab\FlaskPHP\Action\ListActionInterface
		 *
		 */

		public function setURL( string $url )
		{
			$this->setParam('url',$url);
			return $this;
		}


	}


?>
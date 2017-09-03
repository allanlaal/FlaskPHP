<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The action interface class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class ContentPartialInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Back-reference to parent object
		 *   @var object
		 *   @access public
		 */

		public $parentObject = null;


		/**
		 *
		 *   Init content partial
		 *   --------------------
		 *   @access public
		 *   @param object $parentObject Parent object
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Action\ContentPartialInterface
		 *
		 */

		public function __construct( object $parentObject=null )
		{
			$this->parentObject=$parentObject;
		}


		/**
		 *
		 *   Render content
		 *   --------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContent()
		{
			// This should be implemented in the subclass.
			throw new FlaskPHP\Exception\NotImplementedException('Function renderContent() not implemented in '.get_class());
		}


	}


?>
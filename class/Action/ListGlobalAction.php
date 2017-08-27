<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The list global action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class ListGlobalAction extends ListActionInterface
	{


		/**
		 *
		 *   Init action
		 *   ------------
		 *   @access public
		 *   @param string $tag Action tag
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Action\ListGlobalAction
		 */

		public function __construct( string $tag=null )
		{
			if ($tag!==null)
			{
				$this->tag=$tag;
			}
		}


	}


?>
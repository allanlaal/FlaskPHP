<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The list row action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class ListRowAction extends ListActionInterface
	{


		/**
		 *
		 *   Init action
		 *   ------------
		 *   @access public
		 *   @param string $tag Action tag
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Action\ListRowAction
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
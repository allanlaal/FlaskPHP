<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The block content partial header item
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\ContentPartial;
	use Codelab\FlaskPHP as FlaskPHP;


	class BlockContentHeaderItem
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @access public
		 *   @return BlockContentHeaderItem
		 *
		 */

		public function __construct()
		{
		}


		/**
		 *
		 *   Set item ID
		 *   -----------
		 *   @param string $itemID Item ID
		 *   @return BlockContentHeaderItem
		 *
		 */

		public function setID( string $itemID )
		{
			$this->setParam('id',$itemID);
			return $this;
		}


		/**
		 *
		 *   Set item class
		 *   --------------
		 *   @param string $itemClass Item class
		 *   @return BlockContentHeaderItem
		 *
		 */

		public function setClass( string $itemClass )
		{
			$this->setParam('class',$itemClass);
			return $this;
		}


		/**
		 *
		 *   Render item
		 *   -----------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderItem()
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function renderItem() not implemented in '.get_called_class().'.');
		}


	}


?>
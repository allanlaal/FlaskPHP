<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The menu bar right side item
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\ContentPartial;
	use Codelab\FlaskPHP as FlaskPHP;


	class MenuBarRightSideItem
	{


		/**
		 *   Content
		 *   @access public
		 *   @var string
		 */

		public $content=null;


		/**
		 *   Wrapper tag
		 *   @access public
		 *   @var string
		 */

		public $wrapperTag='div';


		/**
		 *   Wrapper class
		 *   @access public
		 *   @var string
		 */

		public $wrapperClass=null;


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @param string $content Content
		 *   @param string $wrapperTag Wrapper tag
		 *   @param string $wrapperClass Wrapper class
		 *   @return MenuBarRightSideItem
		 *
		 */

		public function __construct( string $content, string $wrapperTag='div', string $wrapperClass=null )
		{
			$this->content=$content;
			$this->wrapperTag=$wrapperTag;
			$this->wrapperClass=$wrapperClass;
		}


		/**
		 *
		 *   Render item
		 *   -----------
		 *   @access public
		 *   @return string
		 *
		 */

		public function renderItem()
		{
			$c='';
			$wrapperClass='item';
			if ($this->wrapperClass!==null) $wrapperClass.=' '.$this->wrapperClass;
			$c.='<'.$this->wrapperTag.' class="'.$wrapperClass.'">';;

				$c.=$this->content;

			$c.='</'.$this->wrapperTag.'>';
			return $c;
		}


	}


?>
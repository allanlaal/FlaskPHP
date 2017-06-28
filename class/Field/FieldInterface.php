<?php


	/**
	 *
	 *   FlaskPHP
	 *   The field interface
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class FieldInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Field tag
		 *   @var string
		 *   @access public
		 */

		public $tag = null;


		/**
		 *   Back-reference to model object
		 *   @var FlaskPHP\Model\ModelInterface
		 *   @access public
		 */

		public $modelObject = null;


		/**
		 *   Back-reference to form object
		 *   @var FlaskPHP\Action\FormAction
		 *   @access public
		 */

		public $formObject = null;


		/**
		 *   Back-reference to list object
		 *   @var FlaskPHP\Action\ListAction
		 *   @access public
		 */

		public $listObject = null;


		/**
		 *   The constructor
		 *   @access public
		 *   @param string $tag Field tag
		 *   @return \Codelab\FlaskPHP\Field\FieldInterface
		 */

		public function __construct( $tag=null )
		{
			if (!empty($tag)) $this->tag=$tag;
		}


		/**
		 *   Set title
		 *   @access public
		 *   @param string $title Field title
		 *   @return void
		 */

		public function setTitle( $title )
		{
			$this->setParam('title',$title);
		}


		/**
		 *   Get title
		 *   @access public
		 *   @return string
		 */

		public function getTitle()
		{
			return $this->getParam('title');
		}


	}


?>
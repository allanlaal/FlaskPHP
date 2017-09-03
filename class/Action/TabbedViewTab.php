<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The tabbed view tab object
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class TabbedViewTab
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Tab tag
		 *   @var string
		 *   @access public
		 */

		public $tag = null;


		/**
		 *   Back-reference to the view object
		 *   @var FlaskPHP\Action\TabbedViewAction
		 *   @access public
		 */

		public $viewObject = null;


		/**
		 *
		 *   Set title
		 *   ---------
		 *   @access public
		 *   @param string $title Tab title
		 *   @return \Codelab\FlaskPHP\Action\TabbedViewTab
		 *
		 */

		public function setTitle( string $title )
		{
			$this->setParam('title',$title);
			return $this;
		}


		/**
		 *
		 *   Set icon
		 *   --------
		 *   @access public
		 *   @param string $icon Tab icon
		 *   @return \Codelab\FlaskPHP\Action\TabbedViewTab
		 *
		 */

		public function setIcon( string $icon )
		{
			$this->setParam('icon',$icon);
			return $this;
		}


		/**
		 *
		 *   Set handler function
		 *   --------------------
		 *   @access public
		 *   @param string $handlerFunction
		 *   @return \Codelab\FlaskPHP\Action\TabbedViewTab
		 *
		 */

		public function setHandlerFunction( string $handlerFunction, array $handlerFunctionParam=null )
		{
			$this->setParam('handlerfunction',$handlerFunction);
			$this->setParam('handlerfunction_param',$handlerFunctionParam);
			return $this;
		}


		/**
		 *
		 *   Add event
		 *   ---------
		 *   @access public
		 *   @param string $eventType Event type
		 *   @param string $eventAction Event action
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Action\TabbedViewTab
		 *
		 */

		public function addListEvent( string $eventType, string $eventAction )
		{
			// Add
			if ($this->hasParam('event') && array_key_exists($eventType,$this->_param['event']))
			{
				$this->_param['event'][$eventType].=$eventAction;
			}

			// Set
			else
			{
				$this->_param['event'][$eventType]=$eventAction;
			}

			return $this;
		}


		/**
		 *
		 *   Get title
		 *   ---------
		 *   @access public
		 *   @return string
		 *
		 */

		public function getTitle()
		{
			return $this->getParam('title');
		}


	}


?>
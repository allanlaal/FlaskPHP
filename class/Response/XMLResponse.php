<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The XML response class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Response;
	use Codelab\FlaskPHP as FlaskPHP;


	class XMLResponse extends ResponseInterface
	{


		/**
		 *   Parse locale/variables in response
		 *   @access public
		 *   @var object|array|string
		 */

		public $responseParseLocale = true;


		/**
		 *   Init response
		 *   @access public
		 *   @param string|\SimpleXMLElement $responseContent Response content
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Response\XMLResponse
		 */

		public function __construct( $responseContent=null )
		{
			$this->responseContent=$responseContent;
		}


		/**
		 *   Set parse locale
		 *   @access public
		 *   @var bool $parseLocale Parse locale
		 *   @throws \Exception
		 *   @return void
		 */

		public function setParseLocale( bool $parseLocale )
		{
			$this->responseParseLocale=$parseLocale;
		}


		/**
		 *   Render response
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function renderResponse()
		{
			// Output headers
			if (!array_key_exists('Content-type',$this->responseHeader))
			{
				$this->setHeader('Content-type','text/xml; charset=UTF-8');
			}
			$this->outputHttpHeaders();

			// Object: we currently support SimpleXMLElement
			if (is_object($this->responseContent))
			{
				// Check
				if (!($this->responseContent instanceof \SimpleXMLElement))
				{
					throw new \Exception('Response not a SimpleXMLElement object.');
				}

				// Output
				$this->responseContent=$this->responseContent->asXML();
			}

			// Output
			if ($this->responseParseLocale)
			{
				$this->responseContent=FlaskPHP\Template\Template::parseContent($this->responseContent);
			}
			if (strncmp($this->responseContent,'<?xml',5))
			{
				echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			}
			echo $this->responseContent;
		}


	}


?>
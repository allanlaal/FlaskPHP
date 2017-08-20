<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The JSON response class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Response;
	use Codelab\FlaskPHP as FlaskPHP;


	class JSONResponse extends ResponseInterface
	{


		/**
		 *   JSON encode options
		 *   @access public
		 *   @var int
		 */

		public $responseJsonOptions = JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;


		/**
		 *   Parse locale/variables in response
		 *   @access public
		 *   @var object|array|string
		 */

		public $responseParseLocale = true;


		/**
		 *   Init response
		 *   @access public
		 *   @param string|array|object $responseContent Response content
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Response\JSONResponse
		 */

		public function __construct( $responseContent=null )
		{
			$this->responseContent=$responseContent;
		}


		/**
		 *   Set JSON options
		 *   @access public
		 *   @var int $jsonOptions JSON options
		 *   @throws \Exception
		 *   @return void
		 */

		public function setJsonOptions( int $jsonOptions )
		{
			$this->responseJsonOptions=$jsonOptions;
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
			$this->setHeader('Content-type','application/json; charset=UTF-8');
			$this->outputHttpHeaders();

			// Output
			if (is_array($this->responseContent) || is_object($this->responseContent))
			{
				if ($this->responseParseLocale)
				{
					echo FlaskPHP\Template\Template::parseContent(json_encode($this->responseContent,$this->responseJsonOptions));
				}
				else
				{
					echo json_encode($this->responseContent,$this->responseJsonOptions);
				}
			}
			else
			{
				if ($this->responseParseLocale)
				{
					echo FlaskPHP\Template\Template::parseContent($this->responseContent);
				}
				else
				{
					echo $this->responseContent;
				}
			}
		}


	}


?>
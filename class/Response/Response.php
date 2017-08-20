<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The HTTP response handler
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Response;
	use Codelab\FlaskPHP as FlaskPHP;


	class Response
	{


		/**
		 *   Response object
		 *   @var ResponseInterface
		 */

		public $responseObject = null;


		/**
		 *   Set type
		 *   @access public
		 *   @param string $responseType Response type
		 *   @param string $responseContentType Response content-type
		 *   @return Response
		 */

		public function setType( string $responseType, string $responseContentType=null )
		{
			$this->responseType=$responseType;
			if (!empty($responseContentType)) $this->responseContentType=$responseContentType;
			return $this;
		}


		/**
		 *   Set template
		 *   @access public
		 *   @param string $responseTemplate Response template
		 *   @return Response
		 */

		public function setTemplate( string $responseTemplate )
		{
			$this->responseTemplate=$responseTemplate;
			return $this;
		}


		/**
		 *   Handle response
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function handleResponse()
		{
			// Check
			if (empty($this->responseObject))
			{
				throw new FlaskPHP\Exception\FatalException('Empty response.',500);
			}
			if (!($this->responseObject instanceof ResponseInterface))
			{
				throw new FlaskPHP\Exception\FatalException('Response is not a proper ResponseInterface object.',500);
			}

			// Render response
			$this->responseObject->renderResponse();
		}


	}


?>
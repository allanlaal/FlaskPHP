<?php


	/**
	 *
	 *   FlaskPHP
	 *   The response type interface
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Response;
	use Codelab\FlaskPHP as FlaskPHP;


	class ResponseInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Response HTTP status
		 *   @var int
		 */

		public $responseStatus = 200;


		/**
		 *   Other response headers
		 *   @var array
		 *   @access public
		 */

		public $responseHeader = array();


		/**
		 *   Response expiration
		 *   @var int
		 *   @access public
		 */

		public $responseExpires = null;


		/**
		 *   Response content
		 *   @var mixed
		 */

		public $responseContent = null;


		/**
		 *   Possible HTTP statuses
		 *   @var array
		 *   @access public
		 *   @static
		 */

		public static $setHttpStatus = array(
			'100' => 'Continue',
			'101' => 'Switching Protocols',
			'102' => 'Processing',
			'200' => 'OK',
			'201' => 'Created',
			'202' => 'Accepted',
			'203' => 'Non-Authoritative Information',
			'204' => 'No Content',
			'205' => 'Reset Content',
			'206' => 'Partial Content',
			'207' => 'Multi-Status',
			'208' => 'Already Reported',
			'226' => 'IM Used',
			'300' => 'Multiple Choices',
			'301' => 'Moved Permanently',
			'302' => 'Found',
			'303' => 'See Other',
			'304' => 'Not Modified',
			'305' => 'Use Proxy',
			'307' => 'Temporary Redirect',
			'308' => 'Permanent Redirect',
			'400' => 'Bad Request',
			'401' => 'Unauthorized',
			'402' => 'Payment Required',
			'403' => 'Forbidden',
			'404' => 'Not Found',
			'405' => 'Method Not Allowed',
			'406' => 'Not Acceptable',
			'407' => 'Proxy Authentication Required',
			'408' => 'Request Timeout',
			'409' => 'Conflict',
			'410' => 'Gone',
			'411' => 'Length Required',
			'412' => 'Precondition Failed',
			'413' => 'Payload Too Large',
			'414' => 'URI Too Long',
			'415' => 'Unsupported Media Type',
			'416' => 'Range Not Satisfiable',
			'417' => 'Expectation Failed',
			'421' => 'Misdirected Request',
			'422' => 'Unprocessable Entity',
			'423' => 'Locked',
			'424' => 'Failed Dependency',
			'426' => 'Upgrade Required',
			'428' => 'Precondition Required',
			'429' => 'Too Many Requests',
			'431' => 'Request Header Fields Too Large',
			'500' => 'Internal Server Error',
			'501' => 'Not Implemented',
			'502' => 'Bad Gateway',
			'503' => 'Service Unavailable',
			'504' => 'Gateway Timeout',
			'505' => 'HTTP Version Not Supported',
			'506' => 'Variant Also Negotiates',
			'507' => 'Insufficient Storage',
			'508' => 'Loop Detected',
			'510' => 'Not Extended',
			'511' => 'Network Authentication Required'
		);


		/**
		 *   Set response HTTP status
		 *   @access public
		 *   @param int $responseStatus Status
		 *   @throws \Exception
		 *   @return ResponseInterface
		 */

		public function setStatus( int $responseStatus )
		{
			if (empty($responseStatus)) throw new FlaskPHP\Exception\InvalidParameterException('Empty status.');
			if (!array_key_exists($responseStatus,static::$setHttpStatus)) throw new FlaskPHP\Exception\InvalidParameterException('Invalid HTTP status.');
			$this->responseStatus=$responseStatus;
			return $this;
		}


		/**
		 *   Set expiration
		 *   @access public
		 *   @param int $responseExpires expiration as timestamp
		 *   @return ResponseInterface
		 */

		public function setExpires( int $responseExpires )
		{
			$this->responseExpires=$responseExpires;
			return $this;
		}


		/**
		 *   Set content
		 *   @access public
		 *   @var mixed $responseContent Response content
		 *   @throws \Exception
		 *   @return ResponseInterface
		 */

		public function setContent( $responseContent )
		{
			$this->responseContent=$responseContent;
			return $this;
		}


		/**
		 *   Set response header
		 *   @access public
		 *   @param string $headerName Header name
		 *   @param string $headerValue Header value
		 *   @return ResponseInterface
		 */

		function setHeader( $headerName, $headerValue )
		{
			$this->responseHeader[$headerName]=$headerValue;
			return $this;
		}


		/**
		 *   Output HTTP headers
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function outputHttpHeaders()
		{
			// HTTP status
			if ($this->responseStatus!=200)
			{
				header("HTTP/1.1 ".$this->responseStatus." ".static::$setHttpStatus[$this->responseStatus]);
			}

			// Send expires header if set
			if (!empty($this->responseExpires))
			{
				header('Expires: '.date("D, j M Y G:i:s T",$this->responseExpires));
				if ($this->responseExpires>time())
				{
					$age=($this->responseExpires-time());
					header('Pragma: cache');
					header('Cache-Control: max-age='.$age);
					header('User-Cache-Control: max-age='.$age);
				}
				else
				{
					header('Pragma: no-cache');
					header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
				}
			}
			else
			{
				header('Expires: '.date("D, j M Y G:i:s T"));
				header('Pragma: no-cache');
				header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			}

			// Set other headers
			foreach ($this->responseHeader as $headerName => $headerValue)
			{
				header($headerName.': '.(is_array($headerValue)?join('; ',$headerValue):$headerValue));
			}
		}


		/**
		 *   Render response
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function renderResponse()
		{
			// This needs to be implemented in the response class
			throw new FlaskPHP\Exception\NotImplementedException('The function renderResponse() is not implemented in the '.get_called_class().' class.');
		}


	}


?>
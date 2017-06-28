<?php


	/**
	 *
	 *   FlaskPHP
	 *   The HTTP request class
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Http;
	use Codelab\FlaskPHP as FlaskPHP;


	class HttpRequest
	{


		/**
		 *   cURL handle
		 *   @var resource
		 *   @public
		 */

		public $CH = null;


		/**
		 *   URL
		 *   @var string
		 *   @public
		 */

		public $URL = '';


		/**
		 *   Effective URL
		 *   @var string
		 *   @public
		 */

		public $effectiveURL = '';


		/**
		 *   Method
		 *   @var string
		 *   @public
		 */

		public $requestMethod = 'GET';


		/**
		 *   Request headers
		 *   @var array
		 *   @public
		 */

		public $requestHeader = array();


		/**
		 *   Options
		 *   @var array
		 *   @public
		 */

		public $requestOptions = array();


		/**
		 *   Get fields
		 *   @var array
		 *   @public
		 */

		public $getFields = array();


		/**
		 *   Post fields
		 *   @var array
		 *   @public
		 */

		public $postFields = array();


		/**
		 *   File uploads
		 *   @var array
		 *   @public
		 */

		public $fileUploads = array();


		/**
		 *   Raw POST data
		 *   @var string
		 *   @public
		 */

		public $rawPostData = null;


		/**
		 *   Response info
		 *   @var array
		 *   @public
		 */

		public $responseInfo = null;


		/**
		 *   Response body
		 *   @var string
		 *   @public
		 */

		public $responseBody = null;


		/**
		 *   Constructor
		 *   @public
		 *   @param string $URL URL
		 *   @param string $requestMethod Method
		 *   @param array  $requestOptions Options
		 *   @return HTTPRequest
		 */

		public function __construct( string $URL=null, string $requestMethod=null, array $requestOptions=null )
		{
			// Set parameters
			if (!empty($URL)) $this->setURL($URL);
			if (!empty($requestMethod)) $this->setRequestMethod($requestMethod);
			if (!empty($requestOptions)) $this->setOptions($requestOptions);
		}


		/**
		 *   Set URL
		 *   @public
		 *   @param string $URL URL
		 *   @return void
		 */

		public function setURL( string $URL )
		{
			$this->URL=$URL;
		}


		/**
		 *   Set method
		 *   @public
		 *   @param string $requestMethod Request method (POST, GET, PUT, PATCH, DELETE)
		 *   @return void
		 */

		public function setRequestMethod( string $requestMethod )
		{
			$this->requestMethod=mb_strtoupper($requestMethod);
		}


		/**
		 *   Set request option
		 *   @public
		 *   @param string $optionName Option name
		 *   @param mixed $optionValue Option value
		 *   @return void
		 */

		public function setOption( string $optionName, $optionValue )
		{
			$this->requestOptions[$optionName]=$optionValue;
		}


		/**
		 *   Set request options
		 *   @public
		 *   @param array $requestOptions Options
		 *   @return void
		 */

		public function setOptions( array $requestOptions )
		{
			foreach ($requestOptions as $k => $v)
			{
				$this->requestOptions[$k]=$v;
			}
		}


		/**
		 *   Set GET field
		 *   @public
		 *   @param string $field Field name
		 *   @param mixed $value Value
		 *   @return void
		 */

		public function setGetField( string $field, $value )
		{
			$this->getFields[$field]=$value;
		}


		/**
		 *   Set GET fields
		 *   @public
		 *   @param array $getFields GET fields
		 *   @return void
		 */

		public function setGetFields( array $getFields )
		{
			foreach ($getFields as $k => $v)
			{
				$this->getFields[$k]=$v;
			}
		}


		/**
		 *   Set POST field
		 *   @public
		 *   @param string $field Field name
		 *   @param mixed $value Value
		 *   @return void
		 */

		public function setPostField( string $field, $value )
		{
			$this->postFields[$field]=$value;
		}


		/**
		 *   Set POST fields
		 *   @public
		 *   @param array $postFields Post fields
		 *   @return void
		 */

		public function setPostFields( array $postFields )
		{
			foreach ($postFields as $k => $v)
			{
				$this->postFields[$k]=$v;
			}
		}


		/**
		 *   Set raw POST data
		 *   @public
		 *   @param string $rawPostData Raw POST data
		 *   @return void
		 */

		public function setRawPostData( string $rawPostData )
		{
			$this->rawPostData=$rawPostData;
		}


		/**
		 *   Set up file upload
		 *   @public
		 *   @param string $sourceFilename Source filename
		 *   @param string $contentType Content-type
		 *   @param string $postFilename POST file name
		 *   @param string $postFileID Post file ID
		 *   @return void
		 *   @throws \Exception
		 */

		public function setFileUpload( string $sourceFilename, string $contentType=null, string $postFilename=null, string $postFileID=null )
		{
			// Check & init
			if (empty($sourceFilename)) throw new FlaskPHP\Exception\Exception('Missing source file name.');
			if (!is_readable($sourceFilename)) throw new FlaskPHP\Exception\Exception('Source file not readable.');

			// Check/get filename
			if (empty($postFilename))
			{
				$postFilename=pathinfo($sourceFilename,PATHINFO_BASENAME);
			}

			// Check/get content-type
			if (empty($contentType))
			{
				$contentType=FlaskPHP\Util::getMimeType($postFilename);
			}

			// File ID
			if (empty($postFileID))
			{
				$postFileID=uniqid();
			}

			// Create CURL file object
			$cfile=curl_file_create($sourceFilename,$contentType,$postFilename);

			// Set method, just-in-case
			$this->setRequestMethod('POST');

			// Add to file upload list
			$this->fileUploads[$postFileID]=$cfile;
		}


		/**
		 *   Set login
		 *   @public
		 *   @param string $username Username
		 *   @param string $password Password
		 *   @return void
		 */

		public function setLogin( string $username, string $password )
		{
			$this->setOption('httpauth',$username.':'.$password);
		}


		/**
		 *   Set request header
		 *   @public
		 *   @param string $headerName Header name
		 *   @param mixed $headerValue Header value
		 *   @return void
		 */

		public function setRequestHeader( string $headerName, $headerValue )
		{
			$this->requestHeader[$headerName]=$headerValue;
		}


		/**
		 *   Set request options
		 *   @public
		 *   @param array $requestHeaders Headers
		 *   @return void
		 */

		public function setRequestHeaders( array $requestHeaders )
		{
			foreach ($requestHeaders as $k => $v)
			{
				$this->requestHeader[$k]=$v;
			}
		}


		/**
		 *   Set user agent
		 *   @public
		 *   @param string $userAgent User agent string
		 *   @return void
		 */

		public function setUserAgent( string $userAgent )
		{
			$this->setRequestHeader('User-Agent',$userAgent);
		}


		/**
		 *   Set request content-type
		 *   @public
		 *   @param string $requestContentType Request content-type
		 *   @return void
		 */

		public function setRequestContentType( string $requestContentType )
		{
			$this->setRequestHeader('Content-Type',$requestContentType);
		}


		/**
		 *   Build a HTTP GET query string from an array
		 *   @access public
		 *   @static
		 *   @param array $fields Fields
		 *   @param bool $fixSpace Fix spaces
		 *   @param string $encodeTo Encode result to given charset
		 *   @return string
		 */

		public static function buildQueryString( $fields, bool $fixSpace=false, string $encodeTo=null )
		{
			$QS=array();
			foreach ($fields as $k => $v)
			{
				if ($encodeTo) $v=iconv('UTF-8',$encodeTo,$v);
				$v=urlencode($v);
				if ($fixSpace) $v=str_replace('+','%20',$v);
				if ($fixSpace) $v=str_replace('%3B',';',$v);
				$QS[]=$k.'='.$v;
			}
			return join('&',$QS);
		}


		/**
		 *   Make request
		 *   @public
		 *   @return string content
		 *   @throws \Exception
		 */

		public function send()
		{
			try
			{
				// Check
				if (!mb_strlen($this->URL)) throw new FlaskPHP\Exception\HttpRequestException('URL not specified.');
				if (!in_array($this->requestMethod,array('GET','POST','PUT','PATCH','DELETE'))) throw new FlaskPHP\Exception\HttpRequestException('Invalid request method.');

				// Init
				$this->CH=curl_init();
				if (empty($this->CH)) throw new FlaskPHP\Exception\HttpRequestException('Error initializing curl.');

				// Set URL
				$this->effectiveURL=$this->URL;
				if (sizeof($this->getFields))
				{
					$this->effectiveURL.='?'.static::buildQueryString($this->getFields);
				}
				curl_setopt($this->CH,CURLOPT_URL,$this->effectiveURL);

				// Set method
				switch($this->requestMethod)
				{
					case 'GET':
						curl_setopt($this->CH,CURLOPT_HTTPGET,1);
						break;
					case 'POST':
						curl_setopt($this->CH,CURLOPT_POST,1);
						if (!empty($this->fileUploads))
						{
							curl_setopt($this->CH,CURLOPT_POSTFIELDS,$this->fileUploads);
						}
						elseif (mb_strlen($this->rawPostData))
						{
							if (empty($this->requestHeader['Content-Type'])) $this->requestHeader['Content-Type']='text/plain';
							curl_setopt($this->CH,CURLOPT_POSTFIELDS,$this->rawPostData);
							$this->setRequestHeader('Content-Length',strlen($this->rawPostData));
						}
						else
						{
							curl_setopt($this->CH,CURLOPT_POSTFIELDS,$this->postFields);
						}
						break;
					case 'PUT':
						curl_setopt($this->CH,CURLOPT_POST,1);
						curl_setopt($this->CH, CURLOPT_CUSTOMREQUEST, 'PUT');
						if (mb_strlen($this->rawPostData))
						{
							if (empty($this->requestHeader['Content-Type'])) $this->requestHeader['Content-Type']='text/plain';
							curl_setopt($this->CH,CURLOPT_POSTFIELDS,$this->rawPostData);
							$this->setRequestHeader('Content-Length',strlen($this->rawPostData));
						}
						else
						{
							curl_setopt($this->CH,CURLOPT_POSTFIELDS,$this->postFields);
						}
						break;
					case 'PATCH':
						curl_setopt($this->CH,CURLOPT_POST,1);
						curl_setopt($this->CH, CURLOPT_CUSTOMREQUEST, 'PATCH');
						if (mb_strlen($this->rawPostData))
						{
							if (empty($this->requestHeader['Content-Type'])) $this->requestHeader['Content-Type']='text/plain';
							curl_setopt($this->CH,CURLOPT_POSTFIELDS,$this->rawPostData);
							$this->setRequestHeader('Content-Length',strlen($this->rawPostData));
						}
						else
						{
							curl_setopt($this->CH,CURLOPT_POSTFIELDS,$this->postFields);
						}
						break;
					case 'DELETE':
						curl_setopt($this->CH,CURLOPT_POST,1);
						curl_setopt($this->CH, CURLOPT_CUSTOMREQUEST, 'DELETE');
						if (mb_strlen($this->rawPostData))
						{
							if (empty($this->requestHeader['Content-Type'])) $this->requestHeader['Content-Type']='text/plain';
							curl_setopt($this->CH,CURLOPT_POSTFIELDS,$this->rawPostData);
							$this->setRequestHeader('Content-Length',strlen($this->rawPostData));
						}
						else
						{
							curl_setopt($this->CH,CURLOPT_POSTFIELDS,$this->postFields);
						}
						break;
					default:
						throw new FlaskPHP\Exception\HttpRequestException('Unknown request method: '.$this->requestMethod);
				}

				// Apply options
				if (array_key_exists('httpauth',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_HTTPAUTH,CURLAUTH_BASIC) ;
					curl_setopt($this->CH,CURLOPT_USERPWD,$this->requestOptions['httpauth']);
				}
				elseif (array_key_exists('username',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_HTTPAUTH,CURLAUTH_BASIC) ;
					curl_setopt($this->CH,CURLOPT_USERPWD,$this->requestOptions['username'].":".$this->requestOptions['password']);
				}
				if (array_key_exists('ssl_verifyhost',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_SSL_VERIFYHOST,$this->requestOptions['ssl_verifyhost']);
				}
				else
				{
					curl_setopt($this->CH,CURLOPT_SSL_VERIFYHOST,false);
				}
				if (array_key_exists('ssl_verifypeer',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_SSL_VERIFYPEER,$this->requestOptions['ssl_verifypeer']);
				}
				else
				{
					curl_setopt($this->CH,CURLOPT_SSL_VERIFYPEER,false);
				}
				if (array_key_exists('ssl_cainfo',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_CAINFO,$this->requestOptions['ssl_cainfo']);
				}
				else
				{
					curl_setopt($this->CH, CURLOPT_CAINFO,Flask()->getFlaskPath().'/data/cacert/cacert.pem');
				}
				if (array_key_exists('ssl_version',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_SSLVERSION,$this->requestOptions['ssl_version']);
				}
				if (array_key_exists('followlocation',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_FOLLOWLOCATION,$this->requestOptions['followlocation']);
				}
				else
				{
					curl_setopt($this->CH,CURLOPT_FOLLOWLOCATION,true);
				}
				if (array_key_exists('connecttimeout',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_CONNECTTIMEOUT,$this->requestOptions['connecttimeout']);
				}
				else
				{
					curl_setopt($this->CH,CURLOPT_CONNECTTIMEOUT,'15');
				}
				if (array_key_exists('timeout',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_TIMEOUT,$this->requestOptions['timeout']);
				}
				else
				{
					curl_setopt($this->CH,CURLOPT_TIMEOUT,'60');
				}
				if (array_key_exists('cookiejar',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_COOKIEJAR,$this->requestOptions['cookiejar']);
					curl_setopt($this->CH,CURLOPT_COOKIEFILE,$this->requestOptions['cookiejar']);
				}
				elseif (!empty($this->requestOptions['newcookiesession']))
				{
					curl_setopt($this->CH,CURLOPT_COOKIESESSION,true);
				}
				if (array_key_exists('referer',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_REFERER,$this->requestOptions['referer']);
				}
				if (array_key_exists('useragent',$this->requestOptions))
				{
					curl_setopt($this->CH,CURLOPT_USERAGENT,$this->requestOptions['useragent']);
				}

				// Apply headers
				if (sizeof($this->requestHeader))
				{
					$requestHeader=array();
					foreach ($this->requestHeader as $header => $value)
					{
						$requestHeader[]=$header.': '.$value;
					}
					curl_setopt($this->CH,CURLOPT_HTTPHEADER,$requestHeader);
				}

				// Those shall we always need
				curl_setopt($this->CH,CURLOPT_ENCODING,'');
				curl_setopt($this->CH,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($this->CH,CURLINFO_HEADER_OUT,1);

				// Exec
				$this->responseBody=curl_exec($this->CH);

				// Error?
				if ($this->responseBody===false)
				{
					throw new FlaskPHP\Exception\HttpRequestException('Error making HTTP request: '.curl_errno($this->CH).' - '.curl_error($this->CH));
				}

				// Store request info
				$this->responseInfo=curl_getinfo($this->CH);

				// Close handle
				curl_close($this->CH);

				// Return
				return $this->responseBody;
			}
			catch (\Exception $e)
			{
				if (!empty($this->CH)) curl_close($this->CH);
				throw $e;
			}
		}


		/**
		 *   Get response code
		 *   @public
		 *   @return int
		 */

		public function getResponseCode()
		{
			return (array_key_exists('http_code',$this->responseInfo)?$this->responseInfo['http_code']:null);
		}


	}


?>
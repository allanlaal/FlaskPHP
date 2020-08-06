<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The request handler class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Request;
	use Codelab\FlaskPHP as FlaskPHP;


	class Request
	{


		/**
		 *   Request method
		 *   @var string
		 *   @access public
		 */

		public $requestMethod = null;


		/**
		 *   Request URI
		 *   @var string
		 *   @access public
		 */

		public $requestURI = null;


		/**
		 *   Request URL
		 *   @var string
		 *   @access public
		 */

		public $requestURL = null;


		/**
		 *   Request headers
		 *   @var string
		 *   @access public
		 */

		public $requestHeader = array();


		/**
		 *   Request URI variables
		 *   @var array
		 *   @access public
		 */

		public $requestUriVar = array();


		/**
		 *   Request URI variables by position
		 *   @var array
		 *   @access public
		 */

		public $requestUriVarByPos = array();


		/**
		 *   Request language is set from URI?
		 *   @var boolean
		 *   @access public
		 */

		public $requestLangFromURI = null;


		/**
		 *   Request controller mapping handlers
		 *   @var array
		 *   @access public
		 */

		public $requestControllerMapper = array();


		/**
		 *   Request controller name
		 *   @var string
		 *   @access public
		 */

		public $requestController = null;


		/**
		 *   Request controller instance object
		 *   @var \Codelab\FlaskPHP\Controller\ControllerInterface
		 *   @access public
		 */

		public $requestControllerObject = null;


		/**
		 *   Request language
		 *   @var string
		 *   @access public
		 */

		public $requestLang = null;


		/**
		 *
		 *   Init controller mapper handlers
		 *   -------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initControllerMappers()
		{
			// Flask handler
			$this->requestControllerMapper[]=array(
				'mapper' => new FlaskRequestControllerMapper(),
				'priority' => -999
			);

			// Standard handler
			$this->requestControllerMapper[]=array(
				'mapper' => new AppRequestControllerMapper(),
				'priority' => 0
			);

			// See if we have any in config
			if (is_array(Flask()->Config->get('request.controllermapper')))
			{
				foreach (Flask()->Config->get('request.controllermapper') as $controllerMapper)
				{
					if (!is_array($controllerMapper)) throw new FlaskPHP\Exception\InvalidParameterException('Controller mapper must be an array.');
					if (empty($controllerMapper['mapper'])) throw new FlaskPHP\Exception\InvalidParameterException('Controller mapper must contain a "mapper" key.');
					if (!is_object($controllerMapper['mapper']) || !($controllerMapper['mapper'] instanceof RequestControllerMapperInterface)) throw new FlaskPHP\Exception\InvalidParameterException('The "mapper" element must be an object of RequestControllerMapperInterface type.');
					if (!empty($controllerMapper['priority']) && !is_numeric($controllerMapper['priority'])) throw new FlaskPHP\Exception\InvalidParameterException('The "priority" element must be a numeric value.');
					if (empty($controllerMapper['priority'])) $controllerMapper['priority']=(sizeof($this->requestControllerMapper)+1);
					$this->requestControllerMapper[]=$controllerMapper;
				}
			}

			// Sort
			$this->requestControllerMapper=sortdataset($this->requestControllerMapper,'priority');
		}


		/**
		 *
		 *   Handle request
		 *   --------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function handleRequest()
		{
			// Init controller handlers
			$this->initControllerMappers();
			// Request method
			$this->requestMethod=$_SERVER['REQUEST_METHOD'];

			// Language
			$this->requestLang=Flask()->Locale->localeLanguage;

			// Headers
			$this->requestHeader=$this->parseRequestHeaders();

			// Parse URI
			$uriArray=oneof(str_array(preg_split("/[?&#]/",$_SERVER['REQUEST_URI'])[0],'\/'),array('index'));
			$this->requestURI='/'.join('/',$uriArray);
			$this->requestURL=Flask()->Config->get('app.url').$this->requestURI;
			while (true)
			{
				// Done?
				if (!sizeof($uriArray)) break;

				// Get next element
				$uriElement=array_shift($uriArray);

				// Check for language
				if ($this->requestLangFromURI===null)
				{
					if (mb_strlen($uriElement)==2 && Flask()->Config->get('request.setlang')!==false && Flask()->Locale->localeAvailable($uriElement))
					{
						$this->requestLang=mb_strtolower($uriElement);
						Flask()->Session->set('LANG',$uriElement);
						Flask()->Locale->loadLocale($this->requestLang);
						$this->requestLangFromURI=true;
						if (!sizeof($uriArray))
						{
							$uriArray=array('index');
						}
						continue;
					}
					else
					{
						$this->requestLangFromURI=false;
					}
				}

				// Check for controller
				if ($this->requestControllerObject===null)
				{
					// Check name
					if (!preg_match("/^[A-Za-z0-9\.\_\-]+$/",$uriElement))
					{
						header("Status: 400 Bad Request");
						throw new FlaskPHP\Exception\FatalException('HTTP 400: Bad request.');
					}

					// Cycle through mappers
					foreach ($this->requestControllerMapper as $controllerMapper)
					{
						$requestController=$controllerMapper['mapper']->runControllerMapper($uriElement,$uriArray);
						if (is_object($requestController))
						{
							$this->requestController=$uriElement;
							$this->requestControllerObject=$requestController;
							continue 2;
						}
					}

					// Nothing found?
					if (empty($this->requestControllerObject))
					{
						throw new FlaskPHP\Exception\FatalException('HTTP 404: The requested address /'.$uriElement.' was not found.',404);
					}
				}

				// Must be a variable
				$varName=$varValue='';
				if (mb_strpos($uriElement,'=')!==false)
				{
					list($varName,$varValue)=preg_split('/=/',$uriElement,2);
				}
				else
				{
					$varName=$uriElement;
					$varValue=true;
				}

				// Set variable
				if (is_string($varValue) && preg_match("/^[0-9]+$/",$varValue)) $varValue=intval($varValue);
				if (is_string($varValue) && preg_match("/^[0-9\.]+$/",$varValue)) $varValue=floatval($varValue);
				$this->requestUriVar[$varName]=$varValue;
				$this->requestUriVarByPos[sizeof($this->requestUriVarByPos)+1]=array(
					'name'  => $varName,
					'value' => $varValue
				);
			}

			// Load language if not set from URI
			if (!$this->requestLangFromURI && mb_strlen(Flask()->Session->get('LANG')))
			{
				Flask()->Locale->loadLocale(Flask()->Session->get('LANG'));
			}

			// Bootstrap request
			if (is_array(Flask()->Config->get('request.bootstrap')))
			{
				foreach (Flask()->Config->get('request.bootstrap') as $requestBootstrapFile)
				{
					$resolvedRequestBootstrapFile=Flask()->resolvePath($requestBootstrapFile);
					if (!$resolvedRequestBootstrapFile) throw new FlaskPHP\Exception\FatalException('Request bootstrap file '.$requestBootstrapFile.' not found.');
					$bootstrapRetVal=require($resolvedRequestBootstrapFile);
					if (is_object($bootstrapRetVal) && ($bootstrapRetVal instanceof FlaskPHP\Response\ResponseInterface))
					{
						Flask()->Response->responseObject=$bootstrapRetVal;
						return;
					}
				}
			}

			// Run controller
			$this->runController();
		}


		/**
		 *
		 *   Run controller
		 *   --------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function runController()
		{
			// Init controller
			$this->requestControllerObject->initController();

			// Run controller
			Flask()->Response->responseObject=$this->requestControllerObject->runController();
		}


		/**
		 *
		 *   Sanitize input
		 *   --------------
		 *   @access public
		 *   @static
		 *   @param mixed $value Value
		 *   @return mixed Sanitized value
		 *
		 */

		public static function sanitizeInput( $value )
		{
			if (is_array($value))
			{
				foreach ($value as $k => $v)
				{
					$value[$k]=static::sanitizeInput($v);
				}
				return $value;
			}
			else
			{
				return trim(strip_tags($value));
			}
		}


		/**
		 *
		 *   Get GET variable
		 *   ----------------
		 *   @access public
		 *   @param string $var Variable name
		 *   @param bool $sanitize Sanitize input
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function getVar( string $var, bool $sanitize=true )
		{
			if (array_key_exists($var,$_GET))
			{
				if ($sanitize) return static::sanitizeInput($_GET[$var]);
				return $_GET[$var];
			}
			return null;
		}


		/**
		 *
		 *   Get POST variable
		 *   -----------------
		 *   @access public
		 *   @param string $var Variable name
		 *   @param bool $sanitize Sanitize input
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function postVar( string $var, bool $sanitize=true )
		{
			if (array_key_exists($var,$_POST))
			{
				if ($sanitize) return static::sanitizeInput($_POST[$var]);
				return $_POST[$var];
			}
			return null;
		}


		/**
		 *
		 *   Get URI variable
		 *   ----------------
		 *   @access public
		 *   @param string $var Variable name
		 *   @param bool $sanitize Sanitize input
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function uriVar( string $var, bool $sanitize=true )
		{
			if (array_key_exists($var,$this->requestUriVar))
			{
				if ($sanitize) return static::sanitizeInput($this->requestUriVar[$var]);
				return $this->requestUriVar[$var];
			}
			return null;
		}


		/**
		 *
		 *   Get URI variable by position
		 *   ----------------------------
		 *   @access public
		 *   @param string $var Variable name
		 *   @param bool $sanitize Sanitize input
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function uriVarByPos( int $pos, bool $sanitize=true )
		{
			if (array_key_exists($pos,$this->requestUriVarByPos))
			{
				$var=$this->requestUriVarByPos[$pos];
				if ($sanitize) $var['value']=static::sanitizeInput($var['value']);
				return $var;
			}
			return null;
		}


		/**
		 *
		 *   Get cookie
		 *   ----------
		 *   @access public
		 *   @param string $var Cookie name
		 *   @param bool $sanitize Sanitize input
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function getCookie( string $var, bool $sanitize=true )
		{
			if (array_key_exists($var,$_COOKIE))
			{
				if ($sanitize) return static::sanitizeInput($_COOKIE[$var]);
				return $_COOKIE[$var];
			}
			return null;
		}


		/**
		 *
		 *   Is user using Internet Explorer?
		 *   --------------------------------
		 *   @access public
		 *   @return boolean
		 *
		 */

		public function isIE()
		{
			$userAgent=$_SERVER['HTTP_USER_AGENT'];
			if (strpos($userAgent,'; MSIE ')!==false) return true;
			if (strpos($userAgent,'Trident/')!==false && strpos($userAgent,'rv:')!==false) return true;
			return false;
		}


		/**
		 *
		 *   Are we handling an XHR?
		 *   -----------------------
		 *   @access public
		 *   @return boolean
		 *
		 */

		public function isXHR()
		{
			return ((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')?true:false);
		}


		/**
		 *
		 *   Is this an HTTPS request?
		 *   -------------------------
		 *   @access public
		 *   @return boolean
		 *
		 */

		public function isHTTPS()
		{
			if (!array_key_exists('HTTPS',$_SERVER)) return false;
			if ($_SERVER['HTTPS']=='off') return false;
			return true;
		}


		/**
		 *
		 *   Get user's IP
		 *   -------------
		 *   @access public
		 *   @return string IP
		 *
		 */

		public function remoteIP()
		{
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			elseif (isset($_SERVER['REMOTE_ADDR']))
			{
				return $_SERVER['REMOTE_ADDR'];
			}
			else
			{
				return '';
			}
		}


		/**
		 *
		 *   Get user's hostname
		 *   -------------------
		 *   @access public
		 *   @return string hostname/IP
		 *
		 */

		public function remoteHost()
		{
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				return @gethostbyaddr($_SERVER['HTTP_X_FORWARDED_FOR']);
			}
			elseif (isset($_SERVER['REMOTE_ADDR']))
			{
				return @gethostbyaddr($_SERVER['REMOTE_ADDR']);
			}
			else
			{
				return '';
			}
		}


		/**
		 *
		 *   Get request method
		 *   ------------------
		 *   @access public
		 *   @return string Request method
		 *
		 */

		public function getRequestMethod()
		{
			return $this->requestMethod;
		}


		/**
		 *
		 *   Get request URI
		 *   ---------------
		 *   @access public
		 *   @return string Request URI
		 *
		 */

		public function getRequestURI()
		{
			return $this->requestURI;
		}


		/**
		 *
		 *   Get request URL
		 *   ---------------
		 *   @access public
		 *   @return string Request URL
		 *
		 */

		public function getRequestURL()
		{
			return $this->requestURL;
		}


		/**
		 *
		 *   Parse request headers
		 *   ---------------------
		 *   @access public
		 *   @return array
		 *
		 */

		public function parseRequestHeaders()
		{
			// Do we have apache_request_headers() ?
			if (function_exists('apache_request_headers'))
			{
				return apache_request_headers();
			}

			// Parse from $_SERVER
			$requestHeader=array();
			foreach ($_SERVER as $k => $v)
			{
				if (preg_match("/^HTTP\_/",$k))
				{
					$headerName=ucwords(str_replace('_','-',$k),'-');
					$requestHeader[$headerName]=$v;
				}
			}
			return $requestHeader;
		}


		/**
		 *
		 *   Get request header
		 *   ------------------
		 *   @access public
		 *   @param string $headerName Header name
		 *   @param bool $sanitize Sanitize input
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function getRequestHeader( string $headerName, bool $sanitize=true )
		{
			foreach ($this->requestHeader as $rhName => $rhValue)
			{
				if (mb_strtolower($headerName)==mb_strtolower($rhName))
				{
					if ($sanitize) return static::sanitizeInput($rhValue);
					return $rhValue;
				}
			}
			return null;
		}


		/**
		 *
		 *   Get bearer token
		 *   ----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function getBearerToken()
		{
			$authHeader=$this->getRequestHeader('Authorization');
			if ($authHeader && preg_match('/Bearer\s(\S+)/',$authHeader,$match))
			{
				return $match[1];
			}
			return null;
		}



	}


?>
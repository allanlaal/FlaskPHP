<?php


	/**
	 *
	 *   FlaskPHP
	 *   FlaskPHP superobject class
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP;


	class FlaskPHP
	{


		/**
		 *   Request type (http, cli)
		 *   @var string
		 */

		public $requestType = null;


		/**
		 *   Request ID
		 *   @var string
		 */

		public $requestID = null;


		/**
		 *   Request start timer
		 *   @var int
		 */

		public $requestStartTime = null;


		/**
		 *   App path
		 *   @var string
		 */

		public $appPath = null;


		/**
		 *   FlaskPHP path
		 *   @var string
		 */

		public $flaskPath = null;


		/**
		 *   Configuration
		 *   @var \Codelab\FlaskPHP\Config\Config
		 */

		public $Config = null;


		/**
		 *   Locale
		 *   @var \Codelab\FlaskPHP\Locale\Locale
		 */

		public $Locale = null;


		/**
		 *   I18n
		 *   @var \Codelab\FlaskPHP\I18n\I18n
		 */

		public $I18n = null;


		/**
		 *   Cache
		 *   @var \Codelab\FlaskPHP\Cache\Cache
		 */

		public $Cache = null;


		/**
		 *   Debug
		 *   @var \Codelab\FlaskPHP\Debug\Debug
		 */

		public $Debug = null;


		/**
		 *   Database connection
		 *   @var \Codelab\FlaskPHP\DB\DBInterface
		 */

		public $DB = null;


		/**
		 *   Session handler
		 *   @var \Codelab\FlaskPHP\Session\SessionInterface
		 */

		public $Session = null;


		/**
		 *   User handler
		 *   @var \Codelab\FlaskPHP\User\UserInterface
		 */

		public $User = null;


		/**
		 *   Request handler
		 *   @var \Codelab\FlaskPHP\Request\Request
		 */

		public $Request = null;


		/**
		 *   Response handler
		 *   @var \Codelab\FlaskPHP\Response\Response
		 */

		public $Response = null;


		/**
		 *   Init
		 *   @access public
		 *   @var string $requestType Request type
		 *   @var string $appPath App path
		 *   @var string $flaskPath FlaskPHP path
		 */

		public function __construct( string $requestType, string $appPath=null, string $flaskPath=null )
		{
			$this->requestType=$requestType;
			$this->requestID=date('YmdHis').'-'.uniqid();
			$this->requestStartTime=microtime_float();
			$this->appPath=$appPath;
			$this->flaskPath=$flaskPath;
		}


		/**
		 *   Get request ID
		 *   @access public
		 *   @return string
		 */

		public function getRequestID()
		{
			return $this->requestID;
		}


		/**
		 *   Get request start time (in milliseconds)
		 *   @access public
		 *   @return int
		 */

		public function getRequestStartTime()
		{
			return $this->requestStartTime;
		}


		/**
		 *   Get request time (in seconds)
		 *   @access public
		 *   @return float
		 */

		public function getRequestTime()
		{
			$currentTime=microtime_float();
			return floatval($currentTime-$this->requestStartTime);
		}


		/**
		 *   Get app path
		 *   @access public
		 *   @return string
		 */

		public function getAppPath()
		{
			return $this->appPath;
		}


		/**
		 *   Get FlaskPHP path
		 *   @access public
		 *   @return string
		 */

		public function getFlaskPath()
		{
			return $this->flaskPath;
		}


		/**
		 *   Resolve path
		 *   @access public
		 *   @param string $filename File name
		 *   @param bool $includeFlaskPath Include Flask paths
		 *   @return string
		 */

		public function resolvePath( string $filename, bool $includeFlaskPath=true )
		{
			// Absolute path?
			if ($filename[0]=='/') return (is_readable($filename)?realpath($filename):null);

			// Exists in app path?
			if (is_readable($this->appPath.'/'.$filename)) return realpath($this->appPath.'/'.$filename);

			// Include path
			if (is_array(Flask()->Config->get('app.includepath')))
			{
				foreach (Flask()->Config->get('app.includepath') as $includePath)
				{
					if (is_readable($includePath.'/'.$filename)) return realpath($includePath.'/'.$filename);
				}
			}

			// In Flask path?
			if ($includeFlaskPath && is_readable($this->flaskPath.'/'.$filename)) return realpath($this->flaskPath.'/'.$filename);

			// Nothing found
			return null;
		}


	}


?>
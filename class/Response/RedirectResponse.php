<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The HTTP redirect response class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Response;
	use Codelab\FlaskPHP as FlaskPHP;


	class RedirectResponse extends ResponseInterface
	{


		/**
		 *   Response redirect
		 *   @var string
		 *   @access public
		 */

		public $responseRedirect = null;


		/**
		 *   Response redirect HTTP status
		 *   @var int
		 *   @access public
		 */

		public $responseRedirectStatus = 302;


		/**
		 *   Init response
		 *   @access public
		 *   @param string $responseRedirect Redirect URL
		 *   @param int $redirectStatus Redirect status
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Response\RedirectResponse
		 */

		public function __construct( string $responseRedirect=null, int $redirectStatus=null )
		{
			if (!empty($responseRedirect))
			{
				$this->setRedirect($responseRedirect,$redirectStatus);
			}
		}


		/**
		 *   Set redirect
		 *   @access public
		 *   @param string $responseRedirect Redirect URL
		 *   @param int $redirectStatus Redirect status
		 *   @throws \Exception
		 *   @return void
		 */

		public function setRedirect( string $responseRedirect, int $redirectStatus=null )
		{
			$this->responseRedirect=$responseRedirect;
			if (!empty($redirectStatus))
			{
				if ($redirectStatus<300 || $redirectStatus>399) throw new FlaskPHP\Exception\InvalidParameterException('Invalid redirect status.');
				$this->setStatus($redirectStatus);
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
			// CHeck
			if (empty($this->responseRedirect))
			{
				throw new FlaskPHP\Exception\FatalException('Missing redirect address.',500);
			}

			// Redirect
			if (!empty($this->responseRedirect))
			{
				// Send redirect headers
				if (strpos($this->responseRedirect,"://")===false)
				{
					$this->responseRedirect=Flask()->Config->get('app.url').$this->responseRedirect;
				}
				header("Request-URI: ".$this->responseRedirect);
				header("Content-Location: ".$this->responseRedirect);
				header("Location: ".$this->responseRedirect);
				header('Expires: '.date("D, j M Y G:i:s T"));
				header('Pragma: no-cache');
				header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			}
		}


	}


?>
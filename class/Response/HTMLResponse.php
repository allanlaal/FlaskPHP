<?php


	/**
	 *
	 *   FlaskPHP
	 *   The HTML response class
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Response;
	use Codelab\FlaskPHP as FlaskPHP;


	class HTMLResponse extends ResponseInterface
	{


		/**
		 *   Meta tags
		 *   @var array
		 *   @access public
		 */

		public $responseMeta = array();


		/**
		 *   Response template
		 *   @var string
		 *   @access public
		 */

		public $responseTemplate = 'default';


		/**
		 *   Response variables
		 *   @var array
		 *   @access public
		 */

		public $responseVar = array();


		/**
		 *   Response title
		 *   @var string
		 *   @access public
		 */

		public $responseTitle = null;


		/**
		 *   Page title
		 *   @var string
		 *   @access public
		 */

		public $responsePageTitle = '';


		/**
		 *   HTML <body> attributes
		 *   @var array
		 *   @access public
		 */

		public $responseBodyAttr = array();


		/**
		 *   Response JavaScript
		 *   @var \Codelab\FlaskPHP\Response\ResponseJS
		 */

		public $reponseJS = null;


		/**
		 *   Response CSS
		 *   @var \Codelab\FlaskPHP\Response\ResponseCSS
		 */

		public $responseCSS = null;


		/**
		 *   Constructor
		 *   @access public
		 *   @param string $responseContent Response content
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Response\HTMLResponse
		 */

		public function __construct( $responseContent=null )
		{
			$this->responseJS=new ResponseJS();
			$this->responseCSS=new ResponseCSS();
			$this->responseContent=$responseContent;
		}


		/**
		 *   Set meta header
		 *   @access public
		 *   @param string|array $paramName Parameter name (or array of names)
		 *   @param string $paramValue Value
		 *   @return void
		 */

		function setMeta( $paramName, $paramValue )
		{
			if (is_array($paramName))
			{
				foreach ($paramName as $p)
				{
					$this->responseMeta[$p]=$paramValue;
				}
			}
			else
			{
				$this->responseMeta[$paramName]=$paramValue;
			}
		}


		/**
		 *   Set body attribute
		 *   @access public
		 *   @param string|array $attributeName Parameter name (or array of names)
		 *   @param string $attributeValue Value
		 *   @return void
		 */

		function setBodyAttribute( $attributeName, $attributeValue )
		{
			if (!is_array($this->responseBodyAttr)) $this->responseBodyAttr=array();
			$this->responseBodyAttr[$attributeName]=$attributeValue;
		}


		/**
		 *   Set page title
		 *   @access public
		 *   @param string $title Page title
		 *   @param boolean|int $append Append (true or 1) or prepend (2 or -1) to current title
		 *   @param string $titleSeparator Title separator
		 *   @return void
		 */

		function setPageTitle( $title, $append=true, $titleSeparator='»' )
		{
			if ($append)
			{
				if (intval($append)==2 || intval($append)==-1)
				{
					$this->responsePageTitle=$title.(!empty($this->responsePageTitle)?' '.$titleSeparator.' '.$this->responsePageTitle:'');
				}
				else
				{
					$this->responsePageTitle.=(!empty($this->responsePageTitle)?' '.$titleSeparator.' ':'').$title;
				}
			}
			else
			{
				$this->responsePageTitle=$title;
			}
		}


		/**
		 *   Get page title
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 */

		public function getPageTitle()
		{
			return $this->responsePageTitle;
		}


		/**
		 *   Output extra content
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function outputExtraContent()
		{
			// Display debug output
			echo Flask()->Debug->getDebugOutput();
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
				$this->setHeader('Content-type','text/html; charset=UTF-8');
			}
			$this->outputHttpHeaders();

			// Parse template
			$responseTemplate=new FlaskPHP\Template\Template((strpos($this->responseTemplate,'/')===false?'response.':'').oneof($this->responseTemplate,'default'));
			$responseTemplate->templateVar=$this->responseVar;
			$responseTemplate->templateVar['CSS']=$this->responseCSS;
			$responseTemplate->templateVar['JS']=$this->responseJS;
			$responseTemplate->templateVar['content']=$this->responseContent;
			$responseTemplate->templateVar['response']=$this;
			$responseContent=$responseTemplate->render();

			// Display HTML
			echo '<!DOCTYPE html>';
			echo '<html lang="'.Flask()->Request->requestLang.'">';

			// Head begins
			echo '<head>';

				// Title
				echo '<title>'.$this->getPageTitle().'</title>';

				// For our dear friends at Microsoft
				echo '<meta charset="utf-8">';
				echo '<meta name="MSSmartTagsPreventParsing" content="true">';
				echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
				if (is_readable(Flask()->getAppPath().'/public/browserconfig.xml'))
				{
					echo '<meta name="msapplication-config" content="/browserconfig.xml" />';
				}
				else
				{
					echo '<meta name="msapplication-config" content="none" />';
				}
				echo '<!--[if lt IE 9]>';
				echo '<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>';
				echo '<![endif]-->';

				// Viewport
				if (empty($this->responseMeta['viewport']))
				{
					echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
				}

				// Open Graph and other meta
				foreach ($this->responseMeta as $param => $content)
				{
					if (preg_match("/^og:/",$param) || preg_match("/^article:/",$param))
					{
						$paramType='property';
					}
					elseif (in_array($param,array('content-language')))
					{
						$paramType='http-equiv';
					}
					else
					{
						$paramType='name';
					}
					echo '<meta '.$paramType.'="'.htmlspecialchars($param).'" content="'.htmlspecialchars($content).'">';
				}

				// Icons
				$iconPath=oneof($this->getParam('iconpath'),Flask()->Config->get('app.iconpath'),'/static/gfx');
				$iconFilePath=Flask()->getAppPath().'/public'.$iconPath;
				$appleIcons=array(
					'57x57',
					'60x60',
					'72x72',
					'76x76',
					'76x76',
					'120x120',
					'144x144',
					'152x152',
					'180x180'
				);
				foreach ($appleIcons as $appleIcon)
				{
					if (file_exists($iconFilePath.'/apple-touch-icon-'.$appleIcon.'.png'))
					{
						echo '<link rel="apple-touch-icon" sizes="'.$appleIcon.'" href="'.$iconPath.'/apple-touch-icon-'.$appleIcon.'.png">';
					}
				}
				$androidIcons=array(
					'192x192'
				);
				foreach ($androidIcons as $androidIcon)
				{
					if (file_exists($iconFilePath.'/android-icon-'.$androidIcon.'.png'))
					{
						echo '<link rel="icon" type="image/png" sizes="'.$androidIcon.'" href="'.$iconPath.'/android-icon-'.$androidIcon.'.png">';
					}
				}
				$favIcons=array(
					'16x16',
					'32x32',
					'96x96'
				);
				foreach ($favIcons as $favIcon)
				{
					if (file_exists($iconFilePath.'/favicon-'.$favIcon.'.png'))
					{
						echo '<link rel="icon" type="image/png" sizes="'.$favIcon.'" href="'.$iconPath.'/favicon-'.$favIcon.'.png">';
					}
				}
				if (file_exists($iconFilePath.'/favicon.ico'))
				{
					echo '<link rel="shortcut icon" href="'.$iconPath.'/favicon.ico" />';
				}
				elseif (file_exists(Flask()->getAppPath().'/public/favicon.ico'))
				{
					echo '<link rel="shortcut icon" href="/favicon.ico" />';
				}
				if (file_exists($iconFilePath.'/ms-icon-144x144.png'))
				{
					echo '<meta name="msapplication-TileColor" content="#ffffff">';
					echo '<meta name="msapplication-TileImage" content="'.$iconPath.'/ms-icon-144x144.png">';
				}

				// CSS
				echo $this->responseCSS->outputCSS();

				// JavaScript
				echo $this->responseJS->outputJS();

				// Other
				if (strlen($this->getParam('header_js')))
				{
					echo $this->getParam('header_js');
				}

			// Head ends
			echo '</head>';

			// Body begins
			echo '<body';
			foreach ($this->responseBodyAttr as $param => $value)
			{
				echo ' '.$param.='="'.htmlspecialchars($value).'"';
			}
			echo '>';

				// Display content
				echo $responseContent;

				// Profiler info and errors
				$this->outputExtraContent();

			echo '</body>';
			echo '</html>';
		}


	}


?>
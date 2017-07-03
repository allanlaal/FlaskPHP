<?php


	/**
	 *
	 *   FlaskPHP
	 *   The response CSS provider
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Response;
	use Codelab\FlaskPHP as FlaskPHP;


	class ResponseCSS
	{


		/**
		 *   Response CSS files
		 *   @var array
		 *   @access public
		 */

		public $responseCSS = array();


		/**
		 *   Response external CSS files
		 *   @var string
		 *   @access public
		 */

		public $responseExternalCSS = array();


		/**
		 *   Response CSS watchlist (for SCSS @import includes)
		 *   @var array
		 *   @access public
		 */

		public $responseWatchList = array();


		/**
		 *   SCSS parser
		 *   @var \Leafo\ScssPhp\Compiler
		 *   @access private
		 */

		private $SCSS = null;


		/**
		 *   Add a CSS file
		 *   @access public
		 *   @param string $cssFilename Filename
		 *   @param string $cssID ID
		 *   @param int $cssPriority Priority (1-9)
		 *   @param string $cssBundle Bundle
		 *   @return void
		 */

		public function addCSS( string $cssFilename, string $cssID=null, int $cssPriority=5, string $cssBundle='css' )
		{
			// Init bundle
			$bundleItem=new ResponseCSSItem();
			$bundleItem->itemType='file';
			$bundleItem->itemFilename=$cssFilename;
			$bundleItem->itemPriority=$cssPriority;

			// Add
			if ($cssID===null) $cssID=intval(sizeof($this->responseCSS[$cssBundle])+1);
			$this->responseCSS[$cssBundle][$cssID]=$bundleItem;
		}


		/**
		 *   Add dynamic CSS
		 *   @access public
		 *   @param string $cssSource Source
     *   @param int $modifiedTimestamp Modified timestamp
		 *   @param string $cssID ID
		 *   @param int $cssPriority Priority (1-9)
		 *   @param string $cssBundle Bundle
		 *   @throws \Exception
		 *   @return void
		 */

		public function addDynamicCSS( string $cssSource, int $modifiedTimestamp, string $cssID, int $cssPriority=5, string $cssBundle='css' )
		{
			// Check
			if (!strlen($cssID)) throw new FlaskPHP\Exception\Exception('ID is required, but empty for dynamic CSS.');

			// Init bundle
			$bundleItem=new ResponseCSSItem();
			$bundleItem->itemType='inline';
			$bundleItem->itemSource=$cssSource;
			$bundleItem->itemModifiedTimestamp=$modifiedTimestamp;
			$bundleItem->itemPriority=$cssPriority;

			// Add
			if (!strlen($cssID)) $cssID=uniqid();
			$this->responseCSS[$cssBundle][$cssID]=$bundleItem;
		}


		/**
		 *   Add an external CSS resource
		 *   @access public
		 *   @param string $cssURL URL
		 *   @param string $cssID ID
		 *   @param int $cssPriority Priority (1-9)
		 *   @return void
		 */

		public function addExternalCSS( string $cssURL, string $cssID=null, int $cssPriority=5 )
		{
			// Init bundle
			$bundleItem=new ResponseCSSItem();
			$bundleItem->itemType='url';
			$bundleItem->itemURL=$cssURL;
			$bundleItem->itemPriority=$cssPriority;

			// Add
			if ($cssID===null) $cssID=uniqid();
			$this->responseExternalCSS[$cssID]=$bundleItem;
		}


		/**
		 *   Add Bootstrap
		 *   @access public
		 *   @param string $bootstrapTheme Bootstrap theme
		 *   @param string $cssID ID
		 *   @param int $cssPriority Priority (1-9)
		 *   @param string $cssBundle Bundle
		 *   @return void
		 */

		public function addBootstrap( string $bootstrapTheme=null, int $cssPriority=1, string $cssBundle='css' )
		{
			// Init bundle
			$bundleItem=new ResponseCSSItem();
			$bundleItem->itemType='bootstrap';
			$bundleItem->itemFilename=$bootstrapTheme;
			$bundleItem->itemPriority=$cssPriority;

			// Add
			$this->responseCSS[$cssBundle]['bootstrap']=$bundleItem;
		}


		/**
		 *   Add a watchlist item
		 *   @access public
		 *   @param string $fileName Filename
		 *   @param string $cssBundle Bundle
		 *   @return void
		 */

		public function addWatchList( string $fileName, string $cssBundle='css' )
		{
			$this->responseWatchList[$cssBundle][$fileName]=$fileName;
		}


		/**
		 *   Minify CSS
		 *   @access public
		 *   @return string
		 */

		public function minifyCSS( $CSS )
		{
			// Remove comments
			$CSS=preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','',$CSS);

			// Remove space after colons
			$CSS=str_replace(': ', ':',$CSS);

			// Remove whitespace
			$CSS=str_replace(array("\r\n","\r","\n","\t",'  ','   ','    ','     ','      '),'',$CSS);

			// Return
			return $CSS;
		}


		/**
		 *   Init SCSS compiler
		 *   @access public
		 *   @return string
		 *   @throws \Exception
		 */

		public function initSCSS()
		{
			// Init SCSS compiler
			$this->SCSS=new \Leafo\ScssPhp\Compiler();
			$this->SCSS->setFormatter('Leafo\ScssPhp\Formatter\Expanded');
			$this->SCSS->addImportPath(Flask()->getAppPath());
			$this->SCSS->addImportPath(Flask()->getFlaskPath());
		}


		/**
		 *   Parse SCSS
		 *   @access public
		 *   @return string
		 *   @throws \Exception
		 */

		public function parseSCSS( $CSS )
		{
			try
			{
				return $this->minifyCSS($this->SCSS->compile($CSS));
			}
			catch (\Exception $e)
			{
				throw new FlaskPHP\Exception\Exception('Error parsing CSS/SCSS: '.$e->getMessage());
			}
		}


		/**
		 *   Output CSS
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 */

		public function outputCSS()
		{
			$c='';

			//
			//  Standard CSS bundles
			//

			foreach ($this->responseCSS as $bundleID => $bundleContents)
			{
				// Build asset array
				$assetArray=array();
				$assetTimeStamp=0;
				$bundleContents=sortdataset($bundleContents,'itemPriority');
				foreach ($bundleContents as $cssID => $cssItem)
				{
					if ($cssItem->itemType=='inline')
					{
						$assetArray[$cssID]=$cssID;
						$filemtime=$cssItem->itemModifiedTimestamp;
					}
					elseif ($cssID->itemType=='bootstrap')
					{
						// Bootstrap itself
						$assetArray['bootstrap']='bootstrap';

						// Themed Bootstrap
						if (!empty($cssItem->itemFilename))
						{
							if (!Flask()->resolvePath($cssItem->itemFilename)) throw new FlaskPHP\Exception\Exception('Bootstrap theme file not readable: '.$cssItem->itemFilename);
							$filemtime=filemtime(Flask()->resolvePath($cssItem->itemFilename));
						}

						// Bootstrap file
						$filemtime=filemtime(Flask()->resolvePath('static/vendor/bootstrap/css/bootstrap-themed.scss'));
						if ($filemtime>$assetTimeStamp) $assetTimeStamp=$filemtime;
					}
					else
					{
						if (!Flask()->resolvePath($cssItem->itemFilename)) throw new FlaskPHP\Exception\Exception('CSS file not readable: '.$cssItem->itemFilename);
						$assetArray[$cssID]=$cssItem->itemFilename;
						$filemtime=filemtime(Flask()->resolvePath($cssItem->itemFilename));
					}
					if ($filemtime>$assetTimeStamp) $assetTimeStamp=$filemtime;
				}
				if (is_array($this->responseWatchList[$bundleID]))
				{
					foreach ($this->responseWatchList[$bundleID] as $watchListFile)
					{
						if (!Flask()->resolvePath($watchListFile)) throw new FlaskPHP\Exception\Exception('CSS watchlist file not readable: '.$watchListFile);
						$filemtime=filemtime(Flask()->resolvePath($watchListFile));
						if ($filemtime>$assetTimeStamp) $assetTimeStamp=$filemtime;
					}
				}
				$assetHash=md5(join('|',array_keys($assetArray)));
				$assetFileName=oneof(Flask()->Config->get('app.assetcachepath'),Flask()->Config->getTmpPath()).'/'.Flask()->Config->get('app.id').'.asset.'.$bundleID.'.'.$assetHash.'.'.intval($assetTimeStamp).'.css';

				// If file does not exist, build it
				if (!file_exists($assetFileName))
				{
					$this->initSCSS();
					$assetFileContents='';
					foreach ($bundleContents as $cssID => $cssItem)
					{
						if ($cssItem->itemType=='inline')
						{
							$assetFileContents.=$cssItem->itemSource;
						}
						elseif ($cssItem->itemType=='bootstrap')
						{
							if (!empty($cssItem->itemFilename))
							{
								$this->SCSS->addImportPath(pathinfo(Flask()->resolvePath($cssItem->itemFilename),PATHINFO_DIRNAME));
								$assetFileContents.=file_get_contents(Flask()->resolvePath($cssItem->itemFilename));
							}
							$bootStrapFilename='static/vendor/bootstrap/css/bootstrap.scss';
							$this->SCSS->addImportPath(pathinfo(Flask()->resolvePath($bootStrapFilename),PATHINFO_DIRNAME));
							$assetFileContents.=file_get_contents(Flask()->resolvePath($bootStrapFilename));
						}
						else
						{
							$this->SCSS->addImportPath(pathinfo(Flask()->resolvePath($cssItem->itemFilename),PATHINFO_DIRNAME));
							$assetFileContents.=file_get_contents(Flask()->resolvePath($cssItem->itemFilename));
						}
					}
					$assetFileContents=$this->parseSCSS($assetFileContents);
					file_put_contents($assetFileName,$assetFileContents);
				}

				// CSS bundle link
				$c.='<link rel="stylesheet" href="/flask/css/bundle/'.$bundleID.'.'.$assetHash.'.'.intval($assetTimeStamp).'.css">';
			}

			//
			//   External CSS
			//

			if (sizeof($this->responseExternalCSS))
			{
				$this->responseExternalCSS=sortdataset($this->responseExternalCSS,'itemPriority');
				foreach ($this->responseExternalCSS as $cssID => $cssItem)
				{
					$c.='<link rel="stylesheet" href="'.$cssItem->itemURL.'">';
				}
			}

			// Return
			return $c;
		}


	}


?>
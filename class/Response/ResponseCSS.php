<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The response CSS provider
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
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
		 *   Response CSS compiler
		 *   @var string
		 *   @access public
		 */

		public $responseCompiler = 'less';


		/**
		 *   Import paths
		 *   @var array
		 *   @access private
		 */

		private $responseImportPath = array();


		/**
		 *   Less parser
		 *   @var lessc
		 *   @access private
		 */

		private $Less = null;


		/**
		 *   SCSS parser
		 *   @var \Leafo\ScssPHP\Compiler
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
			if ($cssID===null) $cssID=md5($bundleItem->itemFilename);
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
			if ($cssID===null) $cssID=md5($bundleItem->itemURL);
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
		 *   Add Semantic UI
		 *   @access public
		 *   @param string $semanticTheme Semantic theme config file
		 *   @param int $cssPriority Priority (1-9)
		 *   @param string $cssBundle Bundle
		 *   @return void
		 */

		public function addSemantic( string $semanticTheme=null, int $cssPriority=1, string $cssBundle='css' )
		{
			// Init bundle
			$bundleItem=new ResponseCSSItem();
			$bundleItem->itemType='semantic';
			$bundleItem->itemFilename=$semanticTheme;
			$bundleItem->itemPriority=$cssPriority;

			// Add
			$this->responseCSS[$cssBundle]['semantic']=$bundleItem;
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
		 *   Add import path
		 *   @access public
		 *   @param string $importPath
		 *   @return void
		 */

		public function addImportPath( string $importPath )
		{
			if (!in_array($importPath,$this->responseImportPath))
			{
				$this->responseImportPath[]=$importPath;
			}
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

		public function initLess()
		{
			// Init SCSS compiler
			$this->Less=new \Less_Parser(array(
				'compress' => true,
				'strictMath' => true
			));
			$this->addImportPath(Flask()->getAppPath());
			$this->addImportPath(Flask()->getFlaskPath());
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
			$this->addImportPath(Flask()->getAppPath());
			$this->addImportPath(Flask()->getFlaskPath());
		}


		/**
		 *   Parse Less
		 *   @access public
		 *   @return string
		 *   @throws \Exception
		 */

		public function parseLess( $CSS )
		{
			try
			{
				$importPathList=array();
				foreach ($this->responseImportPath as $importPath)
				{
					$importPathList[$importPath]='';
				}
				$this->Less->SetImportDirs($importPathList);
				$this->Less->parse($CSS);
				return $this->Less->getCss();
			}
			catch (\Exception $e)
			{
				throw new FlaskPHP\Exception\Exception('Error parsing CSS/Less: '.$e->getMessage());
			}
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
				foreach ($this->responseImportPath as $importPath)
				{
					$this->SCSS->addImportPath($importPath);
				}
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
					elseif ($cssItem->itemType=='semantic')
					{
						// Ugly Hack until LessPHP is fixed
						$cssItem->filename='static/vendor/semantic/semantic.min.css';
						if (!Flask()->resolvePath($cssItem->itemFilename)) throw new FlaskPHP\Exception\Exception('CSS file not readable: '.$cssItem->itemFilename);
						$assetArray[$cssID]=$cssItem->itemFilename;
						$filemtime=filemtime(Flask()->resolvePath($cssItem->itemFilename));

						/*
						// Semantic itself
						$assetArray['semantic']='semantic';

						// Themed Bootstrap
						if (!empty($cssItem->itemFilename))
						{
							if (!Flask()->resolvePath($cssItem->itemFilename)) throw new FlaskPHP\Exception\Exception('Semantic theme file not readable: '.$cssItem->itemFilename);
							$filemtime=filemtime(Flask()->resolvePath($cssItem->itemFilename));
						}

						// Bootstrap file
						$filemtime=filemtime(Flask()->resolvePath('static/vendor/semantic/semantic.less'));
						if ($filemtime>$assetTimeStamp) $assetTimeStamp=$filemtime;
						*/
					}
					elseif ($cssItem->itemType=='bootstrap')
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
					switch (mb_strtolower($this->responseCompiler))
					{
						case 'scss':
							$this->initSCSS();
							break;
						default:
							$this->initLess();
							break;
					}
					$assetFileContents='';
					foreach ($bundleContents as $cssID => $cssItem)
					{
						if ($cssItem->itemType=='inline')
						{
							$assetFileContents.=$cssItem->itemSource;
						}
						elseif ($cssItem->itemType=='semantic')
						{
							if (mb_strtolower($this->responseCompiler)!='less') throw new FlaskPHP\Exception\Exception('Semantic needs Less as the CSS compiler.');

							// Ugly Hack until LessPHP is fixed
							$semanticFilename='static/vendor/semantic/semantic.min.css';
							$this->addImportPath(pathinfo(Flask()->resolvePath($semanticFilename),PATHINFO_DIRNAME));
							$assetFileContents.=file_get_contents(Flask()->resolvePath($semanticFilename));

							/*
							if (!empty($cssItem->itemFilename))
							{
								$this->addImportPath(pathinfo(Flask()->resolvePath($cssItem->itemFilename),PATHINFO_DIRNAME));
								$assetFileContents.="@semanticThemeConfig: '".$cssItem->itemFilename."';\n";
							}
							$semanticFilename='static/vendor/semantic/semantic.less';
							$this->addImportPath(pathinfo(Flask()->resolvePath($semanticFilename),PATHINFO_DIRNAME));
							$assetFileContents.=file_get_contents(Flask()->resolvePath($semanticFilename));
							*/
						}
						elseif ($cssItem->itemType=='bootstrap')
						{
							if (mb_strtolower($this->responseCompiler)!='scss') throw new FlaskPHP\Exception\Exception('Bootstrap needs SCSS as the CSS compiler.');
							if (!empty($cssItem->itemFilename))
							{
								$this->addImportPath(pathinfo(Flask()->resolvePath($cssItem->itemFilename),PATHINFO_DIRNAME));
								$assetFileContents.=file_get_contents(Flask()->resolvePath($cssItem->itemFilename));
							}
							$bootStrapFilename='static/vendor/bootstrap/css/bootstrap.scss';
							$this->addImportPath(pathinfo(Flask()->resolvePath($bootStrapFilename),PATHINFO_DIRNAME));
							$assetFileContents.=file_get_contents(Flask()->resolvePath($bootStrapFilename));
						}
						else
						{
							$this->addImportPath(pathinfo(Flask()->resolvePath($cssItem->itemFilename),PATHINFO_DIRNAME));
							$assetFileContents.=file_get_contents(Flask()->resolvePath($cssItem->itemFilename));
						}
					}

					// Run parser
					switch (mb_strtolower($this->responseCompiler))
					{
						case 'scss':
							$assetFileContents=$this->parseSCSS($assetFileContents);
							break;
						default:
							$assetFileContents=$this->parseLess($assetFileContents);
							break;
					}

					// Delete earlier copies of the asset
					array_map('unlink', glob(oneof(Flask()->Config->get('app.assetcachepath'),Flask()->Config->getTmpPath()).'/'.Flask()->Config->get('app.id').'.asset.'.$bundleID.'.'.$assetHash.'.*.css'));

					// Write file
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
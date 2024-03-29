<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The response JS provider
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Response;
	use Codelab\FlaskPHP as FlaskPHP;


	class ResponseJS
	{


		/**
		 *   Response JavaScripts
		 *   @var array
		 *   @access public
		 */

		public $responseJS = array();


		/**
		 *   Response external JavaScripts
		 *   @var array
		 *   @access public
		 */

		public $responseExternalJS = array();


		/**
		 *   Response inline JavaScripts
		 *   @var array
		 *   @access public
		 */

		public $responseInlineJS = array();


		/**
		 *   Response base JS
		 *   @var array
		 *   @access public
		 */

		public $responseBaseJS = array(
			'jquery',
			'tether',
			'flask'
		);


		/**
		 *   Response locale
		 *   @var string
		 *   @access public
		 */

		public $responseLocale = null;


		/**
		 *   Response locale is simple/short?
		 *   @var string
		 *   @access public
		 */

		public $responseLocaleShort = null;


		/**
		 *   Add a JS file
		 *   @access public
		 *   @param string $jsFilename Filename
		 *   @param string $jsID ID
		 *   @param int|bool $jsPriority Priority (1-9)
		 *   @param array $jsExtraParam Extra parameters
		 *   @param string $jsBundle Bundle ID
		 *   @return ResponseJS
		 */

		public function addJS( string $jsFilename, string $jsID=null, int $jsPriority=5, array $jsExtraParam=null, string $jsBundle='js' )
		{
			// Init bundle
			$bundleItem=new ResponseJSItem();
			$bundleItem->itemType='file';
			$bundleItem->itemFilename=$jsFilename;
			$bundleItem->itemPriority=$jsPriority;
			$bundleItem->itemParam=$jsExtraParam;

			// Add
			if ($jsID===null) $jsID=md5($bundleItem->itemFilename);
			$this->responseJS[$jsBundle][$jsID]=$bundleItem;

			// Return self
			return $this;
		}


		/**
		 *   Add an external JS source
		 *   @access public
		 *   @param string $jsURL URL
		 *   @param string $jsID ID
		 *   @param int $jsPriority Priority (1-9)
		 *   @param array $jsExtraParam Extra parameters
		 *   @return ResponseJS
		 */

		public function addExternalJS( string $jsURL, string $jsID=null, int $jsPriority=5, array $jsExtraParam=null )
		{
			// Init bundle
			$bundleItem=new ResponseJSItem();
			$bundleItem->itemType='url';
			$bundleItem->itemURL=$jsURL;
			$bundleItem->itemPriority=$jsPriority;
			$bundleItem->itemParam=$jsExtraParam;

			// Add
			if ($jsID===null) $jsID=md5($bundleItem->itemURL);
			$this->responseExternalJS[$jsID]=$bundleItem;

			// Return self
			return $this;
		}


		/**
		 *   Add an inline JS
		 *   @access public
		 *   @param string $jsSource JS source
		 *   @param string $jsID ID
		 *   @param int $jsPriority Priority (1-9)
		 *   @param array $jsExtraParam Extra parameters
		 *   @return ResponseJS
		 */

		public function addInlineJS( string $jsSource, string $jsID=null, int $jsPriority=5, array $jsExtraParam=null )
		{
			// Init bundle
			$bundleItem=new ResponseJSItem();
			$bundleItem->itemType='inline';
			$bundleItem->itemSource=$jsSource;
			$bundleItem->itemPriority=$jsPriority;
			$bundleItem->itemParam=$jsExtraParam;

			// Add
			if ($jsID===null) $jsID=md5($bundleItem->itemSource);
			$this->responseInlineJS[$jsID]=$bundleItem;

			// Return self
			return $this;
		}


		/**
		 *   Output locale?
		 *   @access public
		 *   @param string $lang Language tag
		 *   @param bool $shortLocale Output short locale
		 *   @return ResponseJS
		 */

		public function addLocale( string $lang, bool $shortLocale=false )
		{
			$this->responseLocale=$lang;
			$this->responseLocaleShort=$shortLocale;
			return $this;
		}


		/**
		 *   Add base JS module
		 *   @access public
		 *   @param string|array $module Module(s)
		 *   @return ResponseJS
		 */

		public function addBaseJS( $module )
		{
			$moduleList=str_array($module);
			foreach ($moduleList as $m)
			{
				if (!in_array($m,$this->responseBaseJS))
				{
					$this->responseBaseJS[]=$m;
				}
			}
			return $this;
		}


		/**
		 *   Set base JS modules
		 *   @access public
		 *   @param array $moduleList Module list
		 *   @return ResponseJS
		 */

		public function setBaseJS( array $moduleList )
		{
			$this->responseBaseJS=$moduleList;
			return $this;
		}


		/**
		 *   Clear base JS module list
		 *   @access public
		 *   @return ResponseJS
		 */

		public function clearBaseJS()
		{
			$this->responseBaseJS=array();
			return $this;
		}


		/**
		 *   Minify JS
		 *   @access public
		 *   @param string $js JS source
		 *   @throws \Exception
		 *   @return string
		 */

		public function minifyJS( string $js )
		{
			// Do not minify in dev
			if (Flask()->Debug->devEnvironment) return $js;

			// Do the magic
			try
			{
				// Minify
				$js=\JShrink\Minifier::minify($js,array(
					'flaggedComments' => false
				));

				// Return
				return $js;
			}
			catch (\Exception $e)
			{
				throw new FlaskPHP\Exception\Exception('Error minifying JS: '.$e->getMessage());
			}
		}


		/**
		 *   Output JS
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 */

		public function outputJS()
		{
			$c='';


			//
			//  Priority external JS
			//

			if (sizeof($this->responseExternalJS))
			{
				$this->responseExternalJS=sortdataset($this->responseExternalJS,'itemPriority',false,SORT_NUMERIC);
				foreach ($this->responseExternalJS as $jsID => $jsItem)
				{
					if ($jsItem->itemPriority>=0) continue;
					$scriptParam=array();
					$scriptParam[]='src="'.$jsItem->itemURL.'"';
					foreach ($jsItem->itemParam as $itemParam => $itemParamValue) $scriptParam[]=$itemParam.'="'.$itemParamValue.'"';
					$c.='<script '.join(' ',$scriptParam).'></script>';
				}
			}


			//
			//  Add base components
			//

			if (in_array('jquery',$this->responseBaseJS))
			{
				$this->addJS('static/vendor/jquery/jquery.min.js','base_jquery',-9999);
				$this->addJS('static/vendor/jquery/jquery.form.min.js','base_jquery_form',-9998);
				$this->addJS('static/vendor/jquery/jquery.maskedinput.min.js','base_jquery_maskedinput',-9997);
			}
			if (in_array('tether',$this->responseBaseJS))
			{
				$this->addJS('static/vendor/tether/js/tether.js','base_tether',-9980);
			}
			if (in_array('popper',$this->responseBaseJS))
			{
				$this->addJS('static/vendor/popper/popper.min.js','base_popper',-9970);
			}
			if (in_array('semantic',$this->responseBaseJS))
			{
				$this->addJS('static/vendor/semantic/semantic.min.js','base_semantic',-9950);
				$this->addJS('static/vendor/moment/moment.min.js','base_moment',-9930);
			}
			if (in_array('bootstrap',$this->responseBaseJS))
			{
				$this->addJS('static/vendor/bootstrap/js/bootstrap.js','base_bootstrap',-9960);
			}
			if (in_array('underscore',$this->responseBaseJS))
			{
				$this->addJS('static/vendor/underscore/underscore.min.js','base_underscore',-9940);
			}
			if (in_array('moment',$this->responseBaseJS))
			{
				$this->addJS('static/vendor/moment/moment.min.js','base_moment',-9930);
			}
			if (in_array('flask',$this->responseBaseJS))
			{
				$this->addJS('js/flask.js','base_flask',-9000);
			}

			//
			//  Standard JS bundles
			//

			foreach ($this->responseJS as $bundleID => $bundleContents)
			{
				// Build asset array
				$assetArray=array();
				$assetTimeStamp=0;
				$bundleContents=sortdataset($bundleContents,'itemPriority',false,SORT_NUMERIC);
				foreach ($bundleContents as $jsID => $jsItem)
				{
					if (!Flask()->resolvePath($jsItem->itemFilename)) throw new FlaskPHP\Exception\Exception('JS file not readable: '.$jsItem->itemFilename);
					$assetArray[$jsID]=$jsItem->itemFilename;
					$filemtime=filemtime(Flask()->resolvePath($jsItem->itemFilename));
					if ($filemtime>$assetTimeStamp) $assetTimeStamp=$filemtime;
				}
				$assetHash=md5(join('|',array_keys($assetArray)));
				$assetFileName=oneof(Flask()->Config->get('app.assetcachepath'),Flask()->Config->getTmpPath()).'/'.Flask()->Config->get('app.id').'.asset.'.$bundleID.'.'.$assetHash.'.'.intval($assetTimeStamp).'.js';

				// If file does not exist, build it
				if (!file_exists($assetFileName))
				{
					$assetFileContents='';
					foreach ($bundleContents as $jsID => $jsItem)
					{
						if (mb_strpos($jsItem->itemFilename,'.min.js')!==false)
						{
							$assetFileContents.=file_get_contents(Flask()->resolvePath($jsItem->itemFilename))."\n\n";
						}
						else
						{
							$assetFileContents.=$this->minifyJS(file_get_contents(Flask()->resolvePath($jsItem->itemFilename)))."\n\n";
						}
					}

					// Delete earlier copies of the asset
					array_map('unlink', glob(oneof(Flask()->Config->get('app.assetcachepath'),Flask()->Config->getTmpPath()).'/'.Flask()->Config->get('app.id').'.asset.'.$bundleID.'.'.$assetHash.'.*.js'));

					// Write file
					file_put_contents($assetFileName,$assetFileContents);
				}

				// JS bundle link
				$c.='<script src="/flask/js/bundle/'.$bundleID.'.'.$assetHash.'.'.intval($assetTimeStamp).'.js"></script>';
			}


			//
			//  Locale
			//

			if ($this->responseLocale!==null)
			{
				if ($this->responseLocaleShort)
				{
					$c.='<script src="/flask/js/slocale/'.$this->responseLocale.'/locale.js"></script>';
				}
				else
				{
					$c.='<script src="/flask/js/locale/'.$this->responseLocale.'/locale.js"></script>';
				}
			}


			//
			//  External JS
			//

			if (sizeof($this->responseExternalJS))
			{
				$this->responseExternalJS=sortdataset($this->responseExternalJS,'itemPriority',false,SORT_NUMERIC);
				foreach ($this->responseExternalJS as $jsID => $jsItem)
				{
					if ($jsItem->itemPriority<0) continue;
					$scriptParam=array();
					$scriptParam[]='src="'.$jsItem->itemURL.'"';
					foreach ($jsItem->itemParam as $itemParam => $itemParamValue) $scriptParam[]=$itemParam.'="'.$itemParamValue.'"';
					$c.='<script '.join(' ',$scriptParam).'></script>';
				}
			}


			//
			//  Inline JS
			//

			if (sizeof($this->responseInlineJS))
			{
				$this->responseInlineJS=sortdataset($this->responseInlineJS,'itemPriority',false,SORT_NUMERIC);
				foreach ($this->responseInlineJS as $jsID => $jsItem)
				{
					$scriptParam=array();
					foreach ($jsItem->itemParam as $itemParam => $itemParamValue) $scriptParam[]=$itemParam.'="'.$itemParamValue.'"';
					$c.='<script'.(sizeof($scriptParam)?' '.join(' ',$scriptParam):'').'>'.$jsItem->itemSource.'</script>';
				}
			}


			// Return
			return $c;
		}


	}


?>
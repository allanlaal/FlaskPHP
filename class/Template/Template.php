<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Template parser
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Template;
	use Codelab\FlaskPHP as FlaskPHP;


	class Template
	{


		/**
		 *   Template name
		 *   @var string
		 *   @access public
		 */

		public $templateName = '';


		/**
		 *   Template variables
		 *   @var array
		 *   @access public
		 */

		public $templateVar = array();


		/**
		 *   Template functions
		 *   @var array
		 *   @access public
		 */

		public $templateFunc = array();


		/**
		 *   Template contents
		 *   @var string
		 *   @access public
		 */

		public $templateContent = '';


		/**
		 *   Parse template vars
		 *   @var boolean
		 *   @access public
		 */

		public $templateParseVars = true;


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @access public
		 *   @param string $templateName Template name/identifier
		 *   @throws \Exception
		 *   @return Template
		 *
		 */

		public function __construct( string $templateName=null )
		{
			// Set template name
			$this->templateName=$templateName;

			// Load template if specified
			if (!empty($templateName))
			{
				$this->loadTemplate();
			}
		}


		/**
		 *
		 *   Load the template
		 *   -----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function loadTemplate()
		{
			// Template location
			$templateFilename='';
			if ($this->templateName[0]=='/')
			{
				$templateFilename=$this->templateName;
			}
			elseif (strpos($this->templateName,'/')!==false)
			{
				$tnArray=preg_split("/\//",$this->templateName);
				if ($tnArray[sizeof($tnArray)-2]!='template')
				{
					$fn=$tnArray[sizeof($tnArray)-1];
					$tnArray[sizeof($tnArray)-1]='template';
					$tnArray[]=$fn;
				}
				$templateFilename=join('/',$tnArray);
			}
			elseif (Flask()->resolvePath('template/'.$this->templateName.'.tpl'))
			{
				$templateFilename='template/'.$this->templateName.'.tpl';
			}
			if (!preg_match("/\.tpl$/",$templateFilename))
			{
				$templateFilename.='.tpl';
			}

			// Load template contents
			$templateFilename=Flask()->resolvePath($templateFilename);
			if (!empty($templateFilename))
			{
				$this->templateContent=file_get_contents($templateFilename);
			}
			else
			{
				throw new FlaskPHP\Exception\TemplateRenderException('Unable to load template: '.$this->templateName);
			}
		}


		/**
		 *
		 *   Set a template var
		 *   ------------------
		 *   @access public
		 *   @param string $varName variable name
		 *   @param mixed $varValue value
		 *   @throws \Exception
		 *   @return Template
		 *
		 */

		public function set( $varName, $varValue )
		{
			traverse_set($varName,$this->templateVar,$varValue);
			return $this;
		}


		/**
		 *
		 *   Set template variables from array
		 *   ---------------------------------
		 *   @access public
		 *   @param array $variables Variables
		 *   @throws \Exception
		 *   @return Template
		 *
		 */

		public function setVariables( array $variables )
		{
			foreach ($variables as $k => $v)
			{
				$this->templateVar[$k]=$v;
			}
			return $this;
		}


		/**
		 *
		 *   Get a template var
		 *   ------------------
		 *   @access public
		 *   @param string $varName variable name
		 *   @throws \Exception
		 *   @return mixed Value
		 *
		 */

		public function get( $varName )
		{
			return traverse_get($varName,$this->templateVar);
		}


		/**
		 *
		 *   Set template function
		 *   ---------------------
		 *   @access public
		 *   @param string $functionName Function name
		 *   @param mixed $functionValue Function value
		 *   @throws \Exception
		 *   @return Template
		 *
		 */

		public function setFunction( string $functionName, $functionValue )
		{
			$this->templateFunc[mb_strtolower($functionName)]=$functionValue;
			return $this;
		}


		/**
		 *
		 *   Set template functions from array
		 *   ---------------------------------
		 *   @access public
		 *   @param array $functions Functions
		 *   @throws \Exception
		 *   @return Template
		 *
		 */

		public function setFunctions( array $functions )
		{
			foreach ($functions as $k => $v)
			{
				$this->templateFunc[mb_strtolower($k)]=$v;
			}
			return $this;
		}


		/**
		 *
		 *   Parse the static content for locale variables
		 *   ---------------------------------------------
		 *   @access public
		 *   @static
		 *   @param string $content Content
		 *   @param bool $parseLocale Parse locale tags
		 *   @param array $variables Variables
		 *   @throws \Exception
		 *   @return string parsed template
		 *
		 */

		public static function parseContent( $content, $parseLocale=true, array $variables=null )
		{
			$template=new Template();
			if ($variables!==null)
			{
				$template->setVariables($variables);
				return ($template->parse($content,$parseLocale,true));
			}
			else
			{
				return ($template->parse($content,$parseLocale,false));
			}
		}


		/**
		 *
		 *   Parse template for locale tags
		 *   ------------------------------
		 *   @access public
		 *   @static
		 *   @param string $content Content
		 *   @throws \Exception
		 *   @return string parsed template
		 *
		 */

		public static function parseLocale( $content )
		{
			$Template=new Template();
			$content=preg_replace_callback("/\[\[\s*(.+?)\s*\]\]/",array($Template,'_parse_locale'),$content);
			return $content;
		}


		/**
		 *
		 *   Parse template for variables
		 *   ----------------------------
		 *   @access public
		 *   @static
		 *   @param string $content Content
		 *   @param array $variables
		 *   @param string $variableTagFormat Variable tag format
		 *   @throws \Exception
		 *   @return string parsed template
		 *
		 */

		public static function parseVariables( $content, array $variables=array(), string $variableTagFormat='{{' )
		{
			$Template=new Template();
			$Template->setVariables($variables);
			switch ($variableTagFormat)
			{
				case '{{':
					$content=preg_replace_callback("/\{\{\s*(.+?)\s*\}\}/",array($Template,'_parse_variable'),$content);
					break;
				case '{':
					$content=preg_replace_callback("/\{\s*(.+?)\s*\}/",array($Template,'_parse_variable'),$content);
					break;
				case '$':
					$content=preg_replace_callback("/\$([A-Za-z0-9]+?)/",array($Template,'_parse_variable'),$content);
					break;
				default:
					throw new FlaskPHP\Exception\InvalidParameterException('Invalid $variableTagFormat value.');
			}
			return $content;
		}



		/**
		 *
		 *   Parse template contents
		 *   -----------------------
		 *   @access public
		 *   @param string $src Source
		 *   @param bool $parseLocale Parse locale tags
		 *   @param bool $parseVariables Parse variable tags
		 *   @throws \Exception
		 *   @return string parsed template
		 *
		 */

		public function parse( $src=null, $parseLocale=true, $parseVariables=true )
		{
			// Get source
			if ($src===null)
			{
				$src=$this->templateContent;
			}

			// Parse variables
			if ($src===false || $parseVariables)
			{
				if ($this->templateParseVars)
				{
					$src=preg_replace_callback("/\{\{\s*(.+?)\s*\}\}/",array($this,'_parse_variable'),$src);
					$src=preg_replace_callback("/\{\%\s*(.+?)\s*\%\}/",array($this,'_parse_func'),$src);
				}

				// Import template vars into local context
				extract($this->templateVar,EXTR_SKIP);

				// Parse template
				ob_start();
				eval('?>'.$src.'<?');
				$tmplContent=ob_get_contents();
				ob_end_clean();
			}
			else
			{
				$tmplContent=$src;
			}

			// Parse localized strings
			if (($src===false && $this->templateParseVars) || ($src!==false && $parseLocale))
			{
				$tmplContent=preg_replace_callback("/\[\[\s*(.+?)\s*\]\]/",array($this,'_parse_locale'),$tmplContent);
			}

			return $tmplContent;
		}


		/**
		 *
		 *   Get a variable
		 *   --------------
		 *   @access private
		 *   @param string $match Match
		 *   @param array $var Variables as array
		 *   @throws \Exception
		 *   @return string parsed variable
		 *
		 */

		private function _parse_variable( $match, $var=null )
		{
			// Variables
			$var=($var===null)?$this->templateVar:$var;

			// Split functions
			$modifierFunctionList=str_array(trim($match[1]),'|');
			$varName=array_shift($modifierFunctionList);

			// Variable
			$retval=traverse_get($varName,$var);

			// Modifier functions?
			try
			{
				if (sizeof($modifierFunctionList))
				{
					foreach ($modifierFunctionList as $func)
					{
						$retval=$this->_apply_function($retval,$func);
					}
				}
			}
			catch (\Exception $e)
			{
				$retval='{{ ERROR APPLYING MODIFIER: '.$e->getMessage().' }}';
			}

			// Return
			return $retval;
		}


		/**
		 *
		 *   Apply a function
		 *   ----------------
		 */

		private function _parse_func( $match )
		{
			// Split functions
			$modifierFunctionList=str_array(trim($match[1]),'|');
			$funcName=array_shift($modifierFunctionList);

			// Parse parameters
			$funcParamList=$funcTagParamList=array();
			if (mb_strpos($funcName,':')!==false)
			{
				list($funcName,$funcParam)=str_array($funcName,':',2);
				$funcParamList=str_array($funcParam,';');
				foreach ($funcParamList as $param => $paramValue)
				{
					if (mb_strpos($paramValue,'=')!==false)
					{
						list($paramName,$paramValue)=str_array($paramValue,'=',2);
						$funcTagParamList[$paramName]=$paramValue;
						$funcParamList[$param]=$paramValue;
					}
					else
					{
						$funcTagParamList[trim($paramValue)]=true;
					}
				}
			}

			// Run function
			$funcName=mb_strtolower($funcName);
			if (array_key_exists($funcName,$this->templateFunc))
			{
				try
				{
					$retval=call_user_func($this->templateFunc[$funcName],$funcTagParamList,$this);
				}
				catch (\Exception $e)
				{
					$retval='{{ ERROR RUNNING FUNCTION '.$funcName.': '.$e->getMessage().' }}';
				}
			}
			else
			{
				$retval='{{ UNKNOWN FUNCTION: '.$funcName.' }}';
			}

			// Modifier functions?
			try
			{
				if (sizeof($modifierFunctionList))
				{
					foreach ($modifierFunctionList as $func)
					{
						$retval=$this->_apply_function($retval,$func);
					}
				}
			}
			catch (\Exception $e)
			{
				$retval='[[ ERROR APPLYING MODIFIER: '.$e->getMessage().' ]]';
			}

			// Return
			return $retval;
		}


		/**
		 *
		 *   Get a localized string
		 *   ----------------------
		 *   @access private
		 *   @param string $match match
		 *   @throws \Exception
		 *   @return string localized string
		 *
		 */

		function _parse_locale( $match )
		{
			// Split functions
			$modifierFunctionList=str_array(trim($match[1]),'|');
			$localeTag=array_shift($modifierFunctionList);

			// Parse locale tag
			$localeParamList=$localeTagParamList=array();
			if (mb_strpos($localeTag,':')!==false)
			{
				list($localeTag,$localeParamList)=str_array($localeTag,':',2);
				$localeParamList=str_array($localeParamList,';');
				foreach ($localeParamList as $param => $paramValue)
				{
					if (mb_strpos($paramValue,'=')!==false)
					{
						list($paramName,$paramValue)=str_array($paramValue,'=',2);
						$localeTagParamList[$paramName]=$paramValue;
						$localeParamList[$param]=$paramValue;
					}
				}
			}

			// Get from locale
			$retval=Flask()->Locale->get($localeTag);
			if ($retval===null) return '[[ UNKNOWN LOCALE TAG: '.$localeTag.' ]]';

			// Parameters?
			if (sizeof($localeParamList))
			{
				$retval=vsprintf($retval,$localeParamList);
			}
			if (sizeof($localeTagParamList))
			{
				$retval=static::parseSimpleVariables($retval,$localeTagParamList);
			}

			// Modifier functions?
			try
			{
				if (sizeof($modifierFunctionList))
				{
					foreach ($modifierFunctionList as $func)
					{
						$retval=$this->_apply_function($retval,$func);
					}
				}
			}
			catch (\Exception $e)
			{
				$retval='[[ ERROR APPLYING MODIFIER: '.$e->getMessage().' ]]';
			}

			// Return
			return $retval;
		}


		/**
		 *
		 *   Apply function
		 *   ---------------
		 *   @access private
		 *   @param string $str String
		 *   @param string $func Modifier function
		 *   @throws \Exception
		 *   @return string Processed string
		 *
		 */

		function _apply_function( $str, $func )
		{
			// Get function parameters
			if (mb_strpos($func,':')!==false)
			{
				list($func,$funcParamList)=str_array($func,':',2);
				$funcParamList=str_array($funcParamList,';');
			}
			else
			{
				$func=trim($func);
				$funcParamList=array();
			}

			if (!empty($funcParamList))
			{
				$unparsedFuncParamList=$funcParamList;
				$funcParamList=array();
				foreach ($unparsedFuncParamList as $param)
				{
					if (mb_strpos($param,'=')!==false)
					{
						list($k,$v)=str_array($param,'=',2);
						$funcParamList[$k]=$v;
					}
					else
					{
						$funcParamList[$param]=true;
					}
				}
			}

			// Do the magic
			switch (mb_strtolower($func))
			{
				// Uppercase
				case 'toupper':
					return mb_strtoupper($str);

				// Lowercase
				case 'tolower':
					return mb_strtolower($str);

				// Format date
				case 'date':
					return Flask()->I18n->formatDate($str);

				// Format time
				case 'time':
					switch (Flask()->I18n->i18nTimeFormat)
					{
						case '12':
							return date("h:i",strtotime($str));
						default:
							return date("H:i",strtotime($str));
					}

				// Format number
				case 'number':
					return Flask()->I18n->formatDecimalValue(floatval($str),intval($funcParamList['precision']),(!empty($funcParamList['trim'])?true:false));

				// Format currency
				case 'currency':
					if (!empty($funcParamList['currency']))
					{
						return Flask()->I18n->formatCurrency(floatval($str),$funcParamList['currency'],true);
					}
					else
					{
						return Flask()->I18n->formatDecimalValue(floatval($str),2,false);
					}

				// If empty
				case 'ifempty':
					if (empty($str))
					{
						if (!empty($funcParamList['replace']))
						{
							$str=preg_replace('/(^[\"\']|[\"\']$)/','',$funcParamList['replace']);
						}
						if (!empty($funcParamList['prepend']))
						{
							$str=preg_replace('/(^[\"\']|[\"\']$)/','',$funcParamList['prepend']).$str;
						}
						if (!empty($funcParamList['append']))
						{
							$str=$str.preg_replace('/(^[\"\']|[\"\']$)/','',$funcParamList['append']);
						}
					}
					return $str;

				// If not empty
				case 'ifnotempty':
					if (!empty($str))
					{
						if (!empty($funcParamList['replace']))
						{
							$str=preg_replace('/(^[\"\']|[\"\']$)/','',$funcParamList['replace']);
						}
						if (!empty($funcParamList['prepend']))
						{
							$str=preg_replace('/(^[\"\']|[\"\']$)/','',$funcParamList['prepend']).$str;
						}
						if (!empty($funcParamList['append']))
						{
							$str=$str.preg_replace('/(^[\"\']|[\"\']$)/','',$funcParamList['append']);
						}
					}
					return $str;

				// Unknown function
				default:
					throw new FlaskPHP\Exception\TemplateRenderException('Unknown modifier function: '.$func);
			}
		}


		/**
		 *
		 *   Render the template and return the contents
		 *   -------------------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string parsed template
		 *
		 */

		public function render()
		{
			return $this->parse();
		}


		/**
		 *
		 *   Replace simple $-variables
		 *   --------------------------
		 *   @access public
		 *   @static
		 *   @param string $string Input string
		 *   @param array|object $variables Variables
		 *   @return string parsed string
		 *
		 */

		public static function parseSimpleVariables( string $string, $variables )
		{
			uksort($localeTagParamList,function($a,$b){
				return (strlen($b)-strlen($a));
			});
			foreach ($variables as $k => $v)
			{
				if ($k[0]=='_') continue;
				$string=str_replace('$'.$k,$v,$string);
			}
			return $string;
		}


	}


?>
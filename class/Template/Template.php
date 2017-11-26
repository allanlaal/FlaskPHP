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

		public $templateParseVars  = true;


		/**
		 *   The constructor
		 *   @access public
		 *   @param string $templateName Template name/identifier
		 *   @throws \Exception
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
		 *   Load the template
		 *   @access public
		 *   @throws \Exception
		 *   @return void
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
		 *   Set a template var
		 *   @access public
		 *   @param string $varName variable name
		 *   @param mixed $varValue value
		 *   @return void
		 */

		public function set( $varName, $varValue )
		{
			traverse_set($varName,$this->templateVar,$varValue);
		}


		/**
		 *   Get a template var
		 *   @access public
		 *   @param string $varName variable name
		 *   @return mixed Value
		 */

		public function get( $varName )
		{
			return traverse_get($varName,$this->templateVar);
		}


		/**
		 *   Parse the static content for locale variables
		 *   @access public
		 *   @static
		 *   @param string $content Content
		 *   @param bool $parseLocale Parse locale tags
		 *   @param bool $parseVariables Parse variables
		 *   @return string parsed template
		 */

		public static function parseContent( $content, $parseLocale=true, $parseVariables=true )
		{
			$template=new Template();
			return ($template->parse($content,$parseLocale,$parseVariables));
		}


		/**
		 *   Parse template contents
		 *   @access public
		 *   @param string $src Source
		 *   @param bool $parseLocale Parse locale tags
		 *   @param bool $parseVariables Parse variable tags
		 *   @return string parsed template
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
				if ($this->templateParseVars) $src=preg_replace_callback("/\{\{\s*(.+?)\s*\}\}/",array($this,'_parse_variable'),$src);

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
		 *   Get a variable
		 *   @access private
		 *   @param string $match Match
		 *   @param array $var Variables as array
		 *   @return string parsed variable
		 */

		private function _parse_variable( $match, $var=null )
		{
			$var=($var===null)?$this->templateVar:$var;
			return traverse_get($match[1],$var);
		}


		/**
		 *   Get a localized string
		 *   @access private
		 *   @param string $match match
		 *   @return string localized string
		 */

		function _parse_locale( $match )
		{
			// Split functions
			$functionList=str_array(trim($match[1]),'|');
			$localeTag=array_shift($functionList);

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

			// Functions?
			try
			{
				if (sizeof($functionList))
				{
					foreach ($functionList as $func)
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
		 *   Get a localized string
		 *   @access private
		 *   @param string $str String
		 *   @param string $func Modifier function
		 *   @return string Processed string
		 *   @throws \Exception
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

			// Do the magic
			switch (mb_strtolower($func))
			{
				// Uppercase
				case 'toupper':
					return mb_strtoupper($str);

				// Lowercase
				case 'tolower':
					return mb_strtolower($str);

				// Unknown function
				default:
					throw new FlaskPHP\Exception\TemplateRenderException('Unknown modifier function: '.$func);
			}
		}


		/**
		 *   Render the template and return the contents
		 *   @access public
		 *   @return string parsed template
		 */

		public function render()
		{
			return $this->parse();
		}


		/**
		 *   Replace simple $-variables
		 *   @access public
		 *   @static
		 *   @param string $string Input string
		 *   @param array|object $variables Variables
		 *   @return string parsed string
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
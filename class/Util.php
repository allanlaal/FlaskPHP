<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   General utility functions class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP;

	use Codelab\FlaskPHP as FlaskPHP;
	use Codelab\FlaskPHP\Exception\Exception;
	use Codelab\FlaskPHP\Exception\NotFoundException;


	class Util
	{


		/**
		 *   Sanitize input
		 *   @access public
		 *   @static
		 *   @param mixed $value Value
		 *   @return string Sanitized value
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
			return trim(strip_tags($value));
		}


		/**
		 *   Is this a valid e-mail address
		 *   @access public
		 *   @static
		 *   @param string $email E-mail address
		 *   @param bool $allowMultiple Allow multiple e-mail addresses?
		 *   @return bool
		 */

		public static function isValidEmail( $email, $allowMultiple=false )
		{
			if ($allowMultiple)
			{
				$emailArr=str_array($email);
				foreach ($emailArr as $e)
				{
					if (!preg_match('/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',$e))
					{
						return false;
					}
				}
			}
			else
			{
				if (!preg_match('/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',$email))
				{
					return false;
				}
			}
			return true;
		}


		/**
		 *  Convert string to a URL-compatible format
		 *  @access public
		 *  @static
		 *  @param string $str Text
		 *  @param int $maxLength Maximum length
		 *  @return string
		 */

		public static function stringToURL( $str, $maxLength=100 )
		{
			// Transliterate cyrillic
			$chrCyr=array(
				'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
				'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
			);
			$chrLat=array(
				'a','b','v','g','d','e','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','sht','i','y','y','e','yu','ya',
				'A','B','V','G','D','E','E','Zh','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','Ts','Ch','Sh','Sht','I','Y','Y','E','Yu','Ya'
			);
			$str=str_replace($chrCyr,$chrLat,$str);

			// Trim
			$str=substr(trim(mb_strtolower($str)),0,$maxLength);

			// Täpid jms
			$str=iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$str);

			// Muu
			$str=preg_replace("/[^a-z0-9\ ]/","",$str);
			$str=str_replace(" ","-",$str);
			return $str;
		}


		/**
		 *   Convert array to <select> options
		 *   @access public
		 *   @static
		 *   @param array $options Options array
		 *   @param string $selected Selected option value
		 *   @param boolean $optGrouping Option grouping ($options is a two-level array)
		 *   @param array $optionAttributes Option attributes
		 *   @param bool $escapeContent Escape parameters
		 *   @return string
		 */

		public static function arrayToSelectOptions( array $options, $selected=null, $optGrouping=false, $optionAttributes=null, $escapeContent=true )
		{
			$c='';
			if (empty($options)) return '';
			if ($optGrouping)
			{
				foreach ($options as $optGroupLabel => $optGroup)
				{
					$c.='<optgroup label="'.$optGroupLabel.'">';
					foreach ($optGroup as $k => $v)
					{
						$c.='<option value="'.$k.'"'.($k==$selected?' selected="selected"':'').'>'.$v.'</option>';
					}
					$c.='</optgroup>';
				}
			}
			else
			{
				foreach ($options as $k => $v)
				{
					$c.='<option';
						$c.=' value="'.($escapeContent?htmlspecialchars($k):$k).'"';
						if ($k==$selected) $c.=' selected="selected"';
						if (!empty($optionAttributes) && is_array($optionAttributes) && is_array($optionAttributes[$k]))
						{
							foreach ($optionAttributes[$k] as $attr => $attrValue)
							{
								$c.=' '.$attr.'="'.($escapeContent?htmlspecialchars($attrValue):$attrValue).'"';
							}
						}
					$c.='>';
					$c.=($escapeContent?htmlspecialchars($v):$v);
					$c.='</option>';
				}
			}
			return $c;
		}


		/**
		 *   Get array/object value
		 *   @access public
		 *   @static
		 *   @param array|object $input Input element
		 *   @param string $key Key
		 *   @throws \Exception
		 *   @return mixed
		 */

		public static function getValue( $input, $key, bool $throwExceptionOnNonExisting=false, $defaultValue=null )
		{
			if (is_object($input))
			{
				if (property_exists($input,$key)) return $input->{$key};
				if ($throwExceptionOnNonExisting) throw new NotFoundException('Object property '.$key.' not set.');
				return $defaultValue;
			}
			elseif (is_array($input))
			{
				if (array_key_exists($key,$input)) return $input[$key];
				if ($throwExceptionOnNonExisting) throw new NotFoundException('Array element '.$key.' not set.');
				return $defaultValue;
			}
			else
			{
				if ($throwExceptionOnNonExisting) throw new Exception('Input parameter $input must be an object or an array.');
				return null;
			}
		}


		/**
		 *   Get MIME type for filename
		 *   @access public
		 *   @static
		 *   @param string $filename Filename
		 *   @throws \Exception
		 *   @return string|bool
		 */

		public static function getMimeType( $filename )
		{
			// Get file extension
			$fileExtension=mb_strtolower(pathinfo($filename,PATHINFO_EXTENSION));

			// Read file
			$mimeTypesFile=Flask()->resolvePath('data/mime/mime.types');
			if (empty($mimeTypesFile)) throw new Exception('Cannot locate mime.types file.');
			$mimeTypes=file($mimeTypesFile,FILE_IGNORE_NEW_LINES);
			if (empty($mimeTypes) || !is_array($mimeTypes)) throw new Exception('Error reading mime.types file.');

			// Find MIME type
			foreach ($mimeTypes as $line)
			{
				if (empty($line) || $line[0]=='#') continue;
				$lineArr=preg_split("/\s+/",$line);
				if (empty($lineArr[1])) continue;
				for ($i=1;$i<sizeof($lineArr);++$i)
				{
					if (mb_strtolower($lineArr[$i])==$fileExtension) return $lineArr[0];
				}
			}

			// Nothing?
			return false;
		}


	}


?>
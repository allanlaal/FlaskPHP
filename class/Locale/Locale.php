<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The locale interface
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Locale;
	use Codelab\FlaskPHP as FlaskPHP;


	class Locale
	{


		/**
		 *   Current language
		 *   @var string
		 *   @access public
		 */

		public $localeLanguage = null;


		/**
		 *   Languages available
		 *   @var array
		 *   @access public
		 */

		public $localeLanguageSet = array();


		/**
		 *   Default language
		 *   @var string
		 *   @access public
		 */

		public $localeLanguageDefault = null;


		/**
		 *   Additional locale paths
		 *   @var array
		 *   @access public
		 */

		public $localePath = array();


		/**
		 *   Locale data
		 *   @var array
		 *   @access public
		 */

		public $localeData = array();


		/**
		 *   Available locales
		 *   @var array
		 *   @static
		 *   @access public
		 */

		public static $localeInfo=array(

			'en' => array(
				'tag'               => 'EN',
				'name'              => 'English',
				'name_short'        => 'English',
				'name_sel'          => 'In English',
				'name_eng'          => 'English',
				'phplocale'         => 'en_US',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'et' => array(
				'tag'               => 'ET',
				'name'              => 'Eesti keel',
				'name_short'        => 'Eesti',
				'name_sel'          => 'Eesti keeles',
				'name_eng'          => 'Estonian',
				'phplocale'         => 'et_EE',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'lv' => array(
				'tag'               => 'LV',
				'name'              => 'Latviešu',
				'name_short'        => 'Latviešu',
				'name_sel'          => 'Latviešu',
				'name_eng'          => 'Latvian',
				'phplocale'         => 'lv_LV',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'lt' => array(
				'tag'               => 'LT',
				'name'              => 'Lietuvių',
				'name_short'        => 'Lietuvių',
				'name_sel'          => 'Lietuvių',
				'name_eng'          => 'Lithuanian',
				'phplocale'         => 'lt_LT',
				'dateformat_disp'   => 'Y.m.d',
				'dateformat_check'  => '(\d\d\d\d).(\d\d).(\d\d)'
			),

			'ru' => array(
				'tag'               => 'RU',
				'name'              => 'Русский язык',
				'name_short'        => 'Русский',
				'name_sel'          => 'По-русски',
				'name_eng'          => 'Russian',
				'phplocale'         => 'ru_RU',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'fi' => array
			 (
				'tag'               => 'FI',
				'name'              => 'Suomen kieli',
				'name_short'        => 'Suomi',
				'name_sel'          => 'Suomeksi',
				'name_eng'          => 'Finnish',
				'phplocale'         => 'fi_FI',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			 ),

			'sv' => array
			 (
				'tag'               => 'SV',
				'name'              => 'Svenska',
				'name_short'        => 'Svenska',
				'name_sel'          => 'På svenska',
				'name_eng'          => 'Swedish',
				'phplocale'         => 'sv_SE',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			 ),

			'da' => array
			 (
				'tag'               => 'DA',
				'name'              => 'Dansk',
				'name_short'        => 'Dansk',
				'name_sel'          => 'På dansk',
				'name_eng'          => 'Danish',
				'phplocale'         => 'da_DK',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			 ),

			'de' => array(
				'tag'               => 'DE',
				'name'              => 'Deutsch',
				'name_short'        => 'Deutsch',
				'name_sel'          => 'Deutsch',
				'name_eng'          => 'German',
				'phplocale'         => 'de_DE',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'es' => array(
				'tag'               => 'ES',
				'name'              => 'Español',
				'name_short'        => 'Español',
				'name_sel'          => 'En Español',
				'name_eng'          => 'Spanish',
				'phplocale'         => 'es_ES',
				'dateformat_disp'   => 'd/m/Y',
				'dateformat_check'  => '(\d\d)\/(\d\d)\/(\d\d\d\d)'
			),

			'fr' => array
			(
				'tag'               => 'FR',
				'name'              => 'Français',
				'name_short'        => 'Français',
				'name_sel'          => 'En français',
				'name_eng'          => 'French',
				'phplocale'         => 'fr_FR',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'it' => array(
				'tag'               => 'IT',
				'name'              => 'Italiano',
				'name_short'        => 'Italiano',
				'name_sel'          => 'In italiano',
				'name_eng'          => 'Italian',
				'phplocale'         => 'it_IT',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'pt' => array(
				'tag'               => 'PT',
				'name'              => 'Português',
				'name_short'        => 'Português',
				'name_sel'          => 'Em Português',
				'name_eng'          => 'Portuguese',
				'phplocale'         => 'pt_PT',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'nl' => array(
				'tag'               => 'NL',
				'name'              => 'Nederlandse',
				'name_short'        => 'Nederlandse',
				'name_sel'          => 'In het Nederlands',
				'name_eng'          => 'Dutch',
				'phplocale'         => 'nl_NL',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'no' => array(
				'tag'               => 'PT',
				'name'              => 'Norsk',
				'name_short'        => 'Norsk',
				'name_sel'          => 'På Norsk',
				'name_eng'          => 'Norwegian',
				'phplocale'         => 'no_NO',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'pl' => array(
				'tag'               => 'PL',
				'name'              => 'Polski',
				'name_short'        => 'Polski',
				'name_sel'          => 'Polski',
				'name_eng'          => 'Polish',
				'phplocale'         => 'pl_PL',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'cs' => array
			(
				'tag'               => 'CS',
				'name'              => 'Český jazyk',
				'name_short'        => 'Česky',
				'name_sel'          => 'V češtině',
				'name_eng'          => 'Czech',
				'phplocale'         => 'cs_CZ',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'sk' => array
			(
				'tag'               => 'SK',
				'name'              => 'Slovenský jazyk',
				'name_short'        => 'Slovenský',
				'name_sel'          => 'Po slovensky',
				'name_eng'          => 'Slovak',
				'phplocale'         => 'sk_SK',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

			'hu' => array
			(
				'tag'               => 'HU',
				'name'              => 'Magyar nyelv',
				'name_short'        => 'Magyar',
				'name_sel'          => 'Alapján a magyar',
				'name_eng'          => 'Hungarian',
				'phplocale'         => 'hu_HU',
				'dateformat_disp'   => 'd.m.Y',
				'dateformat_check'  => '(\d\d)\.(\d\d)\.(\d\d\d\d)'
			),

		);


		/**
		 *
		 *   The constructor
		 *   ---------------
		 *   @access public
		 *   @return Locale
		 *
		 */

		public function __construct()
		{
			setlocale(LC_ALL,'en_US.UTF-8');
		}


		/**
		 *
		 *   Init the locale system
		 *   ----------------------
		 *   @access public
		 *   @var string $lang Language
		 *   @throws FlaskPHP\Exception\FatalException
		 *   @return void
		 *
		 */

		public function initLocale()
		{
			// Get additional locale paths
			if (!empty(Flask()->Config->get('locale.localepath')))
			{
				foreach (Flask()->Config->get('locale.localepath') as $localePath)
				{
					$this->addLocalePath($localePath,false);
				}
			}
		}


		/**
		 *
		 *   Add locale path
		 *   ---------------
		 *   @access public
		 *   @var string $localePath Locale path
		 *   @var bool $reloadLocale Reload locale
		 *   @throws FlaskPHP\Exception\FatalException
		 *   @return Locale
		 *
		 */

		public function addLocalePath( string $localePath, bool $reloadLocale=true )
		{
			// Already exists?
			if (in_array($localePath,$this->localePath)) return;

			// Is readable?
			if (!is_readable($localePath)) throw new FlaskPHP\Exception\FatalException('Locale path '.$localePath.' not readable.');

			// Add to paths
			$this->localePath[]=$localePath;

			// Reload?
			if ($reloadLocale)
			{
				$this->loadLocale($this->localeLanguage,true);
			}

			// Return self
			return $this;
		}


		/**
		 *
		 *   Add available language
		 *   ----------------------
		 *   @access public
		 *   @var string $lang Language
		 *   @throws FlaskPHP\Exception\FatalException
		 *   @return Locale
		 *
		 */

		public function addLanguage( string $lang )
		{
			// Check if it's a known locale
			if (!$this->localeExists($lang))
			{
				throw new FlaskPHP\Exception\FatalException('Unknown locale: '.$lang);
				return;
			}

			// Set default if we don't have one yet
			if (!sizeof($this->localeLanguageSet))
			{
				$this->localeLanguageDefault = $lang;
			}

			// Add to available locales
			$this->localeLanguageSet[] = $lang;

			// Return self
			return $this;
		}


		/**
		 *
		 *   Get default language
		 *   --------------------
		 *   @access public
		 *   @return string 2-letter locale tag
		 *
		 */

		public function getDefaultLanguage()
		{
			return $this->localeLanguageDefault;
		}


		/**
		 *
		 *   Check if the given locale exists
		 *   --------------------------------
		 *   @access public
		 *   @param string $localeTag Locale tag
		 *   @return bool
		 *
		 */

		public function localeExists( string $localeTag )
		{
			return array_key_exists($localeTag,static::$localeInfo);
		}


		/**
		 *
		 *   Check is the locale is available
		 *   --------------------------------
		 *   @access public
		 *   @param string $localeTag Locale tag
		 *   @return bool
		 *
		 */

		public function localeAvailable( string $localeTag )
		{
			return in_array(mb_strtolower($localeTag),$this->localeLanguageSet);
		}


		/**
		 *
		 *   Load locale
		 *   -----------
		 *   @access public
		 *   @param string $localeTag Locale tag
		 *   @param bool $forceReload Force reload even if current language
		 *   @return void
		 *
		 */

		public function loadLocale( string $localeTag, bool $forceReload=false )
		{
			// Already loaded?
			if ($this->localeLanguage==$localeTag && !$forceReload) return;

			// Init
			$this->localeData=array();

			// Load FlaskPHP locale
			if ($localeTag!='en') $this->_loadLocaleFile(Flask()->getFlaskPath().'/locale/en.locale');
			$this->_loadLocaleFile(Flask()->getFlaskPath().'/locale/'.$localeTag.'.locale');

			// Load locales in site include path
			if (!empty(Flask()->Config->get('app.includepath')))
			{
				foreach (Flask()->Config->get('app.includepath') as $includePath)
				{
					if ($localeTag!='en') $this->_loadLocaleFile($includePath.'/locale/en.locale');
					$this->_loadLocaleFile($includePath.'/locale/'.$localeTag.'.locale');
				}
			}

			// Load locales in locale path
			foreach ($this->localePath as $localePath)
			{
				if ($localeTag!='en') $this->_loadLocaleFile($localePath.'/en.locale');
				$this->_loadLocaleFile($localePath.'/'.$localeTag.'.locale');
			}

			// Load site locale
			if ($localeTag!='en') $this->_loadLocaleFile(Flask()->getAppPath().'/locale/en.locale');
			$this->_loadLocaleFile(Flask()->getAppPath().'/locale/'.$localeTag.'.locale');

			// Mark as loaded
			$this->localeLanguage=$localeTag;
		}



		/**
		 *
		 *   Get a localized string
		 *   ----------------------
		 *   @access public
		 *   @param string $varName variable
		 *   @throws \Exception
		 *   @return string Localized string
		 *
		 */

		public function get( string $varName )
		{
			// Check that we are loaded
			if (empty($this->localeLanguage))
			{
				throw new FlaskPHP\Exception\Exception('Locale not loaded.');
			}

			// Locale item does not exist
			if (!isset($this->localeData[mb_strtolower($varName)])) return null;

			// Return value
			return $this->localeData[mb_strtolower($varName)];
		}


		/**
		 *
		 *   Set a locale string
		 *   -------------------
		 *   @access public
		 *   @param string $varName Variable
		 *   @param string $varValue Value
		 *   @return Locale
		 *
		 */

		public function set( string $varName, string $varValue )
		{
			$varName=mb_strtolower($varName);
			$this->localeData[$varName]=$varValue;
			return $this;
		}


		/**
		 *
		 *   Load the locale file
		 *   --------------------
		 *   @access private
		 *   @param string $localeFile File name
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		private function _loadLocaleFile( $localeFile )
		{
			// Get file contents
			if (!file_exists($localeFile)) return;
			if (!is_readable($localeFile)) return;
			$fileContents=file_get_contents($localeFile);

			// JSON file?
			if (mb_substr($fileContents,0,1)=='{')
			{
				$fileContents=json_decode($fileContents,true);
				if ($fileContents===null)
				{
					throw new FlaskPHP\Exception\Exception('Failed to parse locale file '.$localeFile.' as JSON.');
				}
				foreach ($fileContents as $key => $val)
				{
					$this->set($key,$val);
				}
			}

			// Regular old-style file
			else
			{
				$fileContents=str_array($fileContents,"\n");
				foreach ($fileContents as $line)
				{
					$line=trim($line);
					if ($line[0]=='/' || $line[0]=='#') continue;
					list($key,$val)=preg_split("/\s+/", $line,2);
					if (strlen($key) && strlen($val)) $this->set(trim($key),trim($val));
				}
			}
		}


		/**
		 *
		 *   Get locale name
		 *   ---------------
		 *   @access public
		 *   @static
		 *   @param string Locale tag
		 *   @param string $name Name Type
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public static function getName( $localeTag, $name=null )
		{
			global $LAB;

			if (empty(static::$localeInfo[mb_strtolower($localeTag)])) throw new FlaskPHP\Exception\InvalidParameterException('Unknown locale: '.$localeTag);
			if (!empty($name))
			{
				return static::$localeInfo[mb_strtolower($localeTag)]['name_'.$name];
			}
			else
			{
				return static::$localeInfo[mb_strtolower($localeTag)]['name'];
			}
		}


	}


?>

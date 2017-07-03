<?php


	/**
	 *
	 *   FlaskPHP
	 *   The main configuration provider
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Config;
	use Codelab\FlaskPHP;


	class Config
	{


		/**
		 *   Configuration loaded?
		 *   @var bool
		 *   @access public
		 */

		public $configLoaded = false;


		/**
		 *   Configuration file name
		 *   @var string
		 *   @access public
		 */

		public $configFilename = 'config/config.php';


		/**
		 *   Configuration data
		 *   @var array
		 *   @access public
		 */

		public $configData = array();


		/**
		 *   Include path
		 *   @var array
		 *   @access public
		 */

		public $configIncludePath = array();


		/**
		 *   Load configuration file
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function loadConfig()
		{
			// Config file name overrides
			if (isset($_SERVER['FLASK_CONFIG']))
			{
				$this->configFilename=$_SERVER['FLASK_CONFIG'];
			}
			elseif (isset($_ENV['FLASK_CONFIG']))
			{
				$this->configFilename=$_ENV['FLASK_CONFIG'];
			}

			// Load
			$configFileName=($this->configFilename[0]=='/'?$this->configFilename:Flask()->getAppPath().'/'.$this->configFilename);
			if (!is_readable($configFileName)) throw new FlaskPHP\Exception\FatalException('Configuration file '.$configFileName.' does not exist or is not readable.');
			$this->configData=require $configFileName;

			// Set app URL
			if (Flask()->requestType=='http')
			{
				if (!isset($this->configData['app']['url']) || empty($this->configData['app']['url']))
				{
					if (isset($_SERVER['FLASK_BASEURL']))
					{
						$this->configData['app']['url']=$_SERVER['FLASK_BASEURL'];
					}
					elseif (isset($_ENV['FLASK_BASEURL']))
					{
						$this->configData['app']['url']=$_ENV['FLASK_BASEURL'];
					}
					else
					{
						$this->configData['app']['url']=(!empty($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'];
					}
				}
			}

			// Init locales
			if (!isset($this->configData['locale']['lang']) || empty($this->configData['locale']['lang']))
			{
				throw new FlaskPHP\Exception\ConfigException('No languages defined.');
			}
			$langSet=str_array($this->configData['locale']['lang']);
			foreach ($langSet as $lang)
			{
				Flask()->Locale->addLanguage($lang);
			}

			// Done
			$this->configLoaded=true;
		}


		/**
		 *   Set a configuration directive
		 *   @access public
		 *   @param string $configDirective Configuration directive
		 *   @param mixed $value Value
		 *   @return void
		 */

		public function set( string $configDirective, $value )
		{
			traverse_set(mb_strtolower($configDirective),$this->configData,$value);
		}


		/**
		 *   Get a configuration directive value
		 *   @access public
		 *   @param string $configDirective Configuration directive
		 *   @return mixed Value
		 */

		public function get( string $configDirective )
		{
			return traverse_get(mb_strtolower($configDirective),$this->configData);
		}


		/**
		 *   Get temporary directory path
		 *   @access public
		 *   @static
		 *   @throws \Exception
		 *   @return string Path
		 */

		public function getTmpPath()
		{
			if (!$this->configLoaded) throw new FlaskPHP\Exception\Exception('Configuration not loaded.');
			if (strlen($this->get('app.tmppath')))
			{
				return $this->get('app.tmppath');
			}
			else
			{
				return '/tmp';
			}
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The internationalization provider
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\I18n;
	use Codelab\FlaskPHP;


	class I18n
	{


		/**
		 *   Date/time format definitions
		 *   @access public
		 *   @var array
		 */

		public static $setDateFormat = array(

			'dd.mm.yyyy' => array(
				'disp' => 'd.m.Y',
				'disp_time' => 'd.m.Y H:i',
				'disp_time_seconds' => 'd.m.Y H:i:s',
				'disp_time12' => 'd.m.Y h:iA',
				'disp_time12_seconds' => 'd.m.Y h:i:sA',
				'disp_user' => '[[ FLASK.COMMON.DayAbbr ]][[ FLASK.COMMON.DayAbbr ]].[[ FLASK.COMMON.MonthAbbr ]][[ FLASK.COMMON.MonthAbbr ]].[[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]]',
				'datepicker' => 'DD.MM.YYYY',
				'phpexcel' => 'dd.mm.yyyy',
				'phpexcel_time' => 'dd.mm.yyyy hh:mm',
				'phpexcel_time_seconds' => 'dd.mm.yyyy hh:mm:ss',
				'phpexcel_time12' => 'dd.mm.yyyy h:mm AM/PM',
				'phpexcel_time12_seconds' => 'dd.mm.yyyy h:mm:ss AM/PM',
				'mask' => '99.99.9999',
				'check' => '\d\d\.\d\d\.\d\d\d\d',
				'check_time' => '\d\d\.\d\d\.\d\d\d\d \d\d:\d\d',
				'check_time_seconds' => '\d\d\.\d\d\.\d\d\d\d \d\d:\d\d:\d\d',
				'unpack' => 'a2day/a1sep1/a2month/a1sep2/a4year',
				'unpack_time' => 'a2day/a1sep1/a2month/a1sep2/a4year/a1space/a2hour/a1sep3/a2min',
				'unpack_time_seconds' => 'a2day/a1sep1/a2month/a1sep2/a4year/a1space/a2hour/a1sep3/a2min/a1sep4/a2sec',
			),

			'dd/mm/yyyy' => array(
				'disp' => 'd/m/Y',
				'disp_time' => 'd/m/Y H:i',
				'disp_time_seconds' => 'd/m/Y H:i:s',
				'disp_time12' => 'd/m/Y h:iA',
				'disp_time12_seconds' => 'd/m/Y h:i:sA',
				'disp_user' => '[[ FLASK.COMMON.DayAbbr ]][[ FLASK.COMMON.DayAbbr ]]/[[ FLASK.COMMON.MonthAbbr ]][[ FLASK.COMMON.MonthAbbr ]]/[[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]]',
				'datepicker' => 'DD/MM/YYYY',
				'phpexcel' => 'dd/mm/yyyy',
				'phpexcel_time' => 'dd/mm/yyyy hh:mm',
				'phpexcel_time_seconds' => 'dd/mm/yyyy hh:mm:ss',
				'phpexcel_time12' => 'dd/mm/yyyy h:mm AM/PM',
				'phpexcel_time12_seconds' => 'dd/mm/yyyy h:mm:ss AM/PM',
				'mask' => '99/99/9999',
				'check' => '\d\d\/\d\d\/\d\d\d\d',
				'check_time' => '\d\d\/\d\d\/\d\d\d\d \d\d:\d\d',
				'check_time_seconds' => '\d\d\/\d\d\/\d\d\d\d \d\d:\d\d:\d\d',
				'unpack' => 'a2day/a1sep1/a2month/a1sep2/a4year',
				'unpack_time' => 'a2day/a1sep1/a2month/a1sep2/a4year/a1space/a2hour/a1sep3/a2min',
				'unpack_time_seconds' => 'a2day/a1sep1/a2month/a1sep2/a4year/a1space/a2hour/a1sep3/a2min/a1sep4/a2sec',
			),

			'mm/dd/yyyy' => array(
				'disp' => 'm/d/Y',
				'disp_time' => 'm/d/Y H:i',
				'disp_time_seconds' => 'm/d/Y H:i:s',
				'disp_time12' => 'm/d/Y h:iA',
				'disp_time12_seconds' => 'm/d/Y h:i:sA',
				'disp_user' => '[[ FLASK.COMMON.MonthAbbr ]][[ FLASK.COMMON.MonthAbbr ]]/[[ FLASK.COMMON.DayAbbr ]][[ FLASK.COMMON.DayAbbr ]]/[[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]]',
				'datepicker' => 'MM/DD/YYYY',
				'phpexcel' => 'mm/dd/yyyy',
				'phpexcel_time' => 'mm/dd/yyyy hh:mm',
				'phpexcel_time_seconds' => 'mm/dd/yyyy hh:mm:ss',
				'phpexcel_time12' => 'mm/dd/yyyy h:mm AM/PM',
				'phpexcel_time12_seconds' => 'mm/dd/yyyy h:mm:ss AM/PM',
				'mask' => '99/99/9999',
				'check' => '\d\d\/\d\d\/\d\d\d\d',
				'check_time' => '\d\d\/\d\d\/\d\d\d\d \d\d:\d\d',
				'check_time_seconds' => '\d\d\/\d\d\/\d\d\d\d \d\d:\d\d:\d\d',
				'unpack' => 'a2month/a1sep/a2day/a1sep2/a4year',
				'unpack_time' => 'a2month/a1sep1/a2day/a1sep2/a4year/a1space/a2hour/a1sep3/a2min',
				'unpack_time_seconds' => 'a2month/a1sep1/a2day/a1sep2/a4year/a1space/a2hour/a1sep3/a2min/a1sep4/a2sec',
			),

			'yyyy.mm.dd' => array(
				'disp' => 'Y.m.d',
				'disp_time' => 'Y.m.d H:i',
				'disp_time_seconds' => 'Y.m.d H:i:s',
				'disp_time12' => 'Y.m.d h:iA',
				'disp_time12_seconds' => 'Y.m.d h:i:sA',
				'disp_user' => '[[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]][[ FLASK.COMMON.YearAbbr ]].[[ FLASK.COMMON.MonthAbbr ]][[ FLASK.COMMON.MonthAbbr ]].[[ FLASK.COMMON.DayAbbr ]][[ FLASK.COMMON.DayAbbr ]]',
				'datepicker' => 'YYYY.MM.DD',
				'phpexcel' => 'yyyy.mm.dd',
				'phpexcel_time' => 'yyyy.mm.dd hh:mm',
				'phpexcel_time_seconds' => 'yyyy.mm.dd hh:mm:ss',
				'phpexcel_time12' => 'yyyy.mm.dd h:mm AM/PM',
				'phpexcel_time12_seconds' => 'yyyy.mm.dd h:mm:ss AM/PM',
				'mask' => '9999.99.99',
				'check' => '\d\d\d\d\.\d\d\.\d\d',
				'check_time' => '\d\d\d\d\.\d\d\.\d\d \d\d:\d\d',
				'check_time_seconds' => '\d\d\d\d\.\d\d\.\d\d \d\d:\d\d:\d\d',
				'unpack' => 'a4year/a1sep1/a2month/a1sep2/a2day',
				'unpack_time' => 'a4year/a1sep1/a2month/a1sep2/a2day/a1space/a2hour/a1sep3/a2min',
				'unpack_time_seconds' => 'a4year/a1sep1/a2month/a1sep2/a2day/a1space/a2hour/a1sep3/a2min/a1sep4/a2sec',
			)

		);


		/**
		 *   Time formats
		 *   @access public
		 *   @var array
		 */

		public static $setTimeFormat = array(

			'24' => '24h',
			'12' => '12h'

		);


		/**
		 *   Decimal separators
		 *   @access public
		 *   @var array
		 */

		public static $setDecimalSeparator = array(
			'.' => '.',
			',' => ','
		);


		/**
		 *   Thousand separators
		 *   @access public
		 *   @var array
		 */

		public static $setThousandSeparator = array(
			''  => 'none',
			' ' => 'space',
			'.' => '.',
			',' => ',',
			"'" => "'"
		);


		/**
		 *   Currency placement
		 *   @access public
		 *   @var array
		 */

		public static $setCurrencyPlacement = array(
			'afterspace' => 'after value, separated with a space',
			'after' => 'after value',
			'beforespace' => 'before value, separated with a space',
			'before' => 'before',
		);


		/**
		 *   Current timezone
		 *   @access public
		 *   @var string
		 */

		public $i18nTimeZone = null;


		/**
		 *   Current date format
		 *   @access public
		 *   @var string
		 */

		public $i18nDateFormat = null;


		/**
		 *   Current time format
		 *   @access public
		 *   @var string
		 */

		public $i18nTimeFormat = null;


		/**
		 *   Current decimal separator
		 *   @access public
		 *   @var string
		 */

		public $i18nDecimalSeparator = '.';


		/**
		 *   Current thousand separator
		 *   @access public
		 *   @var string
		 */

		public $i18nThousandSeparator = ' ';


		/**
		 *   Current currency placement
		 *   @access public
		 *   @var string
		 */

		public $i18nCurrencyPlacement = 'afterspace';


		/**
		 *
		 *   Init I18n framework
		 *   -------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initI18n()
		{
			// Set date/time settings
			$this->i18nTimeZone=oneof(Flask()->Config->get('locale.timezone'),null);
			$this->i18nDateFormat=oneof(Flask()->Config->get('locale.dateformat'),null);
			$this->i18nTimeFormat=oneof(Flask()->Config->get('locale.timeformat'),null);

			// Set timezone
			if ($this->i18nTimeZone)
			{
				date_default_timezone_set($this->i18nTimeZone);
			}

			// Set regional settings
			if (Flask()->Config->get('locale.decimalseparator')!==null) $this->setDecimalSeparator(Flask()->Config->get('locale.decimalseparator'));
			if (Flask()->Config->get('locale.thousandseparator')!==null) $this->setThousandSeparator(Flask()->Config->get('locale.thousandseparator'));
			if (Flask()->Config->get('locale.currencyplacement')!==null) $this->setCurrencyPlacement(Flask()->Config->get('locale.currencyplacement'));
		}


		/**
		 *
		 *   Set timezone
		 *   ------------
		 *   @access public
		 *   @param string $timeZone Timezone
		 *   @throws \Exception
		 *   @return I18n
		 *
		 */

		public function setTimeZone( string $timeZone )
		{
			$this->i18nTimeZone=$timeZone;
			date_default_timezone_set($this->i18nTimeZone);
			return $this;
		}


		/**
		 *
		 *   Set date format
		 *   ---------------
		 *   @access public
		 *   @param string $dateFormat Date format identifier
		 *   @throws \Exception
		 *   @return I18n
		 *
		 */

		public function setDateFormat( string $dateFormat )
		{
			if (!array_key_exists($dateFormat,static::$setDateFormat)) throw new FlaskPHP\Exception\InvalidParameterException('Unknown date format.');
			$this->i18nDateFormat=$dateFormat;
			return $this;
		}


		/**
		 *
		 *   Set time format
		 *   ---------------
		 *   @access public
		 *   @param string $timeFormat Time format identified
		 *   @throws \Exception
		 *   @return I18n
		 *
		 */

		public function setTimeFormat( string $timeFormat )
		{
			if (!array_key_exists($timeFormat,static::$setTimeFormat)) throw new FlaskPHP\Exception\InvalidParameterException('Unknown time format.');
			$this->i18nTimeFormat=$timeFormat;
			return $this;
		}


		/**
		 *
		 *   Set decimal separator
		 *   ---------------------
		 *   @access public
		 *   @param string $decimalSeparator Decimal separator
		 *   @throws \Exception
		 *   @return I18n
		 *
		 */

		public function setDecimalSeparator( string $decimalSeparator )
		{
			if (!array_key_exists($decimalSeparator,static::$setDecimalSeparator)) throw new FlaskPHP\Exception\InvalidParameterException('Unknown decimal separator.');
			$this->i18nDecimalSeparator=$decimalSeparator;
			return $this;
		}


		/**
		 *
		 *   Set thousand separator
		 *   ----------------------
		 *   @access public
		 *   @param string $thousandSeparator Thousand separator
		 *   @throws \Exception
		 *   @return I18n
		 *
		 */

		public function setThousandSeparator( string $thousandSeparator )
		{
			if (!array_key_exists($thousandSeparator,static::$setThousandSeparator)) throw new FlaskPHP\Exception\InvalidParameterException('Unknown thousand separator.');
			$this->i18nThousandSeparator=$thousandSeparator;
			return $this;
		}


		/**
		 *
		 *   Set decimal separator
		 *   ---------------------
		 *   @access public
		 *   @param string $currencyPlacement Currency placement
		 *   @throws \Exception
		 *   @return I18n
		 *
		 */

		public function setCurrencyPlacement( string $currencyPlacement )
		{
			if (!array_key_exists($currencyPlacement,static::$setCurrencyPlacement)) throw new FlaskPHP\Exception\InvalidParameterException('Unknown currency placement.');
			$this->i18nCurrencyPlacement=$currencyPlacement;
			return $this;
		}


		/**
		 *
		 *   Get date format
		 *   ---------------
		 *   @param string $formatType Format type
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function getDateFormat( string $formatType )
		{
			if (empty($this->i18nDateFormat)) throw new FlaskPHP\Exception\Exception('Date format not set.');
			if (!array_key_exists($formatType,static::$setDateFormat[$this->i18nDateFormat])) throw new FlaskPHP\Exception\Exception('Unknown format type.');
			return static::$setDateFormat[$this->i18nDateFormat][$formatType];
		}


		/**
		 *
		 *   Format date
		 *   -----------
		 *   @access public
		 *   @param string|int Date value (YYYY-MM-DD or Unix timestamp)
		 *   @param bool $showTime Show time (hh:mm)
		 *   @param bool $showTimeSeconds Show seconds as well
		 *   @param bool $showEmptyDate Show empty date
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function formatDate( $dateValue, bool $showTime=false, bool $showTimeSeconds=false, bool $showEmptyDate=false )
		{
			// Sanity checks
			if (empty($this->i18nDateFormat)) throw new FlaskPHP\Exception\Exception('Date format not set.');

			// Check empty value
			if (!$showEmptyDate)
			{
				if (!mb_strlen($dateValue)) return '';
				if (is_numeric($dateValue) && empty($dateValue)) return '';
				if (!is_numeric($dateValue) && ($dateValue=='0000-00-00' || $dateValue=='0000-00-00 00:00:00')) return '';
			}

			// Format and return
			$dateFormat=$this->getDateFormat('disp'.($showTime?'_time'.($showTimeSeconds?'_seconds':''):''));
			$formattedDate=date($dateFormat,(is_numeric($dateValue)?$dateValue:strtotime($dateValue)));
			return $formattedDate;
		}


		/**
		 *
		 *   Format period
		 *   -------------
		 *   @access public
		 *   @param int $period Period in seconds
		 *   @param bool $showSeconds Show seconds if period is over a minute
		 *   @param string $separator Period separator
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function formatPeriod( int $period, bool $showSeconds=true, string $separator=' ' )
		{
			// Init
			$remainingPeriod=$period;
			$displayPeriod=array();

			// Years
			$years=floor($remainingPeriod/(60*60*24*365));
			if ($years)
			{
				$displayPeriod[]=intval($years).' [[ FLASK.COMMON.Year'.($years==1?'':'s').' ]]';
				$remainingPeriod-=round($years*(60*60*24*365));
			}

			// Months
			$months=floor($remainingPeriod/(60*60*24*30));
			if ($months)
			{
				$displayPeriod[]=intval($months).' [[ FLASK.COMMON.Month'.($months==1?'':'s').' ]]';
				$remainingPeriod-=round($months*(60*60*24*30));
			}

			// Days
			$days=floor($remainingPeriod/(60*60*24));
			if ($days)
			{
				$displayPeriod[]=intval($days).' [[ FLASK.COMMON.Day'.($days==1?'':'s').' ]]';
				$remainingPeriod-=round($days*(60*60*24));
			}

			// Hours
			$hours=floor($remainingPeriod/(60*60));
			if ($hours)
			{
				$displayPeriod[]=intval($hours).' [[ FLASK.COMMON.Hour'.($hours==1?'':'s').' ]]';
				$remainingPeriod-=round($hours*(60*60));
			}

			// Minutes
			$minutes=floor($remainingPeriod/60);
			if ($minutes)
			{
				$displayPeriod[]=intval($minutes).' [[ FLASK.COMMON.Min'.($minutes==1?'':'s').' ]]';
				$remainingPeriod-=round($minutes*60);
			}

			// Seconds
			if (!sizeof($displayPeriod) || ($showSeconds && $remainingPeriod>0))
			{
				$displayPeriod[]=intval($remainingPeriod).' [[ FLASK.COMMON.Second'.($remainingPeriod==1?'':'s').' ]]';
			}

			// Return
			return join($separator,$displayPeriod);
		}



		/**
		 *
		 *   Convert display format to YYYY-MM-DD (HH:II:SS)
		 *   -----------------------------------------------
		 *   @access public
		 *   @param string $dateString Date value in display format
		 *   @param bool $acceptYMD Accept if the input is already in YYYY-MM-DD format
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function toYMD( string $dateString, bool $acceptYMD=true )
		{
			// Sanity checks
			if (empty($this->i18nDateFormat)) throw new FlaskPHP\Exception\Exception('Date format not set.');

			// Already in YMD?
			if ($acceptYMD)
			{
				if (preg_match("/^\d\d\d\d\-\d\d\-\d\d$/",$dateString) || preg_match("/^\d\d\d\d\-\d\d\-\d\d \d\d\:\d\d\:\d\d$/",$dateString))
				{
					return $dateString;
				}
			}

			// Parse
			$time=$seconds=false;
			$dateString=trim($dateString);
			if (preg_match("/^(".$this->getDateFormat('check_time_seconds').")$/",$dateString,$match))
			{
				$time=$seconds=true;
				$dateArr=unpack($this->getDateFormat('unpack_time_seconds'),$dateString);
			}
			elseif (preg_match("/^(".$this->getDateFormat('check_time').")$/",$dateString,$match))
			{
				$time=true;
				$dateArr=unpack($this->getDateFormat('unpack_time'),$dateString);
			}
			elseif (preg_match("/^(".$this->getDateFormat('check').")$/",$dateString,$match))
			{
				$dateArr=unpack($this->getDateFormat('unpack'),$dateString);
			}
			else
			{
				throw new FlaskPHP\Exception\InvalidParameterException('[[ FLASK.FIELD.Error.InvalidDate.Fmt : format='.FlaskPHP\Template\Template::parseContent($this->getDateFormat('disp_user')).' ]]');
			}

			// Check
			try
			{
				if (intval($dateArr['month'])<1 || intval($dateArr['month'])>12) throw new \Exception('Invalid month');
				if (intval($dateArr['day'])<1) throw new \Exception('Invalid day');
				if (intval($dateArr['month'])==2 && intval($dateArr['day'])>29) throw new \Exception('Invalid day');
				if (in_array(intval($dateArr['month']),array(4,6,9,11)) && intval($dateArr['day'])>30) throw new \Exception('Invalid day');
				if ($time)
				{
					if (intval($dateArr['hour'])<0 || intval($dateArr['hour'])>23) throw new \Exception('Invalid hour');
					if (intval($dateArr['min'])<0 || intval($dateArr['min'])>59) throw new \Exception('Invalid minute');
					if ($seconds)
					{
						if (intval($dateArr['sec'])<0 || intval($dateArr['sec'])>59) throw new \Exception('Invalid second');
					}
				}
			}
			catch (\Exception $e)
			{
				throw new FlaskPHP\Exception\InvalidParameterException('[[ FLASK.FIELD.Error.InvalidDate.Fmt : format='.FlaskPHP\Template\Template::parseContent($this->getDateFormat('disp_user')).' ]]');
			}

			// Compile
			$dateYMD=sprintf("%04d-%02d-%02d",$dateArr['year'],$dateArr['month'],$dateArr['day']);
			if ($time) $dateYMD.=' '.sprintf("%02d:%02d:%02d",$dateArr['hour'],$dateArr['min'],($seconds?$dateArr['sec']:0));

			// Return
			return $dateYMD;
		}


		/**
		 *
		 *   Convert display format to timestamp
		 *   -----------------------------------
		 *   @access public
		 *   @param string $dateString Date value in display format
		 *   @param bool $acceptYMD Accept input in YYYY-MM-DD format
		 *   @throws \Exception
		 *   @return int
		 *
		 */

		public function toTimestamp( $dateString, bool $acceptYMD=true )
		{
			// Sanity checks
			if (empty($this->i18nDateFormat)) throw new FlaskPHP\Exception\Exception('Date format not set.');

			// Convert and return
			return strtotime($this->toYMD($dateString,$acceptYMD));
		}


		/**
		 *
		 *   Validate date input
		 *   -------------------
		 *   @access public
		 *   @param string $dateString Date value in display format
		 *   @param bool $throwException Throw exception on failure
		 *   @throws \Exception
		 *   @return bool
		 *
		 */

		public function validateDateInput( string $dateString, bool $throwException=false )
		{
			try
			{
				$this->toYMD($dateString);
				return true;
			}
			catch (\Exception $e)
			{
				if ($throwException) throw new FlaskPHP\Exception\InvalidParameterException($e->getMessage());
				return false;
			}
		}


		/**
		 *
		 *   Format decimal value
		 *   --------------------
		 *   @access public
		 *   @param float $value Value
		 *   @param int $precision Max precision
		 *   @param bool $trim Trim decimal value if there is none
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function formatDecimalValue( $value, int $precision=2, bool $trim=false )
		{
			// Sanity checks
			if (empty($this->i18nThousandSeparator)) throw new FlaskPHP\Exception\Exception('Thousand separator not set.');
			if ($precision>0 && empty($this->i18nDecimalSeparator)) throw new FlaskPHP\Exception\Exception('Decimal separator not set.');

			$formattedValue=number_format($value,$precision,$this->i18nDecimalSeparator,$this->i18nThousandSeparator);
			if ($precision && $trim)
			{
				$formattedValue=rtrim($formattedValue,'0');
				$formattedValue=preg_replace("/\\".$this->i18nDecimalSeparator."$/","",$formattedValue);
			}
			return $formattedValue;
		}


		/**
		 *
		 *   Format currency value
		 *   ---------------------
		 *   @access public
		 *   @param float $value Value
		 *   @param string $currency Currency
		 *   @param bool $useCurrencySymbol Use currency symbol if exists
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function formatCurrency( $value, string $currency, bool $useCurrencySymbol=false )
		{
			// Sanity checks
			if (empty($this->i18nCurrencyPlacement)) throw new FlaskPHP\Exception\Exception('Currency placement not set.');
			if (!array_key_exists($currency,CurrencyData::$setStandardCurrency)) throw new FlaskPHP\Exception\Exception('Unknown currency.');

			$precision=intval(CurrencyData::$setStandardCurrency[$currency]['decimals']);
			$formattedValue=$this->formatDecimalValue($value,$precision,($precision?false:true));
			if ($useCurrencySymbol && !empty(CurrencyData::$setStandardCurrency[$currency]['symbol'])) $currency=CurrencyData::$setStandardCurrency[$currency]['symbol'];
			switch ($this->i18nCurrencyPlacement)
			{
				case 'afterspace':
					$formattedValue=$formattedValue.' '.$currency;
					break;
				case 'after':
					$formattedValue=$formattedValue.$currency;
					break;
				case 'beforespace':
					$formattedValue=$currency.' '.$formattedValue;
					break;
				case 'before':
					$formattedValue=$currency.$formattedValue;
					break;
				default:
					throw new FlaskPHP\Exception\Exception('Unknown currency placement setting.');
			}
			return $formattedValue;
		}


	}


?>
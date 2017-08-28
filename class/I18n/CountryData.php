<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The country data class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\I18n;
	use Codelab\FlaskPHP as FlaskPHP;


	class CountryData
	{


		/**
		 *   Master country List
		 *   @var array
		 *   @static
		 *   @access public
		 */

		public static $countryList = array
		(
			'AF' => 'Afghanistan',
			'AX' => 'Åland Islands',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BQ' => 'Bonaire, Sint Eustatius and Saba',
			'BA' => 'Bosnia and Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'BN' => 'Brunei',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'CV' => 'Cape Verde',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CG' => 'Congo',
			'CD' => 'Democratic Republic of Congo',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => "Côte d'Ivoire",
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CW' => 'Curaçao',
			'CY' => 'Cyprus',
			'CZ' => 'Czech Republic',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island and McDonald Islands',
			'VA' => 'Vatican City',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KP' => 'Democratic Peoples Republic of Korea',
			'KR' => 'Republic of Korea',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Laos',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MK' => 'Macedonia',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestine',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'RE' => 'Réunion',
			'RO' => 'Romania',
			'RU' => 'Russia',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthélemy',
			'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SX' => 'Sint Maarten',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia and the South Sandwich Islands',
			'SS' => 'South Sudan',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard and Jan Mayen',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand',
			'TL' => 'Timor-Leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom',
			'US' => 'United States of America',
			'UM' => 'United States Minor Outlying Islands',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'VG' => 'Virgin Islands (GB)',
			'VI' => 'Virgin Islands (US)',
			'WF' => 'Wallis and Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe'
		);


		/**
		 *   Country name translations
		 *   @var array
		 *   @static
		 *   @access public
		 */

		public static $countryListLocalized = array(

			// Estonian
			'et' => array(
				'AF' => 'Afganistan',
				'AL' => 'Albaania',
				'DZ' => 'Alžeeria',
				'US' => 'Ameerika Ühendriigid',
				'AD' => 'Andorra',
				'AO' => 'Angola',
				'AI' => 'Anguilla',
				'AQ' => 'Antarktis',
				'AG' => 'Antigua ja Barbuda',
				'AE' => 'Araabia Ühendemiraadid',
				'AR' => 'Argentina',
				'AM' => 'Armeenia',
				'AW' => 'Aruba',
				'AZ' => 'Aserbaidžaan',
				'AU' => 'Austraalia',
				'AT' => 'Austria',
				'BS' => 'Bahama',
				'BH' => 'Bahrein',
				'BD' => 'Bangladesh',
				'BB' => 'Barbados',
				'BE' => 'Belgia',
				'BZ' => 'Belize',
				'BJ' => 'Benin',
				'BM' => 'Bermuda',
				'BT' => 'Bhutan',
				'BO' => 'Boliivia',
				'BA' => 'Bosnia ja Hertsegoviina',
				'BW' => 'Botswana',
				'BR' => 'Brasiilia',
				'BN' => 'Brunei',
				'BG' => 'Bulgaaria',
				'BF' => 'Burkina Faso',
				'BI' => 'Burundi',
				'CO' => 'Colombia',
				'CK' => 'Cooki saared',
				'CR' => 'Costa Rica',
				'DJ' => 'Djibouti',
				'DM' => 'Dominica',
				'DO' => 'Dominikaani Vabariik',
				'EC' => 'Ecuador',
				'EG' => 'Egiptus',
				'GQ' => 'Ekvatoriaal-Guinea',
				'SV' => 'El Salvador',
				'CI' => 'Elevandiluurannik',
				'ER' => 'Eritrea',
				'ET' => 'Etioopia',
				'FO' => 'Fääri saared',
				'FK' => 'Falklandi saared',
				'FJ' => 'Fidži',
				'PH' => 'Filipiinid',
				'GA' => 'Gabon',
				'GM' => 'Gambia',
				'GH' => 'Ghana',
				'GI' => 'Gibraltar',
				'GD' => 'Grenada',
				'GL' => 'Gröönimaa',
				'GE' => 'Gruusia',
				'GP' => 'Guadeloupe',
				'GT' => 'Guatemala',
				'GG' => 'Guernsey',
				'GN' => 'Guinea',
				'GW' => 'Guinea-Bissau',
				'GY' => 'Guyana',
				'HT' => 'Haiti',
				'CN' => 'Hiina',
				'ES' => 'Hispaania',
				'NL' => 'Holland',
				'HN' => 'Honduras',
				'HK' => 'Hong Kong',
				'HR' => 'Horvaatia',
				'IE' => 'Iirimaa',
				'IL' => 'Iisrael',
				'IN' => 'India',
				'ID' => 'Indoneesia',
				'IQ' => 'Iraak',
				'IR' => 'Iraan',
				'IS' => 'Island',
				'IT' => 'Itaalia',
				'JP' => 'Jaapan',
				'JM' => 'Jamaica',
				'YE' => 'Jeemen',
				'JE' => 'Jersey',
				'JO' => 'Jordaania',
				'CX' => 'Jõulusaar',
				'KY' => 'Kaimanisaared',
				'KH' => 'Kambodža',
				'CM' => 'Kamerun',
				'IC' => 'Kanaari saared',
				'CA' => 'Kanada',
				'KZ' => 'Kasahstan',
				'QA' => 'Katar',
				'KE' => 'Keenia',
				'CF' => 'Kesk-Aafrika Vabariik',
				'KI' => 'Kiribati',
				'KM' => 'Komoorid',
				'CD' => 'Kongo Demokraatlik Vabariik',
				'CG' => 'Kongo Vabariik',
				'KG' => 'Kõrgõzstan',
				'XK' => 'Kosovo',
				'GR' => 'Kreeka',
				'CY' => 'Küpros',
				'CU' => 'Kuuba',
				'KW' => 'Kuveit',
				'LA' => 'Laos',
				'LV' => 'Läti',
				'LT' => 'Leedu',
				'LS' => 'Lesotho',
				'LR' => 'Libeeria',
				'LI' => 'Liechtenstein',
				'LB' => 'Liibanon',
				'LY' => 'Liibüa',
				'ZA' => 'Lõuna-Aafrika',
				'KR' => 'Lõuna-Korea',
				'SS' => 'Lõuna-Sudaan',
				'LU' => 'Luksemburg',
				'MG' => 'Madagaskar',
				'MK' => 'Makedoonia',
				'MY' => 'Malaisia',
				'MW' => 'Malawi',
				'MV' => 'Maldiivid',
				'ML' => 'Mali',
				'MT' => 'Malta',
				'MA' => 'Maroko',
				'MH' => 'Marshalli Saared',
				'MQ' => 'Martinique',
				'MR' => 'Mauritaania',
				'MU' => 'Mauritius',
				'YT' => 'Mayotte',
				'MX' => 'Mehhiko',
				'FM' => 'Mikroneesia',
				'MD' => 'Moldova',
				'MC' => 'Monaco',
				'MN' => 'Mongoolia',
				'ME' => 'Montenegro',
				'MS' => 'Montserrat',
				'MZ' => 'Mosambiik',
				'MM' => 'Myanmar (Birma)',
				'NA' => 'Namiibia',
				'NR' => 'Nauru',
				'VG' => 'Neitsisaared (GB)',
				'VI' => 'Neitsisaared (US)',
				'NP' => 'Nepal',
				'NI' => 'Nicaragua',
				'NG' => 'Nigeeria',
				'NE' => 'Niger',
				'NU' => 'Niue',
				'NF' => 'Norfolk',
				'NO' => 'Norra',
				'OM' => 'Omaan',
				'PG' => 'Paapua Uus-Guinea',
				'PK' => 'Pakistan',
				'PS' => 'Palestiina',
				'PA' => 'Panama',
				'PY' => 'Paraguay',
				'PE' => 'Peruu',
				'KP' => 'Põhja-Korea',
				'PL' => 'Poola',
				'PT' => 'Portugal',
				'GF' => 'Prantsuse Guajaana',
				'TF' => 'Prantsuse Lõunaalad',
				'PF' => 'Prantsuse Polüneesia',
				'FR' => 'Prantsusmaa',
				'PR' => 'Puerto Rico',
				'CV' => 'Roheneemesaared',
				'SE' => 'Rootsi',
				'RO' => 'Rumeenia',
				'RW' => 'Rwanda',
				'SB' => 'Saalomoni saared',
				'KN' => 'Saint Kitts ja Nevis',
				'LC' => 'Saint Lucia',
				'DE' => 'Saksamaa',
				'ZM' => 'Sambia',
				'WS' => 'Samoa',
				'SM' => 'San Marino',
				'SA' => 'Saudi Araabia',
				'SC' => 'Seišellid',
				'SN' => 'Senegal',
				'RS' => 'Serbia',
				'SL' => 'Sierra Leone',
				'SG' => 'Singapur',
				'SK' => 'Slovakkia',
				'SI' => 'Sloveenia',
				'SO' => 'Somaalia',
				'FI' => 'Soome',
				'LK' => 'Sri Lanka',
				'SD' => 'Sudaan',
				'SR' => 'Suriname',
				'GB' => 'Suurbritannia',
				'SY' => 'Süüria',
				'SZ' => 'Svaasimaa',
				'SJ' => 'Svalbard ja Jan Mayen',
				'CH' => 'Šveits',
				'DK' => 'Taani',
				'TJ' => 'Tadžikistan',
				'TH' => 'Tai',
				'TW' => 'Taiwan',
				'TZ' => 'Tansaania',
				'TG' => 'Togo',
				'TO' => 'Tonga',
				'TT' => 'Trinidad ja Tobago',
				'TD' => 'Tšaad',
				'CZ' => 'Tšehhi',
				'CL' => 'Tšiili',
				'TN' => 'Tuneesia',
				'TR' => 'Türgi',
				'TM' => 'Türkmenistan',
				'TC' => 'Turks ja Caicos',
				'TV' => 'Tuvalu',
				'UG' => 'Uganda',
				'UA' => 'Ukraina',
				'HU' => 'Ungari',
				'UY' => 'Uruguay',
				'UZ' => 'Usbekistan',
				'NC' => 'Uus-Kaledoonia',
				'NZ' => 'Uus-Meremaa',
				'BY' => 'Valgevene',
				'VU' => 'Vanuatu',
				'VA' => 'Vatikan',
				'RU' => 'Venemaa',
				'VE' => 'Venezuela',
				'VN' => 'Vietnam',
				'WF' => 'Wallis ja Futuna',
				'ZW' => 'Zimbabwe',
				'AX' => 'Ahvenamaa',
				'AS' => 'Ameerika Samoa',
				'BQ' => 'Bonaire, Sint Eustatius ja Saba',
				'BV' => "Bouvet' saar",
				'IO' => 'Briti India ookeani ala',
				'CC' => 'Kookossaared',
				'CW' => 'Curacao',
				'EE' => 'Eesti',
				'GU' => 'Guam',
				'HM' => 'Heard ja McDonald',
				'IM' => 'Mani saar',
				'MO' => 'Macao',
				'MP' => 'Põhja-Mariaanid',
				'PW' => 'Palau',
				'PN' => 'Pitcairn',
				'RE' => 'Reunion',
				'BL' => 'Saint Barthelemy',
				'SH' => 'Saint Helena',
				'MF' => 'Saint Martin',
				'PM' => 'Saint-Pierre ja Miquelon',
				'VC' => 'Saint Vincent',
				'ST' => 'Sao Tome ja Principe',
				'SX' => 'Sint Maarten',
				'GS' => 'Lõuna-Georgia ja Lõuna-Sandwichi saared',
				'TL' => 'Ida-Timori',
				'TK' => 'Tokelau',
				'UM' => 'Ühendriigikide hajasaared',
				'EH' => 'Lääne-Sahara',
			)

		);


		/**
		 *   EU member country List
		 *   @var array
		 *   @static
		 *   @access public
		 */

		static public $countryListEU = array
		(
			'AT', // Austria
			'BE', // Belgium
			'BG', // Bulgaria
			'HR', // Croatia
			'CY', // Cyprus
			'CZ', // Czech Republic
			'DK', // Denmark
			'EE', // Estonia
			'FI', // Finland
			'FR', // France
			'DE', // Germany
			'GR', // Greece
			'HU', // Hungary
			'IE', // Ireland
			'IT', // Italy
			'LV', // Latvia
			'LT', // Lithuania
			'LU', // Luxembourg
			'MT', // Malta
			'NL', // Netherlands
			'PL', // Poland
			'PT', // Portugal
			'RO', // Romania
			'SK', // Slovakia
			'SI', // Slovenia
			'ES', // Spain
			'SE', // Sweden
			'UK'  // United Kingdom
		);


		/**
		 *
		 *   Return country list
		 *   -------------------
		 *   @static
		 *   @access public
		 *   @param bool $localize Localize result if possible
		 *   @param string $prioritizeCountry Prioritize country
		 *   @return array
		 *
		 */

		public static function getCountryList( bool $localize=true, string $prioritizeCountry=null )
		{
			global $LAB;

			// Init
			$retVal=array();

			// Do we have own country? If so, put it first
			if ($prioritizeCountry)
			{
				$retVal[$prioritizeCountry]=($localize?oneof(static::$countryListLocalized[Flask()->Locale->localeLanguage][$prioritizeCountry],static::$countryList[$prioritizeCountry]):static::$countryList[$prioritizeCountry]);
			}

			// Other countries
			$countryList=static::$countryList;
			asort($countryList);
			foreach ($countryList as $cCode => $cName)
			{
				if ($cCode==$prioritizeCountry) continue;
				$retVal[$cCode]=($localize?oneof(static::$countryListLocalized[Flask()->Locale->localeLanguage][$cCode],$cName):$cName);
			}

			// Return
			return $retVal;
		}


		/**
		 *
		 *   Get country name by country code
		 *   --------------------------------
		 *   @static
		 *   @access public
		 *   @param string $cCode Country code
		 *   @param bool $localize Localize result if possible
		 *   @return string
		 *   @throws \Exception
		 *
		 */

		public static function getName( string $cCode, bool $localize=true )
		{
			global $LAB;
			if (empty($cCode)) throw new FlaskPHP\Exception\InvalidParameterException('Country code not specified.');
			return ($localize?oneof(static::$countryListLocalized[Flask()->Locale->localeLanguage][$cCode],static::$countryList[$cCode]):static::$countryList[$cCode]);
		}


		/**
		 *
		 *   Is an EU member country?
		 *   ------------------------
		 *   @access public
		 *   @static
		 *   @param string $cCode Country code
		 *   @return bool
		 *   @throws \Exception
		 *
		 */

		public static function isEUmember( string $cCode )
		{
			if (empty($cCode)) throw new FlaskPHP\Exception\InvalidParameterException('Country code not specified.');
			return (in_array($cCode,static::$countryListEU)?true:false);
		}


		/**
		 *
		 *   Get country data
		 *   ----------------
		 *   @access public
		 *   @static
		 *   @param string $cCode Country code (or FALSE for a list of all)
		 *   @param string $data Return only specific data item
		 *   @return mixed
		 *   @throws \Exception
		 */

		public static function getCountryData( string $cCode=null, string $data=null )
		{
			global $LAB;
			try
			{
				// Get from cache or parse
				if (!Flask()->Cache->countryData)
				{
					$countryDataFile=Flask()->resolvePath('data/countries/countries.json');
					if (!$countryDataFile) throw new FlaskPHP\Exception\Exception('Could not load countries.json file.');
					Flask()->Cache->countryData=json_decode(file_get_contents($countryDataFile));
					if (!is_object($LAB->CACHE->countryData) && !is_array($LAB->CACHE->countryData)) throw new FlaskPHP\Exception\Exception('Could not parse countries.json file: '.json_last_error().' / '.json_last_error_msg());
				}
				$countryData=&Flask()->Cache->countryData;

				// Check
				if (!is_object($countryData) && !is_array($countryData)) throw new FlaskPHP\Exception\Exception('Could not parse countries.json file: '.json_last_error().' / '.json_last_error_msg());

				// Return data
				if (!empty($cCode))
				{
					foreach ($countryData as $country)
					{
						if (mb_strlen($cCode)==3)
						{
							if ($country->cca3!=$cCode) continue;
						}
						else
						{
							if ($country->cca2!=$cCode) continue;
						}
						if (!empty($data))
						{
							return $country->{$data};
						}
						else
						{
							return $country;
						}
					}
					return null;
				}
				else
				{
					$retVal=array();
					foreach ($countryData as $country)
					{
						$retVal[strval($country->cca2)]=$country;
					}
					return $retVal;
				}
			}
			catch (\Exception $e)
			{
				return null;
			}
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   The currency data class
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\I18n;
	use Codelab\FlaskPHP as FlaskPHP;


	class CurrencyData
	{


		/**
		 *   All the currencies in the world
		 *   @public
		 *   @static
		 *   @var array
		 */

		public static $setStandardCurrency = array(

			'AFN' => array(
				'name' => 'Afghani',
				'symbol' => '؋',
				'decimals' => '2'
			),

			'EUR' => array(
				'name' => 'Euro',
				'symbol' => '€',
				'decimals' => '2',
				'standard' => true
			),

			'ALL' => array(
				'name' => 'Lek',
				'symbol' => 'Lek',
				'decimals' => '2'
			),

			'DZD' => array(
				'name' => 'Algerian Dinar',
				'decimals' => '2'
			),

			'USD' => array(
				'name' => 'US Dollar',
				'symbol' => '$',
				'decimals' => '2',
				'standard' => true
			),

			'AOA' => array(
				'name' => 'Kwanza',
				'decimals' => '2'
			),

			'XCD' => array(
				'name' => 'East Caribbean Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'ARS' => array(
				'name' => 'Argentine Peso',
				'symbol' => '$',
				'decimals' => '2'
			),

			'AMD' => array(
				'name' => 'Armenian Dram',
				'decimals' => '2'
			),

			'AWG' => array(
				'name' => 'Aruban Florin',
				'symbol' => 'ƒ',
				'decimals' => '2'
			),

			'AUD' => array(
				'name' => 'Australian Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'AZN' => array(
				'name' => 'Azerbaijanian Manat',
				'symbol' => 'ман',
				'decimals' => '2'
			),

			'BSD' => array(
				'name' => 'Bahamian Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'BHD' => array(
				'name' => 'Bahraini Dinar',
				'decimals' => '3'
			),

			'BDT' => array(
				'name' => 'Taka',
				'decimals' => '2'
			),

			'BBD' => array(
				'name' => 'Barbados Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'BYR' => array(
				'name' => 'Belarussian Ruble',
				'symbol' => 'p.',
				'decimals' => '0'
			),

			'BZD' => array(
				'name' => 'Belize Dollar',
				'symbol' => 'BZ$',
				'decimals' => '2'
			),

			'XOF' => array(
				'name' => 'CFA Franc BCEAO',
				'decimals' => '0'
			),

			'BMD' => array(
				'name' => 'Bermudian Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'BTN' => array(
				'name' => 'Ngultrum',
				'decimals' => '2'
			),

			'INR' => array(
				'name' => 'Indian Rupee',
				'decimals' => '2'
			),

			'BOB' => array(
				'name' => 'Boliviano',
				'symbol' => '$b',
				'decimals' => '2'
			),

			'BAM' => array(
				'name' => 'Convertible Mark',
				'symbol' => 'KM',
				'decimals' => '2'
			),

			'BWP' => array(
				'name' => 'Pula',
				'symbol' => 'P',
				'decimals' => '2'
			),

			'NOK' => array(
				'name' => 'Norwegian Krone',
				'symbol' => 'kr',
				'decimals' => '2'
			),

			'BRL' => array(
				'name' => 'Brazilian Real',
				'symbol' => 'R$',
				'decimals' => '2'
			),

			'BND' => array(
				'name' => 'Brunei Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'BGN' => array(
				'name' => 'Bulgarian Lev',
				'symbol' => 'лв',
				'decimals' => '2'
			),

			'BIF' => array(
				'name' => 'Burundi Franc',
				'decimals' => '0'
			),

			'KHR' => array(
				'name' => 'Riel',
				'symbol' => '៛',
				'decimals' => '2'
			),

			'XAF' => array(
				'name' => 'CFA Franc BEAC',
				'decimals' => '0'
			),

			'CAD' => array(
				'name' => 'Canadian Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'CVE' => array(
				'name' => 'Cabo Verde Escudo',
				'decimals' => '2'
			),

			'KYD' => array(
				'name' => 'Cayman Islands Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'CLP' => array(
				'name' => 'Chilean Peso',
				'symbol' => '$',
				'decimals' => '0'
			),

			'CNY' => array(
				'name' => 'Yuan Renminbi',
				'symbol' => '¥',
				'decimals' => '2'
			),

			'COP' => array(
				'name' => 'Colombian Peso',
				'symbol' => '$',
				'decimals' => '2'
			),

			'KMF' => array(
				'name' => 'Comoro Franc',
				'decimals' => '0'
			),

			'CDF' => array(
				'name' => 'Congolese Franc',
				'decimals' => '2'
			),

			'NZD' => array(
				'name' => 'New Zealand Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'CRC' => array(
				'name' => 'Costa Rican Colon',
				'symbol' => '₡',
				'decimals' => '2'
			),

			'HRK' => array(
				'name' => 'Croatian Kuna',
				'symbol' => 'kn',
				'decimals' => '2'
			),

			'CUC' => array(
				'name' => 'Peso Convertible',
				'decimals' => '2'
			),

			'CUP' => array(
				'name' => 'Cuban Peso',
				'symbol' => '₱',
				'decimals' => '2'
			),

			'ANG' => array(
				'name' => 'Netherlands Antillean Guilder',
				'symbol' => 'ƒ',
				'decimals' => '2'
			),

			'CZK' => array(
				'name' => 'Czech Koruna',
				'symbol' => 'Kč',
				'decimals' => '2'
			),

			'DKK' => array(
				'name' => 'Danish Krone',
				'symbol' => 'kr',
				'decimals' => '2'
			),

			'DJF' => array(
				'name' => 'Djibouti Franc',
				'decimals' => '0'
			),

			'DOP' => array(
				'name' => 'Dominican Peso',
				'symbol' => 'RD$',
				'decimals' => '2'
			),

			'EGP' => array(
				'name' => 'Egyptian Pound',
				'symbol' => '£',
				'decimals' => '2'
			),

			'SVC' => array(
				'name' => 'El Salvador Colon',
				'symbol' => '$',
				'decimals' => '2'
			),

			'ERN' => array(
				'name' => 'Nakfa',
				'decimals' => '2'
			),

			'ETB' => array(
				'name' => 'Ethiopian Birr',
				'decimals' => '2'
			),

			'FKP' => array(
				'name' => 'Falkland Islands Pound',
				'symbol' => '£',
				'decimals' => '2'
			),

			'FJD' => array(
				'name' => 'Fiji Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'XPF' => array(
				'name' => 'CFP Franc',
				'decimals' => '0'
			),

			'GMD' => array(
				'name' => 'Dalasi',
				'decimals' => '2'
			),

			'GEL' => array(
				'name' => 'Lari',
				'decimals' => '2'
			),

			'GHS' => array(
				'name' => 'Ghana Cedi',
				'decimals' => '2'
			),

			'GIP' => array(
				'name' => 'Gibraltar Pound',
				'symbol' => '£',
				'decimals' => '2'
			),

			'GTQ' => array(
				'name' => 'Quetzal',
				'symbol' => 'Q',
				'decimals' => '2'
			),

			'GBP' => array(
				'name' => 'Pound Sterling',
				'symbol' => '£',
				'decimals' => '2',
				'standard' => true
			),

			'GNF' => array(
				'name' => 'Guinea Franc',
				'decimals' => '0'
			),

			'GYD' => array(
				'name' => 'Guyana Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'HTG' => array(
				'name' => 'Gourde',
				'decimals' => '2'
			),

			'HNL' => array(
				'name' => 'Lempira',
				'symbol' => 'L',
				'decimals' => '2'
			),

			'HKD' => array(
				'name' => 'Hong Kong Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'HUF' => array(
				'name' => 'Forint',
				'symbol' => 'Ft',
				'decimals' => '2'
			),

			'ISK' => array(
				'name' => 'Iceland Krona',
				'symbol' => 'kr',
				'decimals' => '0'
			),

			'IDR' => array(
				'name' => 'Rupiah',
				'symbol' => 'Rp',
				'decimals' => '2'
			),

			'XDR' => array(
				'name' => 'SDR (Special Drawing Right)',
				'decimals' => '0'
			),

			'IRR' => array(
				'name' => 'Iranian Rial',
				'symbol' => '﷼',
				'decimals' => '2'
			),

			'IQD' => array(
				'name' => 'Iraqi Dinar',
				'decimals' => '3'
			),

			'ILS' => array(
				'name' => 'New Israeli Sheqel',
				'symbol' => '₪',
				'decimals' => '2'
			),

			'JMD' => array(
				'name' => 'Jamaican Dollar',
				'symbol' => 'J$',
				'decimals' => '2'
			),

			'JPY' => array(
				'name' => 'Yen',
				'symbol' => '¥',
				'decimals' => '0'
			),

			'JOD' => array(
				'name' => 'Jordanian Dinar',
				'decimals' => '3'
			),

			'KZT' => array(
				'name' => 'Tenge',
				'symbol' => 'лв',
				'decimals' => '2'
			),

			'KES' => array(
				'name' => 'Kenyan Shilling',
				'decimals' => '2'
			),

			'KPW' => array(
				'name' => 'North Korean Won',
				'symbol' => '₩',
				'decimals' => '2'
			),

			'KRW' => array(
				'name' => 'Won',
				'symbol' => '₩',
				'decimals' => '0'
			),

			'KWD' => array(
				'name' => 'Kuwaiti Dinar',
				'decimals' => '3'
			),

			'KGS' => array(
				'name' => 'Som',
				'symbol' => 'лв',
				'decimals' => '2'
			),

			'LAK' => array(
				'name' => 'Kip',
				'symbol' => '₭',
				'decimals' => '2'
			),

			'LBP' => array(
				'name' => 'Lebanese Pound',
				'symbol' => '£',
				'decimals' => '2'
			),

			'LSL' => array(
				'name' => 'Loti',
				'decimals' => '2'
			),

			'ZAR' => array(
				'name' => 'Rand',
				'symbol' => 'R',
				'decimals' => '2'
			),

			'LRD' => array(
				'name' => 'Liberian Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'LYD' => array(
				'name' => 'Libyan Dinar',
				'decimals' => '3'
			),

			'CHF' => array(
				'name' => 'Swiss Franc',
				'symbol' => 'CHF',
				'decimals' => '2'
			),

			'LTL' => array(
				'name' => 'Lithuanian Litas',
				'symbol' => 'Lt',
				'decimals' => '2'
			),

			'MOP' => array(
				'name' => 'Pataca',
				'decimals' => '2'
			),

			'MKD' => array(
				'name' => 'Denar',
				'symbol' => 'ден',
				'decimals' => '2'
			),

			'MGA' => array(
				'name' => 'Malagasy Ariary',
				'decimals' => '2'
			),

			'MWK' => array(
				'name' => 'Kwacha',
				'decimals' => '2'
			),

			'MYR' => array(
				'name' => 'Malaysian Ringgit',
				'symbol' => 'RM',
				'decimals' => '2'
			),

			'MVR' => array(
				'name' => 'Rufiyaa',
				'decimals' => '2'
			),

			'MRO' => array(
				'name' => 'Ouguiya',
				'decimals' => '2'
			),

			'MUR' => array(
				'name' => 'Mauritius Rupee',
				'symbol' => '₨',
				'decimals' => '2'
			),

			'XUA' => array(
				'name' => 'ADB Unit of Account',
				'decimals' => '0'
			),

			'MXN' => array(
				'name' => 'Mexican Peso',
				'symbol' => '$',
				'decimals' => '2'
			),

			'MDL' => array(
				'name' => 'Moldovan Leu',
				'decimals' => '2'
			),

			'MNT' => array(
				'name' => 'Tugrik',
				'symbol' => '₮',
				'decimals' => '2'
			),

			'MAD' => array(
				'name' => 'Moroccan Dirham',
				'decimals' => '2'
			),

			'MZN' => array(
				'name' => 'Mozambique Metical',
				'symbol' => 'MT',
				'decimals' => '2'
			),

			'MMK' => array(
				'name' => 'Kyat',
				'decimals' => '2'
			),

			'NAD' => array(
				'name' => 'Namibia Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'NPR' => array(
				'name' => 'Nepalese Rupee',
				'symbol' => '₨',
				'decimals' => '2'
			),

			'NIO' => array(
				'name' => 'Cordoba Oro',
				'symbol' => 'C$',
				'decimals' => '2'
			),

			'NGN' => array(
				'name' => 'Naira',
				'symbol' => '₦',
				'decimals' => '2'
			),

			'OMR' => array(
				'name' => 'Rial Omani',
				'symbol' => '﷼',
				'decimals' => '3'
			),

			'PKR' => array(
				'name' => 'Pakistan Rupee',
				'symbol' => '₨',
				'decimals' => '2'
			),

			'PAB' => array(
				'name' => 'Balboa',
				'symbol' => 'B/.',
				'decimals' => '2'
			),

			'PGK' => array(
				'name' => 'Kina',
				'decimals' => '2'
			),

			'PYG' => array(
				'name' => 'Guarani',
				'symbol' => 'Gs',
				'decimals' => '0'
			),

			'PEN' => array(
				'name' => 'Nuevo Sol',
				'symbol' => 'S/.',
				'decimals' => '2'
			),

			'PHP' => array(
				'name' => 'Philippine Peso',
				'symbol' => 'Php',
				'decimals' => '2'
			),

			'PLN' => array(
				'name' => 'Zloty',
				'symbol' => 'zł',
				'decimals' => '2'
			),

			'QAR' => array(
				'name' => 'Qatari Rial',
				'symbol' => '﷼',
				'decimals' => '2'
			),

			'RON' => array(
				'name' => 'New Romanian Leu',
				'symbol' => 'lei',
				'decimals' => '2'
			),

			'RUB' => array(
				'name' => 'Russian Ruble',
				'symbol' => 'руб',
				'decimals' => '2'
			),

			'RWF' => array(
				'name' => 'Rwanda Franc',
				'decimals' => '0'
			),

			'SHP' => array(
				'name' => 'Saint Helena Pound',
				'symbol' => '£',
				'decimals' => '2'
			),

			'WST' => array(
				'name' => 'Tala',
				'decimals' => '2'
			),

			'STD' => array(
				'name' => 'Dobra',
				'decimals' => '2'
			),

			'SAR' => array(
				'name' => 'Saudi Riyal',
				'symbol' => '﷼',
				'decimals' => '2'
			),

			'RSD' => array(
				'name' => 'Serbian Dinar',
				'symbol' => 'Дин.',
				'decimals' => '2'
			),

			'SCR' => array(
				'name' => 'Seychelles Rupee',
				'symbol' => '₨',
				'decimals' => '2'
			),

			'SLL' => array(
				'name' => 'Leone',
				'decimals' => '2'
			),

			'SGD' => array(
				'name' => 'Singapore Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'XSU' => array(
				'name' => 'Sucre',
				'decimals' => '0'
			),

			'SBD' => array(
				'name' => 'Solomon Islands Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'SOS' => array(
				'name' => 'Somali Shilling',
				'symbol' => 'S',
				'decimals' => '2'
			),

			'SSP' => array(
				'name' => 'South Sudanese Pound',
				'decimals' => '2'
			),

			'LKR' => array(
				'name' => 'Sri Lanka Rupee',
				'symbol' => '₨',
				'decimals' => '2'
			),

			'SDG' => array(
				'name' => 'Sudanese Pound',
				'decimals' => '2'
			),

			'SRD' => array(
				'name' => 'Surinam Dollar',
				'symbol' => '$',
				'decimals' => '2'
			),

			'SZL' => array(
				'name' => 'Lilangeni',
				'decimals' => '2'
			),

			'SEK' => array(
				'name' => 'Swedish Krona',
				'symbol' => 'kr',
				'decimals' => '2'
			),

			'SYP' => array(
				'name' => 'Syrian Pound',
				'symbol' => '£',
				'decimals' => '2'
			),

			'TWD' => array(
				'name' => 'New Taiwan Dollar',
				'symbol' => 'NT$',
				'decimals' => '2'
			),

			'TJS' => array(
				'name' => 'Somoni',
				'decimals' => '2'
			),

			'TZS' => array(
				'name' => 'Tanzanian Shilling',
				'decimals' => '2'
			),

			'THB' => array(
				'name' => 'Baht',
				'symbol' => '฿',
				'decimals' => '2'
			),

			'TOP' => array(
				'name' => 'Pa’anga',
				'decimals' => '2'
			),

			'TTD' => array(
				'name' => 'Trinidad and Tobago Dollar',
				'symbol' => 'TT$',
				'decimals' => '2'
			),

			'TND' => array(
				'name' => 'Tunisian Dinar',
				'decimals' => '3'
			),

			'TRY' => array(
				'name' => 'Turkish Lira',
				'symbol' => 'TL',
				'decimals' => '2'
			),

			'TMT' => array(
				'name' => 'Turkmenistan New Manat',
				'decimals' => '2'
			),

			'UGX' => array(
				'name' => 'Uganda Shilling',
				'decimals' => '0'
			),

			'UAH' => array(
				'name' => 'Hryvnia',
				'symbol' => '₴',
				'decimals' => '2'
			),

			'AED' => array(
				'name' => 'UAE Dirham',
				'decimals' => '2'
			),

			'UYU' => array(
				'name' => 'Peso Uruguayo',
				'symbol' => '$U',
				'decimals' => '2'
			),

			'UZS' => array(
				'name' => 'Uzbekistan Sum',
				'symbol' => 'лв',
				'decimals' => '2'
			),

			'VUV' => array(
				'name' => 'Vatu',
				'decimals' => '0'
			),

			'VEF' => array(
				'name' => 'Bolivar',
				'symbol' => 'Bs',
				'decimals' => '2'
			),

			'VND' => array(
				'name' => 'Dong',
				'symbol' => '₫',
				'decimals' => '0'
			),

			'YER' => array(
				'name' => 'Yemeni Rial',
				'symbol' => '﷼',
				'decimals' => '2'
			),

			'ZMW' => array(
				'name' => 'Zambian Kwacha',
				'decimals' => '2'
			),

			'ZWL' => array(
				'name' => 'Zimbabwe Dollar',
				'decimals' => '2'
			)

		);


		/**
		 *   Currency mappings to countries
		 *   @public
		 *   @static
		 *   @var array
		 */

		public static $setCountryCurrency = array(

			'AF' => array(
				'AFN' => 'AFN',
			),

			'AL' => array(
				'ALL' => 'ALL',
			),

			'DZ' => array(
				'DZD' => 'DZD',
			),

			'AS' => array(
				'USD' => 'USD',
			),

			'AD' => array(
				'EUR' => 'EUR',
			),

			'AO' => array(
				'AOA' => 'AOA',
			),

			'AI' => array(
				'XCD' => 'XCD',
			),

			'AG' => array(
				'XCD' => 'XCD',
			),

			'AR' => array(
				'ARS' => 'ARS',
			),

			'AM' => array(
				'AMD' => 'AMD',
			),

			'AW' => array(
				'AWG' => 'AWG',
			),

			'AU' => array(
				'AUD' => 'AUD',
			),

			'AT' => array(
				'EUR' => 'EUR',
			),

			'AZ' => array(
				'AZN' => 'AZN',
			),

			'BS' => array(
				'BSD' => 'BSD',
			),

			'BH' => array(
				'BHD' => 'BHD',
			),

			'BD' => array(
				'BDT' => 'BDT',
			),

			'BB' => array(
				'BBD' => 'BBD',
			),

			'BY' => array(
				'BYR' => 'BYR',
			),

			'BE' => array(
				'EUR' => 'EUR',
			),

			'BZ' => array(
				'BZD' => 'BZD',
			),

			'BJ' => array(
				'XOF' => 'XOF',
			),

			'BM' => array(
				'BMD' => 'BMD',
			),

			'BT' => array(
				'BTN' => 'BTN',
				'INR' => 'INR',
			),

			'BA' => array(
				'BAM' => 'BAM',
			),

			'BW' => array(
				'BWP' => 'BWP',
			),

			'BV' => array(
				'NOK' => 'NOK',
			),

			'BR' => array(
				'BRL' => 'BRL',
			),

			'IO' => array(
				'USD' => 'USD',
			),

			'BN' => array(
				'BND' => 'BND',
			),

			'BG' => array(
				'BGN' => 'BGN',
			),

			'BF' => array(
				'XOF' => 'XOF',
			),

			'BI' => array(
				'BIF' => 'BIF',
			),

			'KH' => array(
				'KHR' => 'KHR',
			),

			'CM' => array(
				'XAF' => 'XAF',
			),

			'CA' => array(
				'CAD' => 'CAD',
			),

			'KY' => array(
				'KYD' => 'KYD',
			),

			'CF' => array(
				'XAF' => 'XAF',
			),

			'TD' => array(
				'XAF' => 'XAF',
			),

			'CL' => array(
				'CLP' => 'CLP',
			),

			'CN' => array(
				'CNY' => 'CNY',
			),

			'CX' => array(
				'AUD' => 'AUD',
			),

			'CC' => array(
				'AUD' => 'AUD',
			),

			'CO' => array(
				'COP' => 'COP',
			),

			'KM' => array(
				'KMF' => 'KMF',
			),

			'CG' => array(
				'XAF' => 'XAF',
			),

			'CK' => array(
				'NZD' => 'NZD',
			),

			'CR' => array(
				'CRC' => 'CRC',
			),

			'HR' => array(
				'HRK' => 'HRK',
			),

			'CU' => array(
				'CUC' => 'CUC',
				'CUP' => 'CUP',
			),

			'CY' => array(
				'EUR' => 'EUR',
			),

			'CZ' => array(
				'CZK' => 'CZK',
			),

			'DK' => array(
				'DKK' => 'DKK',
			),

			'DJ' => array(
				'DJF' => 'DJF',
			),

			'DM' => array(
				'XCD' => 'XCD',
			),

			'DO' => array(
				'DOP' => 'DOP',
			),

			'EC' => array(
				'USD' => 'USD',
			),

			'EG' => array(
				'EGP' => 'EGP',
			),

			'SV' => array(
				'SVC' => 'SVC',
				'USD' => 'USD',
			),

			'GQ' => array(
				'XAF' => 'XAF',
			),

			'ER' => array(
				'ERN' => 'ERN',
			),

			'EE' => array(
				'EUR' => 'EUR',
			),

			'ET' => array(
				'ETB' => 'ETB',
			),

			'FK' => array(
				'FKP' => 'FKP',
			),

			'FO' => array(
				'DKK' => 'DKK',
			),

			'FJ' => array(
				'FJD' => 'FJD',
			),

			'FI' => array(
				'EUR' => 'EUR',
			),

			'FR' => array(
				'EUR' => 'EUR',
			),

			'GF' => array(
				'EUR' => 'EUR',
			),

			'PF' => array(
				'XPF' => 'XPF',
			),

			'TF' => array(
				'EUR' => 'EUR',
			),

			'GA' => array(
				'XAF' => 'XAF',
			),

			'GM' => array(
				'GMD' => 'GMD',
			),

			'GE' => array(
				'GEL' => 'GEL',
			),

			'DE' => array(
				'EUR' => 'EUR',
			),

			'GH' => array(
				'GHS' => 'GHS',
			),

			'GI' => array(
				'GIP' => 'GIP',
			),

			'GR' => array(
				'EUR' => 'EUR',
			),

			'GL' => array(
				'DKK' => 'DKK',
			),

			'GD' => array(
				'XCD' => 'XCD',
			),

			'GP' => array(
				'EUR' => 'EUR',
			),

			'GU' => array(
				'USD' => 'USD',
			),

			'GT' => array(
				'GTQ' => 'GTQ',
			),

			'GG' => array(
				'GBP' => 'GBP',
			),

			'GN' => array(
				'GNF' => 'GNF',
			),

			'GW' => array(
				'XOF' => 'XOF',
			),

			'GY' => array(
				'GYD' => 'GYD',
			),

			'HT' => array(
				'HTG' => 'HTG',
				'USD' => 'USD',
			),

			'HM' => array(
				'AUD' => 'AUD',
			),

			'VA' => array(
				'EUR' => 'EUR',
			),

			'HN' => array(
				'HNL' => 'HNL',
			),

			'HK' => array(
				'HKD' => 'HKD',
			),

			'HU' => array(
				'HUF' => 'HUF',
			),

			'IS' => array(
				'ISK' => 'ISK',
			),

			'IN' => array(
				'INR' => 'INR',
			),

			'ID' => array(
				'IDR' => 'IDR',
			),

			'IR' => array(
				'IRR' => 'IRR',
			),

			'IQ' => array(
				'IQD' => 'IQD',
			),

			'IE' => array(
				'EUR' => 'EUR',
			),

			'IM' => array(
				'GBP' => 'GBP',
			),

			'IL' => array(
				'ILS' => 'ILS',
			),

			'IT' => array(
				'EUR' => 'EUR',
			),

			'JM' => array(
				'JMD' => 'JMD',
			),

			'JP' => array(
				'JPY' => 'JPY',
			),

			'JE' => array(
				'GBP' => 'GBP',
			),

			'JO' => array(
				'JOD' => 'JOD',
			),

			'KZ' => array(
				'KZT' => 'KZT',
			),

			'KE' => array(
				'KES' => 'KES',
			),

			'KI' => array(
				'AUD' => 'AUD',
			),

			'KR' => array(
				'KRW' => 'KRW',
			),

			'KW' => array(
				'KWD' => 'KWD',
			),

			'KG' => array(
				'KGS' => 'KGS',
			),

			'LV' => array(
				'EUR' => 'EUR',
			),

			'LB' => array(
				'LBP' => 'LBP',
			),

			'LS' => array(
				'LSL' => 'LSL',
				'ZAR' => 'ZAR',
			),

			'LR' => array(
				'LRD' => 'LRD',
			),

			'LI' => array(
				'CHF' => 'CHF',
			),

			'LT' => array(
				'LTL' => 'LTL',
			),

			'LU' => array(
				'EUR' => 'EUR',
			),

			'MO' => array(
				'MOP' => 'MOP',
			),

			'MG' => array(
				'MGA' => 'MGA',
			),

			'MW' => array(
				'MWK' => 'MWK',
			),

			'MY' => array(
				'MYR' => 'MYR',
			),

			'MV' => array(
				'MVR' => 'MVR',
			),

			'ML' => array(
				'XOF' => 'XOF',
			),

			'MT' => array(
				'EUR' => 'EUR',
			),

			'MH' => array(
				'USD' => 'USD',
			),

			'MQ' => array(
				'EUR' => 'EUR',
			),

			'MR' => array(
				'MRO' => 'MRO',
			),

			'MU' => array(
				'MUR' => 'MUR',
			),

			'YT' => array(
				'EUR' => 'EUR',
			),

			'MX' => array(
				'MXN' => 'MXN',
			),

			'FM' => array(
				'USD' => 'USD',
			),

			'MD' => array(
				'MDL' => 'MDL',
			),

			'MC' => array(
				'EUR' => 'EUR',
			),

			'MN' => array(
				'MNT' => 'MNT',
			),

			'ME' => array(
				'EUR' => 'EUR',
			),

			'MS' => array(
				'XCD' => 'XCD',
			),

			'MA' => array(
				'MAD' => 'MAD',
			),

			'MZ' => array(
				'MZN' => 'MZN',
			),

			'MM' => array(
				'MMK' => 'MMK',
			),

			'NA' => array(
				'NAD' => 'NAD',
				'ZAR' => 'ZAR',
			),

			'NR' => array(
				'AUD' => 'AUD',
			),

			'NP' => array(
				'NPR' => 'NPR',
			),

			'NL' => array(
				'EUR' => 'EUR',
			),

			'NC' => array(
				'XPF' => 'XPF',
			),

			'NZ' => array(
				'NZD' => 'NZD',
			),

			'NI' => array(
				'NIO' => 'NIO',
			),

			'NE' => array(
				'XOF' => 'XOF',
			),

			'NG' => array(
				'NGN' => 'NGN',
			),

			'NU' => array(
				'NZD' => 'NZD',
			),

			'NF' => array(
				'AUD' => 'AUD',
			),

			'MP' => array(
				'USD' => 'USD',
			),

			'NO' => array(
				'NOK' => 'NOK',
			),

			'OM' => array(
				'OMR' => 'OMR',
			),

			'PK' => array(
				'PKR' => 'PKR',
			),

			'PW' => array(
				'USD' => 'USD',
			),

			'PA' => array(
				'PAB' => 'PAB',
				'USD' => 'USD',
			),

			'PG' => array(
				'PGK' => 'PGK',
			),

			'PY' => array(
				'PYG' => 'PYG',
			),

			'PE' => array(
				'PEN' => 'PEN',
			),

			'PH' => array(
				'PHP' => 'PHP',
			),

			'PN' => array(
				'NZD' => 'NZD',
			),

			'PL' => array(
				'PLN' => 'PLN',
			),

			'PT' => array(
				'EUR' => 'EUR',
			),

			'PR' => array(
				'USD' => 'USD',
			),

			'QA' => array(
				'QAR' => 'QAR',
			),

			'RO' => array(
				'RON' => 'RON',
			),

			'RU' => array(
				'RUB' => 'RUB',
			),

			'RW' => array(
				'RWF' => 'RWF',
			),

			'KN' => array(
				'XCD' => 'XCD',
			),

			'LC' => array(
				'XCD' => 'XCD',
			),

			'PM' => array(
				'EUR' => 'EUR',
			),

			'VC' => array(
				'XCD' => 'XCD',
			),

			'WS' => array(
				'WST' => 'WST',
			),

			'SM' => array(
				'EUR' => 'EUR',
			),

			'ST' => array(
				'STD' => 'STD',
			),

			'SA' => array(
				'SAR' => 'SAR',
			),

			'SN' => array(
				'XOF' => 'XOF',
			),

			'RS' => array(
				'RSD' => 'RSD',
			),

			'SC' => array(
				'SCR' => 'SCR',
			),

			'SL' => array(
				'SLL' => 'SLL',
			),

			'SG' => array(
				'SGD' => 'SGD',
			),

			'SK' => array(
				'EUR' => 'EUR',
			),

			'SI' => array(
				'EUR' => 'EUR',
			),

			'SB' => array(
				'SBD' => 'SBD',
			),

			'SO' => array(
				'SOS' => 'SOS',
			),

			'ZA' => array(
				'ZAR' => 'ZAR',
			),

			'ES' => array(
				'EUR' => 'EUR',
			),

			'LK' => array(
				'LKR' => 'LKR',
			),

			'SD' => array(
				'SDG' => 'SDG',
			),

			'SR' => array(
				'SRD' => 'SRD',
			),

			'SJ' => array(
				'NOK' => 'NOK',
			),

			'SZ' => array(
				'SZL' => 'SZL',
			),

			'SE' => array(
				'SEK' => 'SEK',
			),

			'CH' => array(
				'CHF' => 'CHF',
			),

			'SY' => array(
				'SYP' => 'SYP',
			),

			'TW' => array(
				'TWD' => 'TWD',
			),

			'TJ' => array(
				'TJS' => 'TJS',
			),

			'TZ' => array(
				'TZS' => 'TZS',
			),

			'TH' => array(
				'THB' => 'THB',
			),

			'TL' => array(
				'USD' => 'USD',
			),

			'TG' => array(
				'XOF' => 'XOF',
			),

			'TK' => array(
				'NZD' => 'NZD',
			),

			'TO' => array(
				'TOP' => 'TOP',
			),

			'TT' => array(
				'TTD' => 'TTD',
			),

			'TN' => array(
				'TND' => 'TND',
			),

			'TR' => array(
				'TRY' => 'TRY',
			),

			'TM' => array(
				'TMT' => 'TMT',
			),

			'TC' => array(
				'USD' => 'USD',
			),

			'TV' => array(
				'AUD' => 'AUD',
			),

			'UG' => array(
				'UGX' => 'UGX',
			),

			'UA' => array(
				'UAH' => 'UAH',
			),

			'AE' => array(
				'AED' => 'AED',
			),

			'GB' => array(
				'GBP' => 'GBP',
			),

			'UM' => array(
				'USD' => 'USD',
			),

			'UY' => array(
				'UYU' => 'UYU',
			),

			'UZ' => array(
				'UZS' => 'UZS',
			),

			'VU' => array(
				'VUV' => 'VUV',
			),

			'VE' => array(
				'VEF' => 'VEF',
			),

			'VN' => array(
				'VND' => 'VND',
			),

			'WF' => array(
				'XPF' => 'XPF',
			),

			'EH' => array(
				'MAD' => 'MAD',
			),

			'YE' => array(
				'YER' => 'YER',
			),

			'ZM' => array(
				'ZMW' => 'ZMW',
			),

			'ZW' => array(
				'ZWL' => 'ZWL',
			),

			'US' => array(
				'USD' => 'USD',
			),


		);


		/**
		 *   Get currency info
		 *   @public
		 *   @static
		 *   @param string $currencyCode Currency code
		 *   @param bool $throwException Throw exception on failure?
		 *   @throws \Exception
		 *   @return array
		 */

		public static function getCurrency( string $currencyCode, bool $throwException=true )
		{
			if (!array_key_exists($currencyCode,static::$setStandardCurrency))
			{
				if ($throwException) throw new FlaskPHP\Exception\NotFoundException('Currency not found.');
				return null;
			}
			return static::$setStandardCurrency[$currencyCode];
		}


		/**
		 *   Get currencies used in a country
		 *   @public
		 *   @static
		 *   @param string $countryCode Country code
		 *   @param bool $throwException Throw exception on failure?
		 *   @throws \Exception
		 *   @return array
		 */

		public static function getCountryCurrency( string $countryCode, bool $throwException=true )
		{
			if (!array_key_exists($countryCode,static::$setCountryCurrency))
			{
				if ($throwException) throw new FlaskPHP\Exception\NotFoundException('Country not found.');
				return null;
			}
			return static::$setCountryCurrency[$countryCode];
		}


	}


?>
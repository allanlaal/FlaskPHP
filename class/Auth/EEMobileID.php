<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Auth provider: EE Mobile ID
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Auth;
	use Codelab\FlaskPHP;


	class EEMobileID
	{


		/**
		 *   Dev mode?
		 *   @var bool
		 *   @access public
		 */

		public $devMode = false;


		/**
		 *   Service name
		 *   @var string
		 *   @access public
		 */

		public $serviceName = null;


		/**
		 *   Service message
		 *   @var string
		 *   @access public
		 */

		public $serviceMessage = '';


		/**
		 *   Service language
		 *   @var string
		 *   @access public
		 */

		public $serviceLanguage = 'EST';


		/**
		 *   SP challenge
		 *   @var string
		 *   @access private
		 */

		private $spChallenge = null;



		/**
		 *
		 *   Constructor
		 *   -----------
		 *   @access public
		 *   @param bool $devMode
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Auth\EEMobileID
		 *
		 */

		public function __construct( bool $devMode=null )
		{
			$this->initEEMobileID($devMode);
		}


		/**
		 *
		 *   Init the provider
		 *   -----------------
		 *   @access public
		 *   @param bool $forceDev Force dev environment
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initEEMobileID( bool $devMode=null )
		{
			// Dev mode
			if ($devMode!=null)
			{
				$this->devMode=$devMode;
			}
			else
			{
				$this->devMode=Flask()->Debug->devEnvironment;
			}

			// Service name
			if (Flask()->Config->get('eemobileid.servicename'))
			{
				$this->serviceName=Flask()->Config->get('eemobileid.servicename');
			}
			else
			{
				throw new FlaskPHP\Exception\InvalidParameterException('Missing eemobileid.service configuration directive');
			}

			// Language
			if (Flask()->Config->get('eemobileid.language'))
			{
				$this->serviceLanguage=Flask()->Config->get('eemobileid.language');
			}

			// Default service message
			if (Flask()->Config->get('eemobileid.servicemessage'))
			{
				$this->serviceMessage=Flask()->Config->get('eemobileid.servicemessage');
			}
		}


		/**
		 *
		 *   Init SOAP client
		 *   ----------------
		 *   @access private
		 *   @return \SoapClient
		 *
		 */

		private function initSoapClient()
		{
			// SOAP options
			$streamOptions=array(
				'http' => array(
					'user_agent' => 'PHPSoapClient'
				)
			);
			$streamContext=stream_context_create($streamOptions);
			$soapOptions = array(
				'cache_wsdl' => WSDL_CACHE_MEMORY,
				'stream_context' => $streamContext,
				'trace' => true,
				'encoding' => 'utf-8',
				'classmap' => array(array(
					'MobileAuthenticateResponse' => 'MobileAuthenticateResponse'
				)),
			);

			// Init SOAP client
			if ($this->devMode)
			{
				$WSDL='https://www.openxades.org:8443/?wsdl';
				$this->spChallenge='00000000000000000000';
				$this->serviceName='Testimine';
			}
			else
			{
				$WSDL='https://digidocservice.sk.ee/?wsdl';
				$this->spChallenge='00000010000002000040';
			}

			$soapClient=new SoapClient($WSDL, $soapOptions);
			return $soapClient;
		}


		/**
		 *
		 *   Start Mobile ID auth
		 *   --------------------
		 *   @access public
		 *   @param string $phone Phone number (372xxxxxxx)
		 *   @param string $serviceMessage Service message
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function startAuth( string $phoneNo, string $serviceMessage=null )
		{
			try
			{
				// Remove leading country code
				$phoneNo=preg_replace('/^\+/','',$phoneNo);

				// Set service message
				if ($serviceMessage!==null)
				{
					$this->serviceMessage=$serviceMessage;
				}

				// Init SOAP client
				$soapClient=$this->initSoapClient();

				// Make request
				$soapResponse=$soapClient->MobileAuthenticate(
					'',
					'',
					$phoneNo,
					$this->serviceLanguage,
					$this->serviceName,
					$this->serviceMessage,
					$this->spChallenge,
					'asynchClientServer',
					null,
					false,
					false
				);

				// Success
				if (!empty($soapResponse['UserIDCode']) && !empty($soapResponse['Sesscode']) && !empty($soapResponse['ChallengeID']))
				{
					// Save Mobiil-ID data to session
					Flask()->Session->set('auth.eemobileid.response',serialize($soapResponse));
					Flask()->Session->set('auth.eemobileid.sesscode',strval($soapResponse['Sesscode']));
				}

				// Fail
				else
				{
					throw new FlaskPHP\Exception\Exception('Error talking to the Mobile ID service'.(Flask()->Debug->devEnvironment?': '.var_dump_str($soapResponse):''));
				}
			}
			catch (\SoapFault $soapFault)
			{
				if (!empty($soapFault->detail->message))
				{
					throw new FlaskPHP\Exception\Exception('[[ FLASK.COMMON.Error ]]: '.strval($soapFault->detail->message));
				}
				else
				{
					throw new FlaskPHP\Exception\Exception('Error talking to the Mobile ID service'.(Flask()->Debug->devEnvironment?': '.var_dump_str($soapFault):''));
				}
			}
		}


		/**
		 *
		 *   Check Mobile ID auth status
		 *   ---------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function checkAuthStatus()
		{
			global $LAB;
			try
			{
				// Check
				$midResponse=unserialize(Flask()->Session->get('auth.eemobileid.response'));
				$sessCode=Flask()->Session->get('auth.eemobileid.sesscode');
				if (empty($midResponse['UserIDCode'])) throw new FlaskPHP\Exception\Exception('Error reading session data.');
				if (empty($sessCode)) throw new FlaskPHP\Exception\Exception('Error reading session data.');

				// Init SOAP client
				$soapClient=$this->initSoapClient();

				// Make request
				$soapResponse=$soapClient->GetMobileAuthenticateStatus(
					$sessCode,
					false
				);

				// Success
				$success=false;
				if (!empty($soapResponse['Status']))
				{
					switch (strval($soapResponse['Status']))
					{
						// In progress
						case 'OUTSTANDING_TRANSACTION':
							$response=new EEMobileIDAuthResponse();
							$response->status='pending';
							break;

						// Success
						case 'USER_AUTHENTICATED':
							Flask()->Session->set('auth.eemobileid.response','');
							Flask()->Session->set('auth.eemobileid.sesscode','');
							$response=new EEMobileIDAuthResponse();
							$response->status='pending';
							break;

						// Error
						default:
							Flask()->Session->set('auth.eemobileid.response','');
							Flask()->Session->set('auth.eemobileid.sesscode','');
							$response=new EEMobileIDAuthResponse();
							$response->status='success';
							$response->firstName=$midResponse['UserGivenname'];
							$response->lastName=$midResponse['UserSurname'];
							$response->idCode=$midResponse['UserIDCode'];
							break;
					}
					return $response;
				}
				else
				{
					throw new FlaskPHP\Exception\Exception('Error talking to the Mobile ID service');
				}
			}
			catch (\SoapFault $soapFault)
			{
				if (!empty($soapFault->detail->message))
				{
					throw new FlaskPHP\Exception\Exception('[[ FLASK.COMMON.Error ]]: '.strval($soapFault->detail->message));
				}
				else
				{
					throw new FlaskPHP\Exception\Exception('Error talking to the Mobile ID service'.(Flask()->Debug->devEnvironment?': '.var_dump_str($soapFault):''));
				}
			}
		}


	}


?>
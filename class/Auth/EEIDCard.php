<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Auth provider: EE ID card
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Auth;
	use Codelab\FlaskPHP;


	class EEIDCard
	{


		/**
		 *
		 *   Constructor
		 *   -----------
		 *   @access public
		 *   @param bool $forceDev Force dev environment
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Auth\EEIDCard
		 *
		 */

		public function __construct( bool $forceDev=false )
		{
			$this->initEEIDCard($forceDev);
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

		public function initEEIDCard( bool $forceDev=false )
		{
			// This can be extended in the subclass if necessary.
		}


		/**
		 *
		 *   Get certificate data
		 *   --------------------
		 *   @access public
		 *   @param bool $throwException Throw exception on failure (false = return null)
		 *   @throws \Exception
		 *   @return EEIDCardData
		 *
		 */

		public function getCertificateData( bool $throwException=false )
		{
			try
			{
				// Check
				$CN=oneof($_SERVER['SSL_CLIENT_S_DN_CN'],$_SERVER['REDIRECT_SSL_CLIENT_S_DN_CN'],$_SERVER['HTTP_SSL_CLIENT_S_DN_CN']);
				if (!mb_strlen($CN)) throw new FlaskPHP\Exception\Exception('ID card not detected.');

				// Init object
				$certificateData=new EEIDCardData();

				// CN
				$certificateData->CN=$CN;

				// Parse CN
				list($lastName,$firstName,$idCode)=str_array($CN);
				if (empty($lastName) || empty($firstName) || empty($idCode)) throw new FlaskPHP\Exception\Exception('Error parsing CN.');

				// Set parsed data
				$certificateData->firstName=$firstName;
				$certificateData->lastName=$lastName;
				$certificateData->idCode=$idCode;

				// Return
				return $certificateData;
			}
			catch (\Exception $e)
			{
				if ($throwException) throw $e;
				return null;
			}
		}


		/**
		 *
		 *   Get certificate data value
		 *   --------------------------
		 *   @access public
		 *   @param string $key Data key
		 *   @param bool $throwException Throw exception on failure (false = return null)
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function getCertificateDataValue( string $key, $throwException=false )
		{
			$certificateData=$this->getCertificateData($throwException);
			return $certificateData->{$key};
		}


	}


?>
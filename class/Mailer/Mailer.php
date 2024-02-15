<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The PHPMailer wrapper/extender class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Mailer;
	use Codelab\FlaskPHP as FlaskPHP;


	class Mailer extends \PHPMailer
	{


		/**
		 *
		 *   Constructor
		 *   -----------
		 *   @access public
		 *   @param bool $exceptions Throw exceptions (in FlaskPHP, we always do that)
		 *   @return Mailer
		 *
		 */

		public function __construct( $exceptions=null )
		{
			// We always throw exceptions
			$this->exceptions=true;

			// Use UTF-8
			$this->CharSet='UTF-8';

			// SMTP
			if (Flask()->Config->get('smtp.host'))
			{
				ini_set('openssl.cafile',Flask()->getFlaskPath().'/data/cacert/cacert.pem');
				$this->IsSMTP();
				$this->Host=Flask()->Config->get('smtp.host');
				if (Flask()->Config->get('smtp.auth'))
				{
					$this->SMTPAuth=true;
					$this->Username=Flask()->Config->get('smtp.username');
					$this->Password=Flask()->Config->get('smtp.password');
				}
			}
			if (Flask()->Config->get('smtp.port'))
			{
				$this->Port=Flask()->Config->get('smtp.port');
			}

			// Set default sender
			if (Flask()->Config->get('mail.from'))
			{
				$mailFrom=Flask()->Config->get('mail.from');
				if (is_array($mailFrom))
				{
					$this->Sender=$mailFrom[0];
					$this->setFrom($mailFrom[0],$mailFrom[1]);
				}
				else
				{
					$this->Sender=$mailFrom;
					$this->setFrom($mailFrom);
				}
			}

			// Init mailer
			$this->initMailer();
		}


		/**
		 *
		 *   Init mailer
		 *   -----------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initMailer()
		{
			// This can be implemented in the subclass if necessary.
		}


		/**
		 *
		 *   Add recipient
		 *   -------------
		 *   @access public
		 *   @param string $address E-mail address
		 *   @param string $name Name (optional)
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function AddAddress( $address, $name='' )
		{
			if (Flask()->Debug->devEnvironment)
			{
				if (!Flask()->Config->get('dev.email')) throw new FlaskPHP\Exception\Exception('Cannot send e-mail: dev mode enabled, but dev.email not configured.');
				parent::AddAddress(Flask()->Config->get('dev.email'),$name);
			}
			else
			{
				$recipients=str_array($address,',;');
				foreach ($recipients as $email) parent::AddAddress($email,$name);
			}
		}


		/**
		 *
		 *   Add CC recipient
		 *   ----------------
		 *   @access public
		 *   @param string $address E-mail address
		 *   @param string $name Name (optional)
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function AddCC( $address, $name='' )
		{
			if (Flask()->Debug->devEnvironment)
			{
				if (!Flask()->Config->get('dev.email')) throw new FlaskPHP\Exception\Exception('Cannot send e-mail: dev mode enabled, but dev.email not configured.');
				parent::AddAddress(Flask()->Config->get('dev.email'),$name);
			}
			else
			{
				$recipients=str_array($address,',;');
				foreach ($recipients as $email) parent::AddCC($email,$name);
			}
		}


		/**
		 *
		 *   Add BCC recipient
		 *   -----------------
		 *   @access public
		 *   @param string $address E-mail address
		 *   @param string $name Name (optional)
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function AddBCC( $address, $name='' )
		{
			if (Flask()->Debug->devEnvironment)
			{
				if (!Flask()->Config->get('dev.email')) throw new FlaskPHP\Exception\Exception('Cannot send e-mail: dev mode enabled, but dev.email not configured.');
				parent::AddAddress(Flask()->Config->get('dev.email'),$name);
			}
			else
			{
				$recipients=str_array($address,',;');
				foreach ($recipients as $email) parent::AddBCC($email,$name);
			}
		}


		/**
		 *
		 *   Set message subject
		 *   -------------------
		 *   @access public
		 *   @param string $subject Subject
		 *   @return void
		 *
		 */

		public function setSubject( $subject )
		{
			$this->Subject=$subject;
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   The base user interface
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\User;
	use Codelab\FlaskPHP as FlaskPHP;


	class UserInterface extends FlaskPHP\Model\ModelInterface
	{


		/**
		 *   Roles
		 *   @var array
		 *   @access public
		 */

		public $_roles=array();


		/**
		 *   Init model
		 */

		public function initModel()
		{
			global $LAB;

			// Main parameters
			$this->setParam('table','base_user');
			$this->setParam('idfield','user_oid');
			$this->setParam('setord',false);
			$this->setParam('prop',true);
			$this->setParam('modfields',true);

			// Log info
			$this->setParam('objectname','User');
			$this->setParam('descriptionfield',array('user_email','user_name'));

			// Password mechanism
			$this->setParam('passwordmechanism',oneof(Flask()->Config->get('user.passwordmechanism'),'password_hash'));

			// Authenticate against Voog?
			$this->setParam('auth_voog',false);
			$this->setParam('auth_voog_site',null);
			$this->setParam('auth_voog_createuser',false);

			// User specific fields
			$this->setParam('loginfield_email','user_email');
			$this->setParam('loginfield_password','user_password');
			$this->setParam('loginfield_status','user_status');
			$this->setParam('loginfield_name','user_name');
			$this->setParam('loginfield_lastlogintstamp','user_lastlogin');
			$this->setParam('loginfield_lastloginhost','user_lastlogin_host');

			// Role table
			$this->setParam('roletable','base_user_role');
			$this->setParam('roletable_rolefield','user_role');

			// Log logins?
			$this->setParam('loginlog',true);
			$this->setParam('loginlog_table','base_loginlog');
		}


		/**
		 *   Load user
		 *   @access public
		 *   @param bool $isLogin Is login
		 *   @return void
		 */

		public function loadUser( bool $isLogin=false )
		{
			// Sanity check: make sure we are logged in
			$userOID=Flask()->Session->get('LOGIN.user_oid');
			if (empty($userOID)) return;

			// Load user
			$this->load($userOID);

			// Load roles
			$this->_roles=$this->getRoles();

			// Set login info
			if ($isLogin && (!empty($this->getParam('loginfield_lastlogintstamp')) || !empty($this->getParam('loginfield_lastloginhost'))))
			{
				$saveParam=Flask()->DB->getQueryBuilder();
				if (!empty($this->getParam('loginfield_lastlogintstamp')))
				{
					$this->{$this->getParam('loginfield_lastlogintstamp')}=date('Y-m-d H:i:s');
					$saveParam->addField($this->getParam('loginfield_lastlogintstamp'));
				}
				if (!empty($this->getParam('loginfield_lastloginhost')))
				{
					$this->{$this->getParam('loginfield_lastloginhost')}=Flask()->Request->remoteHost();
					$saveParam->addField($this->getParam('loginfield_lastloginhost'));
				}
				$this->save($saveParam,false);
			}
		}


		/**
		 *   Is user logged in?
		 *   @access public
		 *   @return bool
		 */

		public function isLoggedIn()
		{
			// See if user object is loaded
			$userOID=Flask()->User->{Flask()->User->getParam('idfield')};
			return (intval($userOID)?true:false);
		}


		/**
		 *   Check if user has any of the given roles
		 *   @param string|array $roleList
		 *   @return bool
		 */

		public function checkRole( $roleList )
		{
			// Not logged in?
			if (!$this->isLoggedIn()) return false;

			// Superpowwa
			if (in_array('supervisor',$this->_roles)) return true;

			// Parse: we support checking for multiple possible roles
			$roleList=str_array($roleList);

			// See if we have any
			foreach ($roleList as $r)
			{
				if (in_array($r,$this->_roles)) return true;
			}

			// If we ended up, this means "no"
			return false;
		}


		/**
		 *   Get roles list
		 *   @access public
		 *   @static
		 *   @return array()
		 */

		public static function getRoleList()
		{
			// This can be implemented in the extended class
			return null;
		}


		/**
		 *   Get roles
		 *   @access public
		 *   @return array()
		 *   @throws \Exception
		 */

		public function getRoles()
		{
			// Check
			if (!$this->_loaded) throw new FlaskPHP\Exception\Exception('User not loaded.');

			// Load
			$query=Flask()->DB->getQueryBuilder('SELECT');
			$query->addTable(oneof($this->getParam('roletable'),'base_user_role'));
			$query->addField('*');
			$query->addWhere($this->{$this->getParam('idfield')}.'='.intval($this->{$this->getParam('idfield')}));
			$dataset=Flask()->DB->querySelect($query);

			// Return
			$roleField=oneof($this->getParam('roletable_rolefield'),'user_role');
			$roleList=array();
			foreach ($dataset as $row) $roleList[$row[$roleField]]=$row[$roleField];
			return $roleList;
		}


		/**
		 *   Set roles
		 *   @access public
		 *   @param string|array $roleList Role list (array or tab/comma separated list)
		 *   @return void
		 *   @throws \Exception
		 */

		public function setRoles( $roleList )
		{
			// Check
			if (!$this->_loaded) throw new FlaskPHP\Exception\Exception('User not loaded.');

			// Save
			try
			{
				// Start TX
				Flask()->DB->startTransaction();

				// Delete existing roles
				$query=Flask()->DB->getQueryBuilder('DELETE');
				$query->addTable('base_user_role');
				$query->addWhere('user_oid='.intval($this->{$this->getParam('idfield')}));
				Flask()->DB->queryDelete($query);

				// Add new ones
				if (!is_array($roleList))
				{
					if (strpos($roleList,"\t")!==false)
					{
						$roleList=str_array($roleList,"\t");
					}
					else
					{
						$roleList=str_array($roleList);
					}
				}
				if (is_array($roleList))
				{
					foreach ($roleList as $role)
					{
						$cols=array();
						$cols['user_oid']=$this->{$this->getParam('idfield')};
						$cols['user_role']=$role;
						Flask()->DB->queryInsert('base_user_role',$cols);
					}
				}

				// Commit
				Flask()->DB->doCommit();
			}
			catch (\Exception $e)
			{
				Flask()->DB->doRollback();
				throw $e;
			}
		}


		/**
		 *   Do login
		 *   @access public
		 *   @param string $email E-mail address
		 *   @param string $password Password
		 *   @param bool $throwExceptionOnError Throw exception on error
		 *   @return bool Success/failure
		 *   @throws \Exception
		 */

		public function doLogin( $email, $password, $throwExceptionOnError=true )
		{
			try
			{
				// Check input
				if (!mb_strlen($email)) throw new FlaskPHP\Exception\ValidateException(Flask()->Locale->get('LAB.USER.Login.Error.EmailEmpty'));
				if (!mb_strlen($password)) throw new FlaskPHP\Exception\ValidateException(Flask()->Locale->get('LAB.USER.Login.Error.PasswordEmpty'));

				// Validate against Voog?
				if ($this->getParam('auth_voog'))
				{
					$voogLogin=$this->authVoog($email,$password,false);
				}

				// Select
				$user=Flask()->DB->selectOneSQL("SELECT * FROM ".$this->getParam('table')." WHERE ".$this->getParam('loginfield_email')."='".addslashes($email)."'");

				// User not found
				if (empty($user[$this->getParam('idfield')]))
				{
					// Create new user from Voog?
					if (!empty($this->getParam('auth_voog')) && $voogLogin===true && !empty($this->getParam('auth_voog_createuser')))
					{
						$userClass=get_called_class();
						$NEWUSER=new $userClass();
						$NEWUSER->{$this->getParam('loginfield_email')}=$email;
						$NEWUSER->{$this->getParam('loginfield_password')}=$this->getPasswordHash($password);
						$NEWUSER->{$this->getParam('loginfield_status')}=0;
						$NEWUSER->{$this->getParam('loginfield_name')}='Voog / '.$email;
						$NEWUSER->save(null,'User created from Voog auth');
						$user=Flask()->DB->selectOneSQL("SELECT * FROM ".$this->getParam('table')." WHERE ".$this->getParam('idfield')."='".intval($NEWUSER->{$this->getParam('idfield')})."'");
					}
					else
					{
						throw new FlaskPHP\Exception\LoginFailedException(Flask()->Locale->get('LAB.USER.Login.Error.NoSuchUser'));
					}
				}

				// Check password
				if (empty($this->getParam('auth_voog')) || !$voogLogin)
				{
					$passwordAlgorithm=oneof($this->getParam('passwordmechanism'),'password_hash');
					switch($passwordAlgorithm)
					{
						// PHP password_hash
						case 'password_hash':
							if (!password_verify($password,$user[$this->getParam('loginfield_password')])) throw new FlaskPHP\Exception\LoginFailedException(Flask()->Locale->get('LAB.USER.Login.Error.WrongPassword'));
							break;

						// MD5
						case 'md5':
							if (strpos($user[$this->getParam('loginfield_password')],':')!==false)
							{
								list($hash,$salt)=preg_split('/:/',$user[$this->getParam('loginfield_password')],2);
								if (md5($salt.$password)!=$hash) throw new FlaskPHP\Exception\LoginFailedException(Flask()->Locale->get('LAB.USER.Login.Error.WrongPassword'));
							}
							else
							{
								if (md5($password)!=$user[$this->getParam('loginfield_password')]) throw new FlaskPHP\Exception\LoginFailedException(Flask()->Locale->get('LAB.USER.Login.Error.WrongPassword'));
							}
							break;

						// Default: meh?
						default:
							throw new FlaskPHP\Exception\Exception('Invalid/unknown/missing password encryption algorithm.');
					}
				}

				// Check status
				if ($user[$this->getParam('loginfield_status')]==1) throw new FlaskPHP\Exception\LoginFailedException(Flask()->Locale->get('LAB.USER.Login.Error.UserPending'));
				if ($user[$this->getParam('loginfield_status')]>1) throw new FlaskPHP\Exception\LoginFailedException(Flask()->Locale->get('LAB.USER.Login.Error.UserDisabled'));

				// Set session
				Flask()->Session->set('LOGIN.user_oid',$user[$this->getParam('idfield')]);

				// Load user
				$this->loadUser(true);
				$this->triggerLogin();

				// Log success
				if ($this->getParam('loginlog'))
				{
					$cols=array(
						'user_oid' => $this->{$this->getParam('idfield')},
						'loginlog_tstamp' => date('Y-m-d H:i:s'),
						'loginlog_type' => 1,
						'loginlog_ip' => Flask()->Request->remoteIP(),
						'loginlog_hostname' => Flask()->Request->remoteHost(),
						'loginlog_email' => $this->{$this->getParam('loginfield_email')},
						'loginlog_entry' => '[[ LAB.USER.Login.Success ]]'
					);
					if (is_array($this->getParam('loginlog_data')))
					{
						foreach ($this->getParam('loginlog_data') as $k => $v)
						{
							$cols[$k]=$v;
						}
					}
					Flask()->DB->queryInsert(oneof($this->getParam('loginlog_table'),'base_loginlog'),$cols);
				}

				// Success
				return TRUE;
			}
			catch (FlaskPHP\Exception\LoginFailedException $e)
			{
				// Log failure
				if ($this->getParam('loginlog'))
				{
					$cols=array(
						'user_oid' => intval($this->{$this->getParam('idfield')}),
						'loginlog_tstamp' => date('Y-m-d H:i:s'),
						'loginlog_type' => 2,
						'loginlog_ip' => Flask()->Request->remoteIP(),
						'loginlog_hostname' => Flask()->Request->remoteHost(),
						'loginlog_email' => $email,
						'loginlog_entry' => $e->getMessage()
					);
					if (is_array($this->getParam('loginlog_data')))
					{
						foreach ($this->getParam('loginlog_data') as $k => $v)
						{
							$cols[$k]=$v;
						}
					}
					Flask()->DB->queryInsert(oneof($this->getParam('loginlog_table'),'base_loginlog'),$cols);
				}

				// Handle error
				if ($throwExceptionOnError)
				{
					if (Flask()->Debug->devEnvironment) throw $e;
					throw new FlaskPHP\Exception\LoginFailedException(Flask()->Locale->get('LAB.USER.Login.Error.Unsuccessful'));
				}
				return false;
			}
			catch (\Exception $e)
			{
				// Handle error
				if ($throwExceptionOnError)
				{
					if (Flask()->Debug->devEnvironment) throw $e;
					throw new FlaskPHP\Exception\LoginFailedException(Flask()->Locale->get('LAB.USER.Login.Error.Unsuccessful'));
				}
				return false;
			}
		}


		/**
		 *   Authenticate against Voog
		 *   @access public
		 *   @param string $email E-mail address
		 *   @param string $password Password
		 *   @param bool $throwExceptionOnError Throw exception on error
		 *   @return bool Success/failure
		 *   @throws \Exception
		 */

		public function authVoog( $email, $password, $throwExceptionOnError=true )
		{
			try
			{
				// Check input
				if (!mb_strlen($email)) throw new FlaskPHP\Exception\Exception(Flask()->Locale->get('LAB.USER.Login.Error.EmailEmpty'));
				if (!mb_strlen($password)) throw new FlaskPHP\Exception\Exception(Flask()->Locale->get('LAB.USER.Login.Error.PasswordEmpty'));
				if (!mb_strlen($this->getParam('auth_voog_site'))) throw new FlaskPHP\Exception\Exception('auth_voog_site not specified.');

				// Make request
				$HTTP=new FlaskPHP\Http\HttpRequest();
				$HTTP->setURL($this->getParam('auth_voog_site').'/admin/login');
				$HTTP->setRequestMethod('POST');
				$HTTP->setOption('newcookiesession',true);
				$HTTP->setOption('followlocation',false);
				$HTTP->setRequestHeaders(array(
					'User-Agent' => 'FlaskPHP by Codelab, Voog user auth agent (curlHTTPRequest)',
					'Accept' => '',
					'Accept-Encoding' => ''
				));
				$HTTP->setPostFields(array(
					'utf8' => '✓',
					'language' => 'en',
					'email' => $email,
					'password' => $password
				));
				$HTTP->send();

				// 302 - login OK
				if ($HTTP->getResponseCode()==302)
				{
					return true;
				}

				// Otherwise, fail
				else
				{
					throw new FlaskPHP\Exception\Exception('Login failed, HTTP response: '.$HTTP->getResponseCode());
				}
			}
			catch (\Exception $e)
			{
				if ($throwExceptionOnError) throw $e;
				return false;
			}
		}


		/**
		 *   Do logout
		 *   @access public
		 *   @return void
		 */

		public function doLogout()
		{
			Flask()->Session->set('LOGIN.user_oid',null);
			$this->triggerLogout();
		}


		/**
		 *   Get password hash
		 *   @access public
		 *   @param string $password Password
		 *   @return string Password hash
		 *   @throws \Exception
		 */

		public function getPasswordHash( string $password )
		{
			// Encrypt
			$passwordAlgorithm=oneof($this->getParam('passwordmechanism'),'password_hash');
			switch($passwordAlgorithm)
			{
				// PHP password_hash
				case 'password_hash':
					$passwordCryptStrength=oneof($this->getParam('passwordmechanism_cost'),'12');
					$hash=password_hash($password,PASSWORD_DEFAULT,array(
						'cost' => $passwordCryptStrength
					));
					break;

				// MD5
				case 'md5':
					$hash=md5($password);
					break;

				// Default: meh?
				default:
					throw new FlaskPHP\Exception\Exception('Invalid/unknown/missing password encryption algorithm.');
			}

			// Return
			return $hash;
		}


		/**
		 *   Verify password
		 *   @access public
		 *   @param string $password Password
		 *   @return bool
		 *   @throws \Exception
		 */

		public function verifyPassword( string $password )
		{
			// Encrypt
			$passwordAlgorithm=oneof($this->getParam('passwordmechanism'),'password_hash');
			switch($passwordAlgorithm)
			{
				// PHP password_hash
				case 'password_hash':
					if (!password_verify($password,$this->{$this->getParam('loginfield_password')})) return false;
					break;

				// MD5
				case 'md5':
					if (strpos($this->{$this->getParam('loginfield_password')},':')!==false)
					{
						list($hash,$salt)=preg_split('/:/',$this->{$this->getParam('loginfield_password')},2);
						if (md5($salt.$password)!=$hash) return false;
					}
					else
					{
						if (md5($password)!=$this->{$this->getParam('loginfield_password')}) return false;
					}
					break;

				// Default: meh?
				default:
					throw new FlaskPHP\Exception\Exception('Invalid/unknown/missing password encryption algorithm.');
			}

			// Return
			return true;
		}


		/**
		 *   Login trigger
		 *   @access public
		 *   @return void
		 */

		public function triggerLogin()
		{
			// This can be implemented in the subclass if needed.
		}


		/**
		 *   Logout trigger
		 *   @access public
		 *   @return void
		 */

		public function triggerLogout()
		{
			// This can be implemented in the subclass if needed.
		}


	}


?>
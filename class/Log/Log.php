<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The log class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Log;
	use Codelab\FlaskPHP as FlaskPHP;


	class Log extends FlaskPHP\Model\ModelInterface
	{


		/**
		 *
		 *   Init model
		 *   ----------
		 *   @access public
		 *   @return void
		 *
		 */

		public function initModel()
		{
			$this->setParam('table','flask_log');
			$this->setParam('objectname','Log entry');
		}


		/**
		 *
		 *   Load data object by OID
		 *   -----------------------
		 *   @static
		 *   @access public
		 *   @param int $refOID Reference OID
		 *   @param string $logEntry Log entry
		 *   @param string $logData Detailed log data
		 *   @param int $affectedOID Affected element OID (null=none)
		 *   @param int $userOID User OID (null=current user)
		 *   @return void
		 *
		 */

		public static function logEntry( $refOID, $logEntry, $logData='', $affectedOID=null, $userOID=null, $timestamp=null )
		{
			try
			{
				$refOID=str_array($refOID,',');
				foreach ($refOID as $r)
				{
					$cols=array();
					$cols['ref_oid']=$r;
					$cols['affected_oid']=($affectedOID===null?$refOID:$affectedOID);
					$cols['user_oid']=($userOID!==null?$userOID:(Flask()->User->isLoggedIn()?Flask()->User->{Flask()->User->getParam('idfield')}:0));
					$cols['log_tstamp']=($timestamp!==null?$timestamp:date('Y-m-d H:i:s'));
					$cols['log_entry']=$logEntry;
					$cols['log_data']=(is_array($logData)?json_encode($logData):$logData);
					Flask()->DB->queryInsert('flask_log',$cols);
				}
			}
			catch (\Exception $e)
			{
				return;
			}
		}


	}


?>
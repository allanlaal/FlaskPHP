<?php


	/**
	 *
	 *   FlaskPHP
	 *   The model interface
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Model;
	use Codelab\FlaskPHP as FlaskPHP;


	class ModelInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Fields/columns
		 *   @var array
		 *   @access public
		 */

		public $_field = array();


		/**
		 *   Foreign relations
		 *   @var array
		 *   @access public
		 */

		public $_rel = array();


		/**
		 *   Loaded data
		 *   @var array
		 *   @access public
		 */

		public $_in = array();


		/**
		 *   Is loaded?
		 *   @var bool
		 *   @access public
		 */

		public $_loaded = false;


		/**
		 *   OID
		 *   @var int
		 *   @access public
		 */

		public $_oid = null;


		/**
		 *   Parent OID
		 *   @var int
		 *   @access public
		 */

		public $_parent = null;


		/**
		 *   Access: is readable?
		 *   @var bool
		 *   @access public
		 */

		public $_readable = null;


		/**
		 *   Access: is writable?
		 *   @var bool
		 *   @access public
		 */

		public $_writable = null;


		/**
		 *    Skip fields for log
		 *    @var array
		 *    @access public
		 */

		public $_logSkipFields = array();


		/**
		 *    Log field titles
		 *    @var array
		 *    @access public
		 */

		public $_logFieldTitle = array();


		/**
		 *    Log field value mappings
		 *    @var array
		 *    @access public
		 */

		public $_logFieldValueMapping = array();


		/**
		 *   Unique ID of this object instance
		 *   @var string
		 *   @access public
		 */

		public $_uniqID = null;


		/**
		 *   Init model
		 *   @access public
		 *   @return \Codelab\FlaskPHP\Model\ModelInterface
		 */

		public function __construct()
		{
			$this->initModel();
			$this->initFields();
			$this->setDefaults();
		}


		/**
		 *   Init model
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function initModel()
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function initModel() not implemented in the model class.');
		}


		/**
		 *   Init data fields
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function initFields()
		{
			// This function can be implemented in the model class if necessary
		}


		/**
		 *   Set undefined parameters to defaults
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function setDefaults()
		{
			// Set property table values to defaults
			if ($this->_param['prop'])
			{
				if (!array_key_exists('prop_table',$this->_param))
				{
					$this->_param['prop_table']=$this->getParam('table').'_prop';
				}
				if (!array_key_exists('prop_referencefield',$this->_param))
				{
					$this->_param['prop_referencefield']=$this->getParam('idfield');
				}
				if (!array_key_exists('prop_namefield',$this->_param) || !array_key_exists('prop_valuefield',$this->_param))
				{
					$prop_table_arr=preg_split('/_/',$this->_param['prop_table']);
					if (sizeof($prop_table_arr)==1 || (sizeof($prop_table_arr)==2 && $prop_table_arr[1]=='prop'))
					{
						$fld_base=$prop_table_arr[0];
					}
					else
					{
						$fld_base=preg_replace("/^".$prop_table_arr[0]."_/","",$this->_param['prop_table']);
						if ($prop_table_arr[sizeof($prop_table_arr)-1]!='prop') $fld_base.='prop';
					}
					if (!array_key_exists('prop_namefield',$this->_param))
					{
						$this->_param['prop_namefield']=$fld_base.'_name';
					}
					if (!array_key_exists('prop_valuefield',$this->_param))
					{
						$this->_param['prop_valuefield']=$fld_base.'_value';
					}
				}
			}
		}


		/**
		 *   Clear data object
		 *   @access public
		 *   @return void
		 */

		public function clear()
		{
			// Clear variables
			foreach ($this as $k=>$v)
			{
				if (!strncmp($k,'_',1)) continue;
				unset($this->$k);
			}

			// Set system variables to defaults
			$this->_in=array();
			$this->_loaded=false;
			$this->_oid=null;
			$this->_parent=null;
			$this->_readable=null;
			$this->_writable=null;
		}


		/**
		 *   Load model by OID
		 *   @static
		 *   @access public
		 *   @param int $oid Object OID
		 *   @param bool $forceReload Force reload from DB
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Parameters
		 *   @return ModelInterface loaded object
		 *   @throws \Exception
		 */

		public static function getObject( int $oid, bool $forceReload=false, FlaskPHP\DB\QueryBuilderInterface $param=null )
		{
			// See if we have it in cache
			$className=get_called_class();
			if (!$forceReload && is_object(Flask()->Cache->modelCache[mb_strtolower($className)][$oid]))
			{
				$retVal=Flask()->Cache->modelCache[mb_strtolower($className)][$oid];
				return $retVal;
			}

			// Create and return
			$model=new $className();
			$model->load($oid,$param);
			return $model;
		}


		/**
		 *   Get data object by field
		 *   @static
		 *   @access public
		 *   @param string $field Field name
		 *   @param mixed $value Value
		 *   @param bool $forceReload Force reload from DB
		 *   @param bool $throwException Throw exception if not found
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Additional parameters
		 *   @return ModelInterface loaded object
		 *   @throws \Exception
		 */

		public static function getByField( string $field, $value, bool $forceReload=false, bool $throwException=true, FlaskPHP\DB\QueryBuilderInterface $param=null )
		{
			// Get class
			$className=get_called_class();
			$model=new $className();

			// Param
			$query=oneof($param,Flask()->DB->getQueryBuilder());
			$query->setModel($model);
			$query->addWhere($field.'='.$query::colValue($value));

			// See if there is a match
			$oid=Flask()->DB->selectField($model->getParam('idfield'),$query);
			if ($oid===null)
			{
				if ($throwException) throw new FlaskPHP\Exception\Exception('Model '.$className.' not found where '.$field.' is '.strval($value));
				return null;
			}

			// Load
			$model->load($oid,$param);
			return $model;
		}


		/**
		 *   Load model
		 *   @access public
		 *   @param int $oid OID value
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param parameters
		 *   @return void
		 *   @throws \Exception
		 */

		public function load( int $oid, FlaskPHP\DB\QueryBuilderInterface $param=null )
		{
			// Already loaded?
			if ($this->_loaded) return;

			// Clear
			$this->clear();

			// Nothing to load?
			if (empty($oid)) throw new FlaskPHP\Exception\Exception('Cannot load '.get_called_class().': missing OID');

			// Parameters
			$query=oneof($param,Flask()->DB->getQueryBuilder());
			$query->setModel($this);
			$query->queryField=null;
			$query->addWhere($this->getParam('idfield').'='.$query::colValue($oid));

			// Load
			$row=Flask()->DB->selectOne($query);
			if ($row[$this->getParam('idfield')]!=$oid) throw new FlaskPHP\Exception\Exception('Cannot load '.get_called_class().': OID '.intval($oid).' not found');

			// Set values
			foreach ($row as $k => $v)
			{
				$this->_in[$k]=$v;
				$this->$k=$v;
			}

			// OID
			$this->_oid=$this->{$this->getParam('idfield')};

			// Props
			if ($this->getParam('prop'))
			{
				$propList=array();
				if ($param!==null && is_array($param->queryField))
				{
					foreach ($param->queryField as $col)
					{
						if (strncasecmp($col,'prop_',5)) continue;
						$propList[]=substr($col,5);
					}
				}
				if ($param===null || empty($param->queryField) || sizeof($propList))
				{
					$propQuery=Flask()->DB->getQueryBuilder();
					$propQuery->addTable($this->getParam('prop_table'));
					$propQuery->addWhere($this->getParam('prop_referencefield').'='.intval($oid));
					if (!empty($propList))
					{
						$propQuery->addWhere($this->getParam('prop_namefield').' IN('.$propQuery::inValues($propList).')');
					}
					$propSet=Flask()->DB->querySelect($propQuery);
					foreach ($propSet as $prop)
					{
						$this->{'prop_'.$prop[$this->getParam('prop_namefield')]}=$prop[$this->getParam('prop_valuefield')];
						$this->_in['prop_'.$prop[$this->getParam('prop_namefield')]]=$prop[$this->getParam('prop_valuefield')];
					}
				}
			}

			// Put into object cache
			Flask()->Cache->modelCache[mb_strtolower(get_called_class())][$this->_oid]=&$this;

			// Relations
			if (sizeof($this->_rel))
			{
				foreach ($this->_rel as $relID => $relParam)
				{
					if (empty($this->{$relParam['field']})) continue;
					if (intval($this->{$relParam['field']})<1000000) continue;
					$this->{$relID}=$relParam['remotemodel']::getObject($this->{$relParam['field']});
				}
			}

			// Loaded
			$this->_oid=$oid;
			$this->_loaded=true;
			$this->_uniqID=uniqid();

			// Post-load trigger
			$this->triggerPostLoad();
		}


		/**
		 *   Post load trigger
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 */

		public function triggerPostLoad()
		{
			// This can be implemented in the subclass if necessary
		}


		/**
		 *   Save model
		 *   @access public
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Parameters
		 *   @param string|boolean $logMessage Log message (null for default, FALSE for no log, string for message)
		 *   @param string $logData Log data
		 *   @param int $refOID Log reference OID
		 *   @param string $logOp Log operation
		 *   @param bool $skipValidation Skip validation
		 *   @param \Codelab\FlaskPHP\Action\FormAction $formObject Form object for logging helper info
		 *   @return void
		 *   @throws \Exception
		 */

		public function save( FlaskPHP\DB\QueryBuilderInterface $param=null, $logMessage=null, $logData=null, $refOID=null, $logOp=null, $skipValidation=false, $formObject=null )
		{
			// Init
			if ($this->_loaded)
			{
				$op='edit';
				$updated=false;
			}
			else
			{
				$op='add';
				$updated=true;
			}

			// Validate
			if (!$skipValidation)
			{
				$this->validateSave($op,$param,$formObject);
			}

			// Try
			try
			{
				// Start transaction
				Flask()->DB->startTransaction();

				// Save
				if ($this->_loaded)
				{
					// Op
					$op='edit';

					// Check for OID
					$oid=$this->_oid;

					// Save main record
					$cols=array();
					foreach ($this as $col => $val)
					{
						if (is_object($val)) continue;
						if ($col[0]=='_') continue;
						if (mb_strtolower($col)!=$col) continue;
						if ($this->_param['prop'] && !strncasecmp($col,'prop_',5)) continue;
						if (!empty($param) && !empty($param->queryField) && !in_array($col,$param->queryField)) continue;
						if (!empty($param) && !empty($param->skipField) && in_array($col,$param->skipField)) continue;
						if ((empty($param) || empty($param->queryField)) && $val==$this->_in[$col]) continue;
						$cols[$col]=$val;
						if ($val!=$this->_in[$col]) $updated=true;
					}

					// Mod fields
					if (sizeof($cols) && !empty($this->getParam('modfields')))
					{
						$cols['mod_tstamp']=date('Y-m-d H:i:s');
						if (!empty(Flask()->User->{Flask()->User->getParam('idfield')}))
						{
							$cols['mod_user_oid']=intval(Flask()->User->{Flask()->User->getParam('idfield')});
						}
						else
						{
							$cols['mod_user_oid']=0;
						}
					}

					// Query
					if (sizeof($cols))
					{
						$queryBuilder=Flask()->DB->getQueryBuilder();
						$where=array($this->getParam('idfield')."=".$queryBuilder::colValue($oid));
						if (!empty($param) && !empty($param->queryWhere))
						{
							$where=array_merge($where,$param->queryWhere);
						}
						Flask()->DB->queryUpdate($this->getParam('table'),$cols,$where);
					}
				}

				// Add
				else
				{
					// Get OID
					$oid=$this->getOID();
					$_POST[$this->getParam('idfield')]=$this->_oid=$this->{$this->getParam('idfield')}=$oid;

					// Init cols
					$cols=array();
					$cols[$this->getParam('idfield')]=$oid;
					if ($this->getParam('setord')) $cols['ord']=$oid;

					// Number hack
					if ($this->getParam('no'))
					{
						// Parse format
						$noFormat=oneof($this->getParam('no_format'),'N');
						$fmt=array();
						foreach (str_split($noFormat) as $f)
						{
							if (empty($f)) continue;
							$fmt[$f]=$fmt[$f].$f;
						}

						// Fields
						$icols=array();
						$icols[$this->getParam('no_field').'_year']=(isset($fmt['Y'])?date('Y'):'0');
						$icols[$this->getParam('no_field').'_month']=(isset($fmt['M'])?date('m'):'0');
						$icols[$this->getParam('no_field').'_day']=(isset($fmt['D'])?date('d'):'0');
						$sql="
							INSERT INTO ".$this->getParam('table')." (".$this->_param['idfield'].",".$this->getParam('no_field')."_year,".$this->getParam('no_field')."_month,".$this->getParam('no_field')."_day,".$this->getParam('no_field')."_seq)
							SELECT ".intval($oid).",".intval($icols[$this->getParam('no_field').'_year']).",".intval($icols[$this->getParam('no_field').'_month']).",".intval($icols[$this->getParam('no_field').'_day']).",coalesce(max(".$this->getParam('no_field')."_seq),0)+1
							FROM ".$this->getParam('table')."
							WHERE
							".$this->getParam('no_field')."_year='".intval($icols[$this->getParam('no_field').'_year'])."'
							and ".$this->getParam('no_field')."_month='".intval($icols[$this->getParam('no_field').'_month'])."'
							and ".$this->getParam('no_field')."_day='".intval($icols[$this->getParam('no_field').'_day'])."'
						";
						Flask()->DB->queryInsertSQL($sql);

						// Load back
						$insertedRow=Flask()->DB->selectOneSQL("SELECT * FROM ".$this->getParam('table')." WHERE ".$this->_param['idfield']."=".intval($oid));
						if ($insertedRow[$this->_param['idfield']]!=$oid) throw new FlaskPHP\Exception\Exception('Could not load back inserted row.');

						if (empty($this->{$this->getParam('no_field').'_no'}))
						{
							// Make display number
							$displayNo=$this->_param['no_format'];
							if (isset($fmt['Y']))
							{
								$str=$insertedRow[$this->getParam('no_field').'_year'];
								if (strlen($fmt['Y'])<strlen($str))
								{
									$str=substr($str,0-strlen($fmt['Y']));
								}
								elseif (strlen($fmt['Y'])>strlen($str))
								{
									$str=str_pad($str, strlen($fmt['Y']), "0", STR_PAD_LEFT);
								}
								$displayNo=str_replace($fmt['Y'],$str,$displayNo);
							}
							if (isset($fmt['M']))
							{
								$str=$insertedRow[$this->getParam('no_field').'_month'];
								if (strlen($fmt['M'])<strlen($str))
								{
									$str=substr($str,0-strlen($fmt['M']));
								}
								elseif (strlen($fmt['M'])>strlen($str))
								{
									$str=str_pad($str, strlen($fmt['M']), "0", STR_PAD_LEFT);
								}
								$displayNo=str_replace($fmt['M'],$str,$displayNo);
							}
							if (isset($fmt['D']))
							{
								$str=$insertedRow[$this->getParam('no_field').'_day'];
								if (strlen($fmt['D'])<strlen($str))
								{
									$str=substr($str,0-strlen($fmt['D']));
								}
								elseif (strlen($fmt['D'])>strlen($str))
								{
									$str=str_pad($str, strlen($fmt['D']), "0", STR_PAD_LEFT);
								}
								$displayNo=str_replace($fmt['D'],$str,$displayNo);
							}
							if (isset($fmt['N']))
							{
								if ($fmt['N']=='N')
								{
									$displayNo=str_replace('N',$insertedRow[$this->getParam('no_field').'_seq'],$displayNo);
								}
								else
								{
									$str=$insertedRow[$this->getParam('no_field').'_seq'];
									if (strlen($fmt['N'])<strlen($str))
									{
										$str=substr($str,0-strlen($fmt['N']));
									}
									elseif (strlen($fmt['N'])>strlen($str))
									{
										$str=str_pad($str, strlen($fmt['N']), "0", STR_PAD_LEFT);
									}
									$displayNo=str_replace($fmt['N'],$str,$displayNo);
								}
							}
							else
							{
								$displayNo.=$insertedRow[$this->getParam('no_field').'_seq'];
							}

							$this->{$this->getParam('no_field').'_no'}=$this->_param['no_prefix'].$displayNo;
							$cols[$this->getParam('no_field').'_no']=$this->{$this->getParam('no_field').'_no'};
						}
					}

					// Add main record
					foreach ($this as $col => $val)
					{
						if (is_object($val)) continue;
						if ($col[0]=='_') continue;
						if (mb_strtolower($col)!=$col) continue;
						if ($this->_param['prop'] && !strncasecmp($col,'prop_',5)) continue;
						if (!empty($param) && !empty($param->queryField) && !in_array($col,$param->queryField)) continue;
						if (!empty($param) && !empty($param->skipField) && in_array($col,$param->skipField)) continue;
						$cols[$col]=$val;
					}

					// Mod fields
					if (!empty($this->getParam('modfields')))
					{
						if (!empty($this->_add_tstamp))
						{
							$cols['add_tstamp']=$this->_add_tstamp;
						}
						else
						{
							$cols['add_tstamp']=date('Y-m-d H:i:s');
						}
						if (!empty($this->_add_user_oid))
						{
							$cols['add_user_oid']=intval($this->_add_user_oid);
						}
						else
						{
							if (!empty(Flask()->User->{Flask()->User->getParam('idfield')}))
							{
								$cols['add_user_oid']=intval(Flask()->User->{Flask()->User->getParam('idfield')});
							}
							else
							{
								$cols['add_user_oid']=0;
							}
						}
					}

					// Query
					if ($this->getParam('no'))
					{
						$queryBuilder=Flask()->DB->getQueryBuilder();
						$where=array($this->getParam('idfield')."=".$queryBuilder::colValue($oid));
						Flask()->DB->queryUpdate($this->getParam('table'),$cols,$where);
					}
					else
					{
						Flask()->DB->queryInsert($this->getParam('table'),$cols);
					}

					// We're loaded now
					$this->_loaded=true;
					$this->_uniqID=uniqid();

					// Link to objectCache
					Flask()->Cache->modelCache[mb_strtolower(get_called_class())][$oid]=&$this;
				}

				// Save properties
				if ($this->getParam('prop'))
				{
					$queryCols=array();

					foreach ($this as $col => $val)
					{
						if (strncasecmp($col,'prop_',5)) continue;
						if (!empty($param) && !empty($param->queryField) && !in_array($col,$param->queryField)) continue;
						if (!empty($param) && !empty($param->skipField) && in_array($col,$param->skipField)) continue;
						if ((empty($param) || empty($param->queryField)) && $val==$this->_in[$col]) continue;
						$queryCols[]=array(
							$this->getParam('prop_referencefield') => $oid,
							$this->getParam('prop_namefield')      => preg_replace('/^prop_/','',$col),
							$this->getParam('prop_valuefield')     => $val
						);
						if ($val!=$this->_in[$col]) $updated=true;
					}

					if (sizeof($queryCols))
					{
						Flask()->DB->queryReplace($this->getParam('prop_table'),$queryCols);
					}
				}

				// (Re-)load relations
				if (sizeof($this->_rel))
				{
					foreach ($this->_rel as $relID => $relParam)
					{
						if (empty($this->{$relParam['field']})) continue;
						if (intval($this->{$relParam['field']})<1000000) continue;
						$this->{$relID}=$relParam['remotemodel']::getObject($this->{$relParam['field']});
					}
				}

				// Log
				if (($updated || Flask()->Debug->devEnvironment) && !empty($this->_param['log']) && $logMessage!==false)
				{
					$logMessage=oneof($logMessage,$this->getParam('objectname').' [[ BASE.LOG.LogEntry.Action.'.$op.' ]]');
					if ($refOID===NULL)
					{
						if (!empty($this->getParam('log_refoid')))
						{
							// Numeric: konkreetne väärtus
							if (is_numeric($this->getParam('log_refoid')))
							{
								$refOID=$this->getParam('log_refoid');
							}
							// Otherwise: antud välja väärtus
							else
							{
								$refOID=$this->{$this->getParam('log_refoid')};
							}
						}
						else
						{
							$refOID=intval($this->{$this->getParam('idfield')});
						}
					}
					$logData=($this->getParam('log')!=='simple'?oneof($logData,$this->getLogData(oneof($logOp,$op),$formObject,$refOID)):'');
					$affectedOID=($refOID!=$this->{$this->_param['idfield']}?$this->{$this->_param['idfield']}:'0');
					FlaskPHP\Log\LogProvider::logEntry($refOID,$logMessage,$logData,$affectedOID,($this->_add_user_oid?$this->_add_user_oid:null),($this->_add_tstamp?$this->_add_tstamp:null));
				}

				// Set $_in to current values
				$this->_in=array();
				foreach ($this as $col => $val)
				{
					if (is_object($val)) continue;
					if ($col[0]=='_') continue;
					if (mb_strtolower($col)!=$col) continue;
					$this->_in[$col]=$val;
				}

				// Post-save trigger
				$this->triggerPostSave($op,$param,$formObject);

				// Commit
				Flask()->DB->doCommit();
				return;
			}

			// Catch exception: rollback and re-throw
			catch (\Exception $e)
			{
				Flask()->DB->doRollback();
				throw $e;
			}
		}


		/**
		 *   Validate for save
		 *   @access public
		 *   @param string $op Operation
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Parameters
		 *   @param \Codelab\FlaskPHP\Action\FormAction $formObject Form object if saving from form
		 *   @return void
		 *   @throws \Exception
		 */

		public function validateSave( $op, FlaskPHP\DB\QueryBuilderInterface $param=null, $formObject=null )
		{
			// TODO: finish
		}


		/**
		 *   Post save trigger
		 *   @access public
		 *   @param string $op Operation
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Parameters
		 *   @param \Codelab\FlaskPHP\Action\FormAction $formObject Form object if saving from form
		 *   @return void
		 *   @throws \Exception
		 */

		public function triggerPostSave( $op, FlaskPHP\DB\QueryBuilderInterface $param=null, $formObject=null )
		{
			// This can be implemented in the subclass if necessary
		}


		/**
		 *   Delete model
		 *   @param string|boolean $logMessage Log message (null for default, FALSE for no log, string for message)
		 *   @param string $logData Log data
		 *   @param int $refOID Log reference OID
		 *   @param string $logOp Log operation
		 *   @param bool $skipValidation Skip validation
		 *   @return void
		 *   @throws \Exception
		 */

		public function delete( $logMessage=null, $logData=null, $refOID=null, $logOp=null, $skipValidation=false )
		{
			// Check
			if (!$this->_loaded) throw new FlaskPHP\Exception\Exception(get_called_class().'::delete() failed: object not loaded.');

			// Validate
			if (!$skipValidation) $this->validateDelete();

			// Try
			try
			{
				// Start transaction
				Flask()->DB->startTransaction();

				// Delete main entry
				$query=Flask()->DB->getQueryBuilder('DELETE');
				$query->setModel($this);
				$query->addWhere($this->getParam('idfield').'='.$query::colValue($this->_oid));
				Flask()->DB->queryDelete($query);

				// Delete props
				if ($this->_param['prop'])
				{
					$query=Flask()->DB->getQueryBuilder('DELETE');
					$query->addTable($this->getParam('prop_table'));
					$query->addWhere($this->getParam('prop_referencefield').'='.$query::colValue($this->_oid));
					Flask()->DB->queryDelete($query);
				}

				// Log
				if (!empty($this->_param) && $logMessage!==FALSE)
				{
					$logMessage=oneof($logMessage,$this->getParam('objectname').' [[ BASE.LOG.LogEntry.Action.delete ]]');
					if ($refOID===null)
					{
						if (!empty($this->getParam('log_refoid')))
						{
							// Numeric: konkreetne väärtus
							if (is_numeric($this->getParam('log_refoid')))
							{
								$refOID=$this->getParam('log_refoid');
							}
							// Otherwise: antud välja väärtus
							else
							{
								$refOID=$this->{$this->getParam('log_refoid')};
							}
						}
						else
						{
							$refOID=intval($this->{$this->getParam('idfield')});
						}
					}
					$logData=($this->_param['log']!=='simple'?oneof($logData,$this->getLogData(oneof($logOp,'delete'),$formObject,$refOID)):'');
					$affectedOID=($refOID!=$this->{$this->getParam('idfield')}?$this->{$this->getParam('idfield')}:false);
					FlaskPHP\Log\Log::logEntry($refOID,$logMessage,$logData,$affectedOID);
				}

				// Post-save trigger
				$this->triggerPostDelete();

				// Commit
				Flask()->DB->doCommit();

				// Reset self
				$this->_oid=null;
				$this->_loaded=false;
				$this->_in=array();
				foreach ($this->_rel as $relID => $relParam) unset($this->$relID);
			}

			// Catch exception: rollback and re-throw
			catch (\Exception $e)
			{
				Flask()->DB->doRollback();
				throw $e;
			}
		}


		/**
		 *   Validate for delete
		 *   @access public
		 *   @param string $op Operation
		 *   @return void
		 *   @throws \Exception
		 */

		public function validateDelete()
		{
			// TODO: finish
		}


		/**
		 *   Post delete trigger
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 */

		public function triggerPostDelete()
		{
			// This can be implemented in the subclass if necessary
		}


		/**
		 *   Swap item order on two items
		 *   @access public
		 *   @static
		 *   @param int $oid1 OID 1
		 *   @param int $oid2 OID 2
		 *   @return void
		 *   @throws \Exception
		 */

		public static function swap( int $oid1, int $oid2 )
		{
			// Generate model instance
			$modelClassName=get_called_class();
			$model=new $modelClassName();

			// Check
			if (!$model->getParam('setord')) throw new FlaskPHP\Exception\Exception('swap(): model '.$modelClassName.' does not have setord set.');

			// Try
			try
			{
				// Start transaction
				Flask()->DB->startTransaction();

				// Load
				$query=Flask()->DB->getQueryBuilder('SELECT');
				$query->setModel($model);
				$query->addField($model->getParam('idfield'));
				$query->addField('ord');
				$query->addWhere($model->getParam('idfield').' IN('.$query::inValues(array($oid1,$oid2)).')');
				$dataset=Flask()->DB->querySelect($query);
				if (sizeof($dataset)!=2) throw new FlaskPHP\Exception\Exception('[[ LAB.COMMON.ErrorReadingData ]]');

				// Swap 1
				$cols=array();
				$cols['ord']=$dataset[$oid2]['ord'];
				Flask()->DB->queryUpdate($model->getParam('table'),$cols,$model->getParam('idfield')."=".$query::colValue($oid1));

				// Swap 2
				$cols=array();
				$cols['ord']=$dataset[$oid1]['ord'];
				Flask()->DB->queryUpdate($model->getParam('table'),$cols,$model->getParam('idfield')."=".$query::colValue($oid2));

				// Commit
				Flask()->DB->doCommit();
			}

			// Catch exception: rollback and re-throw
			catch (\Exception $e)
			{
				Flask()->DB->doRollback();
				throw $e;
			}
		}


		/**
		 *   Get a new OID number
		 *   @access public
		 *   @return int OID
		 */

		public function getOID()
		{
			// Init
			$sequenceTable=oneof($this->_param['sequence'],'base_sequence');
			$objectType=mb_strtolower(oneof($this->getParam('table'),get_class($this)));

			// Insert an entry into the object table
			$cols=array();
			$cols['sequence_objecttype']=$objectType;
			$oid=Flask()->DB->queryInsert($sequenceTable,$cols);

			// Return the ID we got
			return $oid;
		}


		/**
		 *   Get log data
		 *   @access public
		 *   @param string $op Operation
		 *   @param \Codelab\FlaskPHP\Action\FormAction $formObject Form object if submitting a form
		 *   @param int $refOID Reference OID
		 *   @return string
		 */

		public function getLogData( $op='edit', &$formObject=null, $refOID=null )
		{
			// Init
			$updated=false;
			$logData=new LogData();

			// On delete, log key relations and values
			if ($op=='delete')
			{
				// Init
				$logData->operation='delete';
				$updated=true;

				// ID field
				if (!empty($this->getParam('idfield')))
				{
					$ldf=$logData->addField($this->getParam('idfield'),$this->getParam('objectname').' OID');
					$ldf->setValue($this->{$this->getParam('idfield')});
				}

				// Key relations
				if (sizeof($this->_rel))
				{
					reset($this->_rel);
					foreach ($this->_rel as $relID => $relParam)
					{
						// Skip those
						if (!$relParam['keyrelation']) continue;
						if ($relParam['type']!='onetomany') continue;
						if (empty($this->{$relParam['field']})) continue;
						if (!is_object($this->$relID) || !$this->{$relID}->_loaded) continue;

						// Add
						$ldf=$logData->addField(
							$this->{$relParam['field']},
							oneof(
								( $relParam['name']!==null ? $relParam['name'] : null ),
								( (array_key_exists($relParam['field'],$this->_field) && is_object($this->_field[$relParam['field']])) ? $this->_field[$relParam['field']]->getTitle() : null ),
								( (is_object($this->{$relID}) && !empty($this->{$relID}->getParam('objectname'))) ? $this->{$relID}->getParam('objectname') : null ),
								null
							)
						);

						// Relation value
						$relValue=null;
						if (!empty($this->{$relID}->getParam('descriptionfield')))
						{
							$descFldArr=str_array($this->{$relID}->getParam('descriptionfield'));
							$descArr=array();
							foreach ($descFldArr as $descFld)
							{
								if (strlen($this->{$relID}->{$descFld})) $descArr[]=$this->{$relID}->{$descFld};
							}
							if (sizeof($descArr)) $desc=join('; ',$descArr);
						}
						$ldf->setValue($this->{$relParam['field']},$relValue);
					}
				}

				// Key fields
				if (!empty($this->getParam('descriptionfield')))
				{
					$descFldArr=str_array($this->getParam('descriptionfield'));
					foreach ($descFldArr as $descFld)
					{
						// If field object exists, then get value form vielf object
						if (array_key_exists($descFld,$this->_field))
						{
							$this->_field[$descFld]->getLogData($logData,'delete');
						}
						else
						{
							// Skip if empty
							if (!strlen($this->{$descFld})) continue;

							// Add
							$ldf=$logData->addField(
								$descFld,
								oneof(
									( (array_key_exists($descFld,$this->_field) && is_object($this->_field[$descFld])) ? $this->_field[$descFld]->getTitle() : null ),
									null
								)
							);

							// Set value
							$ldf->setValue($this->{$descFld});
						}
					}
				}

				// Return
				return $logData->getSerializedData();
			}

			// Changed values
			$logValues=array();
			foreach ($this as $col => $val)
			{
				// Skip system parameters
				if ($col[0]=='_') continue;
				if ($col!=mb_strtolower($col)) continue;
				if ($col==$this->getParam('idfield')) continue;

				// Skip those that need to be skipped
				if (in_array($col,$this->_logSkipFields)) continue;
				if (array_key_exists($col,$this->_field) && $this->_field[$col]->noLog()) continue;

				// Mitte-editi korral tühja väärtust ei näita
				if ($op!='edit' && !mb_strlen($val)) continue;

				// Editi korral skipime mittemuutunud asjad
				if ($op=='edit')
				{
					if ($val==$this->_in[$col]) continue;
					if ($val=='' && $this->_in[$col]=='0') continue;
					if ($val=='0' && $this->_in[$col]=='') continue;
				}

				// Otherwise, logime
				$logValues[$col]=$col;
			}

			// Traverse through form objects if they exist
			$formObjectField=array();
			if (is_object($formObject))
			{
				foreach ($formObject->field as $fieldTag => $fieldObject)
				{
					$formObjectField[$fieldTag]=$fieldTag;
					$fieldObject->getLogData($logData,$op,$logValues,$formObject);
				}
			}

			// Traverse through model fields
			foreach ($this->_field as $fieldTag => $fieldObject)
			{
				if (in_array($fieldTag,$formObjectField)) continue;
				$fieldObject->getLogData($logData,$op,$logValues);
			}

			// Process the remaining fields
			foreach ($logValues as $k)
			{
				// Skip some
				if ($k=='ord') continue;
				if (in_array($k,$this->_logSkipFields)) continue;

				// TODO: finish
			}

			// Return
			if ($op=='edit' && (!Flask()->Debug->devEnvironment && !$updated)) return '';
			return $logData->getSerializedData();
		}


		/**
		 *   Check if the field value is unique
		 *   @access public
		 *   @param string $fieldName Field name
		 *   @param mixed $fieldValue Field value
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Additional parameters
		 *   @return bool
		 *   @throws \Exception
		 */

		public function isUnique( $fieldName, $fieldValue, FlaskPHP\DB\QueryBuilderInterface $param=null )
		{
			// Init query
			$query=oneof($param,Flask()->DB->getQueryBuilder());
			$query->setModel($this);
			$query->addWhere($fieldName.'='.$query::colValue($fieldValue));
			if ($this->_loaded) $query->addWhere($this->getParam('idfield').'='.$query::colValue($this->{$this->getParam('idfield')}));
			$query->addLimit(1);

			// Run query and check
			$dataset=Flask()->DB->querySelect($query);
			return (sizeof($dataset)?false:true);
		}


		/**
		 *   Add relation
		 *   @access public
		 *   @param string $id Relation ID
		 *   @param string $field Field/column name
		 *   @param string $remoteModel Remote model name
		 *   @param string $remoteField Remote field name (if empty, same as in this table)
		 *   @param string $relationName Relation name
		 *   @param bool $keyRelation Is a key relation
		 *   @return void
		 */

		public function addRelation( $id, $field, $remoteModel, $remoteField=null, $relationName=null, $keyRelation=false )
		{
			$this->_rel[$id]=new ModelRelation($id,$field,$remoteModel,$remoteField,$relationName,$keyRelation);
			$this->_rel[$id]->model=$this;
		}


		/**
		 *   Add a column/field
		 *   @access public
		 *   @param string $field Field ID/name
		 *   @param FlaskPHP\Field\FieldInterface $fieldObject Data field object
		 *   @return FlaskPHP\Field\FieldInterface field object instance
		 *   @throws \Exception
		 */

		public function addField( string $field, FlaskPHP\Field\FieldInterface $fieldObject )
		{
			// Check if the action already exists
			if (isset($this->_field[$field]))
			{
				throw new FlaskPHP\Exception\Exception('Field '.$field.' already exists.');
			}

			// Create field
			$this->_field=$fieldObject;

			// Set tag/column
			$this->_field[$field]->tag=$field;
			$this->_field[$field]->fieldColumn=$field;

			// Set back-reference
			$this->_field[$field]->modelObject=$this;

			// Return reference to action
			return $this->_field[$field];
		}


		/**
		 *   Load object by query
		 *   @access public
		 *   @static
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Additional parameters
		 *   @param bool $throwExceptionOnError Throw exception on error
		 *   @throws \Exception
		 *   @return ModelInterface
		 */

		public static function getObjectByQuery( FlaskPHP\DB\QueryBuilderInterface $param=null, $throwExceptionOnError=true )
		{
			// Check
			if ($param===null) throw new FlaskPHP\Exception\InvalidParameterException('The $param parameter cannot be empty.');

			// Do
			try
			{
				// Init class
				$modelClassName=get_called_class();
				$model=new $modelClassName();

				// Init query
				$query=$param;
				$query->setModel($model);

				// Run query
				$dataset=Flask()->DB->querySelect($query);
				if (!sizeof($dataset)) throw new FlaskPHP\Exception\Exception('No results found to query.');
				if (sizeof($dataset)>1) throw new FlaskPHP\Exception\Exception('Multiple results found to query.');

				// Return
				return static::getObject($dataset[0][$model->getParam('idfield')]);
			}
			catch (\Exception $e)
			{
				if ($throwExceptionOnError) throw $e;
				return null;
			}
		}


		/**
		 *   Get list as an array of objects
		 *   @access public
		 *   @static
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Additional parameters
		 *   @throws \Exception
		 *   @return array
		 */

		public static function getObjectList( FlaskPHP\DB\QueryBuilderInterface $param=null )
		{
			// Init class
			$modelClassName=get_called_class();
			$model=new $modelClassName();

			// Init query
			$query=oneof($param,Flask()->DB->getQueryBuilder());
			$query->addColumns($model->getParam('idfield'));
			$query->setModel($model);

			// Run query
			$dataset=Flask()->DB->querySelect($query);

			// Compose return array
			$retval=array();
			foreach ($dataset as $row)
			{
				$retval[$row[$model->getParam('idfield')]]=static::getObject($dataset[0][$model->getParam('idfield')]);
			}

			// Return
			return $retval;
		}


		/**
		 *   Get object list as an associative array
		 *   @access public
		 *   @static
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Additional parameters
		 *   @throws \Exception
		 *   @return array
		 */

		public static function getList( FlaskPHP\DB\QueryBuilderInterface $param=null )
		{
			// Init class
			$modelClassName=get_called_class();
			$model=new $modelClassName();

			// Init query
			$query=oneof($param,Flask()->DB->getQueryBuilder());
			$query->setModel($model);

			// Run query
			return Flask()->DB->querySelect($query);
		}


	}


?>
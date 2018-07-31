<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The MySQL database handler
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\DB;
	use Codelab\FlaskPHP as FlaskPHP;


	class MySQLDB extends DBInterface
	{


		/**
		 *   Database handle
		 *   @var \mysqli
		 *   @access public
		 */

		public $dbConnection = null;


		/**
		 *   Database connection properties
		 *   @var array
		 *   @access public
		 */

		public $dbConnectionParam = array();


		/**
		 *   Transaction level
		 *   @var int
		 *   @access public
		 */

		public $transactionLevel = 0;


		/**
		 *   Connect to the database
		 */

		public function connect( $connectionParam=null )
		{
			// Get connection parameters
			$this->dbConnectionParam['hostname']=oneof($connectionParam['hostname'],Flask()->Config->get('db.mysql.hostname'));
			$this->dbConnectionParam['database']=oneof($connectionParam['database'],Flask()->Config->get('db.mysql.database'));
			$this->dbConnectionParam['username']=oneof($connectionParam['username'],Flask()->Config->get('db.mysql.username'));
			$this->dbConnectionParam['password']=oneof($connectionParam['password'],Flask()->Config->get('db.mysql.password'));
			$this->dbConnectionParam['socket']=oneof($connectionParam['socket'],Flask()->Config->get('db.mysql.socket'),'/tmp/mysql.sock');
			$this->dbConnectionParam['port']=oneof($connectionParam['port'],Flask()->Config->get('db.mysql.port'),'3306');
		}


		/**
		 *   Connect to the datasource
		 *   @access private
		 *   @throws \Exception
		 *   @return void
		 */

		public function realConnect()
		{
			// Return if already connected
			if ($this->dbConnection) return;

			// Connect
			$this->dbConnection=new \mysqli($this->dbConnectionParam['hostname'],$this->dbConnectionParam['username'],$this->dbConnectionParam['password'],$this->dbConnectionParam['database'],$this->dbConnectionParam['port'],$this->dbConnectionParam['socket']);
			if (!$this->dbConnection)
			{
				throw new FlaskPHP\Exception\DbException('Could not connect to database: '.oneof(mysqli_error(),'unknown error'));
			}

			// Select DB
			if (!$this->dbConnection->select_db($this->dbConnectionParam['database']))
			{
				throw new FlaskPHP\Exception\DbException('Could not connect to database: '.oneof($this->dbConnection->error,'unknown error'));
			}

			// Set connection charset/collation (or default to UTF8)
			$this->dbConnection->query("SET NAMES ".oneof(Flask()->Config->get('db.mysql.charset'),'utf8')." COLLATE ".oneof(Flask()->Config->get('db.mysql.collation'),'utf8_estonian_ci'));
		}


		/**
		 *   Disconnect from the database
		 */

		public function disconnect()
		{
			if ($this->dbConnection)
			{
				$this->dbConnection->close();
			}
		}


		/**
		 *   Get query builder interface
		 */

		public function getQueryBuilder( string $queryType=null )
		{
			return new FlaskPHP\DB\MySQLQueryBuilder($queryType);
		}


		/**
		 *   Select database
		 */

		public function selectDatabase( $database )
		{
			// Connect if not yet
			$this->realConnect();

			if ($this->dbConnection->select_db($database))
			{
				throw new FlaskPHP\Exception\DbException('Could not connect to database '.$database.': '.$this->dbError);
			}
		}


		/**
		 *   Returns last error message
		 */

		public function getError()
		{
			return $this->dbConnection->error;
		}


		/**
		 *  Add profiler info
		 */

		public function addProfilerInfo( $sql, $queryTime, $rows=0, $explain=false )
		{
			// These we'll add in any case
			Flask()->Debug->debugProfilerData['totalQueryCount']++;
			if (preg_match("/^\s*SELECT/i",$sql) || preg_match("/^\s*EXPLAIN/i",$sql))
			{
				Flask()->Debug->debugProfilerData['totalQueryCountSelect']++;
			}
			else
			{
				Flask()->Debug->debugProfilerData['totalQueryCountUpdate']++;
			}
			Flask()->Debug->debugProfilerData['totalQueryTime']+=$queryTime;

			// Query logging?
			if (Flask()->Debug->debugLogQueries)
			{
				$logEntry="Query #".Flask()->Debug->debugProfilerData['totalQueryCount'].":\n\n";
				$logEntry.=$sql."\n\n";
				$logEntry.="Time: ".$queryTime." seconds.\n\n --- 8< --- \n\n";
				file_put_contents('/tmp/querylog-'.Flask()->requestID.'.txt',$logEntry,FILE_APPEND);
			}

			// Only debug info from here on
			if (!Flask()->Debug->debugOn && !Flask()->Debug->profilerOn<2) return;

			// Query profiler
			$queryInfo=array(
				'sql'     => $sql,
				'time'    => $queryTime,
				'rows'    => $rows,
				'explain' => array()
			);
			if ($explain)
			{
				list($explainres,$fieldMetaData)=$this->query("EXPLAIN ".$sql,false);
				while($row=$this->fetchRow($explainres,$fieldMetaData))
				{
					$queryInfo['explain'][]=$row;
				}
				$this->endquery($explainres);
			}
			Flask()->Debug->debugProfilerData['queryInfo'][]=$queryInfo;
		}


		/**
		 *   Execute a query
		 */

		public function query( string $sql, bool $returnFieldTypeMetaData=true )
		{
			// Connect if not yet
			$this->realConnect();

			// Run query
			$query=$this->dbConnection->query($sql);
			if (!$query)
			{
				if (Flask()->Debug->debugOn)
				{
					Flask()->Debug->addMessage('MySQL query error',$this->dbConnection->error.'<br/>The query:<br/>'.htmlspecialchars($sql),2);
				}
				throw new FlaskPHP\Exception\DbQueryException('MySQL query error: '.$this->dbConnection->error.(Flask()->Debug->debugOn?' / Query: '.$sql:''));
			}

			// Get metadata
			if ($returnFieldTypeMetaData)
			{
				$fieldTypeMetaData=$query->fetch_fields();
			}
			else
			{
				$fieldTypeMetaData=null;
			}

			// Return
			return array($query,$fieldTypeMetaData);
		}


		/**
		 *  End query
		 */

		public function endQuery( $query )
		{
			$query->free();
		}


		/**
		 *  Fetch next row from the query handle
		 */

		public function fetchRow( $query, array $fieldMetaData=null )
		{
			// Fetch row
			$row=$query->fetch_assoc();

			// Convert field types
			if (is_array($row) && is_array($fieldMetaData))
			{
				$f=0;
				foreach ($row as $k => $v)
				{
					// Skip null values
					if ($v===null)
					{
						$f++;
						continue;
					}

					// Find metadata
					$metaData=null;
					foreach ($fieldMetaData as $fmd)
					{
						if ($fmd->name==$k)
						{
							$metaData=$fmd;
							break;
						}
					}
					if ($metaData===null) continue;

					// Integer types
					if (in_array($metaData->type,array(0,1,2,3,8,9,13,16)))
					{
						$row[$k]=intval($v);
					}

					// Decimal types
					elseif (in_array($metaData->type,array(246)))
					{
						$row[$k]=round(floatval($v),$metaData->decimals);
					}

					// Float types
					elseif (in_array($metaData->type,array(4,5)))
					{
						$row[$k]=floatval($v);
					}
					$f++;
				}
			}

			return $row;
		}


		/**
		 *   Lock tables
		 */

		public function lockTables( $tableList, $lockType='WRITE' )
		{
			// Connect if not yet
			$this->realConnect();

			$lockList=array();
			foreach (str_array($tableList) as $table) $lockList[]=$table.' '.$lockType;
			$this->query("LOCK TABLES ".join(', ',$lockList),false);
		}


		/**
		 *   Unlock tables
		 */

		public function unlockTables()
		{
			// Connect if not yet
			$this->realConnect();

			$this->query('UNLOCK TABLES',false);
		}


		/**
		 *  Start transaction
		 */

		public function startTransaction()
		{
			// Connect if not yet
			$this->realConnect();

			// Already in a transaction: create a savepoint
			if ($this->transactionLevel>0)
			{
				$this->transactionLevel++;
				$sql='SAVEPOINT trans'.intval($this->transactionLevel);
				$this->query($sql,false);
				$this->addProfilerInfo($sql,0,0);
			}

			// Start transaction
			else
			{
				$sql='BEGIN';
				$this->query($sql,false);
				$this->addProfilerInfo($sql,0,0);
				$this->transactionLevel++;
			}
		}


		/**
		 *  Commit
		 */

		public function doCommit()
		{
			// Connect if not yet
			$this->realConnect();

			// In a nested transaction: release savepoint
			if ($this->transactionLevel>1)
			{
				$sql='RELEASE SAVEPOINT trans'.intval($this->transactionLevel);
				$this->query($sql,false);
				$this->addProfilerInfo($sql,0,0);
				$this->transactionLevel--;
			}

			// Commit
			else
			{
				$sql='COMMIT';
				$this->query($sql,false);
				$this->addProfilerInfo($sql,0,0);
				$this->transactionLevel--;
			}
		}


		/**
		 *  Rollback
		 */

		public function doRollback()
		{
			// Connect if not yet
			$this->realConnect();

			// Nested transaction: rollback to savepoint
			if ($this->transactionLevel>1)
			{
				$sql='ROLLBACK TO trans'.intval($this->transactionLevel);
				$this->query($sql,false);
				$this->addProfilerInfo($sql,0,0);
				$this->transactionLevel--;
			}

			// Otherwise: full rollback
			else
			{
				$sql='ROLLBACK';
				$this->query($sql,false);
				$this->addProfilerInfo($sql,0,0);
				$this->transactionLevel--;
			}
		}


		/**
		 *  Perform a query and return results as an array
		 */

		public function querySelect( QueryBuilderInterface $query, string $key=null, bool $convertFieldTypes=true )
		{
			// Build query
			$sql=$query->getSQL('SELECT');

			// Run query
			return $this->querySelectSQL($sql,$key,$convertFieldTypes);
		}


		/**
		 *   Execute a SELECT SQL query and return results as an array
		 */

		public function querySelectSQL( string $sql, string $key=null, bool $convertFieldTypes=true )
		{
			// Connect if not yet
			$this->realConnect();

			// Reset dataset
			$dataset=array();

			// Execute query and get results
			$timeStart=microtime_float();
			list($res,$fieldMetaData)=$this->query($sql,$convertFieldTypes);
			$timeEnd=microtime_float();
			$queryTime=$timeEnd-$timeStart;
			if ($res===false || $res->num_rows==0) return array();
			while ($row=$this->fetchRow($res,$fieldMetaData))
			{
				if ($key)
				{
					$dataset[$row[$key]]=$row;
				}
				else
				{
					array_push($dataset,$row);
				}
			}
			$this->endQuery($res);

			// Found rows
			if (mb_stripos($sql,'SQL_CALC_FOUND_ROWS')!==false)
			{
				list($res,$fieldMetaData)=$this->query("SELECT found_rows() as found_rows",false);
				if ($res!==false) while($row=$this->fetchrow($res,$fieldMetaData)) $this->foundRows=intval($row['found_rows']);
				$this->endquery($res);
			}

			// Profiler info
			$explain=(!strncasecmp(trim($sql),"SELECT",6))?true:false;
			$this->addProfilerInfo($sql,$queryTime,sizeof($dataset),$explain);

			// Return dataset
			return $dataset;
		}


		/**
		 *   Perform a SELECT query and return one row
		 */

		public function selectOne( QueryBuilderInterface $query, bool $returnFieldTypeMetaData=true )
		{
			// Get dataset
			$query->addLimit(1,null);
			$dataset=$this->querySelect($query);

			// Return
			if (!sizeof($dataset)) return null;
			return $dataset[0];
		}


		/**
		 *   Execute a raw SQL query and return one row
		 */

		public function selectOneSQL( string $sql, bool $returnFieldTypeMetaData=true )
		{
			// Get dataset
			if (mb_stripos($sql,"limit")===false) $sql.=" LIMIT 1";
			$dataset=$this->querySelectSQL($sql);

			// Return
			if (!sizeof($dataset)) return null;
			return $dataset[0];
		}


		/**
		 *   Perform a select query and return one field
		 */

		public function selectField( string $field, QueryBuilderInterface $query, bool $returnFieldTypeMetaData=true )
		{
			// Get dataset
			$query->addLimit(1,null);
			$dataset=$this->querySelect($query);

			// Return
			if (!sizeof($dataset)) return null;
			return $dataset[0][$field];
		}


		/**
		 *   Execute a SQL select query and return one field
		 */

		public function selectFieldSQL( string $field, string $sql, bool $returnFieldTypeMetaData=true )
		{
			// Get dataset
			if (mb_stripos($sql,"limit")===false) $sql.=" LIMIT 1";
			$dataset=$this->querySelectSQL($sql);

			// Return
			if (!sizeof($dataset)) return null;
			return $dataset[0][$field];
		}


		/**
		 *   Insert a row into the table
		 */

		public function queryInsert( string $table, array $columns )
		{
			// Connect if not yet
			$this->realConnect();

			// Build query
			$sql="INSERT INTO ".addslashes($table)." SET ";
			$fcnt=0;
			foreach ($columns as $col => $value)
			{
				$sql.=$fcnt?',':'';
				$sql.=addslashes($col)."=".MySQLQueryBuilder::colValue($value);
				$fcnt++;
			}

			// Execute query
			$timeStart=microtime_float();
			list($res,$fieldMetaData)=$this->query($sql,false);
			$timeEnd=microtime_float();
			$queryTime=$timeEnd-$timeStart;

			// Return on error
			if ($res===false) return null;

			// Profiler info
			$this->addProfilerInfo($sql,$queryTime);

			// Return insert ID
			$id=$this->dbConnection->insert_id;
			return $id;
		}


		/**
		 *   Insert a row into the table by SQL
		 */

		public function queryInsertSQL( string $sql )
		{
			// Connect if not yet
			$this->realConnect();

			// Execute query
			$timeStart=microtime_float();
			list($res,$fieldMetaData)=$this->query($sql,false);
			$timeEnd=microtime_float();
			$queryTime=$timeEnd-$timeStart;

			// Return on error
			if ($res===false) return null;

			// Profiler info
			$this->addProfilerInfo($sql,$queryTime);

			// Return ID
			$id=$this->dbConnection->insert_id;
			return $id;
		}


		/**
		 *   Perform a REPLACE query
		 *   @access public
		 *   @param string $table Table name
		 *   @param array $columns columns
		 *   @return int Generated auto_increment value
		 */

		public function queryReplace( string $table, array $columns )
		{
			// Connect if not yet
			$this->realConnect();

			// Build query
			if (is_array($columns[0]))
			{
				$sql="REPLACE INTO ".addslashes($table)." (";
				$fcnt=0;
				foreach ($columns[0] as $col => $value)
				{
					$sql.=($fcnt?',':'').$col;
					$fcnt++;
				}
				$sql.=") VALUES";
				$rcnt=0;
				foreach ($columns as $c)
				{
					$sql.=($rcnt?',':'').'(';
					$fcnt=0;
					foreach ($c as $col => $value)
					{
						$sql.=($fcnt?',':'').MySQLQueryBuilder::colValue($value);
						$fcnt++;
					}
					$sql.=')';
					$rcnt++;
				}
			}
			else
			{
				$sql="REPLACE INTO ".addslashes($table)." SET ";
				$fcnt=0;
				foreach ($columns as $col => $value)
				{
					$sql.=$fcnt?',':'';
					$sql.=addslashes($col)."=".MySQLQueryBuilder::colValue($value);
					$fcnt++;
				}
			}

			// Execute query
			$timeStart=microtime_float();
			list($res,$fieldMetaData)=$this->query($sql,false);
			$timeEnd=microtime_float();
			$queryTime=$timeEnd-$timeStart;

			// Return on error
			if ($res===false) return null;

			// Debug info
			$this->addProfilerInfo($sql,$queryTime);

			// Return ID
			$id=$this->dbConnection->insert_id;
			return $id;
		}


		/**
		 *   Perform an UPDATE query
		 */

		public function queryUpdate( string $table, array $columns, $where=null )
		{
			// Sanity check
			if ($where===null) throw new FlaskPHP\Exception\DbQueryException('Refusing to run queryUpdate() with no WHERE clause.');

			// Connect if not yet
			$this->realConnect();

			// Build query
			$sql="UPDATE ".addslashes($table)." SET ";
			$fcnt=0;
			foreach ($columns as $col => $value)
			{
				$sql.=$fcnt?',':'';
				$sql.=addslashes($col)."=".MySQLQueryBuilder::colValue($value);
				$fcnt++;
			}
			$sql.=" WHERE (".(is_array($where)?join(') and (',$where):$where).")";

			// Execute query
			$timeStart=microtime_float();
			list($res,$fieldMetaData)=$this->query($sql,false);
			$timeEnd=microtime_float();
			$queryTime=$timeEnd-$timeStart;
			$affectedRows=intval($this->dbConnection->affected_rows);

			// Return on error
			if ($res===false) return false;

			// Debug info
			$this->addProfilerInfo($sql,$queryTime,$affectedRows);

			// Return affected rows
			return $affectedRows;
		}


		/**
		 *   Delete rows from the table
		 */

		public function queryDelete( QueryBuilderInterface $query )
		{
			// Connect if not yet
			$this->realConnect();

			// Build query
			$sql=$query->getSQL('DELETE');

			// Execute query
			$timeStart=microtime_float();
			list($res,$fieldMetaData)=$this->query($sql,false);
			$timeEnd=microtime_float();
			$queryTime=$timeEnd-$timeStart;
			$affectedRows=intval($this->dbConnection->affected_rows);

			// Debug info
			$this->addProfilerInfo($sql,$queryTime,$affectedRows);

			// Return affected rows
			return $affectedRows;
		}


		/**
		 *   Perform a raw UPDATE query
		 */

		public function queryUpdateSQL( string $sql )
		{
			// Connect if not yet
			$this->realConnect();

			// Execute query
			$timeStart=microtime_float();
			list($res,$fieldMetaData)=$this->query($sql,false);
			$timeEnd=microtime_float();
			$queryTime=$timeEnd-$timeStart;
			$affectedRows=intval($this->dbConnection->affected_rows);

			// Return on error
			if ($res===false) return null;

			// Debug info
			$this->addProfilerInfo($sql,$queryTime,$affectedRows);

			// Return affected rows
			return $affectedRows;
		}


	}


?>
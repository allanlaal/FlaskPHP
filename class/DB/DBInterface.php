<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The base database interface
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\DB;
	use Codelab\FlaskPHP as FlaskPHP;


	class DBInterface
	{


		/**
		 *   Rows found during last query
		 *   @var int
		 *   @access public
		 */

		public $foundRows = null;


		/**
		 *   Connect to database
		 *   @access public
		 *   @param array $connectionParam Connection parameters
		 *   @throws \Exception
		 *   @return void
		 */

		public function connect( $connectionParam=null )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function connect() not implemented in the DB interface.');
		}


		/**
		 *   Disconnect from database
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function disconnect()
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function disconnect() not implemented in the DB interface.');
		}


		/**
		 *   Get query builder interface
		 *   @access public
		 *   @param $queryType $queryType Query type
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\DB\QueryBuilderInterface
		 */

		public function getQueryBuilder( string $queryType=null )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function getQueryBuilder() not implemented in the DB interface.');
		}


		/**
		 *   Select database
		 *   @access public
		 *   @param string $database database
		 *   @throws \Exception
		 *   @return void
		 */

		public function selectDatabase( $database )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function selectDatabase() not implemented in the DB interface.');
		}


		/**
		 *   Returns last error message
		 *   @access public
		 *   @return string error message
		 *   @throws \Exception
		 */

		public function getError()
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function getError() not implemented in the DB interface.');
		}


		/**
		 *   Add profiler info
		 *   @access public
		 *   @param string $sql SQL
		 *   @param int $queryTime Query time
		 *   @param int $rows Rows affected
		 *   @param boolean $explain Run EXPLAIN?
		 *   @return void
		 *   @throws \Exception
		 */

		public function addProfilerInfo( $sql, $queryTime, $rows=0, $explain=false )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function addProfilerInfo() not implemented in the DB interface.');
		}


		/**
		 *   Returns number of found rows in last query
		 *   @access public
		 *   @return int Number of found rows
		 */

		public function foundRows()
		{
			return intval($this->foundRows);
		}


		/**
		 *   Execute a query
		 *   @access public
		 *   @param string $sql SQL statement
		 *   @param bool $returnFieldTypeMetaData Return field type metadata
		 *   @throws \Exception
		 *   @return resource Query handle
		 */

		public function query( string $sql, bool $returnFieldTypeMetaData=true )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function query() not implemented in the DB interface.');
		}


		/**
		 *  End query
		 *  @access public
		 *  @param resource $query query handle
		 *  @return void
		 */

		public function endQuery( $query )
		{
			// This can be implemented in the subclass if necessary.
		}


		/**
		 *   Fetch next row from the query handle
		 *   @access public
		 *   @param resource $query query handle
		 *   @param array $fieldMetaData Field metadata
		 *   @return array|boolean row or FALSE on error
		 *   @throws \Exception
		 */

		public function fetchRow( $query, array $fieldMetaData=null )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function fetchRow() not implemented in the DB interface.');
		}


		/**
		 *   Lock tables
		 *   @access public
		 *   @param string|array $tableList List of tables
		 *   @param string $lockType lock type
		 *   @return void
		 *   @throws \Exception
		 */

		public function lockTables( $tableList, $lockType='WRITE' )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function lockTables() not implemented in the DB interface.');
		}


		/**
		 *   Unlock tables
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 */

		public function unlockTables()
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function unlockTables() not implemented in the DB interface.');
		}


		/**
		 *   Start transaction
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 */

		public function startTransaction()
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function startTransaction() not implemented in the DB interface.');
		}


		/**
		 *   Commit
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 */

		public function doCommit()
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function doCommit() not implemented in the DB interface.');
		}


		/**
		 *   Rollback
		 *   @access public
		 *   @return void
		 *   @throws \Exception
		 */

		public function doRollback()
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function doRollback() not implemented in the DB interface.');
		}


		/**
		 *   Perform a query and return results as an array
		 *   @access public
		 *   @param QueryBuilderInterface $query Query object
		 *   @param string $key Field to use as the array key
		 *   @param bool $convertFieldTypes Convert result to correct field types
		 *   @return array resultset
		 *   @throws \Exception
		 */

		public function querySelect( QueryBuilderInterface $query, string $key=null, bool $convertFieldTypes=true )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function querySelect() not implemented in the DB interface.');
		}


		/**
		 *   Execute a raw SQL query and return results as an array
		 *   @access public
		 *   @param string $sql SQL statement
		 *   @param string $key field to use as the array key
		 *   @param bool $convertFieldTypes Convert result to correct field types
		 *   @return array resultset
		 *   @throws \Exception
		 */

		public function querySelectSQL( string $sql, string $key=null, bool $convertFieldTypes=true )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function querySelectSQL() not implemented in the DB interface.');
		}


		/**
		 *   Perform a SELECT query and return one row
		 *   @access public
		 *   @param QueryBuilderInterface $query Query object
		 *   @param bool $convertFieldTypes Convert result to correct field types
		 *   @return array resultset
		 *   @throws \Exception
		 */

		public function selectOne( QueryBuilderInterface $query, bool $convertFieldTypes=true )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function selectOne() not implemented in the DB interface.');
		}


		/**
		 *   Execute a raw SQL query and return one row
		 *   @access public
		 *   @param string $sql SQL
		 *   @param bool $convertFieldTypes Convert result to correct field types
		 *   @return array resultset
		 *   @throws \Exception
		 */

		public function selectOneSQL( string $sql, bool $convertFieldTypes=true )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function selectOneSQL() not implemented in the DB interface.');
		}


		/**
		 *   Perform a select query and return one field
		 *   @access public
		 *   @param string $field Field/column name
		 *   @param QueryBuilderInterface $query Query object
		 *   @param bool $convertFieldTypes Convert result to correct field types
		 *   @return mixed value
		 *   @throws \Exception
		 */

		public function selectField( string $field, QueryBuilderInterface $query, bool $convertFieldTypes=true )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function selectField() not implemented in the DB interface.');
		}


		/**
		 *   Execute a SQL select query and return one field
		 *   @access public
		 *   @param string $field Field/column name
		 *   @param string $sql SQL
		 *   @param bool $convertFieldTypes Convert result to correct field types
		 *   @return mixed value
		 *   @throws \Exception
		 */

		public function selectFieldSQL( string $field, string $sql, bool $convertFieldTypes=true )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function selectFieldSQL() not implemented in the DB interface.');
		}


		/**
		 *   Insert a row into the table
		 *   @access public
		 *   @param string $table Table name
		 *   @param array $columns Columns as key/value array
		 *   @return int Generated auto_increment value
		 *   @throws \Exception
		 */

		public function queryInsert( string $table, array $columns )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function queryInsert() not implemented in the DB interface.');
		}


		/**
		 *   Insert a row into the table by SQL
		 *   @access public
		 *   @param string $sql SQL statement
		 *   @return int Generated auto_increment value
		 *   @throws \Exception
		 */

		public function queryInsertSQL( string $sql )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function queryInsertSQL() not implemented in the DB interface.');
		}


		/**
		 *   Perform a REPLACE query
		 *   @access public
		 *   @param string $table Table name
		 *   @param array $columns columns
		 *   @return int Generated auto_increment value
		 *   @throws \Exception
		 */

		public function queryReplace( string $table, array $columns )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function queryReplace() not implemented in the DB interface.');
		}


		/**
		 *   Perform an UPDATE query
		 *   @access public
		 *   @param string $table Table name
		 *   @param array $columns columns
		 *   @param string|array $where WHERE clause
		 *   @return int Number of affected rows
		 *   @throws \Exception
		 */

		public function queryUpdate( string $table, array $columns, $where=null )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function queryUpdate() not implemented in the DB interface.');
		}


		/**
		 *   Delete rows from the table
		 *   @access public
		 *   @param QueryBuilderInterface $query Query object
		 *   @return int Number of affected rows
		 *   @throws \Exception
		 */

		public function queryDelete( QueryBuilderInterface $query )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function queryDelete() not implemented in the DB interface.');
		}


		/**
		 *   Perform a raw date update query
		 *   @access public
		 *   @param string $sql SQL statement
		 *   @return int Number of affected rows
		 *   @throws \Exception
		 */

		public function queryUpdateSQL( string $sql )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function queryUpdateSQL() not implemented in the DB interface.');
		}


	}


?>
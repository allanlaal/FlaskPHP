<?php


	/**
	 *
	 *   FlaskPHP
	 *   The base Query Builder interface
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\DB;
	use Codelab\FlaskPHP;


	class QueryBuilderInterface
	{


		/**
		 *   Model
		 *   @var FlaskPHP\Model\ModelInterface
		 *   @access public
		 */

		public $model = null;


		/**
		 *   Query type
		 *   @var string
		 *   @access public
		 */

		public $queryType = null;


		/**
		 *   Table(s)
		 *   @var array
		 *   @access public
		 */

		public $queryTable = null;


		/**
		 *   Columns
		 *   @var array
		 *   @access public
		 */

		public $queryField = null;


		/**
		 *   WHERE clauses
		 *   @var array
		 *   @access public
		 */

		public $queryWhere = null;


		/**
		 *   WHERE type
		 *   @var array
		 *   @access public
		 */

		public $queryWhereType = 'and';


		/**
		 *   GROUP BY clauses
		 *   @var array
		 *   @access public
		 */

		public $queryGroupBy = null;


		/**
		 *   HAVING clauses
		 *   @var array
		 *   @access public
		 */

		public $queryHaving = null;


		/**
		 *   ORDER BY
		 *   @var array
		 *   @access public
		 */

		public $queryOrderBy = null;


		/**
		 *   LIMIT
		 *   @var int
		 *   @access public
		 */

		public $queryLimit = null;


		/**
		 *   LIMIT offset
		 *   @var int
		 *   @access public
		 */

		public $queryLimitOffset = null;


		/**
		 *   Calculate found rows
		 *   @var boolean
		 *   @access public
		 */

		public $calcFoundRows = false;


		/**
		 *   Constructor
		 *   @param string $queryType Query type
		 *   @return QueryBuilderInterface
		 */

		public function __construct( string $queryType=null )
		{
			$this->queryType=$queryType;
		}


		/**
		 *   Set model
		 *   @param FlaskPHP\Model\ModelInterface $model Model
		 */

		public function setModel( FlaskPHP\Model\ModelInterface $model )
		{
			$this->model=$model;
		}


		/**
		 *   Add table(s)
		 *   @access public
		 *   @param string $table Table
		 *   @param string|array $joinCondition JOIN condition
		 *   @param string $joinType JOIN type
		 *   @return void
		 *   @throws \Exception
		 */

		public function addTable( string $table, $joinCondition=null, $joinType='LEFT JOIN' )
		{
			if (!is_array($this->queryTable)) $this->queryTable=array();
			if (sizeof($this->queryTable))
			{
				if (empty($joinCondition))
				{
					throw new FlaskPHP\Exception\DbQueryException('Empty join condition<hr/>'.var_dump_str($this).'<hr>'.$table.'<hr>'.$joinCondition);
				}
				$joinCondition=str_array($joinCondition);
				$this->queryTable[$table]=$joinType.' '.$table.' ON ('.join(' and ',$joinCondition).')';
			}
			else
			{
				$this->queryTable[$table]=$table;
			}
		}


		/**
		 *   Add fields
		 *   @param string|array $field Field(s)
		 *   @return void
		 */

		public function addField( $field )
		{
			if (!is_array($this->queryField)) $this->queryColumns=array();
			$field=str_array($field);
			foreach ($field as $col)
			{
				$this->queryField[$col]=$col;
			}
		}


		/**
		 *   Add WHERE clause
		 *   @param string $where Where claus
		 *   @return void
		 */

		public function addWhere( string $where )
		{
			if (!is_array($this->queryWhere)) $this->queryWhere=array();
			$this->queryWhere[$where]=$where;
		}


		/**
		 *   Set WHERE type
		 *   @access public
		 *   @param string $whereType WHERE type (and|or)
		 *   @return void
		 *   @throws \Exception
		 */

		public function setWhereType( string $whereType )
		{
			$whereType=mb_strtolower($whereType);
			switch($whereType)
			{
				case 'and':
				case 'or':
					$this->queryWhereType=$whereType;
					return;
				default:
					throw new FlaskPHP\Exception\DbQueryException('Invalid WHERE type.');
			}
		}


		/**
		 *   Add GROUP BY clause(s)
		 *   @access public
		 *   @param string|array $groupBy GROUP BY clause(s)
		 *   @return void
		 */

		public function addGroupBy( $groupBy )
		{
			if (!is_array($this->queryGroupBy)) $this->queryGroupBy=array();
			$groupBy=str_array($groupBy);
			foreach ($groupBy as $gb)
			{
				$this->queryGroupBy[$gb]=$gb;
			}
		}


		/**
		 *   Add HAVING clause(s)
		 *   @access public
		 *   @param string|array $having HAVING clause(s)
		 *   @return void
		 */

		public function addHaving( $having )
		{
			if (!is_array($this->queryHaving)) $this->queryHaving=array();
			$having=str_array($having);
			foreach ($having as $h)
			{
				$this->queryHaving[$h]=$h;
			}
		}


		/**
		 *   Add ORDER BY clause(s)
		 *   @access public
		 *   @param string|array $orderBy ORDER BY clause(s)
		 *   @return void
		 */

		public function addOrderBy( $orderBy )
		{
			if (!is_array($this->queryOrderBy)) $this->queryOrderBy=array();
			$orderBy=str_array($orderBy);
			foreach ($orderBy as $ob)
			{
				$this->queryOrderBy[$ob]=$ob;
			}
		}


		/**
		 *   Add LIMIT
		 *   @access public
		 *   @param int $limit Limit
		 *   @param int $offset Offset
		 *   @return void
		 */

		public function addLimit ( $limit, $offset=null )
		{
			$this->queryLimit=$limit;
			$this->queryLimitOffset=$offset;
		}


		/**
		 *   Get column value
		 *   @static
		 *   @access public
		 *   @param mixed $value Value
		 *   @param string $cast Cast value as
		 *   @return string
		 *   @throws \Exception
		 */

		public static function colValue( $value, $cast=null )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function colValue() not implemented in the QueryBuilder interface.');
		}


		/**
		 *   Create IN() statement from array
		 *   @static
		 *   @access public
		 *   @param array $options options
		 *   @return string
		 *   @throws \Exception
		 */

		public static function inValues( $options )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function inValues() not implemented in the QueryBuilder interface.');
		}


		/**
		 *   Build and return SQL
		 *   @param string $queryType Query type
		 *   @return string
		 *   @throws \Exception
		 */

		public function getSQL( string $queryType=null )
		{
			throw new FlaskPHP\Exception\NotImplementedException('Function getSQL() not implemented in the QueryBuilder interface.');
		}


	}


?>
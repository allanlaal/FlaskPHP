<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   MySQL Query Builder
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\DB;
	use Codelab\FlaskPHP as FlaskPHP;


	class MySQLQueryBuilder extends QueryBuilderInterface
	{


		/**
		 *   Get column value
		 */

		public static function colValue( $value, $cast=null )
		{
			// Value
			if (is_integer($value))
			{
				$retval=strval(intval($value));
			}
			elseif (is_float($value))
			{
				$retval=strval(floatval($value));
			}
			elseif (is_bool($value))
			{
				$retval=($value?'1':'0');
			}
			elseif (is_null($value))
			{
				$retval='NULL';
			}
			elseif (is_string($value))
			{
				if (preg_match("/^([A-Za-z]+)\(.*?\)$/",trim($value)))
				{
					$retval=trim($value);
				}
				else
				{
					$retval="'".addslashes($value)."'";
				}
			}
			else
			{
				throw new FlaskPHP\Exception\DbQueryException('Unsupported variable type: '.gettype($value));
			}

			// Cast
			if (!empty($cast))
			{
				$retval='CAST('.$value.' AS '.$cast.')';
			}

			// Return
			return $retval;
		}


		/**
		 *   Create IN() statement from array
		 */

		public static function inValues( $options )
		{
			$optionSet=array();
			foreach ($options as $o)
			{
				$optionSet=static::colValue($o);
			}
			return join(',',$optionSet);
		}


		/**
		 *   Build and return SQL
		 */

		public function getSQL( string $queryType=null )
		{
			// Set query type
			if ($queryType!==null) $this->queryType=$queryType;

			// Sanity checks
			if ($this->queryType===null) throw new FlaskPHP\Exception\DbQueryException('Query type not specified.');
			if (($this->queryTable===null || !is_array($this->queryTable) || !sizeof($this->queryTable)) && $this->model===null) throw new FlaskPHP\Exception\DbQueryException('No table or model specified.');

			// Start query
			$sql=$this->queryType." ";

			// Found rows
			if ($this->calcFoundRows)
			{
				$sql.="SQL_CALC_FOUND_ROWS ";
			}

			// Insert model table to the beginning
			if ($this->model!==null && !array_key_exists($this->model->getParam('table'),$this->queryTable))
			{
				$this->queryTable=array_merge(
					array($this->model->getParam('table') => $this->model->getParam('table')),
					(is_array($this->queryTable)?$this->queryTable:array())
				);
			}

			// Columns
			if ($this->queryField!==null)
			{
				$queryFieldList=array();
				foreach ($this->queryField as $field)
				{
					if (mb_strpos($field,'->')!==false)
					{
						if ($this->model===null) throw new FlaskPHP\Exception\DbQueryException('Relation used in query, but model not set.');
						$relationList=preg_split('/\-\>/',$field);
						$effectiveModel=$this->model;
						for ($r=0;$r<sizeof($relationList);++$r)
						{
							if ($r==(sizeof($relationList)-1))
							{
								$queryFieldList[]=$this->parseField($relationList[$r],$effectiveModel,$relationList[$r-1].'_');
							}
							else
							{
								$effectiveModel=$this->parseRelation($relationList[$r],$effectiveModel);
							}
						}
					}
					else
					{
						$queryFieldList[]=$this->parseField($field,$this->model);
					}
				}
				$sql.=join(', ',$queryFieldList);
			}
			else
			{
				if (!in_array($this->queryType,['DELETE']))
				{
					$sql.='*';
				}
			}

			// Tables
			if ($this->queryTable===null || !is_array($this->queryTable) || !sizeof($this->queryTable)) throw new FlaskPHP\Exception\DbQueryException('No tables in the query.');
			$sql.=' FROM '.join(' ',$this->queryTable);

			// WHERE
			if (!empty($this->queryWhere))
			{
				$sql.= " WHERE ".join(' '.$this->queryWhereType.' ',$this->queryWhere);
			}

			// GROUP BY
			if (!empty($this->queryGroupBy))
			{
				$sql.= " GROUP BY ".join(', ',$this->queryGroupBy);
			}

			// HAVING
			if (!empty($this->queryHaving))
			{
				$sql.= " HAVING ".join(' '.$this->queryWhereType.' ',$this->queryHaving);
			}

			// ORDER BY
			if (!empty($this->queryOrderBy))
			{
				$sql.= " ORDER BY ".join(', ',$this->queryOrderBy);
			}

			// LIMIT
			if (!empty($this->queryLimit))
			{
				$sql.= " LIMIT ";
				if ($this->queryLimitOffset!=NULL) $sql.=$this->queryLimitOffset.',';
				$sql.=$this->queryLimit;
			}

			// Return
			return $sql;
		}


		/**
		 *   Parse relation and add necessary tables
		 *   @access private
		 *   @param string $relation Relation definition
		 *   @param FlaskPHP\Model\ModelInterface $model Model
		 *   @return string
		 *   @throws \Exception
		 */

		private function parseRelation( string $relation, FlaskPHP\Model\ModelInterface $model )
		{
			// Sanity check
			if ($model===null) throw new FlaskPHP\Exception\Exception('Error in parseRelation(): model not passed.');
			if (!is_array($model->_rel[$relation])) throw new FlaskPHP\Exception\Exception('Error in parseRelation(): relation '.$relation.' does not exist in '.get_class($model));

			// Create relation model instance
			$relationModelClass=$model->_rel[$relation]['remotemodel'];
			$relationModel=new $relationModelClass();

			// Add table if it does not exist
			if (!array_key_exists($relationModel->getParam('table'),$this->queryTable))
			{
				$this->addTable(
					$relationModel->getParam('table'),
					$relationModel->getParam('table').'.'.oneof($model->_rel[$relation]['remotefield'],$relationModel->getParam('idfield')).'='.$model->getParam('table').'.'.$model->_rel[$relation]['field'],
					'LEFT JOIN'
				);
			}

			// Return
			return $relationModel;
		}


		/**
		 *   Parse field
		 *   @access private
		 *   @param string $field Field definition
		 *   @param FlaskPHP\Model\ModelInterface $model Model
		 *   @param string $fieldPrefix Field prefix
		 *   @return string
		 *   @throws \Exception
		 */

		private function parseField( string $field, FlaskPHP\Model\ModelInterface $model=null, string $fieldPrefix=null )
		{
			// Check for alias
			if (mb_stripos($field,' as ')!==false)
			{
				list($field,$fieldAlias)=preg_split('/ as /',$field,2);
			}
			elseif (mb_stripos($field,' ')!==false)
			{
				list($field,$fieldAlias)=preg_split('/\s+/',$field,2);
			}
			else
			{
				$fieldAlias='';
			}

			// Prop field?
			if (!strncasecmp($field,'prop_',5) && $model!==null && $model->getParam('prop'))
			{
				// Add table
				$this->addTable(
					$model->getParam('prop_table').' as '.$fieldPrefix.$field,
					$model->getParam('table').".".$model->getParam('idfield')."=".$fieldPrefix.$field.".".$model->getParam('prop_referencefield')." and ".$fieldPrefix.$field.".".$model->getParam('prop_namefield')."='".addslashes(substr($field,5))."'"
				);

				// Set column
				$field=$fieldPrefix.$field.'.'.$model->getParam('prop_valuefield');
				$fieldAlias=oneof($fieldAlias,$fieldPrefix.$field);
			}

			// Model? Add table prefix and alias
			if ($model!==null && mb_strpos($field,'.')===false)
			{
				// Add alias
				if ($fieldPrefix!==null && empty($fieldAlias))
				{
					$fieldAlias=$fieldPrefix.$field;
				}

				// Add table prefix
				$field=$model->getParam('table').'.'.$field;
			}

			// Return
			if (!empty($fieldAlias))
			{
				return $field.' as '.$fieldAlias;
			}
			else
			{
				return $field;
			}
		}


	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The list filter interface
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class ListFilterInterface
	{


		/**
		 *   Include traits
		 */

		use FlaskPHP\Traits\Parameters;


		/**
		 *   Filter tag
		 *   @var string
		 *   @access public
		 */

		public $tag = null;


		/**
		 *   Back-reference to list object
		 *   @var FlaskPHP\Action\ListAction
		 *   @access public
		 */

		public $listObject = null;


		/**
		 *
		 *   Set title
		 *   ---------
		 *   @access public
		 *   @param string $title Filter title
		 *   @return \Codelab\FlaskPHP\Action\ListFilterInterface
		 *
		 */

		public function setTitle( string $title )
		{
			$this->setParam('title',$title);
			return $this;
		}


		/**
		 *
		 *   Set query field
		 *   ---------------
		 *   @access public
		 *   @param string|array $field Field
		 *   @return \Codelab\FlaskPHP\Action\ListFilterInterface
		 *
		 */

		public function setField( $field )
		{
			$this->setParam('field',$field);
			return $this;
		}


		/**
		 *
		 *   Set field options source
		 *   ------------------------
		 *   @access public
		 *   @param string $source Source type
		 *   @throws FlaskPHP\Exception\InvalidParameterException
		 *   @return \Codelab\FlaskPHP\Action\ListFilterInterface
		 *
		 */

		public function setSource( string $source )
		{
			switch($source)
			{
				case 'list':
				case 'query':
				case 'model':
				case 'func':
					$this->setParam('source',$source);
					break;
				default:
					throw new FlaskPHP\Exception\InvalidParameterException('Unknown source: '.$source);
			}
			return $this;
		}


		/**
		 *
		 *   Set source list
		 *   ---------------
		 *   @access public
		 *   @param array $sourceList Source list
		 *   @return \Codelab\FlaskPHP\Action\ListFilterInterface
		 *
		 */

		public function setSourceList( array $sourceList )
		{
			$this->setParam('source_list',$sourceList);
			return $this;
		}


		/**
		 *
		 *   Set source model
		 *   ----------------
		 *   @access public
		 *   @param string|FlaskPHP\Model\ModelInterface $sourceModel source data object
		 *   @param string $keyField Key field
		 *   @param string $valueField Value field
		 *   @param FlaskPHP\DB\QueryBuilderInterface $param Additional load parameters
		 *   @return \Codelab\FlaskPHP\Action\ListFilterInterface
		 *
		 */

		public function setSourceModel( $sourceModel, string $keyField, string $valueField, FlaskPHP\DB\QueryBuilderInterface $param=null )
		{
			$this->setParam('source','model');
			$this->setParam('source_model',$sourceModel);
			$this->setParam('source_keyfield',$keyField);
			$this->setParam('source_valuefield',$valueField);
			$this->setParam('source_param',$param);
			return $this;
		}


		/**
		 *
		 *   Set source list
		 *   ---------------
		 *   @access public
		 *   @param string $sourceQuery Source query
		 *   @return \Codelab\FlaskPHP\Action\ListFilterInterface
		 *
		 */

		public function setSourceQuery( $sourceQuery )
		{
			$this->setParam('source_query',$sourceQuery);
			return $this;
		}


		/**
		 *
		 *   Get options
		 *   -----------
		 *   @access public
		 *   @throws \Exception
		 *   @return array
		 *
		 */

		public function getOptions()
		{
			// Init
			$options=array();

			// Get options
			switch($this->getParam('source'))
			{
				// Query
				case 'query':
					$dataset=Flask()->DB->querySelectSQL($this->getParam('source_query'));
					foreach ($dataset as $row)
					{
						$options[$row['value']]=$row['description'];
					}
					break;

				// Model
				case 'model':
					$model=$this->getParam('source_model');
					$query=oneof($this->getParam('source_param'),Flask()->DB->getQueryBuilder('SELECT'));
					$query->setModel($model);
					$query->addField($this->getParam('source_keyfield').' as value');
					$query->addField($this->getParam('source_valuefield').' as description');
					$query->addOrderBy($model->getParam('setord')?'ord':'value');
					$dataset=$model->getList($query);
					foreach ($dataset as $row)
					{
						$options[$row['value']]=$row['description'];
					}
					break;

				// Function
				case 'func':
					$options=call_user_func($this->getParam('source_func'),$this);
					break;

				// Default: source array/list
				default:
					$options=(array)$this->getParam('source_list');
					break;
			}

			// Apply zero-hack if necessary
			if ($this->getParam('zerohack'))
			{
				$origoptions=$options;
				$options=array();
				foreach ($origoptions as $k => $v)
				{
					$options['val_'.$k]=$v;
				}
			}

			// Return
			return $options;
		}


		/**
		 *
		 *   Escape value
		 *   ------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @return string
		 *
		 */

		public function escapeValue( $value )
		{
			if (!is_string($value)) return $value;
			$value=htmlspecialchars($value);
			$value=str_replace('{','&#123;',$value);
			$value=str_replace('}','&#125;',$value);
			$value=str_replace('[','&#091;',$value);
			$value=str_replace(']','&#093;',$value);
			return $value;
		}


		/**
		 *
		 *   Trigger: pre-display
		 *   --------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function triggerPreDisplay()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Render filter
		 *   -------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilter()
		{
			// Pre-display trigger
			$this->triggerPreDisplay();

			// Render
			$filter=$this->renderFilterBeginningBlock();
			$filter.=$this->renderFilterLabel();
			$filter.=$this->renderFilterElement();
			$filter.=$this->renderFilterEndingBlock();
			return $filter;
		}


		/**
		 *
		 *   Render filter: beginning block
		 *   ------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilterBeginningBlock()
		{
			$filterBeginningBlock='<div class="filter-item column" id="filter_'.$this->tag.'">';
			$filterBeginningBlock.='<div class="field">';
			return $filterBeginningBlock;
		}


		/**
		 *
		 *   Render filter: label
		 *   --------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilterLabel()
		{
			$filterLabel='<label class="filter-item-label" for="filter_'.$this->tag.'">';
			$filterLabel.=$this->getParam('title');
			$filterLabel.='</label>';
			return $filterLabel;
		}


		/**
		 *
		 *   Render filter: element
		 *   ----------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilterElement()
		{
			// This should be implemented in the filter class
			throw new FlaskPHP\Exception\NotImplementedException('Function renderFilterElement() not implemented in the filter class.');
		}


		/**
		 *
		 *   Render filter: ending block
		 *   ---------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilterEndingBlock()
		{
			$filterEndingBlock='</div>';
			$filterEndingBlock.='</div>';
			return $filterEndingBlock;
		}


		/**
		 *
		 *   Get filter submit value
		 *   -----------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function getSubmitValue()
		{
			return Flask()->Request->postVar('filter_'.$this->tag);
		}


		/**
		 *
		 *   Get filter value
		 *   ----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function getValue()
		{
			// Not connected to a list?
			if (!is_object($this->listObject)) return null;

			// Return value
			return (Flask()->Session->get('list.'.$this->listObject->getParam('id').'.filter.'.$this->tag));
		}


		/**
		 *
		 *   Apply filter to load list query
		 *   -------------------------------
		 *   @access public
		 *   @param FlaskPHP\DB\QueryBuilderInterface $loadListParam List load parameters
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function applyFilter( FlaskPHP\DB\QueryBuilderInterface $loadListParam )
		{
			// Empty?
			$value=$this->getValue();
			if ($value==null) return;

			// Mark filter applied
			$this->listObject->filterApplied=true;

			// Field list
			if ($this->hasParam('field'))
			{
				$fieldList=str_array($this->getParam('field'));
			}
			else
			{
				$fieldList=array($this->tag);
			}

			// TODO: finish
			$whereList=array();
			foreach ($fieldList as $field)
			{
				if ($this->getParam('exact') || !is_string($value))
				{
					$whereList[]=(mb_strpos($field,'.')===false?$this->listObject->model->getParam('table').'.':'').$field."=".$loadListParam::colValue($value);
				}
				else
				{
					$whereList[]=(mb_strpos($field,'.')===false?$this->listObject->model->getParam('table').'.':'').$field." like ".$loadListParam::colValue('%'.strval($value).'%');
				}
			}
			$loadListParam->addWhere('('.join(') or (',$whereList).')');
		}


	}


?>
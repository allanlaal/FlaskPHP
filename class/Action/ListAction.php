<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The list action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class ListAction extends ActionInterface
	{


		/**
		 *   Template
		 *   @var FlaskPHP\Template\Template
		 *   @access public
		 */

		public $template = null;


		/**
		 *   Global actions
		 *   @var array
		 *   @access public
		 */

		public $globalAction = array();


		/**
		 *   Row actions
		 *   @var array
		 *   @access public
		 */

		public $rowAction = array();


		/**
		 *   List columns/fields
		 *   @var array
		 *   @access public
		 */

		public $field = array();


		/**
		 *   Filters
		 *   @var array
		 *   @access public
		 */

		public $filter = array();


		/**
		 *   Filters applied?
		 *   @var bool
		 *   @access public
		 */

		public $filterApplied = false;


		/**
		 *   List JavaScript
		 *   @var string
		 *   @access public
		 */

		public $js = '';


		/**
		 *   List data
		 *   @var array
		 *   @access public
		 */

		public $data = null;


		/**
		 *   Found rows
		 *   @var int
		 *   @access public
		 */

		public $dataFoundRows = null;


		/**
		 *   List data total/summary
		 *   @var array
		 *   @access public
		 */

		public $dataTotal = null;


		/**
		 *
		 *   Init list
		 *   ---------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initList()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Init fields
		 *   -----------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initFields()
		{
			// This should be implemented in the subclass.
		}


		/**
		 *
		 *   Init filters
		 *   ------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initFilters()
		{
			// This should be implemented in the subclass.
		}


		/**
		 *
		 *   Init actions
		 *   ------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initActions()
		{
			// This should be implemented in the subclass.
		}


		/**
		 *
		 *   Set unset parameters to default values
		 *   --------------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function setDefaults()
		{
			// Parent
			parent::setDefaults();

			// List ID
			if (!$this->hasParam('id'))
			{
				if (is_object($this->model))
				{
					$this->setParam('id',str_replace('.','_',$this->model->getParam('table')));
				}
				else
				{
					$this->setParam('id',uniqid());
				}
			}

			// Parent field for nested list
			if ($this->getParam('nested') && !$this->hasParam('nested_parentfield'))
			{
				$this->setParam('nested_parentfield','parent_oid');
			}

			// Page size
			if ($this->getParam('paging') && !$this->hasParam('paging_pagesize'))
			{
				$this->setParam('paging_pagesize',100);
			}

			// Show found rows
			if (!$this->hasParam('showfoundrows'))
			{
				$this->setParam('showfoundrows',true);
			}
		}


		/**
		 *
		 *   Load data
		 *   ---------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function dataLoad()
		{
			// Check
			if (!is_object($this->model)) throw new FlaskPHP\Exception\Exception('Model not set.');

			// Parameters
			$loadListParam=oneof($this->getParam('loadparam'),Flask()->DB->getQueryBuilder());

			// Base columns
			if ($this->model->getParam('idfield'))
			{
				$loadListParam->addField($this->model->getParam('idfield'));
			}
			if ($this->getParam('nested'))
			{
				$loadListParam->addField($this->getParam('nested_parentfield'));
			}

			// Fields
			foreach ($this->field as $fieldTag => $fieldObject)
			{
				$fieldObject->getListQuery($loadListParam);
			}

			// Filters
			$this->filterApplied=false;
			foreach ($this->filter as $filterTag => $filterObject)
			{
				$filterObject->applyFilter($loadListParam);
			}

			// Sorting
			if ($this->model->getParam('setord'))
			{
				$loadListParam->addOrderBy($this->model->getParam('table').'.ord');
			}
			else
			{
				if ($this->getParam('list_sort'))
				{
					$sortFieldObject=$this->field[$this->getParam('list_sort_field')];
					$sortFieldObject->getListSortCriteria(
						oneof($this->getParam('list_sort_dir'),'asc'),
						$loadListParam
					);
				}
				elseif (empty($loadListParam->queryOrderBy) && sizeof($this->field))
				{
					reset($this->field);
					$sortFieldObject=$this->field[key($this->field)];
					$sortFieldObject->getListSortCriteria(
						oneof($sortFieldObject->getParam('list_sort_defaultsortorder'),'asc'),
						$loadListParam
					);
				}
			}

			// Paging
			$loadListParam->calcFoundRows=true;
			if ($this->getParam('paging'))
			{
				$loadListParam->addLimit($this->getParam('paging_pagesize'),(($this->getParam('paging_page')-1)*$this->getParam('paging_pagesize')));
			}

			// Load data
			if (!$this->filterApplied || !$this->getParam('list_filterrequired'))
			{
				$this->data=$this->model->getList($loadListParam);
				$this->dataFoundRows=Flask()->DB->foundRows();
			}

			// Totals
			if ($this->getParam('list_showtotal'))
			{
				$totalParam=$loadListParam;
				$totalParam->queryField=null;
				$totalParam->queryOrderBy=null;
				$totalParam->queryGroupBy=null;
				$totalParam->queryLimit=null;
				$totalParam->queryLimitOffset=null;
				foreach ($this->field as $fieldTag => $fieldObject)
				{
					if ($fieldObject->getParam('list_showtotal'))
					{
						$fieldObject->getListTotalQuery($totalParam);
					}
				}
				$totalData=$this->model->getList($totalParam);
				$this->dataTotal=$totalData[0];
			}
		}


		/**
		 *
		 *   Set filters
		 *   -----------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function applyFilters()
		{
			// Clear filters
			if (Flask()->Request->postVar('action')=='filter_clear')
			{
				// Clear
				Flask()->Session->set('list.'.$this->getParam('id').'.filter',null);

				// Set to return content
				$this->setParam('returnfilter',true);
				$this->setParam('returncontent',true);
				return;
			}

			// Set filters
			if (Flask()->Request->postVar('action')=='filter_submit')
			{
				// Reset
				Flask()->Session->set('list.'.$this->getParam('id').'.filter',array());

				// Traverse filters
				foreach ($this->filter as $filterTag => $filterObject)
				{
					Flask()->Session->set('list.'.$this->getParam('id').'.filter.'.$filterTag,$filterObject->getSubmitValue());
				}

				// Set to return content
				$this->setParam('returncontent',true);
				return;
			}

			// No action
			return;
		}


		/**
		 *
		 *   Set sorting
		 *   -----------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function applySorting()
		{
			// Set sorting
			if (Flask()->Request->postVar('action')=='sort')
			{
				Flask()->Session->set('list.'.$this->getParam('id').'.sort',array());
				if (Flask()->Request->postVar('sort_field'))
				{
					$sortField=Flask()->Request->postVar('sort_field');
					$sortDir=Flask()->Request->postVar('sort_dir');
					if (array_key_exists($sortField,$this->field) && $this->field[$sortField]->getParam('list_sortable'))
					{
						Flask()->Session->set('list.'.$this->getParam('id').'.sort.field',$sortField);
						Flask()->Session->set('list.'.$this->getParam('id').'.sort.dir',$sortDir);
					}
				}

				// Set to return content
				$this->setParam('returncontent',true);
			}

			// Check if we have sorting?
			$this->setParam('list_sort',false);
			foreach ($this->field as $fieldTag => $fieldObject)
			{
				if ($fieldObject->getParam('list_sortable'))
				{
					$this->setParam('list_sort',true);
					if (Flask()->Session->get('list.'.$this->getParam('id').'.sort.field'))
					{
						$this->setParam('list_sort_field',Flask()->Session->get('list.'.$this->getParam('id').'.sort.field'));
						$this->setParam('list_sort_dir',oneof(
							Flask()->Session->get('list.'.$this->getParam('id').'.sort.dir'),
							$this->field[$this->getParam('list_sort_field')]->getParam('list_sort_default'),
							'asc'
						));
					}
					else
					{
						foreach ($this->field as $sdFieldObject)
						{
							if (!$this->hasParam('list_sort_field'))
							{
								$this->setParam('list_sort_field',$sdFieldObject->tag);
								$this->setParam('list_sort_dir',oneof(
									$sdFieldObject->getParam('list_sort_defaultorder'),
									'asc'
								));
							}
							if ($sdFieldObject->getParam('list_sort_default'))
							{
								$this->setParam('list_sort_field',$sdFieldObject->tag);
								$this->setParam('list_sort_dir',oneof(
									$sdFieldObject->getParam('list_sort_defaultorder'),
									'asc'
								));
							}
						}
					}
					break;
				}
			}
		}


		/**
		 *
		 *   Set paging
		 *   ----------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function applyPaging()
		{
			// Set paging
			if ($this->getParam('paging') && Flask()->Request->postVar('action')=='page')
			{
				Flask()->Session->set('list.'.$this->getParam('id').'.page',null);
				if (intval(Flask()->Request->postVar('page')))
				{
					Flask()->Session->set('list.'.$this->getParam('id').'.page',intval(Flask()->Request->postVar('page')));
				}

				// Set to return content
				$this->setParam('returncontent',true);
				$this->setParam('returnscrolltop',true);
			}

			// Set variables
			if ($this->getParam('paging'))
			{
				$currentPage=oneof(Flask()->Session->get('list.'.$this->getParam('id').'.page'),1);
				$this->setParam('paging_page',$currentPage);
			}
		}


		/**
		 *
		 *   Check paging post-dataload
		 *   --------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return bool
		 *
		 */

		public function checkPaging()
		{
			// Check paging
			if ($this->getParam('paging'))
			{
				// Check
				$currentPage=oneof(Flask()->Session->get('list.'.$this->getParam('id').'.page'),1);
				$totalPages=ceil($this->dataFoundRows/$this->getParam('paging_pagesize'));
				if (!$totalPages) $totalPages=1;

				// Reset
				if ($currentPage>$totalPages)
				{
					Flask()->Session->set('list.'.$this->getParam('id').'.page',1);
					return false;
				}

				// Set these as parameters for convenience
				$this->setParam('paging_page',$currentPage);
				$this->setParam('paging_totalpages',$totalPages);
			}

			// All-OK
			return true;
		}


		/**
		 *
		 *   Add a global action
		 *   -------------------
		 *   @access public
		 *   @param string $tag Action tag
		 *   @param ListGlobalAction $actionObject Action instance
		 *   @throws \Exception
		 *   @return ListGlobalAction
		 *
		 */

		public function addGlobalAction( string $tag, ListGlobalAction $actionObject=null )
		{
			// Check
			if (array_key_exists($tag,$this->globalAction)) throw new FlaskPHP\Exception\InvalidParameterException('Global action '.$tag.' already exists.');

			// Create object
			if (!is_object($actionObject))
			{
				$actionObject=new ListGlobalAction($tag);
			}

			// Set action parameters
			$actionObject->tag=$tag;
			$actionObject->listObject=$this;

			// Add
			$this->globalAction[$tag]=$actionObject;

			// Return action
			return $actionObject;
		}


		/**
		 *
		 *   Add a row action
		 *   ----------------
		 *   @access public
		 *   @param string $tag Action tag
		 *   @param ListRowAction $actionObject Action instance
		 *   @throws \Exception
		 *   @return ListRowAction
		 *
		 */

		public function addRowAction( string $tag, ListRowAction $actionObject=null )
		{
			// Check
			if (array_key_exists($tag,$this->rowAction)) throw new FlaskPHP\Exception\InvalidParameterException('Row action '.$tag.' already exists.');

			// Create object
			if (!is_object($actionObject))
			{
				$actionObject=new ListRowAction($tag);
			}

			// Set action parameters
			$actionObject->tag=$tag;
			$actionObject->listObject=$this;

			// Add
			$this->rowAction[$tag]=$actionObject;

			// Return action
			return $actionObject;
		}


		/**
		 *
		 *   Add a field
		 *   -----------
		 *   @access public
		 *   @param string $fieldTag Field tag
		 *   @param FlaskPHP\Field\FieldInterface $fieldObject Field object
		 *   @throws \Exception
		 *   @return FlaskPHP\Field\FieldInterface Field reference
		 *
		 */

		public function addField( string $fieldTag, FlaskPHP\Field\FieldInterface $fieldObject=null )
		{
			// Check if the action already exists
			if (array_key_exists($fieldTag,$this->field)) throw new FlaskPHP\Exception\InvalidParameterException('Field '.$fieldTag.' already exists.');

			// Field object passed
			if (is_object($fieldObject))
			{
				$this->field[$fieldTag]=$fieldObject;
			}

			// Otherwise - data objekti olemasolev field
			else
			{
				// Do we have a data object?
				if (!is_object($this->model)) throw new FlaskPHP\Exception\Exception('No model defined.');
				if (!($this->model instanceof FlaskPHP\Model\ModelInterface)) throw new Exception('Model not a ModelInterface instance.');

				// Relation?
				if (strpos($fieldTag,'->')!==false)
				{
					$relationList=preg_split('/\-\>/',$fieldTag);
					$effectiveModel=$this->model;
					for ($r=0;$r<sizeof($relationList);++$r)
					{
						if ($r==(sizeof($relationList)-1))
						{
							// Do we have this field?
							if (!array_key_exists($relationList[$r],$effectiveModel->_field)) throw new FlaskPHP\Exception\InvalidParameterException($relationList[$r].': no such field defined in model '.get_class($effectiveModel).'.');

							// Link
							$this->field[$fieldTag]=$effectiveModel->_field[$relationList[$r]];
							$this->field[$fieldTag]->tag=$fieldTag;
						}
						else
						{
							if (!is_object($effectiveModel->_rel[$relationList[$r]])) throw new FlaskPHP\Exception\InvalidParameterException('Error in parseRelation(): relation '.$relationList[$r].' does not exist in '.get_class($effectiveModel));
							$relationModelClass=$effectiveModel->_rel[$relationList[$r]]->relationRemoteModel;
							$effectiveModel=new $relationModelClass();
						}
					}
				}

				// Local field
				else
				{
					// Do we have this field?
					if (!array_key_exists($fieldTag,$this->model->_field)) throw new FlaskPHP\Exception\InvalidParameterException($fieldTag.': no such field defined in model.');

					// Link
					$this->field[$fieldTag]=&$this->model->_field[$fieldTag];
				}
			}

			// Set tag/column
			if (empty($this->field[$fieldTag]->tag)) $this->field[$fieldTag]->tag=$fieldTag;

			// Create backreference to the field
			$this->field[$fieldTag]->listObject=$this;

			// Return reference
			return $this->field[$fieldTag];
		}


		/**
		 *
		 *   Remove a field
		 *   --------------
		 *   @access public
		 *   @param string $fieldTag Field tag
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function removeField( string $fieldTag )
		{
			// Check if the field exists
			if (!array_key_exists($fieldTag,$this->field)) throw new FlaskPHP\Exception\InvalidParameterException('Field '.$fieldTag.' does not exist.');

			// Remove
			unset($this->field[$fieldTag]);
		}


		/**
		 *
		 *   Add a filter
		 *   ------------
		 *   @access public
		 *   @param string $filterTag Filter tag
		 *   @param FlaskPHP\Action\ListFilterInterface $filterObject Filter object
		 *   @throws \Exception
		 *   @return FlaskPHP\Action\ListFilterInterface Filter reference
		 *
		 */

		public function addFilter( string $filterTag, FlaskPHP\Action\ListFilterInterface $filterObject )
		{
			// Check if the action already exists
			if (array_key_exists($filterTag,$this->filter)) throw new FlaskPHP\Exception\InvalidParameterException('Filter '.$filterTag.' already exists.');

			// Add filter
			$this->filter[$filterTag]=$filterObject;

			// Set parameters
			$filterObject->tag=$filterTag;
			$filterObject->listObject=$this;

			// Return reference
			return $filterObject;
		}


		/**
		 *
		 *   Set title
		 *   ---------
		 *   @access public
		 *   @param string $title List title
		 *   @return \Codelab\FlaskPHP\Action\ListAction
		 *
		 */

		public function setTitle( string $title )
		{
			$this->setParam('title',$title);
			return $this;
		}


		/**
		 *
		 *   Set load parameters
		 *   -------------------
		 *   @access public
		 *   @param FlaskPHP\DB\QueryBuilderInterface $loadParam
		 *   @return \Codelab\FlaskPHP\Action\ListAction
		 *
		 */

		public function setLoadParam( FlaskPHP\DB\QueryBuilderInterface $loadParam )
		{
			$this->setParam('loadparam',$loadParam);
			return $this;
		}


		/**
		 *
		 *   Set paging
		 *   ----------
		 *   @access public
		 *   @param bool $pagingEnabled Paging enabled?
		 *   @param int $pageSize Page size
		 *   @return \Codelab\FlaskPHP\Action\ListAction
		 *
		 */

		public function setPaging( bool $pagingEnabled, int $pageSize=null )
		{
			$this->setParam('paging',$pagingEnabled);
			$this->setParam('paging_pagesize',$pageSize);
			return $this;
		}


		/**
		 *
		 *   Set table class
		 *   ---------------
		 *   @access public
		 *   @param string $tableClass Table class
		 *   @return \Codelab\FlaskPHP\Action\ListAction
		 *
		 */

		public function setTableclass( string $tableClass )
		{
			$this->setParam('tableclass',$tableClass);
			return $this;
		}


		/**
		 *
		 *   Set filter required
		 *   -------------------
		 *   @access public
		 *   @param bool $filterRequired Filter required?
		 *   @param string $filterRequiredMessage Required message
		 *   @return \Codelab\FlaskPHP\Action\ListAction
		 *
		 */

		public function setFilterRequired( bool $filterRequired, string $filterRequiredMessage=null )
		{
			$this->setParam('filterrequired',$filterRequired);
			$this->setParam('filterrequired_message',$filterRequiredMessage);
			return $this;
		}


		/**
		 *
		 *   Set filter width
		 *   -----------------
		 *   @access public
		 *   @param string $filterColumnWidth Filter column width
		 *   @return \Codelab\FlaskPHP\Action\ListAction
		 *
		 */

		public function setFilterColumnWidth( string $filterColumnWidth )
		{
			$this->setParam('filtercolumnwidth',$filterColumnWidth);
			return $this;
		}


		/**
		 *
		 *   Set no results messages
		 *   -----------------------
		 *   @access public
		 *   @param string $noResultsMessage No results message
		 *   @param string $noResultsMessageFiltered No results message when filtered
		 *   @return \Codelab\FlaskPHP\Action\ListAction
		 *
		 */

		public function setNoResultsMessage( string $noResultsMessage, string $noResultsMessageFiltered=null )
		{
			$this->setParam('noresults_message',$noResultsMessage);
			$this->setParam('noresults_message_filtered',$noResultsMessageFiltered);
			return $this;
		}


		/**
		 *
		 *   Set row numbering
		 *   -----------------
		 *   @access public
		 *   @param bool $showRowNumbers Show row numbers
		 *   @param bool $rowNumbersReversed Reversed row numbers?
		 *   @param string $rowNumberColumnTitle Row number column title
		 *   @param string $rowNumberColumnWidth Row number column width
		 *   @param string $rowNumberColumnAlign Row number column align
		 *   @return \Codelab\FlaskPHP\Action\ListAction
		 *
		 */

		public function setRowNumbers( bool $showRowNumbers, bool $rowNumbersReversed=false, string $rowNumberColumnTitle='#', string $rowNumberColumnWidth=null, $rowNumberColumnAlign=null )
		{
			if ($showRowNumbers)
			{
				$this->setParam('rownumbers',true);
				$this->setParam('rownumbers_reverse',$rowNumbersReversed);
				$this->setParam('rownumbers_title',$rowNumberColumnTitle);
				$this->setParam('rownumbers_fieldwidth',$rowNumberColumnWidth);
				$this->setParam('rownumbers_fieldalign',$rowNumberColumnAlign);
			}
			else
			{
				$this->setParam('rownumbers',null);
				$this->setParam('rownumbers_reverse',null);
				$this->setParam('rownumbers_title',null);
				$this->setParam('rownumbers_fieldwidth',null);
				$this->setParam('rownumbers_fieldalign',null);
			}
			return $this;
		}


		/**
		 *
		 *   Set return link
		 *   ---------------
		 *   @access public
		 *   @param string $returnLink Return link
		 *   @param string $title Link title
		 *   @return \Codelab\FlaskPHP\Action\ListAction
		 *
		 */

		public function setReturnLink( string $returnLink, string $title=null )
		{
			$this->setParam('returnlink',$returnLink);
			$this->setParam('returnlink_title',$title);
			return $this;
		}


		/**
		 *
		 *   Return list HTML instead of full response
		 *   -----------------------------------------
		 *   @access public
		 *   @param string $returnLink Return link
		 *   @param string $title Link title
		 *   @return \Codelab\FlaskPHP\Action\ListAction
		 *
		 */

		public function setReturnHTML( bool $returnHTML )
		{
			$this->setParam('returnhtml',$returnHTML);
			return $this;
		}


		/**
		 *
		 *   Render list HTML
		 *   ----------------
		 *   @access public
		 *   @param string $title Field title
		 *   @return string
		 *
		 */

		public function renderListHTML()
		{
			// Init template
			$this->template=new FlaskPHP\Template\Template('list.'.$this->getParam('template'));

			// Render parts
			$this->renderTitle();
			$this->renderExtraHeader();
			$this->renderGlobalActions();
			$this->renderFilters(true);
			$this->renderContent(true);
			$this->renderExtraFooter();
			$this->renderReturnLink();
			$this->renderJS();

			// Return
			return $this->template->render();
		}


		/**
		 *
		 *   Render list parts: title
		 *   ------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderTitle()
		{
			$title=null;
			if ($this->hasParam('title'))
			{
				$title=$this->getParam('title');
				$this->template->set('title',$title);
			}
			return $title;
		}


		/**
		 *
		 *   Render list parts: extra header
		 *   -------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderExtraHeader()
		{
			// This can be implemented in the subclass if necessary
			return '';
		}


		/**
		 *
		 *   Render list parts: extra footer
		 *   -------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderExtraFooter()
		{
			// This can be implemented in the subclass if necessary
			return '';
		}


		/**
		 *
		 *   Render list parts: global actions
		 *   ---------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderGlobalActions()
		{
			// No actions?
			if (!sizeof($this->globalAction)) return null;

			$listGlobalActions='<div class="list-global-actions">';
			foreach ($this->globalAction as $actionTag => $actionObject)
			{
				$listGlobalActions.='<div class="list-global-action ui basic button"  onclick="'.$actionObject->getParam('action').'">';
				if ($actionObject->hasParam('icon')) $listGlobalActions.='<i class="'.$actionObject->getParam('icon').' icon"></i> ';
				$listGlobalActions.=$actionObject->getParam('title');
				$listGlobalActions.='</div>';
			}
			$listGlobalActions.='</div>';

			$this->template->set('list_globalaction',$listGlobalActions);
			return $listGlobalActions;
		}


		/**
		 *
		 *   Render list parts: filters
		 *   --------------------------
		 *   @access public
		 *   @param bool $renderContentWrapper Render content wrapper
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilters( bool $renderContentWrapper=true )
		{
			// Check & init
			if (!sizeof($this->filter)) return '';
			$listFilter='';

			// Wrapper
			if ($renderContentWrapper)
			{
				$listFilter.='<div class="list-filter ui segment">';
				$listFilter.='<form id="list_'.$this->getParam('id').'_filter" onsubmit="return false">';
			}

				// Filters
				$listFilter.='<div class="ui form">';
				$listFilter.='<div class="list-filter-filters ui '.oneof($this->getParam('filtercolumnwidth'),'four').' column grid">';
				foreach ($this->filter as $filterTag => $filterObject)
				{
					$listFilter.=$filterObject->renderFilter();
				}
				$listFilter.='</div>';
				$listFilter.='</div>';

				// Submit
				$listFilter.='<div class="list-filter-submit">';
				$listFilter.='<button class="ui button" type="button" onclick="Flask.List.updateFilters(\''.$this->getParam('id').'\',\''.$this->buildURL().'\')"><i class="filter icon"></i> [[ FLASK.LIST.Filter.Submit ]]</button>';
				$listFilter.='<button class="ui button" type="button" onclick="Flask.List.getContent(\''.$this->getParam('id').'\',\''.$this->buildURL().'\',\'filter_clear\')"><i class="remove icon"></i> [[ FLASK.LIST.Filter.Clear ]]</button>';
				$listFilter.='</div>';

			// Wrapper ends
			if ($renderContentWrapper)
			{
				$listFilter.='</form>';
				$listFilter.='</div>';
			}

			// Initial JS
			if ($renderContentWrapper)
			{
				$this->js.="Flask.List.initFilters('".$this->getParam('id')."')";
			}

			// Set and return
			if (is_object($this->template))
			{
				$this->template->set('list_filter',$listFilter);
			}
			return $listFilter;
		}


		/**
		 *
		 *   Render list parts: list content
		 *   -------------------------------
		 *   @access public
		 *   @param bool $renderContentWrapper Render content wrapper
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContent( bool $renderContentWrapper=true )
		{
			// Init
			$listContent='';

			// Content wrapper
			if ($renderContentWrapper)
			{
				$listContent.='<div id="list_'.$this->getParam('id').'">';
			}


			// No filter?
			if ($this->getParam('filterrequired') && !$this->filterApplied)
			{
				$listContent.='<div class="ui message">'.oneof($this->getParam('filterrequired_message'),'[[ FLASK.LIST.Message.FilterRequired ]]').'</div>';
			}

			// No results
			elseif (!sizeof($this->data))
			{
				if ($this->filterApplied)
				{
					$listContent.='<div class="ui warning message">'.oneof($this->getParam('noresults_message_filtered'),'[[ FLASK.LIST.Message.NoResults.Filtered ]]').'</div>';
				}
				else
				{
					$listContent.='<div class="ui message">'.oneof($this->getParam('noresults_message'),'[[ FLASK.LIST.Message.NoResults ]]').'</div>';
				}
			}

			// Render content
			else
			{
				$listContent.=$this->renderContentTableBegin();
				$listContent.=$this->renderContentTableHeader();
				$listContent.=$this->renderContentTableBody();
				$listContent.=$this->renderContentTableEnd();
				$listContent.=$this->renderPaging();
			}

			// Wrapper
			if ($renderContentWrapper)
			{
				$listContent.='</div>';
			}

			// Set and return
			if (is_object($this->template))
			{
				$this->template->set('list_content',$listContent);
			}
			return $listContent;
		}


		/**
		 *
		 *   Render list parts: list table begin
		 *   -----------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContentTableBegin()
		{
			$tableClass=array();
			$tableClass[]='ui table';
			$tableClass[]='list-content';
			if ($this->hasParam('tableclass')) $tableClass[]=$this->getParam('tableclass');

			$contentTableBegin='<table class="'.join(' ',$tableClass).'">';
			return $contentTableBegin;
		}


		/**
		 *
		 *   Render list parts: list table header
		 *   ------------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContentTableHeader()
		{
			// Sort field
			$sortField=$this->getParam('list_sort_field');
			$sortDir=$this->getParam('list_sort_dir');

			// Wrapper
			$contentTableHeader='<thead>';
			$contentTableHeader.=$this->renderContentTableRowBeginningBlock('head');

			// Row number
			if ($this->getParam('rownumbers'))
			{
				// Class
				$fieldClass=array();
				$fieldClass[]='list-table-header';
				$fieldClass[]='list-rownum';
				if ($this->getParam('rownumbers_headerclass')) $fieldClass[]=$this->getParam('rownumbers_headerclass');
				if ($this->getParam('rownumbers_fieldclass')) $fieldClass[]=$this->getParam('rownumbers_fieldclass');

				// Style
				$fieldStyle=array();
				if ($this->getParam('rownumbers_fieldstyle')) $fieldStyle[]=$this->getParam('rownumbers_fieldstyle');
				if ($this->getParam('rownumbers_fieldwidth')) $fieldStyle[]='width: '.$this->getParam('rownumbers_fieldwidth');
				if ($this->getParam('rownumbers_fieldalign')) $fieldStyle[]='text-align: '.$this->getParam('rownumbers_fieldalign');

				// Cell
				$contentTableHeader.='<th';
					if (sizeof($fieldClass)) $contentTableHeader.='  class="'.join(' ',$fieldClass).'"';
					if (sizeof($fieldStyle)) $contentTableHeader.='  style="'.join(';',$fieldStyle).'"';
				$contentTableHeader.='">';
				$contentTableHeader.=$this->getParam('rownumbers_title');
				$contentTableHeader.='</th>';
			}

			// Fields
			foreach ($this->field as $fieldObject)
			{
				// Class
				$fieldClass=array();
				$fieldClass[]='list-table-header';
				if ($this->getParam('fieldclass')) $fieldClass[]=$this->getParam('fieldclass');
				if ($this->getParam('headerclass')) $fieldClass[]=$this->getParam('headerclass');
				if ($fieldObject->getParam('list_fieldclass')) $fieldClass[]=$fieldObject->getParam('list_fieldclass');
				if ($fieldObject->getParam('list_headerclass')) $fieldClass[]=$fieldObject->getParam('list_headerclass');
				if ($fieldObject->getParam('list_nowrap')) $fieldClass[]='list-table-field-nowrap';

				// Style
				$fieldStyle=array();
				if ($this->getParam('fieldstyle')) $fieldClass[]=$this->getParam('fieldstyle');
				if ($this->getParam('headerstyle')) $fieldClass[]=$this->getParam('headerstyle');
				if ($fieldObject->getParam('list_fieldstyle')) $fieldStyle[]=$fieldObject->getParam('list_fieldstyle');
				if ($fieldObject->getParam('list_headerstyle')) $fieldStyle[]=$fieldObject->getParam('list_headerstyle');
				if ($fieldObject->getParam('list_fieldwidth')) $fieldStyle[]='width: '.$fieldObject->getParam('list_fieldwidth');
				if ($fieldObject->getParam('list_fieldalign')) $fieldStyle[]='text-align: '.$fieldObject->getParam('list_fieldalign');
				if ($fieldObject->getParam('list_nowrap')) $fieldStyle[]='white-space: nowrap';

				// Sortable?
				$fieldSortable=($fieldObject->getParam('list_sortable')?true:false);
				if ($sortField==$fieldObject->tag)
				{
					$fieldSorted=($sortDir=='desc'?'desc':'asc');
				}
				else
				{
					$fieldSorted='';
				}
				if ($fieldSortable)
				{
					$fieldClass[]=oneof($this->getParam('list_header_sortclass'),'list-table-field-sortable');
				}
				if (!empty($fieldSorted))
				{
					$fieldClass[]=oneof($this->getParam('list_header_sortclass_sorted'),'list-table-field-sorted');
					$fieldClass[]=oneof($this->getParam('list_header_sortclass_sorted'),'list-table-field-sorted').'-'.$fieldSorted;
				}

				// Cell
				$contentTableHeader.='<th';
					if (sizeof($fieldClass)) $contentTableHeader.='  class="'.join(' ',$fieldClass).'"';
					if (sizeof($fieldStyle)) $contentTableHeader.='  style="'.join(';',$fieldStyle).'"';
				$contentTableHeader.='>';
				if ($fieldSortable)
				{
					$contentTableHeader.='<a class="'.oneof($this->getParam('list_header_sortclass_link'),'list-table-field-sort').(!empty($fieldSorted)?' '.oneof($this->getParam('list_header_sortclass_sorted_link'),'list-table-field-sorted').' '.oneof($this->getParam('list_header_sortclass_sorted_link'),'list-table-field-sorted').'-'.$fieldSorted:'').'" onclick="Flask.List.getContent(\''.$this->getParam('id').'\',\''.$this->buildURL().'\',\'sort\',{sort_field:\''.jsencode($fieldObject->tag).'\',sort_dir:\''.($sortField==$fieldObject->tag?($sortDir=='asc'?'desc':'asc'):oneof($this->getParam('list_sort_defaultorder'),'asc')).'\'})">';
				}
				$contentTableHeader.=$fieldObject->getParam('title');
				if ($fieldSortable)
				{
					$contentTableHeader.=' ';
					if ($sortField==$fieldObject->tag)
					{
						$contentTableHeader.='<span class="'.oneof($this->getParam('list_header_sortclass_sorted'),'list-table-field-sorted').'">';
						$contentTableHeader.=($sortDir=='desc'?'↑':'↓');
						$contentTableHeader.='</span>';
					}
					else
					{
						$contentTableHeader.='<span class="'.oneof($this->getParam('list_header_sortclass_sortable'),'list-table-field-sortable').'">⇵</span>';
					}
					$contentTableHeader.='</a>';
				}
				$contentTableHeader.='</th>';
			}

			// Row actions
			if (sizeof($this->rowAction))
			{
				// Class
				$fieldClass=array();
				$fieldClass[]='list-table-header';
				$fieldClass[]='list-rowactions';
				if ($this->getParam('headerclass_rowactions')) $fieldClass[]=$this->getParam('headerclass_rowactions');
				if ($this->getParam('fieldclass_rowactions')) $fieldClass[]=$this->getParam('fieldclass_rowactions');

				// Cell
				$contentTableHeader.='<th class="'.join(' ',$fieldClass).'"></th>';
			}

			// Wrapper ends
			$contentTableHeader.=$this->renderContentTableRowEndingBlock('head');
			$contentTableHeader.='</thead>';
			return $contentTableHeader;
		}


		/**
		 *
		 *   Render list parts: list table body
		 *   ----------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContentTableBody()
		{
			$contentTableBody='<tbody>';

			// Rows
			$contentTableBody.=$this->renderContentTableRows(0,0);

			// Total
			if ($this->getParam('list_showtotal'))
			{
				$contentTableBody.=$this->renderContentTotal();
			}

			$contentTableBody.='</tbody>';
			return $contentTableBody;
		}


		/**
		 *
		 *   Render list parts: list table rows
		 *   ----------------------------------
		 *   @access public
		 *   @param int $parentID Parent ID
		 *   @param int $depth Nesting depth
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContentTableRows( int $parentID, int $depth )
		{
			// Init
			$contentTableRows='';

			// Gather data
			if ($this->getParam('nested'))
			{
				$dataset=array();
				foreach ($this->data as &$row)
				{
					if ($row[$this->getParam('nested_parentfield')]!=$parentID) continue;
					$dataset[]=&$row;
				}
			}
			else
			{
				$dataset=&$this->data;
			}

			// Check
			if (!is_array($dataset) || !sizeof($dataset)) return null;

			// Row num base
			if ($this->getParam('nested'))
			{
				$rowNum=0;
			}
			else
			{
				if ($this->getParam('paging'))
				{
					$rowNum=round(($this->getParam('paging_page')-1)*$this->getParam('paging_pagesize'));
				}
				else
				{
					$rowNum=0;
				}
			}

			// Render
			for ($i=0;$i<sizeof($dataset);++$i)
			{
				// For convenience
				$row=&$dataset[$i];

				// Count children if nested
				if ($this->getParam('nested'))
				{
					$childcnt=0;
					foreach ($this->data as &$crow)
					{
						if ($crow[$this->getParam('nested_parentfield')]==$row[$this->model->getParam('idfield')]) $childcnt++;
					}
				}

				// Row begins
				$contentTableRows.=$this->renderContentTableRowBeginningBlock('body',$row);

				// Row number
				if ($this->getParam('rownumbers'))
				{
					// Increment
					$rowNum++;

					// Class
					$fieldClass=array();
					$fieldClass[]='list-rownum';
					if ($this->getParam('rownumbers_fieldclass')) $fieldClass[]=$this->getParam('rownumbers_fieldclass');

					// Style
					$fieldStyle=array();
					if ($this->getParam('rownumbers_fieldstyle')) $fieldStyle[]=$this->getParam('rownumbers_fieldstyle');
					if ($this->getParam('rownumbers_fieldwidth')) $fieldStyle[]='width: '.$this->getParam('rownumbers_fieldwidth');
					if ($this->getParam('rownumbers_fieldalign')) $fieldStyle[]='text-align: '.$this->getParam('rownumbers_fieldalign');

					// Cell
					$contentTableRows.='<td';
						if (!empty($fieldClass)) $contentTableRows.=' class="'.join(' ',$fieldClass).'"';
						if (!empty($fieldStyle)) $contentTableRows.=' style="'.join(';',$fieldStyle).'"';
					$contentTableRows.='>';
					if ($this->getParam('rownumbers_reverse'))
					{
						$contentTableRows.=intval($this->dataFoundRows-$rowNum+1);
					}
					else
					{
						$contentTableRows.=intval($rowNum);
					}
					$contentTableRows.='</td>';
				}

				// Fields
				$f=0;
				foreach ($this->field as $fieldObject)
				{
					// Class
					$fieldClass=array();
					$fieldClass[]='list-table-field';
					if ($this->getParam('fieldclass')) $fieldClass[]=$this->getParam('fieldclass');
					if ($fieldObject->getParam('list_fieldclass')) $fieldClass[]=$fieldObject->getParam('list_fieldclass');
					if ($fieldObject->getParam('list_nowrap')) $fieldClass[]='list-table-field-nowrap';
					if ($this->getParam('nested'))
					{
						$fieldClass[]='list-table-field-nested';
						$fieldClass[]='nested-'.$depth;
					}

					// Style
					$fieldStyle=array();
					if ($this->getParam('fieldstyle')) $fieldClass[]=$this->getParam('fieldstyle');
					if ($fieldObject->getParam('list_fieldstyle')) $fieldStyle[]=$fieldObject->getParam('list_fieldstyle');
					if ($fieldObject->getParam('list_fieldwidth')) $fieldStyle[]='width: '.$fieldObject->getParam('list_fieldwidth');
					if ($fieldObject->getParam('list_fieldalign')) $fieldStyle[]='text-align: '.$fieldObject->getParam('list_fieldalign');
					if ($fieldObject->getParam('list_nowrap')) $fieldStyle[]='white-space: nowrap';

					// Cell begins
					$contentTableRows.='<td';
					$contentTableRows.=' class="'.join(' ',$fieldClass).'"';
					$contentTableRows.=' style="'.join(';',$fieldStyle).'"';
					$contentTableRows.='>';

						// Mobile label
						if ($this->getParam('mobilelabel') || $fieldObject->getParam('field_mobilelabel'))
						{
							$mobileLabelClass=array();
							$mobileLabelClass[]='list-table-field-mobilelabel';
							if ($this->getParam('mobilelabelclass')) $mobileLabelClass[]=$this->getParam('mobilelabelclass');
							if ($fieldObject->getParam('list_mobilelabelclass')) $mobileLabelClass[]=$fieldObject->getParam('list_mobilelabelclass');
							$contentTableRows.='<label class="'.join(' ',$mobileLabelClass).'"></label>';
						}

						// Value
						$fld=oneof($fieldObject->getParam('list_field'),$fieldObject->tag);
						if (strpos($fld,' as ')!==false)
						{
							$fldArr=preg_split('/\s+as\s+/',$fld);
							$fld=$fldArr[sizeof($fldArr)-1];
						}
						elseif (strpos($fld,'->')!==false)
						{
							$fldArr=preg_split('/->/',$fld);
							$fld=$fldArr[sizeof($fldArr)-2].'_'.$fldArr[sizeof($fldArr)-1];
						}
						elseif (strpos($fld,'.')!==false)
						{
							$fldArr=preg_split('/\./',$fld);
							$fld=$fldArr[sizeof($fldArr)-1];
						}
						$contentTableRows.=$fieldObject->listValue($row[$fld],$row);

					// Cell ends
					$contentTableRows.='</td>';
				}

				// Row actions
				if (sizeof($this->rowAction))
				{
					// Class
					$fieldClass=array();
					$fieldClass[]='list-rowactions';
					if ($this->getParam('fieldclass_rowactions')) $fieldClass[]=$this->getParam('fieldclass_rowactions');

					// Cell begins
					$contentTableRows.='<td class="'.join(' ',$fieldClass).'">';

						foreach ($this->rowAction as $actionTag => $actionObject)
						{
							// Skip
							if ($this->getParam('nested'))
							{
								if ($actionObject->hasParam('nested_minlevel') && $depth<$actionObject->getParam('nested_minlevel')) continue;
								if ($actionObject->hasParam('nested_maxlevel') && $depth>$actionObject->getParam('nested_maxlevel')) continue;
							}

							// Param substitution
							$paramList=$row;
							$paramList['rownum']=$rowNum;
							$paramList['pos']=$i;
							$paramList['cnt']=sizeof($dataset);
							$paramList['foundrows']=$this->dataFoundRows;
							if ($i>0)
							{
								$paramList['prev']=$dataset[$i-1][$this->model->getParam('idfield')];
							}
							if ($i<(sizeof($dataset)-1))
							{
								$paramList['next']=$dataset[$i+1][$this->model->getParam('idfield')];
							}

							// Is enabled?
							if ($actionObject->hasParam('enabled_if'))
							{
								$enabledEval=FlaskPHP\Template\Template::parseSimpleVariables($actionObject->getParam('enabled_if'),$paramList);
								eval('$actionEnabled=('.$enabledEval.')?true:false;');
							}
							elseif ($actionObject->hasParam('enabled'))
							{
								$actionEnabled=$actionObject->getParam('enabled');
							}
							else
							{
								$actionEnabled=true;
							}

							// Action class
							$actionClass=array();
							$actionClass[]='list-rowactions-action';
							if ($this->getParam('rowactions_actionclass')) $actionClass[]=$this->getParam('rowactions_actionclass');
							if ($actionObject->getParam('actionclass')) $actionClass[]=$actionObject->getParam('actionclass');
							if ($actionEnabled)
							{
								$actionClass[]=oneof($actionObject->getParam('actionclass_enabled'),$this->getParam('rowactions_actionclass_enabled'),'enabled');
							}
							else
							{
								$actionClass[]=oneof($actionObject->getParam('actionclass_disabled'),$this->getParam('rowactions_actionclass_disabled'),'disabled');
							}

							// Action begins
							$contentTableRows.='<div class="'.join(' ',$actionClass).'">';

								if ($actionEnabled)
								{
									$contentTableRows.='<a';
									if ($actionObject->hasParam('action'))
									{
										$actionJS=FlaskPHP\Template\Template::parseSimpleVariables($actionObject->getParam('action'),$paramList);
										$contentTableRows.=' onclick="'.$actionJS.'"';
									}
									elseif ($actionObject->hasParam('url'))
									{
										$actionURL=FlaskPHP\Template\Template::parseSimpleVariables($actionObject->getParam('url'),$paramList);
										$contentTableRows.=' href="'.$actionURL.'"';
									}
									$contentTableRows.='>';
									$contentTableRows.=$actionObject->getParam('title');
									$contentTableRows.='</a>';
								}
								else
								{
									$contentTableRows.=oneof($actionObject->getParam('title_disabled'),$actionObject->getParam('title'));
								}

							// Action ends
							$contentTableRows.='</div>';
						}

					// Cell ends
					$contentTableRows.='</td>';
					$f++;
				}

				// Row ends
				$contentTableRows.=$this->renderContentTableRowEndingBlock('body',$row);

				// Children
				if ($this->getParam('nested'))
				{
					$d=$depth+1;
					$contentTableRows.=$this->renderContentTableRows($row[$this->model->getParam('idfield')],$d);
				}
			}

			// Return
			return $contentTableRows;
		}


		/**
		 *
		 *   Render list parts: list row beginning
		 *   -------------------------------------
		 *   @access public
		 *   @param string $type Row type (head, body)
		 *   @param array $row Row
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContentTableRowBeginningBlock( string $type, array &$row=null )
		{
			$rowBeginningBlock='<tr';

				$rowClass=array();
				if ($this->getParam('rowclass')) $rowClass[]=$this->getParam('rowclass');
				if (sizeof($rowClass)) $rowBeginningBlock.=' class="'.join(' ',$rowClass).'"';

			$rowBeginningBlock.='>';
			return $rowBeginningBlock;
		}


		/**
		 *
		 *   Render list parts: list row ending
		 *   ----------------------------------
		 *   @access public
		 *   @param string $type Row type (head, body)
		 *   @param array $row Row
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContentTableRowEndingBlock( string $type, array &$row=null )
		{
			return '</tr>';
		}


		/**
		 *
		 *   Render list parts: total row
		 *   ----------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContentTotal()
		{
			// Row begins
			$contentTotal=$this->renderContentTableRowBeginningBlock('total');

			// Row number
			if ($this->getParam('rownum'))
			{
				// Class
				$fieldClass=array();
				$fieldClass[]='list-rownum';
				if ($this->getParam('fieldclass_rownum')) $fieldClass[]=$this->getParam('fieldclass_rownum');

				// Cell
				$contentTotal.='<td class="'.join(' ',$fieldClass).'">';
				$contentTotal.='</td>';
			}

			// Fields
			$f=0;
			foreach ($this->field as $fieldObject)
			{
				// Class
				$fieldClass=array();
				$fieldClass[]='list-table-field';
				$fieldClass[]='list-table-total';
				if ($this->getParam('fieldclass')) $fieldClass[]=$this->getParam('fieldclass');
				if ($fieldObject->getParam('list_fieldclass')) $fieldClass[]=$fieldObject->getParam('list_fieldclass');
				if ($fieldObject->getParam('list_nowrap')) $fieldClass[]='list-table-field-nowrap';

				// Style
				$fieldStyle=array();
				if ($this->getParam('fieldstyle')) $fieldClass[]=$this->getParam('fieldstyle');
				if ($fieldObject->getParam('list_fieldstyle')) $fieldStyle[]=$fieldObject->getParam('list_fieldstyle');
				if ($fieldObject->getParam('list_fieldwidth')) $fieldStyle[]='width: '.$fieldObject->getParam('list_fieldwidth');
				if ($fieldObject->getParam('list_fieldalign')) $fieldStyle[]='text-align: '.$fieldObject->getParam('list_fieldalign');
				if ($fieldObject->getParam('list_nowrap')) $fieldStyle[]='white-space: nowrap';

				// Cell begins
				$contentTotal.='<td';
				$contentTotal.=' class="'.join(' ',$fieldClass).'"';
				$contentTotal.=' style="'.join(';',$fieldStyle).'"';
				$contentTotal.='>';

					// Mobile label
					if ($this->getParam('mobilelabel') || $fieldObject->getParam('field_mobilelabel'))
					{
						$mobileLabelClass=array();
						$mobileLabelClass[]='list-table-field-mobilelabel';
						if ($this->getParam('mobilelabelclass')) $mobileLabelClass[]=$this->getParam('mobilelabelclass');
						if ($fieldObject->getParam('list_mobilelabelclass')) $mobileLabelClass[]=$fieldObject->getParam('list_mobilelabelclass');
						$contentTotal.='<label class="'.join(' ',$mobileLabelClass).'"></label>';
					}

					// Value
					$contentTotal.=$fieldObject->listTotalValue($this->dataTotal[$fieldObject->tag],$this->dataTotal);

				// Cell ends
				$contentTotal.='</td>';
			}

			// Row actions
			if (sizeof($this->rowAction))
			{
				// Class
				$fieldClass=array();
				$fieldClass[]='list-rowactions';
				if ($this->getParam('fieldclass_rowactions')) $fieldClass[]=$this->getParam('fieldclass_rowactions');

				// Cell begins
				$contentTotal.='<td class="'.join(' ',$fieldClass).'">';
				$contentTotal.='</td>';
				$f++;
			}

			// Row ends
			$contentTotal.=$this->renderContentTableRowEndingBlock('body',$row);
			return $contentTotal;
		}


		/**
		 *
		 *   Render list parts: list table end
		 *   ---------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContentTableEnd()
		{
			$contentTableEnd='</table>';
			return $contentTableEnd;
		}


		/**
		 *
		 *   Render list parts: paging
		 *   -------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderPaging()
		{
			$listPaging='';
			if ($this->dataFoundRows && ($this->getParam('paging') || $this->getParam('showfoundrows')))
			{
				$listPaging.='<div class="list-paging">';

					// Paging
					$listPaging.='<div class="list-paging-pages">';
					if ($this->getParam('paging') && $this->getParam('paging_totalpages')>1)
					{
						$listPaging.='<div class="list-paging-pagination ui pagination menu">';
						
							// Get paging info
							$totalPages=$this->getParam('paging_totalpages');
							$currentPage=$this->getParam('paging_page');
			
							// In case of a gazillion pages, let's do a trick
							$pageMin=0;
							$pageMax=$totalPages;
							if ($totalPages>20)
							{
								if ($currentPage==$totalPages)
								{
									$pageMin=$this->getParam('paging_page')-2;
								}
								elseif ($currentPage>3)
								{
									$pageMin=$currentPage-1;
								}
								if ($currentPage==1)
								{
									$pageMax=3;
								}
								elseif ($currentPage<($totalPages-3))
								{
									$pageMax=$currentPage+1;
								}
							}
			
							// Build paging
							$skip1=false;
							$skip2=false;
							for($i=1;$i<=$totalPages;++$i)
							{
								// First
								if ($i<($totalPages-2) && $i>$pageMax)
								{
									if (!$skip1)
									{
										$listPaging.='<div class="list-paging-skip disabled item">...</div>';
										$skip1=true;
									}
									continue;
								}
			
								// Last
								if ($i>3 && $i<$pageMin)
								{
									if (!$skip2)
									{
										$listPaging.='<div class="list-paging-skip disabled item">...</div>';
										$skip2=true;
									}
									continue;
								}
			
								if ($i==$currentPage)
								{
									$listPaging.='<div class="list-paging-page active item">'.intval($i).'</div>';
								}
								else
								{
									$listPaging.='<a class="list-paging-page item" onclick="Flask.List.getContent(\''.$this->getParam('id').'\',\''.$this->buildURL().'\',\'page\',{page:'.intval($i).'})">'.intval($i).'</a>';
								}
							}

						$listPaging.='</div>';
					}
					$listPaging.='</div>';

					// Found rows
					$listPaging.='<div class="list-paging-foundrows">';
					if ($this->getParam('showfoundrows'))
					{
						$listPaging.='[[ FLASK.LIST.Message.FoundRows : foundrows='.intval($this->dataFoundRows).' ]]';
					}
					$listPaging.='</div>';

				$listPaging.='</div>';
			}

			// Set and return
			return $listPaging;
		}


		/**
		 *
		 *   Render list parts: return link
		 *   ------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderReturnLink()
		{
			$returnLink=null;
			if ($this->hasParam('returnlink'))
			{
				$returnLink='<div class="returnlink"><a href="'.$this->getParam('returnlink').'"><i class="reply icon"></i>'.oneof($this->getParam('returnlink_title'),'[[ FLASK.LIST.Return ]]').'</a></div>';
				$this->template->set('list_returnlink',$returnLink);
			}
			return $returnLink;
		}


		/**
		 *
		 *   Render list parts: JavaScript
		 *   -----------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderJS()
		{
			$listJS=null;
			if (mb_strlen($this->js))
			{
				$listJS='<script language="JavaScript">';
				$listJS.=$this->js;
				$listJS.='</script>';
				$this->template->set('list_js',$listJS);
			}
			return $listJS;
		}


		/**
		 *
		 *   Run action and return response
		 *   ------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return FlaskPHP\Response\ResponseInterface
		 *
		 */

		public function runAction()
		{
			try
			{
				// Init
				$this->initList();
				$this->initFilters();
				$this->initFields();
				$this->initActions();
				$this->setDefaults();

				// Do some checks
				if (!sizeof($this->field)) throw new FlaskPHP\Exception\Exception('No fields defined.');

				// Set filters
				$this->applyFilters();

				// Set paging
				$this->applyPaging();

				// Set sorting
				$this->applySorting();

				// Load data
				while(true)
				{
					$this->dataLoad();
					if ($this->checkPaging()) break;
				}

				// Render content only
				if ($this->getParam('returnfilter') || $this->getParam('returncontent'))
				{
					$response=new \stdClass();
					$response->status=1;
					if ($this->getParam('returnfilter')) $response->filter=$this->renderFilters(false);
					if ($this->getParam('returncontent')) $response->content=$this->renderContent(false);;
					if ($this->getParam('returnscrolltop')) $response->scrolltop=1;
					return new FlaskPHP\Response\JSONResponse($response);
				}

				// Render full list view
				$listHTML=$this->renderListHTML();
				if ($this->getParam('returnhtml'))
				{
					return $listHTML;
				}
				else
				{
					$Response=new FlaskPHP\Response\HTMLResponse();
					if ($this->getParam('responsetemplate')) $Response->setTemplate($this->getParam('responsetemplate'));
					if ($this->getParam('pagetitle')) $Response->setPageTitle($this->getParam('pagetitle'),$this->getParam('pagetitle_append'),$this->getParam('pagetitle_separator'));
					$Response->setContent($listHTML);
					return $Response;
				}
			}
			catch (\Exception $e)
			{
				// Ajax error
				if (Flask()->Request->isXHR() && !$this->getParam('returnhtml'))
				{
					$response=new \stdClass();
					$response->status=2;
					$response->error=$e->getMessage();
					return new FlaskPHP\Response\JSONResponse($response);
				}

				// HTML
				else
				{
					if ($this->getParam('returnhtml'))
					{
						return '<div class="error">'.htmlspecialchars($e->getMessage()).'</div>';
					}
					else
					{
						$response=new FlaskPHP\Response\HTMLResponse();
						if ($this->getParam('responsetemplate')) $response->setTemplate($this->getParam('responsetemplate'));
						$response->setContent('<h1>[[ FLASK.COMMON.Error ]]</h1><div class="error">'.htmlspecialchars($e->getMessage()).'</div>');
						return $response;
					}
				}
			}
		}


	}


?>
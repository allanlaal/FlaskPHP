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
			$loadListParam->addField($this->model->getParam('idfield'));
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
						oneof($this->getParam('list_sort_order'),'asc'),
						$loadListParam
					);
				}
				elseif (empty($loadListParam->queryOrderBy))
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
				$loadListParam->addLimit($this->getParam('paging_pagesize'),(($this->getParam('paging_currentpage')-1)*$this->getParam('paging_pagesize')));
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
		 *   @return FlaskPHP\Response\ResponseInterface
		 *
		 */

		public function applyFilters()
		{
			// Clear filters
			if (Flask()->Request->postVar('action')=='filter_clear')
			{
				// Clear
				Flask()->Session->set('list.'.$this->getParam('id').'.filter',null);

				// Reload
				return new FlaskPHP\Response\RedirectResponse($this->buildURL());
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

				// Reload
				return new FlaskPHP\Response\RedirectResponse($this->buildURL());
			}

			// No action
			return null;
		}


		/**
		 *
		 *   Set sorting
		 *   -----------
		 *   @access public
		 *   @throws \Exception
		 *   @return FlaskPHP\Response\ResponseInterface
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

				// Reload
				return new FlaskPHP\Response\RedirectResponse($this->buildURL());
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
									$sdFieldObject->getParam('list_sort_default'),
									'asc'
								));
							}
							if ($sdFieldObject->getParam('list_sort_default'))
							{
								$this->setParam('list_sort_field',$sdFieldObject->tag);
								$this->setParam('list_sort_dir',oneof(
									$sdFieldObject->getParam('list_sort_default'),
									'asc'
								));
							}
						}
					}
					break;
				}
			}

			// No action
			return null;
		}


		/**
		 *
		 *   Set paging
		 *   ----------
		 *   @access public
		 *   @throws \Exception
		 *   @return FlaskPHP\Response\ResponseInterface
		 *
		 */

		public function applyPaging()
		{
			// Set paging
			if ($this->getParam('list_paging') && Flask()->Request->postVar('action')=='page')
			{
				Flask()->Session->set('list.'.$this->getParam('id').'.page',null);
				if (intval(Flask()->Request->postVar('page')))
				{
					Flask()->Session->set('list.'.$this->getParam('id').'.page',intval(Flask()->Request->postVar('page')));
				}

				// Reload
				return new FlaskPHP\Response\RedirectResponse($this->buildURL());
			}

			// No action
			return null;
		}


		/**
		 *
		 *   Check paging post-dataload
		 *   --------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return FlaskPHP\Response\ResponseInterface
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
					return new FlaskPHP\Response\RedirectResponse($this->buildURL());
				}

				// Set these as parameters for convenience
				$this->setParam('paging_page',$currentPage);
				$this->setParam('paging_totalpages',$totalPages);
			}

			// No action
			return null;
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

				// Do we have this field?
				if (!array_key_exists($fieldTag,$this->model->_field)) throw new FlaskPHP\Exception\InvalidParameterException($fieldTag.': no such field defined in model.');

				// Link
				$this->field[$fieldTag]=&$this->model->_field[$fieldTag];
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
			$this->renderFilters();
			$this->renderContent();
			$this->renderPaging();
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

			$listGlobalActions='<ul class="list-globalactions">';
			foreach ($this->globalAction as $actionTag => $actionObject)
			{
				$listGlobalActions.='<li class="list-globalactions-action">';
				if ($actionObject->hasParam('icon')) $listGlobalActions.='<span class="icon-'.$actionObject->getParam('icon').'">';
				$listGlobalActions.='<a onclick="'.$actionObject->getParam('action').'">';
				$listGlobalActions.=$actionObject->getParam('title');
				$listGlobalActions.='</a>';
				$listGlobalActions.='</span>';
				$listGlobalActions.='</li>';
			}
			$listGlobalActions.='</ul>';

			$this->template->set('list_globalaction',$listGlobalActions);
			return $listGlobalActions;
		}


		/**
		 *
		 *   Render list parts: filters
		 *   --------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderFilters()
		{
			// Check & init
			if (!sizeof($this->filter)) return null;
			$listFilter='';

			// Wrapper
			$listFilter.='<div class="list-filter">';
			$listFilter.='<form method="post" action="'.$this->buildURL().'">';

				// Filters
				$listFilter.='<div class="list-filter-filters">';
				foreach ($this->filter as $filterTag => $filterObject)
				{
					$listFilter.=$filterObject->renderFilter();
				}
				$listFilter.='</div>';

				// Submit
				$listFilter.='<div class="list-filter-submit">';
				$listFilter.='<button type="submit" id="filter_submit" name="action" value="filter_submit">[[ FLASK.LIST.Filter.Submit ]]</button>';
				$listFilter.='<button type="submit" id="filter_clear" name="action" value="filter_clear">[[ FLASK.LIST.Filter.Clear ]]</button>';
				$listFilter.='</div>';

			// Wrapper ends
			$listFilter.='</form>';
			$listFilter.='</div>';

			// Set and return
			$this->template->set('list_filter',$listFilter);
			return $listFilter;
		}


		/**
		 *
		 *   Render list parts: list content
		 *   -------------------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContent()
		{
			// No filter?
			if ($this->getParam('filterrequired') && !$this->filterApplied)
			{
				$listContent='<p>'.oneof($this->getParam('filterrequired_message'),'[[ FLASK.LIST.Message.FilterRequired ]]').'</p>';
			}

			// No results
			elseif (!sizeof($this->data))
			{
				if ($this->filterApplied)
				{
					$listContent='<p>'.oneof($this->getParam('noresults_message_filtered'),'[[ FLASK.LIST.Message.NoResults.Filtered ]]').'</p>';
				}
				else
				{
					$listContent='<p>'.oneof($this->getParam('noresults_message'),'[[ FLASK.LIST.Message.NoResults ]]').'</p>';
				}
			}

			// Render content
			else
			{
				$listContent=$this->renderContentTableBegin();
				$listContent.=$this->renderContentTableHeader();
				$listContent.=$this->renderContentTableBody();
				$listContent.=$this->renderContentTableEnd();
			}

			// Set and return
			$this->template->set('list_content',$listContent);
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
			$tableClass[]='table';
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
			if ($this->getParam('rownum'))
			{
				// Class
				$fieldClass=array();
				$fieldClass[]='list-table-header';
				$fieldClass[]='list-rownum';
				if ($this->getParam('headerclass_rownum')) $fieldClass[]=$this->getParam('headerclass_rownum');
				if ($this->getParam('fieldclass_rownum')) $fieldClass[]=$this->getParam('fieldclass_rownum');

				// Cell
				$contentTableHeader.='<th class="'.join(' ',$fieldClass).'"></th>';
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

				// Cell
				$contentTableHeader.='<th';
					if (sizeof($fieldClass)) $contentTableHeader.='  class="'.join(' ',$fieldClass).'"';
					if (sizeof($fieldStyle)) $contentTableHeader.='  style="'.join(';',$fieldStyle).'"';
				$contentTableHeader.='>';
				if ($fieldSortable)
				{
					$contentTableHeader.='<a class="'.oneof($this->getParam('list_header_sortclass_link'),'list-table-field-sort').'" onclick="Flask.doPostSubmit(\''.$this->buildURL().'\',{action:\'sort\',sort_field:\''.jsencode($fieldObject->tag).'\',sort_dir:\''.($sortField==$fieldObject->tag?($sortDir=='asc'?'desc':'asc'):oneof($this->getParam('list_sort_defaultorder'),'asc')).'\'})">';
				}
				$contentTableHeader.=htmlspecialchars($fieldObject->getParam('title'));
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
				if ($this->getParam('rownum'))
				{
					// Increment
					$rowNum++;

					// Class
					$fieldClass=array();
					$fieldClass[]='list-rownum';
					if ($this->getParam('fieldclass_rownum')) $fieldClass[]=$this->getParam('fieldclass_rownum');

					// Cell
					$contentTableRows.='<td class="'.join(' ',$fieldClass).'">';
					$contentTableRows.=intval($rowNum);
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
					if ($this->getParam('nested'))
					{
						$nestedPadding=oneof($this->getParam('nested_padding'),'25px');
						$fieldStyle='padding-left: '.$nestedPadding;
					}

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
						if (strpos($fld,'->')!==false)
						{
							$fldArr=preg_split('/->/',$fld);
							$fld=$fldArr[sizeof($fldArr)-2].'_'.$fldArr[sizeof($fldArr)-1];
						}
						elseif (strpos($fld,' ')!==false)
						{
							$fldArr=preg_split('/ /',$fld);
							$fld=$fldArr[sizeof($fldArr)-1];
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
								}
								$contentTableRows.=$actionObject->getParam('title');
								if ($actionEnabled)
								{
									$contentTableRows.='</a>';
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
					if ($this->getParam('paging') && $this->getParam('paging_totalpages')>1)
					{
						$listPaging.='<div class="list-paging-pages">';
						$listPaging.='<ul>';
						
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
										$listPaging.='<li class="list-paging-skip">...</li>';
										$skip1=true;
									}
									continue;
								}
			
								// Last
								if ($i>3 && $i<$pageMin)
								{
									if (!$skip2)
									{
										$listPaging.='<li class="list-paging-skip">...</li>';
										$skip2=true;
									}
									continue;
								}
			
								if ($i==$currentPage)
								{
									$listPaging.='<li class="list-paging-page list-paging-currentpage"><a>'.intval($i).'</a></li>';
								}
								else
								{
									$listPaging.='<li class="list-paging-page"><a onclick="Flask.doPostSubmit(\''.$this->buildURL().'\',{action:\'page\',page:'.intval($i).'})">'.intval($i).'</a></li>';
								}
							}

						$listPaging.='</ul>';
						$listPaging.='</div>';
					}

					// Found rows
					if ($this->getParam('showfoundrows'))
					{
						$listPaging.='<div class="list-paging-foundrows">';
						$listPaging.='[[ FLASK.LIST.Message.FoundRows : foundrows='.intval($this->dataFoundRows).' ]]';
						$listPaging.='</div>';
					}

				$listPaging.='</div>';
			}

			// Set and return
			$this->template->set('list_paging',$listPaging);
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
				$returnLink='<div class="returnlink"><a href="'.$this->getParam('returnlink').'"><span class="icon-back"></span> '.oneof($this->getParam('returnlink_title'),'[[ FLASK.LIST.Return ]]').'</a></div>';
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

				// Set filters
				$res=$this->applyFilters();
				if ($res!==null) return $res;

				// Set paging
				$res=$this->applyPaging();
				if ($res!==null) return $res;

				// Set sorting
				$res=$this->applySorting();
				if ($res!==null) return $res;

				// Load data
				$this->dataLoad();

				// Check paging
				$res=$this->checkPaging();
				if ($res!==null) return $res;

				// Render list view
				$response=new FlaskPHP\Response\HTMLResponse();
				$response->setContent($this->renderListHTML());
				return $response;
			}
			catch (\Exception $e)
			{
				// Ajax error
				if (Flask()->Request->isXHR())
				{
					$response=new \stdClass();
					$response->status=2;
					$response->error=$e->getMessage();
					return new FlaskPHP\Response\JSONResponse($response);
				}

				// HTML
				else
				{
					$response=new FlaskPHP\Response\HTMLResponse();
					$response->setContent('<h1>[[ FLASK.COMMON.Error ]]</h1><div class="error">'.htmlspecialchars($e->getMessage()).'</div>');
					return $response;
				}
			}
		}


	}


?>
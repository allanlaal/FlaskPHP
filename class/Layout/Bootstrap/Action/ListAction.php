<?php


	/**
	 *
	 *   FlaskPHP
	 *   Layout extensions: Bootstrap 4
	 *   ------------------------------
	 *   The list view action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Layout\Bootstrap\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class ListAction extends FlaskPHP\Action\ListAction
	{


		/**
		 *
		 *   Set filter width
		 *   -----------------
		 *   @access public
		 *   @param int $filterWidth Filter width
		 *   @return ListAction
		 *
		 */

		public function setFilterWidth( int $filterWidth )
		{
			$this->setParam('filterwidth',$filterWidth);
			return $this;
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

			$listGlobalActions='<div class="container-fluid my-2"><div class="row text-right"><div class="col-12 mx-0 px-0 text-right">';
			foreach ($this->globalAction as $actionTag => $actionObject)
			{
				$listGlobalActions.='<div class="list-globalactions-action d-inline ml-2">';
				if ($actionObject->hasParam('icon')) $listGlobalActions.='<span class="icon-'.$actionObject->getParam('icon').'">';
				$listGlobalActions.='<a onclick="'.$actionObject->getParam('action').'">';
				$listGlobalActions.=$actionObject->getParam('title');
				$listGlobalActions.='</a>';
				$listGlobalActions.='</span>';
				$listGlobalActions.='</div>';
			}
			$listGlobalActions.='</div></div></div>';

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
			$listFilter.='<div class="list-filter card card-body mb-4"><div class="container-fluid">';
			$listFilter.='<form method="post" action="'.$this->buildURL().'">';

				// Filters
				$listFilter.='<div class="list-filter-filters row my-2">';
				foreach ($this->filter as $filterTag => $filterObject)
				{
					$listFilter.=$filterObject->renderFilter();
				}
				$listFilter.='</div>';

				// Submit
				$listFilter.='<div class="list-filter-submit row my-2 mt-4"><div class="col text-right">';
				$listFilter.='<button class="btn btn-primary mx-1" type="submit" id="filter_submit" name="action" value="filter_submit">[[ FLASK.LIST.Filter.Submit ]]</button>';
				$listFilter.='<button class="btn btn-secondary mx-1" type="submit" id="filter_clear" name="action" value="filter_clear">[[ FLASK.LIST.Filter.Clear ]]</button>';
				$listFilter.='</div></div>';

			// Wrapper ends
			$listFilter.='</form>';
			$listFilter.='</div></div>';

			// Set and return
			$this->template->set('list_filter',$listFilter);
			return $listFilter;
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
			// Set header classes
			if ($this->hasParam('headerclass'))
			{
				$this->setParam('headerclass',$this->getParam('headerclass').' border-top-0');
			}
			else
			{
				$this->setParam('headerclass','border-top-0');
			}
			if ($this->hasParam('headerclass_rownum'))
			{
				$this->setParam('headerclass_rownum',$this->getParam('headerclass_rownum').' border-top-0');
			}
			else
			{
				$this->setParam('headerclass_rownum','border-top-0');
			}
			if ($this->hasParam('headerclass_rowactions'))
			{
				$this->setParam('headerclass_rowactions',$this->getParam('headerclass_rowactions').' border-top-0');
			}
			else
			{
				$this->setParam('headerclass_rowactions','border-top-0');
			}

			// Set sorting class
			if ($this->hasParam('list_header_sortclass_sortable'))
			{
				$this->setParam('list_header_sortclass_sortable',$this->getParam('list_header_sortclass_sortable').' list-table-field-sortable text-muted');
			}
			else
			{
				$this->setParam('list_header_sortclass_sortable','list-table-field-sortable text-muted');
			}

			// Render
			return parent::renderContentTableHeader();
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
				$listPaging.='<div class="container-fluid"><div class="row mt-4">';

					// Paging
					$listPaging.='<div class="list-paging col-12 col-md-6 text-left">';
					if ($this->getParam('paging') && $this->getParam('paging_totalpages')>1)
					{
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
					}
					$listPaging.='</div>';

					// Found rows
					$listPaging.='<div class="list-paging-foundrows col-12 col-md-6 text-right text-muted">';
					if ($this->getParam('showfoundrows'))
					{
						$listPaging.='[[ FLASK.LIST.Message.FoundRows : foundrows='.intval($this->dataFoundRows).' ]]';
					}
					$listPaging.='</div>';

				$listPaging.='</div></div>';
			}

			// Set and return
			$this->template->set('list_paging',$listPaging);
			return $listPaging;
		}


	}


?>
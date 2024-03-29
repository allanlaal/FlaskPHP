<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The tabbed view action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class TabbedViewAction extends ActionInterface
	{


		/**
		 *   Tabs
		 *   @var array
		 *   @access public
		 */

		public $tab = [];


		/**
		 *
		 *   Init the view
		 *   -------------
		 *   @throws \Exception
		 *   @return void|FlaskPHP\Response\ResponseInterface
		 *
		 */

		public function initView()
		{
			// This should be implemented in the subclass.
		}


		/**
		 *
		 *   Init the tabs
		 *   -------------
		 *   @throws \Exception
		 *   @return void|FlaskPHP\Response\ResponseInterface
		 *
		 */

		public function initTabs()
		{
			// This should be implemented in the subclass.
		}


		/**
		 *
		 *   Add a tab
		 *   ---------
		 *   @access public
		 *   @param string $tabTag Tab tag
		 *   @return TabbedViewTab
		 *   @throws \Exception
		 *
		 */

		public function addTab( $tabTag )
		{
			// Check
			if (array_key_exists($tabTag,$this->tab)) throw new FlaskPHP\Exception\InvalidParameterException('Tab '.$tabTag.' already exists.');

			// Create tab
			$this->tab[$tabTag]=new TabbedViewTab();
			$this->tab[$tabTag]->tag=$tabTag;

			// Create backreference
			$this->tab[$tabTag]->viewObject=$this;

			// Return tab
			return $this->tab[$tabTag];
		}


		/**
		 *
		 *   Set title
		 *   ---------
		 *   @access public
		 *   @param string $title List title
		 *   @return \Codelab\FlaskPHP\Action\TabbedViewAction
		 *
		 */

		public function setTitle( string $title )
		{
			$this->setParam('title',$title);
			return $this;
		}


		/**
		 *
		 *   Set return link
		 *   ---------------
		 *   @access public
		 *   @param string $returnLink Return link
		 *   @param string $title Link title
		 *   @return \Codelab\FlaskPHP\Action\TabbedViewAction
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
		 *   Render title
		 *   ------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderTitle()
		{
			$title='';
			if ($this->hasParam('title'))
			{
				$title.='<h1>'.htmlspecialchars($this->getParam('title')).'</h1>';
			}
			return $title;
		}


		/**
		 *
		 *   Render tab bar
		 *   --------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderTabBar()
		{
			$tabBar='<div class="tabbedview-tabs ui tabular menu">';
			$t=0;
			foreach ($this->tab as $tabTag => $tabObject)
			{
				$tabBar.='<a id="tab_'.$tabTag.'" class="tabbedview-tab item'.($t==0?' active':'').'" onclick="Flask.Tab.selectTab(\''.htmlspecialchars($tabTag).'\');">'.$tabObject->getTitle().'</a>';
				$t++;
			}
			$tabBar.='</div>';
			return $tabBar;
		}


		/**
		 *
		 *   Render tab content area
		 *   -----------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderTabContent()
		{
			$t=0;
			$tabContent='<div class="tabbedview-content">';
			foreach ($this->tab as $tabTag => $tabObject)
			{
				if (!$t)
				{
					$tabContent.='<div id="content_'.$tabTag.'" class="tabbedview-tabcontent">';
					$tabContent.=$this->renderTab($tabTag);
					$tabContent.='</div>';
				}
				else
				{
					$tabContent.='<div id="content_'.$tabTag.'" class="tabbedview-tabcontent" rel="'.$this->buildURL().'/get='.$tabTag.'/" data-displaytrigger="'.($tabObject->hasParam('selecttrigger')?$tabObject->getParam('selecttrigger'):'').'"></div>';
				}
				$t++;
			}
			$tabContent.='</div>';
			return $tabContent;
		}


		/**
		 *
		 *   Render return link
		 *   ------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderReturnLink()
		{
			$returnLink='';
			if ($this->hasParam('returnlink'))
			{
				$returnLink='<div class="returnlink"><a href="'.$this->getParam('returnlink').'"><i class="reply icon"></i>'.oneof($this->getParam('returnlink_title'),'[[ FLASK.LIST.Return ]]').'</a></div>';
			}
			return $returnLink;
		}


		/**
		 *
		 *   Render tab
		 *   -----------
		 *   @access public
		 *   @param string $tabTag Tab tag
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderTab( string $tabTag )
		{
			try
			{
				// Check
				if (!array_key_exists($tabTag,$this->tab)) throw new FlaskPHP\Exception\InvalidParameterException('Tab '.$tabTag.' not defined.');

				// Method
				$tabObject=$this->tab[$tabTag];
				$handlerFunction=$tabObject->getParam('handlerfunction');
				if (!method_exists($this,$handlerFunction)) throw new FlaskPHP\Exception\InvalidParameterException('Handler function '.$handlerFunction.' not defined in the tabbed view class.');

				// Render
				$tabContent=call_user_func(array($this,$handlerFunction),$tabObject->getParam('handlerfunction_param'));
				return $tabContent;
			}
			catch (\Exception $e)
			{
				return '<p>ERROR: '.$e->getMessage().'</p>';
			}
		}


		/**
		 *
		 *   Standard tabs: log
		 *   ------------------
		 *   @access public
		 *   @param array $param Parameters
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function getLogTab( $param=null )
		{
			// Create list
			$logList=new ListAction();
			$logList->setReturnHTML(true);
			$logList->setPaging(true,100);

			// ID
			if (is_object($this->model))
			{
				$logList->setParam('id',$this->model->getParam('table').'_log');
			}
			else
			{
				$logList->setParam('id',uniqid());
			}

			// Data
			$logList->model=new FlaskPHP\Log\Log();
			$loadParam=Flask()->DB->getQueryBuilder();
				$loadParam->addTable(Flask()->User->getParam('table'),'flask_log.user_oid='.Flask()->User->getParam('table').'.'.Flask()->User->getParam('idfield'));
				$loadParam->addWhere('flask_log.ref_oid='.intval($this->model->{$this->model->getParam('idfield')}));
				$loadParam->addOrderBy('flask_log.log_tstamp desc');
			$logList->setLoadParam($loadParam);

			// Filters
			$logList->setFilterColumnWidth('three');

			$f=$logList->addFilter('log_tstamp',new FlaskPHP\Action\DateRangeListFilter());
			$f->setTitle('[[ FLASK.COMMON.Date ]]');
			$f->setDateTimeField(true);

			$f=$logList->addFilter('user_name',new FlaskPHP\Action\TextListFilter());
			$f->setTitle('[[ FLASK.LOG.Fld.User ]]');
			$f->setField(Flask()->User->getParam('table').'.'.Flask()->User->getParam('loginfield_name'));

			$f=$logList->addFilter('log_entry',new FlaskPHP\Action\TextListFilter());
			$f->setTitle('[[ FLASK.LOG.Fld.LogEntry ]]');

			// Add fields
			$f=$logList->addField('log_tstamp',new FlaskPHP\Field\DateField());
			$f->setTitle('[[ FLASK.LOG.Fld.TimeStamp ]]');
			$f->setListShowTime(true);
			$f->setListFieldWidth('15%');

			$f=$logList->addField(Flask()->User->getParam('table').'.'.Flask()->User->getParam('loginfield_name').' as user_name',new FlaskPHP\Field\TextField());
			$f->setTitle('[[ FLASK.LOG.Fld.User ]]');
			$f->setListFieldWidth('20%');

			$f=$logList->addField('log_entry',new FlaskPHP\Field\TextField());
			$f->setTitle('[[ FLASK.LOG.Fld.LogEntry ]]');
			$f->setListFieldWidth('60%');

			$f=$logList->addField('log_data',new FlaskPHP\Field\LogDetailsField());
			$f->setTitle('');
			$f->setListFieldWidth('5%');
			$f->setListFieldAlign('right');

			// Render list
			return $logList->runAction();
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
				// Init view
				$res=$this->initView();
				if ($res!==null) return $res;

				// Init tabs
				$res=$this->initTabs();
				if ($res!==null) return $res;
				if (!sizeof($this->tab)) throw new FlaskPHP\Exception\Exception('No tabs defined.');

				// Set defaults
				$this->setDefaults();

				// Get tab contents
				if (Flask()->Request->uriVar('get'))
				{
					// Get content
					$tabContent=$this->renderTab(Flask()->Request->uriVar('get'));

					// Compose response
					if (is_object($tabContent) && $tabContent instanceof FlaskPHP\Response\ResponseInterface)
					{
						return $tabContent;
					}
					else
					{
						$response=new \stdClass();
						$response->status=1;
						$response->content=$tabContent;
						return new FlaskPHP\Response\JSONResponse($response);
					}
				}

				// Init render
				$tabbedView='';

				// Title
				$tabbedView.=$this->renderTitle();

				// Tabs
				$tabbedView.=$this->renderTabBar();

				// Tab content
				$tabbedView.=$this->renderTabContent();

				// Return link
				$tabbedView.=$this->renderReturnLink();

				// Return
				$Response=new FlaskPHP\Response\HTMLResponse();
				if ($this->getParam('responsetemplate')) $Response->setTemplate($this->getParam('responsetemplate'));
				if ($this->getParam('pagetitle')) $Response->setPageTitle($this->getParam('pagetitle'),$this->getParam('pagetitle_append'),$this->getParam('pagetitle_separator'));
				$Response->setContent($tabbedView);
				return $Response;
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
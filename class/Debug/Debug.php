<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The FlaskPHP debug provider class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Debug;
	use Codelab\FlaskPHP as FlaskPHP;


	class Debug
	{


		/**
		 *   Debugger enabled
		 *   @var boolean
		 *   @access public
		 */

		public $debugOn = false;


		/**
		 *   Profiler enabled
		 *   @var boolean
		 *   @access public
		 */

		public $profilerOn = false;


		/**
		 *   Output to file
		 *   @var string
		 *   @access public
		 */

		public $debugFile = null;


		/**
		 *   Log all queries?
		 *   @var bool
		 *   @access public
		 */

		public $debugLogQueries = false;


		/**
		 *   Debug messages
		 *   @var array
		 *   @access public
		 */

		public $debugMessages = array();


		/**
		 *   Profiler data
		 *   @var array
		 *   @access public
		 */

		public $debugProfilerData = null;


		/**
		 *   Are we running in a dev environment?
		 *   @var bool
		 *   @access public
		 */

		public $devEnvironment = false;


		/**
		 *   Custom error display function: XML
		 *   @var string
		 *   @access public
		 */

		public $outputErrorXML = null;


		/**
		 *   Custom error display function: JSON
		 *   @var string
		 *   @access public
		 */

		public $outputErrorJSON = null;


		/**
		 *   Custom error display function: HTML
		 *   @var string
		 *   @access public
		 */

		public $outputErrorHTML = null;


		/**
		 *   Custom error display function: plain text
		 *   @var string
		 *   @access public
		 */

		public $outputErrorPlain = null;


		/**
		 *
		 *   Init debugger
		 *   -------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initDebug()
		{
			// Init parameters
			$this->debugOn=(Flask()->Config->get('dev.debug')?true:false);
			$this->profilerOn=(Flask()->Config->get('dev.profiler')?Flask()->Config->get('dev.profiler'):false);
			$this->debugFile=(Flask()->Config->get('dev.debugfile')?Flask()->Config->get('dev.debugfile'):false);
			$this->devEnvironment=(Flask()->Config->get('dev.dev')?true:false);

			// Init error handler functions
			if (Flask()->Config->get('errorhandler.xml')) $this->outputErrorXML=Flask()->Config->get('errorhandler.xml');
			if (Flask()->Config->get('errorhandler.json')) $this->outputErrorJSON=Flask()->Config->get('errorhandler.json');
			if (Flask()->Config->get('errorhandler.html')) $this->outputErrorHTML=Flask()->Config->get('errorhandler.html');
			if (Flask()->Config->get('errorhandler.plain')) $this->outputErrorPlain=Flask()->Config->get('errorhandler.plain');

			// Init profiler array
			$this->debugProfilerData=array(
				'totalQueryCount' => 0,
				'totalQueryCountSelect' => 0,
				'totalQueryCountUpdate' => 0,
				'totalQueryTime'  => 0
			);
		}


		/**
		 *
		 *   Add a debug message
		 *   -------------------
		 *   @access public
		 *   @param string $debugTitle Message title
		 *   @param string $debugMessage Message content
		 *   @param int $debugMessageType Message type
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function addMessage( $debugTitle, $debugMessage, $debugMessageType=1 )
		{
			// Outputting to file?
			if ($this->debugFile)
			{
				$FH=fopen($this->debugFile,'a+');
				if (!sizeof($this->debugMessages))
				{
					fwrite($FH,"\n\n\n");
					fwrite($FH,"---------------------------------------------------------------------------\n");
					fwrite($FH,'Time: '.date("c")."\n");
					fwrite($FH,'requestURI: '.Flask()->REQUEST->requestURI."\n");
					fwrite($FH,'requestScript: '.Flask()->REQUEST->requestScript."\n");
					fwrite($FH,'requestHandler: '.Flask()->REQUEST->requestHandler."\n");
					fwrite($FH,'requestLang: '.Flask()->REQUEST->requestLang."\n");
					fwrite($FH,"\n");
				}
				fwrite($FH,$debugTitle.":\n");
				fwrite($FH,str_replace('<br/>',"\n",str_replace('<br>',"\n",$debugMessage))."\n\n");
				fclose($FH);
			}

			// No debugging
			if (!$this->debugFile && !$this->debugOn) return;

			// Write to array
			$this->debugMessages[]=array(
				'title'   => $debugTitle,
				'message' => $debugMessage,
				'type'    => $debugMessageType
			);
		}


		/**
		 *
		 *   Log profiler info
		 *   -----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function logProfilerInfo()
		{
			// Only when profiler is enabled
			if (!$this->profilerOn) return;

			// Log main page profiler info
			$pcols=array();
			$pcols['pageprofiler_tstamp']=date('Y-m-d H:i:s');
			$pcols['pageprofiler_request_id']=Flask()->requestID;
			$pcols['pageprofiler_user_oid']=Flask()->User->{Flask()->User->getParam('idfield')};
			$pcols['pageprofiler_request_method']=$_SERVER['REQUEST_METHOD'];
			$pcols['pageprofiler_request_uri']=$_SERVER['REQUEST_URI'];
			$pcols['pageprofiler_requesttime']=Flask()->getRequestTime();
			$pcols['pageprofiler_dbquerytime']=$this->debugProfilerData['totalQueryTime'];
			$pcols['pageprofiler_dbquerycnt']=intval($this->debugProfilerData['totalQueryCount']);
			$pcols['pageprofiler_dbquerycnt_select']=intval($this->debugProfilerData['totalQueryCountSelect']);
			$pcols['pageprofiler_dbquerycnt_update']=intval($this->debugProfilerData['totalQueryCountUpdate']);
			$pcols['pageprofiler_peakmemoryusage']=memory_get_peak_usage(true);
			Flask()->DB->queryInsert('flask_pageprofiler',$pcols);

			// Log queries
			if (intval($this->profilerOn)===2 && is_array($this->debugProfilerData['queryInfo']) && sizeof($this->debugProfilerData['queryInfo']))
			{
				foreach ($this->debugProfilerData['queryInfo'] as $q => $query)
				{
					$qcols=array();
					$qcols['pageprofiler_query_tstamp']=$pcols['pageprofiler_tstamp'];
					$qcols['pageprofiler_query_request_id']=Flask()->requestID;
					$qcols['pageprofiler_query_no']=intval($q+1);
					$qcols['pageprofiler_query_sql']=trim($query['sql']);
					$qcols['pageprofiler_query_time']=$query['time'];
					$qcols['pageprofiler_query_affectedrows']=$query['time'];
					$qcols['pageprofiler_query_explain']=((is_array($query['explain']) && sizeof($query['explain']))?var_dump_str($query['explain']):'');
					Flask()->DB->queryInsert('flask_pageprofiler_query',$qcols);
				}
			}
		}


		/**
		 *
		 *   Get debug output
		 *   ----------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function getDebugOutput()
		{
			// Only when debug is enabled
			if (!$this->debugOn) return '';

			// Wrapper begins
			$c='<div id="flask-debuginfo" style="background: #333333; padding: 20px; color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 12px">';
			$c.='<style>';
			$c.=' #flask-debuginfo table.flask-debuginfo-table { width: 100%; border-collapse: collapse; } ';
			$c.=' #flask-debuginfo table.flask-debuginfo-table th { border: 1px #919191 solid; padding: 5px; 10px; background: #444444; font-weight; bold } ';
			$c.=' #flask-debuginfo table.flask-debuginfo-table td { border: 1px #919191 solid; padding: 5px; 10px; } ';
			$c.=' #flask-debuginfo table.flask-debuginfo-table td.info-name { width: 10%; white-space: nowrap; padding: 4px 20px; text-align: center; } ';
			$c.=' #flask-debuginfo table.flask-debuginfo-table td.info-content { width: 90%; text-align: left; } ';
			$c.='</style>';

				$c.='<div style="background: #919191; padding: 10px; text-align: center; font-weight: bold">QUERY STATS</div>';
				$c.='<table class="flask-debuginfo-table">';
					$c.='<tr><td class="info-name">Total query count</td><td class="info-content">'.intval($this->debugProfilerData['totalQueryCount']).'</td></tr>';
					$c.='<tr><td class="info-name">SELECT queries</td><td class="info-content">'.intval($this->debugProfilerData['totalQueryCountSelect']).'</td></tr>';
					$c.='<tr><td class="info-name">INSERT/UPDATE/DELETE queries</td><td class="info-content">'.intval($this->debugProfilerData['totalQueryCountUpdate']).'</td></tr>';
					$c.='<tr><td class="info-name">Total query time</td><td class="info-content">'.Flask()->I18n->formatDecimalValue($this->debugProfilerData['totalQueryTime'],8,true).' sec</td></tr>';
				$c.='</table>';
				if (is_array($this->debugProfilerData['queryInfo']) && sizeof($this->debugProfilerData['queryInfo']))
				{
					foreach ($this->debugProfilerData['queryInfo'] as $q => $query)
					{
						$c.='<table class="flask-debuginfo-table">';
						$c.='<tr><td colspan="2" style="background: #555555; font-weight; bold">Query #'.($q+1).'</td></tr>';
						$c.='<tr><td class="info-name">Query</td><td class="info-content">'.htmlspecialchars($query['sql']).'</td></tr>';
						$c.='<tr><td class="info-name">Fetched/affected</td><td class="info-content">'.intval($query['rows']).' rows</td></tr>';
						$c.='<tr><td class="info-name">Time</td><td class="info-content">'.Flask()->I18n->formatDecimalValue($query['time'],8,true).' sec</td></tr>';
						$c.='</table>';
						if (is_array($query['explain']) && sizeof($query['explain']))
						{
							$c.='<table class="flask-debuginfo-table">';
							$c.='<tr><th><b>table</b></th><th><b>type</b></th><th><b>possible_keys</b></th><th><b>key</b></th><th><b>key_len</b></th><th><b>ref</b></th><th><b>rows</b></th><th><b>extra</b></th></tr>';
							foreach ($query['explain'] as $row)
							{
								$c.='<tr>';
								$c.='<td>'.$row['table'].'</td>';
								$c.='<td>'.$row['type'].'</td>';
								$c.='<td>'.$row['possible_keys'].'</td>';
								$c.='<td>'.$row['key'].'</td>';
								$c.='<td>'.$row['key_len'].'</td>';
								$c.='<td>'.$row['ref'].'</td>';
								$c.='<td>'.$row['rows'].'</td>';
								$c.='<td>'.$row['Extra'].'</td>';
								$c.='</tr>';
							}
							$c.='</table>';
						}
					}
				}

			// Wrapper ends
			$c.='</div>';
			return $c;
		}


	}


?>
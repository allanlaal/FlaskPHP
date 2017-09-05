<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Error handling
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */



	/**
	 *   The error handler function
	 *   @param integer $errNo Error number
	 *   @param string $errStr Error string
	 *   @param string $errFile Error file
	 *   @param integer $errLine Error line
	 *   @return void
	 */

	function errorHandler( $errNo, $errStr, $errFile, $errLine )
	{
		$debugOn=(Flask()->Debug->debugOn)?true:false;
		$devMode=(Flask()->Debug->devEnvironment)?true:false;
		$XHR=((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')?true:false);
		$XML=((Flask()->Response->responseObject instanceof \Codelab\FlaskPHP\Response\XMLResponse)=='xml'?true:false);
		$stackTrace=array();
		switch($errNo)
		{
			case E_WARNING:
			case E_NOTICE:
			case E_USER_WARNING:
			case E_USER_NOTICE:
			case E_STRICT:
			case E_DEPRECATED:
			case E_RECOVERABLE_ERROR:
				if (!$debugOn) return;
				if (strpos($errStr,'deprecated')!==false) return;
				if (!strncmp($errStr,'Undefined index:',16)) return;
				if (!strncmp($errStr,'Undefined offset:',17)) return;
				if (!strncmp($errStr,'Undefined property:',19)) return;
				if ($debugOn) Flask()->Debug->addMessage('PHP Error','<b>'.$errStr.'</b><br/>(in: '.$errFile.' line '.$errLine.')');
				return;
			default:
				header("HTTP/1.1 500 Internal Server Error");
				header('Expires: '.date("D, j M Y G:i:s T"));
				header('Pragma: no-cache');
				header('Cache-Control: no-store, no-cache, must-revalidate');
				header('Cache-Control: post-check=0, pre-check=0', false);
				if ($debugOn || $devMode)
				{
					$errStr=$errStr.' (in: '.$errFile.' line '.$errLine.')';
					foreach (debug_backtrace() as $lvl => $trace)
					{
						$t='';
						$t.='#'.$lvl.': '.$trace['file'].' line '.$trace['line'];
						if (!empty($trace['function']))
						{
							$t.=' -- ';
							if (!empty($trace['class'])) if ($debugOn) $t.= $trace['class'].$trace['type'];
							if ($debugOn) $t.= $trace['function'].'(';
							if (!empty($trace['args']) && sizeof($trace['args']))
							{
								$arg=array();
								foreach ($trace['args'] as $a)
								{
									if (is_object($a))
									{
										$a.='&'.get_called_class($a);
										if ($a instanceof \Codelab\FlaskPHP\Model\ModelInterface)
										{
											$a.'('.intval($a->_oid).')';
										}
									}
									elseif ($a===null)
									{
										$arg[]='null';
									}
									elseif (is_array($a))
									{
										$arg[]='array('.sizeof($a).')';
									}
									elseif (is_numeric($a))
									{
										$arg[]=$a;
									}
									else
									{
										$arg[]='"'.$a.'"';
									}
								}
								if ($debugOn) $t.= join(', ',$arg);
							}
							if ($debugOn) $t.= ')';
						}
						$stackTrace[]=$t;
					}
				}
				if ($XHR)
				{
					outputErrorJSON($errStr,($debugOn?$stackTrace:false));
				}
				elseif ($XML)
				{
					outputErrorXML($errStr,($debugOn?$stackTrace:false));
				}
				elseif (!empty($_SERVER['HTTP_HOST']))
				{
					$errorFunc=oneof(Flask()->Debug->outputErrorHTML,'outputErrorHTML');
					call_user_func($errorFunc,$errStr,($debugOn?$stackTrace:false));
				}
				else
				{
					$errorFunc=oneof(Flask()->Debug->outputErrorPlain,'outputErrorPlain');
					call_user_func($errorFunc,$errStr,($debugOn?$stackTrace:false));
				}
				exit;
		}
	}



	/**
	 *   The exception handler function
	 *   @param Exception $e Exception
	 *   @return void
	 */

	function exceptionHandler( $e )
	{
		$debugOn=(Flask()->Debug->debugOn)?true:false;
		$devMode=(Flask()->Debug->devEnvironment)?true:false;
		$XHR=((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')?true:false);
		$XML=((Flask()->Response->responseObject instanceof \Codelab\FlaskPHP\Response\XMLResponse)=='xml'?true:false);
		$errStr=$e->getMessage();
		$errCode=$e->getCode();
		$stackTrace=array();
		if ($debugOn || $devMode)
		{
			$errStr=$errStr.' (in: '.$e->getFile().' line '.$e->getLine().')';
			foreach ($e->getTrace() as $lvl => $trace)
			{
				$t='';
				$t.='#'.$lvl.': '.$trace['file'].' line '.$trace['line'];
				if (!empty($trace['function']))
				{
					$t.=' -- ';
					if (!empty($trace['class'])) if ($debugOn) $t.= $trace['class'].$trace['type'];
					if ($debugOn) $t.= $trace['function'].'(';
					if (!empty($trace['args']) && sizeof($trace['args']))
					{
						$arg=array();
						foreach ($trace['args'] as $a)
						{
							if (is_object($a))
							{
								$a.='&'.get_called_class($a);
								if ($a instanceof \Codelab\FlaskPHP\Model\ModelInterface)
								{
									$a.'('.intval($a->_oid).')';
								}
							}
							elseif ($a===null)
							{
								$arg[]='null';
							}
							elseif (is_array($a))
							{
								$arg[]='array('.sizeof($a).')';
							}
							elseif (is_numeric($a))
							{
								$arg[]=$a;
							}
							else
							{
								$arg[]='"'.htmlspecialchars($a).'"';
							}
						}
						if ($debugOn) $t.= join(', ',$arg);
					}
					if ($debugOn) $t.= ')';
				}
				$stackTrace[]=$t;
			}
		}
		if (!empty($errCode) && array_key_exists($errCode,\Codelab\FlaskPHP\Response\ResponseInterface::$setHttpStatus))
		{
			header("HTTP/1.1 ".$errCode." ".\Codelab\FlaskPHP\Response\ResponseInterface::$setHttpStatus[$errCode]);
		}
		if ($XHR)
		{
			outputErrorJSON($errStr,($debugOn?$stackTrace:false));
		}
		elseif ($XML)
		{
			outputErrorXML($errStr,($debugOn?$stackTrace:false));
		}
		elseif (!empty($_SERVER['HTTP_HOST']))
		{
			$errorFunc=oneof(Flask()->Debug->outputErrorHTML,'outputErrorHTML');
			call_user_func($errorFunc,$errStr,($debugOn?$stackTrace:false));
		}
		else
		{
			$errorFunc=oneof(Flask()->Debug->outputErrorPlain,'outputErrorPlain');
			call_user_func($errorFunc,$errStr,($debugOn?$stackTrace:false));
		}
		exit;
	}



	/**
	 *   The shutdown handler function
	 *   @return void
	 */

	function shutdownHandler()
	{
		if(!is_null($e = error_get_last()))
		{
			errorHandler($e['type'],$e['message'],$e['file'],$e['line']);
		}
	}



	/**
	 *   Output error as JSON
	 *   @param string $errorMessage Error message
	 *   @param boolean|array $stackTrace Stack trace (or false if none)
	 *   @return void
	 */

	function outputErrorJSON( $errorMessage, $stackTrace=false )
	{
		$response=array();
		$response['status']='2';
		$response['error']='FATAL ERROR: '.$errorMessage;
		if ($stackTrace!==false) $response['stacktrace']=$stackTrace;
		echo json_encode($response,JSON_FORCE_OBJECT|JSON_HEX_QUOT|JSON_HEX_APOS);
	}



	/**
	 *   Output error as XML
	 *   @param string $errorMessage Error message
	 *   @param boolean|array $stackTrace Stack trace (or false if none)
	 *   @return void
	 */

	function outputErrorXML( $errorMessage, $stackTrace=false )
	{
		header('Content-type: text/xml; charset=UTF-8');
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<response>';
		echo '<status>2</status>';
		echo '<error>FATAL ERROR: '.htmlspecialchars($errorMessage).'</error>';
		if ($stackTrace!==false)
		{
			echo '<stacktrace>';
			echo '<line>'.join('</line><line>',$stackTrace).'</line>';
			echo '</stacktrace>';
		}
		echo '</response>';
		echo "\n";
	}



	/**
	 *   Output error as HTML
	 *   @param string $errorMessage Error message
	 *   @param boolean|array $stackTrace Stack trace (or false if none)
	 *   @return void
	 */

	function outputErrorHTML( $errorMessage, $stackTrace=false )
	{
		header('Content-type: text/html; charset=UTF-8');
		echo '<html><head><title>FATAL ERROR</title></head><body>';
		echo '<h1>FATAL ERROR</h1>';
		echo '<style> body { background-color: #f6f1d4; background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjE0NDBweCIgaGVpZ2h0PSIxMDI0cHgiIHZpZXdCb3g9IjAgMCAxNDQwIDEwMjQiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgc3R5bGU9ImJhY2tncm91bmQ6ICNGNkYxRDQ7Ij4KICAgIDwhLS0gR2VuZXJhdG9yOiBTa2V0Y2ggNDMuMSAoMzkwMTIpIC0gaHR0cDovL3d3dy5ib2hlbWlhbmNvZGluZy5jb20vc2tldGNoIC0tPgogICAgPHRpdGxlPkRlc2t0b3AgSEQ8L3RpdGxlPgogICAgPGRlc2M+Q3JlYXRlZCB3aXRoIFNrZXRjaC48L2Rlc2M+CiAgICA8ZGVmcz48L2RlZnM+CiAgICA8ZyBpZD0iUGFnZS0xIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgICAgICA8ZyBpZD0iRGVza3RvcC1IRCIgZmlsbD0iI0ZGRkZGRiI+CiAgICAgICAgICAgIDxnIGlkPSJHcm91cCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNTM5LjAwMDAwMCwgNTExLjUwMDAwMCkgc2NhbGUoLTEsIDEpIHRyYW5zbGF0ZSgtNTM5LjAwMDAwMCwgLTUxMS41MDAwMDApIHRyYW5zbGF0ZSgwLjAwMDAwMCwgLTEuMDAwMDAwKSI+CiAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMTk5LjQ3MzAwNCw5NTAuMTQxOTU3IEwxOTkuNDczMDA0LDk1MC4xNDE5NTcgTDE5OS40NjA5OTQsOTUwLjE2NTk3NyBMMTk5LjQ3MzAwNCw5NTAuMTQxOTU3IE0xOTkuNDczMDA0LDk1MC4xNDE5NTcgTDE5OS40NzMwMDQsOTUwLjE0MTk1NyBMMTk5LjQ2MDk5NCw5NTAuMTY1OTc3IEwxOTkuNDczMDA0LDk1MC4xNDE5NTcgTTEwNzUuMDU5OTYsODE1LjEyNDkzNyBMMTA3NS4wNjM5Niw4MTUuMTE2OTMxIEwxMDc1LjA1NDYyLDgxNS4xNDIyODUgTDEwNzUuMDU5OTYsODE1LjEyNDkzNyBNMTA3NS4wNzk5Nyw4MTUuMDQ3NTQgTDEwNzUuMDkxOTgsODE1LjAyNjE4OSBMMTA3NS4wNzMzLDgxNS4wODQ5MDQgTDEwNzUuMDY2NjMsODE1LjA5OTU4MyBMMTA3NS4wNjc5Niw4MTUuMDkyOTExIEwxMDc1LjA2OTMsODE1LjA5MDI0MiBMMTA3NS4wNjkzLDgxNS4wOTE1NzYgTDEwNzUuMDc5OTcsODE1LjA0NzU0IE0xMDc1LjA4OTMxLDgxNS4wMTI4NDUgTDEwNzUuMTAyNjYsODE0Ljk5NDE2MyBMMTA3NS4wODkzMSw4MTUuMDEyODQ1IE0xMDc1LjA5NDY1LDgxNC45OTE0OTQgTDEwNzUuMTA4LDgxNC45NzU0OCBMMTA3NS4wOTQ2NSw4MTQuOTkxNDk0IE0xMDc1LjA5OTk5LDgxNC45NzQxNDYgTDEwNzUuMTEyLDgxNC45NjM0NzEgTDEwNzUuMDk5OTksODE0Ljk3NDE0NiBNMTA3NS4xMDgsODE0Ljk0NzQ1NyBMMTA3NS4xMjEzNCw4MTQuOTM2NzgyIEwxMDc1LjExNiw4MTQuOTUyNzk1IEwxMDc1LjEwMjY2LDgxNC45NjM0NzEgTDEwNzUuMTA4LDgxNC45NDc0NTcgTTEwNzUuMTEyLDgxNC45Mjg3NzUgTDEwNzUuMTI1MzQsODE0LjkyMjEwMyBMMTA3NS4xMTIsODE0LjkyODc3NSBNMTA3NS4xMTQ2Nyw4MTQuOTE5NDM0IEwxMDc1LjEyODAxLDgxNC45MTY3NjUgTDEwNzUuMTE0NjcsODE0LjkxOTQzNCBNNjIuOTk2MTEwMyw2NDAuNzgzMzg5IEM2My4wNTQ4MjU2LDY0MC42NTUyODMgNjMuMTE4ODc4Niw2NDAuNTIwNTA1IDYzLjE4NDI2Niw2NDAuMzgxNzIzIEM2NC4yNjUxNjAxLDYzOC4wODc4MjYgNjYuMjIyNzc5NCw2MzQuNDYwODI2IDY4Ljg0MDk0NTIsNjMwLjExMzIyOSBDNzMuNzUzMDA4NCw2MjEuODk4NDM0IDgwLjk1ODk2OTIsNjExLjA0MTQ1MyA4OS42NDIxNTE5LDU5OC42OTUyNCBDMTA0Ljg1ODczOSw1NzcuMDUzMzM4IDEyNC42MTUwODIsNTUwLjcyNzU2MSAxNDUuNTk2NDM3LDUyMy41OTcxMTkgQzE3Ny4wODY0ODYsNDgyLjg4MzQ0MSAyMTEuMzM4ODE5LDQ0MC4yODQyMDMgMjM3LjY4ODYxNiw0MDcuOTE3NDI5IEMyNTAuODYzNTE0LDM5MS43MjgwMzcgMjYyLjA2ODc4MywzNzguMDk2NzYxIDI2OS45NTkzMSwzNjguNTI0ODQzIEMyNzAuNjg3OTEzLDM2Ny42NDAxMTIgMjcxLjM4OTgyNywzNjYuNzkxNDEgMjcyLjA2MjM4MywzNjUuOTc3NDAzIEMzMDIuNjIzNjYzLDM2OC40NTI3ODQgMzcxLjgyMzU3MiwzNzQuMTY4MTc4IDQ0MC42MDk4MDUsMzgwLjYwNjgzOCBDNDgzLjgzMjIyNiwzODQuNjQyMTc2IDUyNi45MjM4NzEsMzg4Ljk3Nzc2MiA1NTkuODkxMTQyLDM5Mi45MjkwMzEgQzU3Ni4zNTY3NjIsMzk0Ljg5ODY2IDU5MC4zMjQzMTYsMzk2Ljc4NDIyIDYwMC4xMzY0MzIsMzk4LjQxNzU3MSBDNjAyLjU3NDQ0OSwzOTguODI1OTA4IDYwNC43NDgyNDcsMzk5LjIxMDIyNiA2MDYuNjA5Nzg3LDM5OS41NTg1MTUgQzYwNy4zMTE3MDEsMzk5LjY5MzI5MyA2MDcuOTY5NTc5LDM5OS44MjY3MzYgNjA4LjU3ODA4MiwzOTkuOTUyMTczIEM2MTAuMTgyMDc1LDQwMi4wNTkyNSA2MTIuNDEzMjU0LDQwNS4zNDA2MzEgNjE0Ljg4NDYzMiw0MDkuNDQyNjkxIEM2MTcuMzcwNjg5LDQxMy41NjIwOTggNjIwLjE0NjMxOCw0MTguNTM0MjExIDYyMy4wODc0MTcsNDI0LjE0Mjg1MSBDNjI4LjYyMzk5Nyw0MzQuNjg3NTczIDYzNC43NjEwNzQsNDQ3LjUxNDE4MyA2NDEuMTUwMzU5LDQ2MS43NDU5NTYgQzY1Mi4zNTU2MjgsNDg2LjY2NjU3IDY2NC4zODk1ODMsNTE1LjkzNDc4MSA2NzYuMDYzMjM5LDU0NS42NzEzNzkgQzY5My41NzM3MjQsNTkwLjMwMDI5NiA3MTAuMzAzNTYzLDYzNi4wNTgxNDcgNzIyLjYyNTc1NSw2NzAuNTg2NzA5IEM3MjguNzg2ODUyLDY4Ny44NTY5OTUgNzMzLjg0MzAzNCw3MDIuMzI4OTY2IDczNy4zNjE5NDUsNzEyLjQ1MzM0MSBDNzM3LjU0MjA5NCw3MTIuOTcxMTAzIDczNy43MTgyNCw3MTMuNDc4MTg5IDczNy44OTAzODIsNzEzLjk3MTkzIEM3MzIuNzU5NDcxLDcyMS44NTcxMiA3MjUuNDY5NDQxLDczMy4wMTcwMTggNzE2LjY1NjgxOCw3NDYuMzkzNDE2IEM3MDMuNDIxODcsNzY2LjQ4NjAzNyA2ODYuNzUyMDgxLDc5MS41NzQ3OSA2NjguODMzMjU4LDgxOC4wNDQ2ODYgQzY1NS40MDYxNTEsODM3Ljg5NzEwOCA2NDEuMjY5MTI0LDg1OC41MTgxNjYgNjI3LjM2Mjk1NCw4NzguMzcwNTg4IEM2MTYuOTM4MzMxLDg5My4yNjI5MDYgNjA2LjY0NTgxNyw5MDcuNzIyODY4IDU5Ni44OTM3NSw5MjEuMDc3OTE1IEM1ODkuNTc5Nyw5MzEuMDk0MiA1ODIuNTY1ODk4LDk0MC40ODU5NjkgNTc2LjA0NDUwNCw5NDguOTUyOTczIEM1NzEuMTY4NDcsOTU1LjMwNjIyOSA1NjYuNTU2NjU1LDk2MS4xNDMwNTcgNTYyLjM0MTE2OCw5NjYuMzA3MzI5IEM1NTkuMTcwNTQ1LDk3MC4xNzQ1MjggNTU2LjIxNjEwMiw5NzMuNjgxNDI5IDU1My41NDk4OTYsOTc2LjcxODYwOCBDNTQ5LjU2MjU5OCw5ODEuMjcxNzA3IDU0Ni4xMzg0MzIsOTg0LjgzODY1OCA1NDMuOTA1OTE5LDk4Ni45MDQzNjcgQzU0My42MTkwMTUsOTg3LjE3MjU4OCA1NDMuMzUyMTI3LDk4Ny40MTU0NTYgNTQzLjExMDU5NCw5ODcuNjMwMyBDNTQyLjcxODI2OSw5ODcuNzEwMzY3IDU0Mi4yNjMyMjYsOTg3Ljc5NDQzNiA1NDEuNzU2MTQsOTg3Ljg3NzE3MSBDNTM5LjA3OTI1OSw5ODguMzIxNTM5IDUzNC45NTg1MTcsOTg4Ljc3NzkxNiA1MjkuOTI2MzU1LDk4OS4wNTQxNDUgQzUyNC44ODIxODIsOTg5LjM1NDM5MyA1MTguODc3MjE1LDk4OS40OTg1MTIgNTEyLjE3NTY3MSw5ODkuNDk4NTEyIEM1MDAuODAyMjYzLDk4OS40OTg1MTIgNDg3LjQxMTE4Niw5ODkuMDY2MTU1IDQ3Mi43NDcwNTYsOTg4LjI2MTQ4OSBDNDQ3LjA5MzgzNiw5ODYuODQ0MzE3IDQxNy41NjE0MDYsOTg0LjI3NDE5MSAzODcuNTEyNTUsOTgxLjAwNzQ4OSBDMzQyLjQ1MTI3NSw5NzYuMTA2MTAxIDI5Ni4xMjg5NTgsOTY5LjYxMDA2MSAyNTkuNjU0Nzg2LDk2My4xMjQ2OTYgQzI1MC41NTEyNTYsOTYxLjUwMzM1NSAyNDIuMDYwMjMyLDk1OS44ODIwMTQgMjM0LjM4NTg4NCw5NTguMjk2NzAyIEMyMjIuODkyMzc2LDk1NS45MzA3NDUgMjEzLjIwMDM1OSw5NTMuNjI0ODM4IDIwNi4zMzA2NzcsOTUxLjY0MzE5OSBDMjAyLjkxOTg1NSw5NTAuNjcwMzk0IDIwMC4yMDU2MSw5NDkuNzU3NjM5IDE5OC41NzIyNTksOTQ5LjEzMzEyMiBDMTk4LjU0OTU3Myw5NDkuMTI1MTE2IDE5OC41MjY4ODgsOTQ5LjExNTc3NSAxOTguNTA1NTM3LDk0OS4xMDY0MzMgQzE5OC40MjI4MDIsOTQ5LjAxMzAyMyAxOTguMzM3Mzk4LDk0OC45MTQyNzUgMTk4LjI0Nzk5MSw5NDguODA4ODU0IEMxOTYuOTAyODc4LDk0Ny4yNDc1NjIgMTk0Ljc4OTEyOSw5NDQuNDg1Mjc4IDE5Mi4zNTExMTMsOTQwLjkwNjMxNyBDMTkwLjA1NzIxNSw5MzcuNTQzNTM1IDE4Ny40MzkwNDksOTMzLjQ0ODE0OCAxODQuNjE2NzE1LDkyOC43NzYyODMgQzE3OS4zMjAzMzQsOTIwLjAwOTAzMSAxNzMuMzAzMzU2LDkwOS4yMzYxMTkgMTY2LjkyNjA4MSw4OTcuMTE4MDk1IEMxNTUuNzU2ODQyLDg3NS45MDg1NTEgMTQzLjQ4MjY4OSw4NTAuNTkxNjA5IDEzMS40MjQ3MTQsODI0LjE1Nzc0MyBDMTEzLjMzNzc1Myw3ODQuNTAwOTM5IDk1LjcxOTE3ODgsNzQyLjIwMTk0OSA4Mi44MzI1MTksNzA3LjA5NjkxIEM3Ni4zOTUxOTQsNjg5LjU2MjQwNiA3MS4xNDY4NTI2LDY3My43OTMzNjEgNjcuNjUxOTYxNyw2NjEuNDExMTE5IEM2NS45MTA1MjExLDY1NS4yMzgwMTIgNjQuNjEzNDQ4Miw2NDkuOTA1NjAxIDYzLjgyMDc5MjUsNjQ1Ljg3MDI2MyBDNjMuNDI0NDY0Nyw2NDMuODUyNTk0IDYzLjE2MDI0NjEsNjQyLjE3MTIwNCA2My4wMTYxMjY5LDY0MC45NDYxOSBDNjMuMDA5NDU0Nyw2NDAuODkwMTQ0IDYzLjAwMjc4MjUsNjQwLjgzNjc2NiA2Mi45OTYxMTAzLDY0MC43ODMzODkgTTg5Ljc2MjI1MTMsMTIuNDUwMjk4OSBMODkuNzY4OTIzNSwxMi40NDc2MyBMODkuNzYyMjUxMywxMi40NTAyOTg5IE05OS42MzE3NDg2LDcuNzE5NzE5MDggTDk5LjYzNzA4NjQsNy43MTcwNTAyIEw5OS42MzE3NDg2LDcuNzE5NzE5MDggTTEwNS43NzE0OTQsNC43NzcyODUxIEwxMDUuNzg2MTczLDQuNzcwNjEyOTEgTDEwNS43NzE0OTQsNC43NzcyODUxIE0xMTEuMDExODI5LDIuMjY5ODc3NjQgTDExMS4wMTU4MzIsMi4yNjg1NDMyIEwxMTEuMDExODI5LDIuMjY5ODc3NjQgTTEwNy45NDEyODksMS43Nzc0NzAzMiBMMTA3Ljk0MjYyMywxLjc3NzQ3MDMyIEwxMDcuOTQxMjg5LDEuNzc3NDcwMzIgTTEwNy45NTU5NjgsMS43NzIxMzI1NyBMMTA3Ljk1NTk2OCwxLjc3MjEzMjU3IEwxMDcuOTU1OTY4LDEuNzczNDY3MDEgTDEwNy45NTU5NjgsMS43NzIxMzI1NyBNMTQyLjAwMTQ2NCwwIEw3Ni41ODYwMTg2LDAgQzc2LjU1MTMyMzIsMC42MzI1MjMyMjMgNzYuNTM5MzEzMywxLjIwNDk5Njc3IDc2LjUzOTMxMzMsMS43Mjk0MzA1OCBDNzYuNTM5MzEzMyw1LjM3NjQ0NzM5IDc3LjEyOTEzNDUsOC44Mjg2MzYzNyA3OC4zMjQ3OTAyLDEyLjE3OTQwODEgQzc4LjQxMDE5NDIsMTIuNDIzNjEwMSA3OC40NzY5MTYsMTIuNjAzNzU5MiA3OC41MzI5NjI0LDEyLjc2NjU2MDUgQzc4Ljc2MTE1MTIsMTMuMzU1MDQ3MyA3OC44NjkyNDA2LDEzLjY0MzI4NTcgNzguOTc3MzMsMTMuOTA3NTA0MyBDNzkuMTkzNTA4OCwxNC40MjM5MzE1IDc5LjM0OTYzOCwxNC43ODQyMjk1IDc5LjUyOTc4NywxNS4yMTY1ODcxIEM3OS44OTAwODUsMTYuMDQ1MjcyNiA4MC4zMjI0NDI3LDE3LjAzMDA4NzMgODAuODc0ODk5NywxOC4yNzkxMjA1IEM4Mi45ODg2NDgxLDIzLjA3MTA4NDQgODYuODQzODM3MiwzMS42NDYxNzc3IDkyLjA0NDEzODgsNDMuMTYzNzA1IEMxMTAuMjAzMTYsODMuNDMzMDE1NyAxNDQuNjExNjIzLDE1OS4yMDM2OTMgMTc0LjQ4MDMzLDIyNC45NDYwNzUgQzE5NC4wNzY1NCwyNjguMDY5NzQ3IDIxMS43MTkxMzQsMzA2Ljg2NzE3NCAyMjEuNTM5MjU3LDMyOC40NTMwMjkgQzIwMi4wMzI0NTQsMzUyLjA5NjU4NyAxNjIuNjAxMTcsNDAwLjIyMDM5NSAxMjIuNjY5NDcyLDQ1MC44NjQ5NTUgQzEwOC42MTc4NDksNDY4LjY3NTY4OCA5NC40OTQxNjU1LDQ4Ni44MTA2ODkgODEuMTUxMTI4Miw1MDQuMzMzMTg0IEM3MS4xMzQ4NDI3LDUxNy40NzIwNTMgNjEuNTUwOTE0OSw1MzAuMjYyNjMzIDUyLjcyMzYxMjksNTQyLjMzMjYxNyBDNDYuMTE4MTQ4OSw1NTEuMzg4MTA4IDM5LjkzMzAzMjYsNTYwLjAzNTI2MSAzNC4zMTIzODMyLDU2OC4xNTM5NzcgQzMwLjA5Njg5NjEsNTc0LjI0MzAxMyAyNi4xOTM2Njc0LDU4MC4wMTk3OTIgMjIuNjUwNzM2Nyw1ODUuNDcyMzAyIEMxNy4zNDIzNDU2LDU5My42NTEwNjggMTIuODUwNjMsNjAxLjAyNTE2OCA5LjIxMTYxOTg1LDYwNy44NzA4MyBDNy4zODYxMDk3OCw2MTEuMzA0MzM3IDUuNzY0NzY4NjEsNjE0LjYyMDQxNCA0LjMyMzU3NjQ2LDYxOC4xMTUzMDUgQzMuNjAyOTgwMzgsNjE5Ljg2NzQyMSAyLjkzMDQyNDA0LDYyMS42NzAyNDUgMi4zMDU5MDc0NCw2MjMuNjUxODg0IEMxLjY4MTM5MDg1LDYyNS42MzM1MjQgMS4xMDQ5MTM5OCw2MjcuNzgzMzAyIDAuNjcyNTU2MzM4LDYzMC4zNzYxMTMgTDAuNjQ4NTM2NDY5LDYzMC40ODU1MzcgQzAuMTMyMTA5MjgxLDYzMy42NjgxNyAwLDYzNi4zOTQ0MjUgMCw2MzguOTc2NTYxIEMwLDY0Mi4wNjMxMTQgMC4yMTYxNzg4MjMsNjQ0LjkyMTQ3OSAwLjU0MDQ0NzA1Nyw2NDcuNzkxODUzIEMxLjE3Njk3MzU5LDY1My4zMDQ0MTMgMi4yMzM4NDc4NCw2NTguODI4OTgzIDMuNjE0OTkwMzIsNjY0LjgwOTkzIEM2LjIyMTE0NjEzLDY3NS45NTUxNSA5Ljk4MDI1NTY2LDY4OC42MDE2MTEgMTQuNjc2MTQwMSw3MDIuNzAxMjc0IEMyMi44OTA5MzU0LDcyNy4zMjE2NCAzMy45NjQwOTUxLDc1Ni4yNTM1NzIgNDYuNDMwNDA3Miw3ODYuMzUwNDY5IEM1NS43ODYxNDYzLDgwOC45MTcxMzYgNjUuOTEwNTIxMSw4MzIuMTA4MzE5IDc2LjE3OTAxNTIsODU0LjQ3MDgxOCBDODMuODc3MzgzMyw4NzEuMjQ4Njk2IDkxLjY0NzgxMSw4ODcuNTU4MTg3IDk5LjI1MDA5OTYsOTAyLjgxMDgwNCBDMTA0Ljk0MjgwOSw5MTQuMjQ0MjYyIDExMC41Mzk0MzgsOTI1LjA4OTIzMyAxMTUuOTQzOTA5LDkzNS4wOTM1MDkgQzEyMC4wMDMyNjcsOTQyLjU5OTcxOCAxMjMuOTQyNTI1LDk0OS42NDk1NDkgMTI3Ljc2MTY4NCw5NTYuMTQ2OTI0IEMxMzAuNjIwMDQ5LDk2MS4wMjI5NTcgMTMzLjQxODM2NCw5NjUuNTk3NDA4IDEzNi4xNDQ2MTksOTY5Ljg1MDI1OSBDMTQwLjI1MjAxNiw5NzYuMjM5NTQ1IDE0NC4xNjcyNTUsOTgxLjg4NDIxNCAxNDguMjUwNjMzLDk4Ni45NjQ0MTYgQzE1MC4zMDQzMzIsOTg5LjUxMDUyMiAxNTIuMzk0MDYsOTkxLjkyNDUxOSAxNTQuNzQ4MDA3LDk5NC4zMDI0ODYgQzE1Ny4xMjU5NzQsOTk2LjY4MDQ1MyAxNTkuNzA4MTEsOTk5LjAzNDQwMSAxNjMuMjk5MDgxLDEwMDEuNDYwNDEgTDE2My4zNzExNCwxMDAxLjUwODQ1IEMxNjUuODMzMTc3LDEwMDMuMTI4NDUgMTY3Ljk4Mjk1NSwxMDA0LjI1ODcyIDE3MC4wNzI2ODQsMTAwNS4yNjc1NiBDMTcyLjA5MDM1MywxMDA2LjIyODM1IDE3NC4wNDc5NzIsMTAwNy4wNDUwMyAxNzYuMDUzNjMxLDEwMDcuODEzNjYgQzE3OS45MDg4MiwxMDA5LjI5MDg4IDE4My45MzIxNDksMTAxMC41ODc5NiAxODguMzI3Nzg1LDEwMTEuODYxMDEgQzE5Ni41NDI1OCwxMDE0LjI0OTY1IDIwNi4wNjY0NTgsMTAxNi41NDQ4OSAyMTYuOTExNDI5LDEwMTguODUwNzkgQzIyNi4yOTM4NTcsMTAyMC44NTExMSAyMzYuNjUwNDI0LDEwMjIuODU4MTEgMjQ3Ljc1Njk0NCwxMDI0Ljg0Mzc1IEw1OTQuNzYzOTg4LDEwMjQuODQzNzUgQzU5Ni44MDMwMDgsMTAyMi42NjQ2MSA1OTguODc4MDU4LDEwMjAuMzYyNzEgNjAxLjAxMzE1OCwxMDE3LjkyNjAzIEM2MDguOTg3NzU0LDEwMDguODEwNDkgNjE3LjgyNzA2Niw5OTcuNzg1MzY3IDYyNy40MTA5OTQsOTg1LjI3MTAxNSBDNjQ0LjE1Mjg0Myw5NjMuMzc4MjM5IDY2My4xNDA1NDksOTM2Ljk0MzAzOSA2ODIuMzA5NzM5LDkwOS40NzYzMTggQzcxMS4wNjAxODgsODY4LjI4MjI0MiA3NDAuMTg0MjgsODI0Ljc5NDI2OSA3NjIuMTYyNDYsNzkxLjU1MDc3IEM3NzEuNTc2OTE1LDc3Ny4zMTA5OTEgNzc5LjY3NTYxNCw3NjQuOTU0MTAzIDc4NS44NjMzOTksNzU1LjQ3NTU5NiBDODEzLjkyNTI3OCw3NjQuNDU3NjkyIDg2My45ODI2ODYsNzgwLjQ4MDI3OSA5MTQuMDc2MTIzLDc5Ni40OTg4NjMgQzk1MS44NTkzNzcsODA4LjU5Mjg2NyA5ODkuNjY2NjUyLDgyMC42NzQ4NjIgMTAxOC4wNzAxNSw4MjkuNzQyMzYyIEMxMDMyLjI3NzksODM0LjI4MjExOCAxMDQ0LjExOTcsODM4LjA1MzIzNyAxMDUyLjQ1NDU5LDg0MC43MDc0MzMgQzEwNTYuNjM0MDUsODQyLjA0MDUzNSAxMDU5LjkxMjc2LDg0My4wNzMzOSAxMDYyLjIwNjY2LDg0My43OTM5ODYgQzEwNjMuMzQ3Niw4NDQuMTY0OTU5IDEwNjQuMjM2MzQsODQ0LjQ0MTE4OCAxMDY0LjkwODg5LDg0NC42NDY2OTEgQzEwNjUuMjQ1MTcsODQ0Ljc1NDc4MSAxMDY1LjUyMTQsODQ0LjgzODg1IDEwNjUuODMzNjYsODQ0LjkzNDkzIEMxMDY2LjAwMTgsODQ0Ljk4Mjk2OSAxMDY2LjE0NTkyLDg0NS4wMzEwMDkgMTA2Ni40NzAxOCw4NDUuMTI3MDg5IEMxMDY2LjU1NDI1LDg0NS4xNTExMDggMTA2Ni42NTAzMyw4NDUuMTc1MTI4IDEwNjYuNzgyNDQsODQ1LjIxMTE1OCBMMTA2Ni45NTA1OCw4NDUuMjU5MTk4IEwxMDY2Ljk1MzI1LDg0NS4yNDk4NTcgQzEwNjkuNjE2NzksODQ1Ljk2NjQ0OSAxMDcyLjQwNzA5LDg0Ni4zNDAwOTIgMTA3NS4xNTMzNyw4NDYuMzQwMDkyIEMxMDc1LjczNjUyLDg0Ni4zNDAwOTIgMTA3Ni40MDc3NCw4NDYuMzMwNzUxIDEwNzcuMjE5MDcsODQ2LjI3NzM3MyBMMTA3Ny4yMTkwNyw3ODIuNDkzOTQ1IEMxMDYwLjE2NzY0LDc3Ni44OTMzMTIgMTAzNS44NDQ4NSw3NjkuMDQxNDg0IDEwMDguMzA2MDcsNzYwLjIwNDg0MSBDOTMzLjIyMjYyOCw3MzYuMTI2MjU2IDgzNC40MjIyMzMsNzA0LjgyNTY5OCA3OTcuMTU1NDA2LDY5My4wMzcyOCBDNzk0LjA4ODg3LDY4NC4xOTUyOTkgNzg5Ljc3OTk3Miw2NzEuODYzNzY1IDc4NC41MzY5NjgsNjU3LjExMTU2MiBDNzc2LjM1ODIwMyw2MzQuMDg4NTE4IDc2NS45MjE1Nyw2MDUuMTY4NTk1IDc1NC4zODAwMjMsNTc0LjM5OTE0MyBDNzQ1LjcyMDg2LDU1MS4zMTYwNDggNzM2LjQ0OTE5LDUyNy4yMDAxIDcyNy4wMzM0MDEsNTAzLjcyMDY3NyBDNzE5Ljk3MTU2LDQ4Ni4xMDIxMDMgNzEyLjgzNzY1OSw0NjguODQzODI3IDcwNS44MjI1MjMsNDUyLjYxODQwNiBDNzAwLjU1MTQ5Niw0NDAuNDUyMzQyIDY5NS4zMzkxODQsNDI4Ljg3NDc2NSA2OTAuMjU3NjQ3LDQxOC4xMjU4NzMgQzY4Ni40Mzk4MjMsNDEwLjA3OTIxNyA2ODIuNjkyNzIzLDQwMi40ODg5MzkgNjc5LjAxNzY4MywzOTUuNDYzMTI3IEM2NzYuMjU1Mzk4LDM5MC4xOTA3NjYgNjczLjU0MTE1MywzODUuMjMwNjYzIDY3MC44Mzg5MTcsMzgwLjU5NDgyOCBDNjY2Ljc3OTU2LDM3My42MjkwNjYgNjYyLjgwNDI3MSwzNjcuNDE5OTI5IDY1OC40OTI3MDUsMzYxLjc2MzI1IEM2NTYuMzE4OTA3LDM1OC45Mjg5MDYgNjU0LjA2MTAzOSwzNTYuMjI2NjcgNjUxLjQ1NDg4MywzNTMuNTcyNDc1IEM2NDguODM2NzE3LDM1MC45MTgyNzkgNjQ1LjkxODMwMywzNDguMjc2MDk0IDY0MS45MzEwMDUsMzQ1LjY5Mzk1OCBMNjQyLjAzOTA5NCwzNDUuNzU0MDA3IEM2MzkuNTUzMDM4LDM0NC4xNTY2ODYgNjM3Ljc2MzU1OCwzNDMuMzUyMDIxIDYzNi4yMDIyNjYsMzQyLjY2NzQ1NCBDNjM0LjY3NzAwNCwzNDIuMDE4OTE4IDYzMy4zOTE5NDEsMzQxLjU2MjU0IDYzMi4xNjY5MjgsMzQxLjE2NjIxMiBDNjI5Ljc3Njk1MSwzNDAuMzg1NTY3IDYyNy42MzkxODMsMzM5LjgzMzExIDYyNS4zODEzMTUsMzM5LjMwNDY3MyBDNjIxLjAzMzcxOSwzMzguMjgzODI4IDYxNi4yNjU3NzUsMzM3LjM4MzA4MyA2MTAuNzE3MTg1LDMzNi40NTgzMTggQzYwMC4zNzY2MzEsMzM0Ljc0MDg5NyA1ODcuNDUzOTQxLDMzMi45NzU0MzcgNTcyLjQ0MTUyMywzMzEuMTM3OTE3IEM1NDYuMjIzODM2LDMyNy45MzEyNjUgNTEzLjc0ODk3MywzMjQuNTIwNDQzIDQ3OS42NjQ3NzgsMzIxLjE5MzY5MSBDNDI4LjU1MDQ5NywzMTYuMjA5NTY4IDM3My44NjkyNjQsMzExLjQxNzYwNCAzMzEuOTMwNTczLDMwNy44NzQ2NzQgQzMxMC4zMTEzNTYsMzA2LjA0MjQ5MSAyOTIuMDgyOTQ0LDMwNC41NDEyNSAyNzkuNDg1ODU3LDMwMy41MTc3MzYgQzI3MC4wODIwNzgsMjgyLjIxMjExMiAyNTIuNzg2NDM4LDI0My4xMjI0NDQgMjMyLjUyNDM0NCwxOTcuODUxNjYzIEMyMTcuMTg3NjU3LDE2My41OTkzMjkgMjAwLjE2OTU4LDEyNS44MTYwNzUgMTgzLjU1OTg0MSw4OS40NzQwMTI4IEMxNjguOTAyMzgzLDU3LjM3OTQ2NDMgMTU0LjU3MzE5NiwyNi40NDA1MzgzIDE0Mi4wMDE0NjQsMCIgaWQ9IkZpbGwtMSI+PC9wYXRoPgogICAgICAgICAgICAgICAgPHBhdGggZD0iTTI4OC44MzkyMzMsMzk5IEMyNzkuNDA4NzY1LDM5OSAyNzAuMDg1MDUzLDQwMy4yMjQ4MjggMjYzLjg5ODYwMiw0MTEuMjc0MTUzIEw5Ni41MDQxMzM1LDYyOS4xMTAzNDcgQzg1LjkzNTM5MTEsNjQyLjg3MzczMiA4OC41MTc1MjcsNjYyLjYwNjA1NSAxMDIuMjgwOTEyLDY3My4xNzQ3OTcgQzEwNy45OTA5NjksNjc3LjU2NTA5NiAxMTQuNzI1ODczLDY3OS42ODgxODUgMTIxLjQwODczNSw2NzkuNjg4MTg1IEMxMzAuODMzODY0LDY3OS42ODgxODUgMTQwLjE1NDkwOCw2NzUuNDYzMzU3IDE0Ni4zNDUzNjIsNjY3LjQxMDAyOSBMMzEzLjcyNzgyMSw0NDkuNTczODM1IEMzMjQuMzA4NTczLDQzNS44MTA0NSAzMjEuNzI2NDM3LDQxNi4wOTAxMzcgMzA3Ljk2MzA1Miw0MDUuNTA5Mzg1IEMzMDIuMjU1NjY0LDQwMS4xMjE3NTUgMjk1LjUxOTQyNSwzOTkgMjg4LjgzOTIzMywzOTkiIGlkPSJGaWxsLTQiPjwvcGF0aD4KICAgICAgICAgICAgPC9nPgogICAgICAgIDwvZz4KICAgIDwvZz4KPC9zdmc+); background-position: top right; background-repeat: no-repeat; background-size: cover; padding: 60px 30px; font-family: Helvetica, Arial; font-size: 15px; line-height: 18px; } h1 { font-size: 64px; font-weight: normal; font-style: italic; padding: 0px 0px 10px 0px; margin: 0px; color: #242424; text-align: left; } p { margin: 20px 0px 0px 0px; background: #ffffff; border-radius: 5px; padding: 40px 20px; text-align: left; } ul { margin: 10px 0px 0px 0px; background: #ffffff; border-radius: 5px; padding: 20px 40px; text-align: left; list-style-type: square; } li { color: #666666; } </style>';
		echo '<p>'.htmlspecialchars($errorMessage).'</p>';
		if ($stackTrace!==false)
		{
			echo '<ul><li>'.join('</li><li>',$stackTrace).'</li></ul>';
		}
		echo '</body></html>';
	}



	/**
	 *   Output error as plain text
	 *   @param string $errorMessage Error message
	 *   @param boolean|array $stackTrace Stack trace (or false if none)
	 *   @return void
	 */

	function outputErrorPlain( $errorMessage, $stackTrace=false )
	{
		echo "FATAL ERROR: ".$errorMessage."\n";
		if ($stackTrace!==false)
		{
			echo "Stack trace:\n";
			echo join("\n",$stackTrace)."\n";
		}
		echo "\n";
	}


?>
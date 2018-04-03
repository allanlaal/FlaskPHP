<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   System helper functions
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */



	/**
	 *   Return a time with milliseconds
	 *   @return float Time in milliseconds
	 */

	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}


	/**
	 *   Return an associative range array
	 *   @param  integer $low Low boundary
	 *   @param  integer $high High boundary
	 *   @param  integer $step Step
	 *   @return array
	 */

	function range_assoc( $low, $high, $step=1 )
	{
		$arr=array();
		foreach (range($low,$high,$step) as $val) $arr[$val]=$val;
		return $arr;
	}


	/**
	 *   Convert an array into associative array
	 *   @param  array $srcArray Array
	 *   @return array
	 */

	function assoc_array( array $srcArray )
	{
		$arr=array();
		foreach ($srcArray as $v)
		{
			$arr[$v]=$v;
		}
		return $arr;
	}
	

	/**
	 *   Return first non-empty parameter from the list
	 *   @param mixed
	 *   @return mixed first non-empty parameter
	 */

	function oneof()
	{
		$args=func_get_args();
		foreach ($args as $arg) if (!empty($arg)) return $arg;
		return null;
	}

	
	/**
	 *   Return var_dump output as string
	 *   @param mixed $var Variable
	 *   @return string
	 */

	function var_dump_str( $var )
	{
		ob_start();
		var_dump($var);
		$vd=ob_get_contents();
		ob_end_clean();
		return $vd;
	}
	

	/**
	 *   Parse a string into array
	 *   @param string|array $input Input as string (or existing array)
	 *   @param string $separator Separator (comma by default)
	 *   @param string $limit Limit (-1 or null for no limit)
	 *   @return array
	 */

	function str_array( $input, $separator=',;\t', $limit=null )
	{
		// Check
		if (is_array($input)) return $input;
		if (!mb_strlen($input)) return array();

		// Parse
		$retarr=array();
		$arr=preg_split('/'.(!empty($separator)?'[':'').$separator.(!empty($separator)?']':'').'/mi',$input,$limit);
		foreach ($arr as $el)
		{
			$el=trim($el);
			if (strlen($el)) $retarr[]=$el;
		}

		// Return
		return $retarr;
	}
	

	/**
	 *   Remove a value from array
	 *   @param  array $array Array pointer
	 *   @param  string $value Value
	 *   @return array
	 */

	function array_remove( &$array, $value )
	{
		foreach ($array as $k => $v)
		{
			if ($v==$value) unset($array[$k]);
		}
	}

	
	/**
	 *   Traverse an array, get a value
	 *   @param  string $path Path
	 *   @param  array|object $data Data (multi-dimensional array or a path of objects)
	 *   @return mixed Value, or false if not found
	 */

	function traverse_get( $path, &$data )
	{
		// Check/init
		if (empty($data)) return null;
		if (!is_object($data) && !is_array($data)) return null;
		$path=str_array($path,'.');

		$level=0;
		$currdata=&$data;
		foreach ($path as $el)
		{
			$level++;

			// Does not exist
			if (is_object($currdata))
			{
				if (!isset($currdata->$el)) return null;
			}
			else
			{
				if (!is_array($currdata)) return null;
				if (!isset($currdata[$el])) return null;
			}

			// Last?
			if ($level==sizeof($path))
			{
				if (is_object($currdata)) return $currdata->$el;
				else return $currdata[$el];
			}

			// Traverse
			if (is_object($currdata))
			{
				$currdata=&$currdata->$el;
			}
			else
			{
				$currdata=&$currdata[$el];
			}
		}
		return null;
	}
	

	/**
	 *   Traverse an array, set a value
	 *   @param  string $path Path
	 *   @param  array|object $data Data (multi-dimensional array or a path of objects)
	 *   @param  mixed $value value
	 *   @throws \Exception
	 *   @return void
	 */

	function traverse_set( string $path, &$data, $value )
	{
		$path=str_array($path,'.');
		$level=0;
		$currdata=&$data;
		foreach ($path as $el)
		{
			$level++;

			// Last?
			if ($level==sizeof($path))
			{
				if (is_object($currdata)) $currdata->$el=$value;
				else $currdata[$el]=$value;
				return;
			}

			// Does not exist, create
			if (is_object($currdata))
			{
				if (!isset($currdata->$el)) $currdata->$el=array();
				if (!is_array($currdata->$el) && !is_object($currdata->$el))
				{
					throw new \Exception('Path node not an array/object.');
					return;
				}
			}
			else
			{
				if (!isset($currdata[$el])) $currdata[$el]=array();
				if (!is_array($currdata[$el]) && !is_object($currdata[$el]))
				{
					if (empty($currData[$el]))
					{
						$currdata[$el]=array();
					}
					else
					{
						throw new \Exception('Path node not an array/object.');
						return;
					}
				}
			}

			// Traverse
			if (is_object($currdata))
			{
				$currdata=&$currdata->$el;
			}
			else
			{
				$currdata=&$currdata[$el];
			}
		}
	}

	
	/**
	 *   Recursive in_array()
	 *   @param  string $needle Needle
	 *   @param  array $haystack Haystack
	 *   @return bool true if match found, false if not
	 */

	function recursive_in_array( string $needle, array $haystack )
	{
		foreach ($haystack as $stalk)
		{
			if ($needle == $stalk || (is_array($stalk) && recursive_in_array($needle, $stalk))) return true;
		}
		return false;
	}
	

	/**
	 *   Recursive array_key_exists()
	 *   @param  string $key Key
	 *   @param  array $array Array
	 *   @return bool true if match found, false if not
	 */

	function recursive_array_key_exists( string $key, array $array )
	{
		foreach ($array as $s_key => $stalk)
		{
			if ($key == $s_key || (is_array($stalk) && recursive_array_key_exists($key, $stalk))) return true;
		}
		return false;
	}

	
	/**
	 *   Sort a dataset (array of associative arrays or objects) on a key of an element
	 *   @param array|object $dataset Dataset
	 *   @param string $key Key to sort on
	 *   @param bool $reverse Reverse direction?
	 *   @param int $sortFlags Sort flags
	 *   @return array sorted dataset
	 *   @throws \Exception
	 */

	function sortdataset( $dataset, $key, $reverse=false, $sortFlags=null )
	{
		$sortarr=array();
		foreach ($dataset as $k => $d)
		{
			if (is_object($d))
			{
				$sortarr['s_'.$k]=$d->{$key};
			}
			elseif (is_array($d))
			{
				$sortarr['s_'.$k]=$d[$key];
			}
			else
			{
				throw new \Exception('Dataset element '.$k.' not an array or an object.');
			}
		}
		asort($sortarr,$sortFlags);
		if ($reverse) $sortarr=array_reverse($sortarr);
		$res=array();
		foreach (array_keys($sortarr) as $k) $res[preg_replace("/^s_/","",$k)]=$dataset[preg_replace("/^s_/","",$k)];
		return $res;
	}
	

	/**
	 *   Exec a command from the given working directory
	 *   @param string $cmd Command
	 *   @param string $cwd Working directory
	 *   @param string $errors Reference to a string where stderr is output
	 *   @return int process return value
	 */

	function exec_with_cwd( string $cmd, string $cwd, string &$errors )
	{
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "w")
		);
		$process = proc_open($cmd, $descriptorspec, $pipes, $cwd);
		if (is_resource($process))
		{
			$errors=stream_get_contents($pipes[1])."\n".stream_get_contents($pipes[2]);
			fclose($pipes[0]);
			fclose($pipes[1]);
			fclose($pipes[2]);
			$return_value = proc_close($process);
		}
		return $return_value;
	}

	
	/**
	 *   Get key name
	 *   @param array $a Array
	 *   @param int $pos Position
	 *   @return string key
	 */

	function array_keyname( array $a, int $pos)
	{
		$temp = array_slice($a, $pos, 1, true);
		return key($temp);
	}


	/**
	 *   Return sign of number
	 *   @param float $number Number
	 *   @return int Sign
	 */

	function sign( float $number )
	{
		return ($number>0)?1:(($number<0)?-1:0);
	}


	/**
	 *   Clean output buffer and return contents
	 *   @return mixed first non-empty parameter
	 */

	function ob_return_contents()
	{
		$c=ob_get_contents();
		ob_end_clean();
		return $c;
	}


	/**
	 *   Encode for JavaScript
	 *   @param $string String
	 *   @return string
	 */

	function jsencode( string $string )
	{
		return str_replace("'","\\'",$string);
	}


	/**
	 *   Clear error for last_error
	 *   @return void
	 */

	function clear_error()
	{
		set_error_handler('var_dump', 0);
		@$undef_var;
		restore_error_handler();
	}


?>
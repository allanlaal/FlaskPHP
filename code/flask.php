<?php


	/**
	 *
	 *   FlaskPHP
	 *   The function that returns the Flask superobject
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 *   @throws \Exception
	 *   @return \Codelab\FlaskPHP\FlaskPHP
	 *
	 */

	function Flask()
	{
		global $FLASK;
		if (!is_object($FLASK)) throw new \Exception('FlaskPHP not initialized.');
		return $FLASK;
	}


?>
<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   HTTP bootstrap code
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	use Codelab\FlaskPHP as FlaskPHP;


	//
	//  Check version
	//

	if (intval(phpversion())<7) trigger_error('PHP version 7.0 or later is required.',E_USER_ERROR);


	//
	//  Include requirements
	//

	require __DIR__ . '/../code/systemfunctions.php';
	require __DIR__ . '/../code/errorhandling.php';
	require __DIR__ . '/../code/flask.php';


	//
	//  Set error handling
	//

	error_reporting(E_ALL);
	ini_set('display_errors',0);
	set_error_handler("errorHandler");
	set_exception_handler("exceptionHandler");
	register_shutdown_function('shutdownHandler');


	//
	//  Init the superobject
	//


	date_default_timezone_set('UTC');
	$appPath=realpath(getcwd().'/../');
	$flaskPath=realpath(__DIR__.'/../');
	$FLASK=new FlaskPHP\FlaskPHP('http',$appPath,$flaskPath);


	//
	//  Init non-configuration-dependent classes
	//

	$FLASK->Locale=new FlaskPHP\Locale\Locale();
	$FLASK->Cache=new FlaskPHP\Cache\Cache();


	//
	//  Config
	//

	$FLASK->Config=new FlaskPHP\Config\Config();
	$FLASK->Config->loadConfig();


	//
	//  Load default locale
	//

	$FLASK->Locale->loadLocale($FLASK->Locale->getDefaultLanguage());


	//
	//  I18n
	//

	mb_internal_encoding('UTF-8');
	$FLASK->I18n=new FlaskPHP\I18n\I18n();
	$FLASK->I18n->initI18n();


	//
	//  Debug interface
	//

	$FLASK->Debug=new FlaskPHP\Debug\Debug();
	$FLASK->Debug->initDebug();


	//
	//  Init database handler & connect
	//

	if (!empty($FLASK->Config->get('db.handler')))
	{
		$dbHandler=$FLASK->Config->get('db.handler');
		$FLASK->DB=new $dbHandler();
		$FLASK->DB->connect();
	}
	elseif (!empty($FLASK->Config->get('db.type')))
	{
		$dbHandler='\Codelab\FlaskPHP\DB\\'.$FLASK->Config->get('db.type').'DB';
		$FLASK->DB=new $dbHandler();
		$FLASK->DB->connect();
	}


	//
	//  Init environment
	//

	if (is_array($FLASK->Config->get('app.bootstrap')))
	{
		foreach ($FLASK->Config->get('app.bootstrap') as $appBootstrapFile)
		{
			$resolvedAppBootstrapFile=Flask()->resolvePath($appBootstrapFile);
			if (!$resolvedAppBootstrapFile) throw new FlaskPHP\Exception\FatalException('App bootstrap file '.$appBootstrapFile.' not found.');
			require $resolvedAppBootstrapFile;
		}
	}


	//
	//  Init session
	//

	$FLASK->Session=new \Codelab\FlaskPHP\Session\ScriptSessionSimulator();
	$FLASK->Session->loadSession();


	//
	//  Init user
	//

	if ($FLASK->Config->get('user.handler'))
	{
		if (!($FLASK->Config->get('user.handler') instanceof FlaskPHP\User\UserInterface)) throw new FlaskPHP\Exception\FatalException('User handler not an instance of UserInterface.');
		$FLASK->User=$FLASK->Config->get('user.handler');
	}
	else
	{
		$FLASK->User=new FlaskPHP\User\UserInterface();
	}


	//
	//  Init request and response classes
	//

	$FLASK->Request=new FlaskPHP\Request\Request();
	$FLASK->Response=new FlaskPHP\Response\Response();


	//
	//  Run script
	//

	$classList=get_declared_classes();
	$scriptClass=null;
	foreach ($classList as $className)
	{
		if (is_subclass_of($className,'Codelab\FlaskPHP\Script\ScriptInterface'))
		{
			if (is_object($scriptClass)) throw new FlaskPHP\Exception\FatalException('Multiple ScriptInterface classes defined.');
			$scriptClass=new $className();
		}
	}
	if (!is_object($scriptClass))
	{
		throw new FlaskPHP\Exception\FatalException('No ScriptInterface classes defined.');
	}
	$returnCode=$scriptClass->runScript();


	//
	//  Close database connection
	//

	$FLASK->DB->disconnect();


	//
	//  Return script value
	//

	return $returnCode;



?>
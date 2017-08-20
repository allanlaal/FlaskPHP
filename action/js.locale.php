<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Base actions: locale JS
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	use Codelab\FlaskPHP as FlaskPHP;

	class JsLocaleAction extends FlaskPHP\Action\ActionInterface
	{


		public function runAction()
		{
			try
			{
				// Pick and load locale
				if (mb_strlen(Flask()->Request->uriVarByPos(2)['name']) && Flask()->Locale->localeExists(Flask()->Request->uriVarByPos(2)['name']))
				{
					$locale=Flask()->Request->uriVarByPos(2)['name'];
				}
				elseif (mb_strlen(Flask()->Session->get('LANG')))
				{
					$locale=Flask()->Session->get('LANG');
				}
				else
				{
					$locale=Flask()->Request->requestLang;
				}
				Flask()->Locale->loadLocale($locale);

				// Create object
				$localeJS ="Locale = new Object();\n";
				$localeJS.="Locale.get = function(tag) { return Locale.data[tag.toLowerCase()]; }\n";

				// Output translations
				$localeJS.="Locale.tag = new String('".Flask()->Locale->localeLanguage."');\n";
				$localeJS.="Locale.data = {";
				$i=0;
				foreach (Flask()->Locale->localeData as $key => $value)
				{
					$localeJS.=($i?",\n":"\n").'"'.$key.'":"'.str_replace('"','\"',$value).'"';
					$i++;
				}
				$localeJS.="\n}\n\n";

				// Output
				$response=new FlaskPHP\Response\RawResponse();
				$response->setContentType('application/javascript; charset=UTF-8');
				$response->setContentDisposition('inline');
				$response->setContent($localeJS);
				return $response;
			}
			catch (\Exception $e)
			{
				throw new FlaskPHP\Exception\FatalException('ERROR trying to output locale: '.$e->getMessage(),500);
			}
		}


	}


?>
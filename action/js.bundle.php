<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Base actions: JS bundle
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	use Codelab\FlaskPHP as FlaskPHP;

	class JsBundleAction extends FlaskPHP\Action\ActionInterface
	{


		public function runAction()
		{
			try
			{
				// Check
				$fileName=Flask()->Request->uriVarByPos(2)['name'];
				if (!mb_strlen($fileName)) throw new FlaskPHP\Exception\FatalException('Invalid parameters.');
				if (mb_strpos($fileName,'..')!==false) throw new FlaskPHP\Exception\FatalException('Invalid parameters.');
				$fileName=oneof(Flask()->Config->get('app.assetcachepath'),Flask()->Config->getTmpPath()).'/'.Flask()->Config->get('app.id').'.asset.'.$fileName;
				if (!file_exists($fileName)) throw new Exception('Invalid parameters.');

				// Output
				$response=new FlaskPHP\Response\RawResponse();
				$response->setContentType('application/javascript; charset=UTF-8');
				$response->setContentDisposition('inline');
				$response->setResponseContentSourceFile($fileName);
				return $response;
			}
			catch (\Exception $e)
			{
				throw new FlaskPHP\Exception\FatalException('ERROR trying to output JS bundle: '.$e->getMessage(),500);
			}
		}


	}


?>
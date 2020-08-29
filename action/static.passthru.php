<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Base actions: static passthru
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	use Codelab\FlaskPHP as FlaskPHP;

	class StaticPassthruAction extends FlaskPHP\Action\ActionInterface
	{


		public function runAction()
		{
			try
			{
				// Check
				if (!is_array(Flask()->Request->requestUriVar) || !sizeof(Flask()->Request->requestUriVar)) throw new FlaskPHP\Exception\Exception('Invalid request.');
				$fileName=join('/',array_keys(Flask()->Request->requestUriVar));
				if (!file_exists(Flask()->getFlaskPath().'/static/'.$fileName)) throw new FlaskPHP\Exception\Exception('File '.$fileName.' not found.',404);
				$fileName=Flask()->getFlaskPath().'/static/'.$fileName;

				// Output
				$response=new FlaskPHP\Response\RawResponse();
				$response->setContentType(oneof(FlaskPHP\Util::getMimeType($fileName),'application/octet-stream'));
				$response->setContentDisposition('inline');
				$response->setExpires(strtotime('+1year'));
				$response->setHeader('Last-Modified',date('r',filemtime($fileName)));
				$response->setResponseContentSourceFile($fileName);
				return $response;
			}
			catch (\Exception $e)
			{
				$response=new FlaskPHP\Response\RawResponse();
				$response->setContentType('text/plain; charset=UTF-8');
				$response->setContentDisposition('inline');
				$response->setStatus(oneof($e->getCode(),500));
				$response->setContent('ERROR: '.$e->getMessage());
				return $response;
			}
		}


	}


?>
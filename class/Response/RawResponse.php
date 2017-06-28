<?php


	/**
	 *
	 *   FlaskPHP
	 *   The raw response class
	 *
	 *   @author Codelab Solutions OÜ <codelab@codelab.ee>
	 *
	 */


	namespace Codelab\FlaskPHP\Response;
	use Codelab\FlaskPHP as FlaskPHP;


	class RawResponse extends ResponseInterface
	{


		/**
		 *   Response content-type (for raw)
		 *   @var string
		 *   @access public
		 */

		public $responseContentType = null;


		/**
		 *   Response content source (string or file)
		 *   @var string
		 *   @access public
		 */

		public $responseContentSource = 'string';


		/**
		 *   Response content filename
		 *   @var string
		 *   @access public
		 */

		public $responseContentFilename = null;


		/**
		 *   Delete content source file after display?
		 *   @var bool
		 *   @access public
		 */

		public $responseContentFileDelete = false;


		/**
		 *   Filename
		 *   @var string
		 *   @access public
		 */

		public $responseFileName = null;


		/**
		 *   Content disposition (inline|attachment)
		 *   @var string
		 *   @access public
		 */

		public $responseContentDisposition = 'attachment';


		/**
		 *   Init
		 *   @access public
		 *   @param string $responseContent Response content
		 *   @throws \Exception
		 *   @return \Codelab\FlaskPHP\Response\RawResponse
		 */

		public function __construct( string $responseContent=null, string $responseContentType=null, string $responseContentDisposition=null )
		{
			if ($responseContent!==null)
			{
				$this->responseContent=$responseContent;
				if ($responseContentType!==null)
				{
					$this->setContentType($responseContentType);
					if ($responseContentDisposition!==null)
					{
						$this->setContentDisposition($responseContentDisposition);
					}
				}
			}
		}


		/**
		 *   Set content source
		 *   @access public
		 *   @param string $responseContentSource filename
		 *   @throws \Exception
		 *   @return void
		 */

		public function setResponseContentSource( string $responseContentSource )
		{
			// Check
			if (!in_array($responseContentSource,['string','file'])) throw new FlaskPHP\Exception\InvalidParameterException('Invalid content source.');

			// Set
			$this->responseContentSource=$responseContentSource;
		}


		/**
		 *   Set content source file
		 *   @access public
		 *   @param string $responseContentFilename Source file name
		 *   @param bool $responseContentFileDelete Delete file after display?
		 *   @throws \Exception
		 *   @return void
		 */

		public function setResponseContentSourceFile( string $responseContentFilename, bool $responseContentFileDelete=false )
		{
			$this->responseContentSource='file';
			$this->responseContentFilename=$responseContentFilename;
			$this->responseContentFileDelete=$responseContentFileDelete;
		}


		/**
		 *   Set content source file
		 *   @access public
		 *   @param bool $responseContentFileDelete Delete file after display?
		 *   @throws \Exception
		 *   @return void
		 */

		public function setResponseContentSourceFileDelete( bool $responseContentFileDelete=false )
		{
			$this->responseContentFileDelete=$responseContentFileDelete;
		}


		/**
		 *   Set response filename
		 *   @access public
		 *   @param string $responseFileName filename
		 *   @return void
		 */

		public function setFileName( string $responseFileName )
		{
			$this->responseFileName=$responseFileName;
		}


		/**
		 *   Set content-type
		 *   @access public
		 *   @param string $responseContentType Content-type
		 *   @return void
		 */

		public function setContentType( string $responseContentType )
		{
			$this->responseContentType=$responseContentType;
		}


		/**
		 *   Set content disposition
		 *   @access public
		 *   @param string $responseContentDisposition Content disposition
		 *   @return void
		 */

		public function setContentDisposition( string $responseContentDisposition )
		{
			$this->responseContentDisposition=$responseContentDisposition;
		}


		/**
		 *   Render response
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 */

		public function renderResponse()
		{
			header('Content-type: '.$this->responseContentType.((!strncmp($this->responseContentType,'text/',5) && strpos($this->responseContentType,'charset=')===FALSE)?'; charset=UTF-8':''));
			if (!array_key_exists('Content-length',$this->responseHeader))
			{
				if ($this->responseContentSource=='file')
				{
					header('Content-length: '.intval(filesize($this->responseContentFilename)));
				}
				else
				{
					header('Content-length: '.intval(strlen($this->responseContent)));
				}
			}
			if (!empty($this->responseFileName))
			{
				header('Content-disposition: '.$this->responseContentDisposition.'; filename='.urlencode($this->responseFileName));
			}
			if ($this->responseContentSource=='file')
			{
				if (!is_readable($this->responseContentFilename))
				{
					throw new FlaskPHP\Exception\FatalException('Response source file not readable.',500);
				}
				readfile($this->responseContentFilename);
				if ($this->responseContentFileDelete)
				{
					@unlink($this->responseContentFilename);
				}
			}
			else
			{
				echo $this->responseContent;
			}
		}


	}


?>
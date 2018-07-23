<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The file streamer action
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Action;
	use Codelab\FlaskPHP as FlaskPHP;


	class FileStreamerAction extends ActionInterface
	{


		/**
		 *   Include standard Ajax action parameters
		 */

		use FlaskPHP\Traits\AjaxActionParameters;


		/**
		 *   The stream
		 *   @var resource
		 *   @access private
		 */

		private $streamHandle = null;


		/**
		 *   The content
		 *   @var string
		 *   @access private
		 */

		private $streamContent = null;


		/**
		 *   The buffer size
		 *   @var int
		 *   @access private
		 */

		private $bufferSize = 102400;


		/**
		 *   Start point
		 *   @var int
		 *   @access private
		 */

		private $streamStart = -1;


		/**
		 *   End point
		 *   @var int
		 *   @access private
		 */

		private $streamEnd = -1;


		/**
		 *   Size
		 *   @var int
		 *   @access private
		 */

		private $streamSize = 0;


		/**
		 *
		 *   Init streamer action
		 *   --------------------
		 *   @access public
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public function initStreamer()
		{
			// This can be implemented in the subclass.
		}


		/**
		 *
		 *   Set source model
		 *   ----------------
		 *   @access public
		 *   @param FlaskPHP\Model\ModelInterface $Model Model object
		 *   @param string $baseField Base field
		 *   @param string $type Type
		 *   @throws \Exception
		 *   @return FileStreamerAction
		 *
		 */

		public function setSourceModel( FlaskPHP\Model\ModelInterface $Model, string $baseField, string $type=null )
		{
			$this->setParam('source','model');
			$this->setParam('source_model',$Model);
			$this->setParam('source_model_basefield',$baseField);
			$this->setParam('source_model_type',$type);
			return $this;
		}


		/**
		 *
		 *   Set source file
		 *   ---------------
		 *   @access public
		 *   @param string $fileName Filename
		 *   @throws \Exception
		 *   @return FileStreamerAction
		 *
		 */

		public function setSourceFile( string $fileName )
		{
			$this->setParam('source','file');
			$this->setParam('source_filename',$fileName);
			return $this;
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
				// Set defaults
				$this->setDefaults();

				// Init delete
				$this->initStreamer();

				// Response
				$Response=new FlaskPHP\Response\RawResponse();

				// Open from model
				if ($this->getParam('source')=='model')
				{
					// Check model
					$Model=$this->getParam('source_model');
					if (!is_object($Model)) throw new FlaskPHP\Exception\InvalidParameterException('No model set.');

					// Check base field and size
					$baseField=$this->getParam('source_model_basefield');
					if (!mb_strlen($baseField)) throw new FlaskPHP\Exception\InvalidParameterException('Model base field not defined.');
					if (!intval($Model->{$baseField.'_fsize'})) throw new FlaskPHP\Exception\InvalidParameterException('File empty.');

					// DB storage
					if (FlaskPHP\File\File::dbStorage())
					{
						$this->streamContent=$Model->{$baseField};
						$this->streamSize=strlen($this->streamContent);
						$Response->setExpires(time()+864000);
						$Response->setHeader('Last-modified',date('r',strtotime(($Model->mod_stamp!='0000-00-00 00:00:00'?$Model->mod_tstamp:$Model->add_tstamp))));
					}

					// Check file
					else
					{
						$fileType=oneof($this->getParam('source_model_type'),$Model->getParam('table'));
						$sourceFile=FlaskPHP\File\File::getFileName($Model->_oid,$fileType);
						if (!file_exists($sourceFile)) throw new FlaskPHP\Exception\InvalidParameterException('File does not exist.');
						if (!is_readable($sourceFile)) throw new FlaskPHP\Exception\InvalidParameterException('File is not readable.');

						// Open stream
						if (!($this->streamHandle=fopen($sourceFile,'rb'))) throw new FlaskPHP\Exception\InvalidParameterException('Could not open file stream for reading.');
						$this->streamSize=filesize($sourceFile);
						$Response->setHeader('Last-Modified',date('r',@filemtime($sourceFile)));
					}
				}

				// Open from file
				elseif ($this->getParam('source')=='file')
				{
					// Check file
					$sourceFile=$this->getParam('source_filename');
					if (!mb_strlen($sourceFile)) throw new FlaskPHP\Exception\InvalidParameterException('Filename not defined.');
					if (!file_exists($sourceFile)) throw new FlaskPHP\Exception\InvalidParameterException('File does not exist.');
					if (!is_readable($sourceFile)) throw new FlaskPHP\Exception\InvalidParameterException('File is not readable.');

					// Open stream
					if (!($this->streamHandle=fopen($sourceFile,'rb'))) throw new FlaskPHP\Exception\InvalidParameterException('Could not open file stream for reading.');
					$this->streamSize=filesize($sourceFile);
					$Response->setHeader('Last-Modified',date('r',@filemtime($sourceFile)));
				}

				// No source
				else
				{
					throw new FlaskPHP\Exception\InvalidParameterException('No source defined.');
				}

				// Set accept range
				$this->streamEnd=intval($this->streamSize-1);
				$Response->setHeader('Accept-Ranges','0-'.$this->streamEnd);

				// Range
				if (isset($_SERVER['HTTP_RANGE']))
				{
					$c_start=$this->streamStart;
					$c_end=$this->streamEnd;

					list(,$range)=explode('=',$_SERVER['HTTP_RANGE'],2);
					if (strpos($range,',')!==false)
					{
						$Response->setStatus(416);
						$Response->setHeader('Content-range','bytes '.$this->streamStart.'-'.$this->streamEnd.'/'.$this->streamSize);
						throw new FlaskPHP\Exception\Exception('416 Requested Range Not Satisfiable');
					}

					if ($range == '-')
					{
						$c_start=$this->streamSize - substr($range, 1);
					}
					else
					{
						$range = explode('-', $range);
						$c_start = $range[0];
						$c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
					}
					$c_end = ($c_end > $this->streamEnd) ? $this->streamEnd : $c_end;
					if ($c_start > $c_end || $c_start > $this->streamSize - 1 || $c_end >= $this->streamSize)
					{
						$Response->setStatus(416);
						$Response->setHeader('Content-range','bytes '.$this->streamStart.'-'.$this->streamEnd.'/'.$this->streamSize);
						throw new FlaskPHP\Exception\Exception('416 Requested Range Not Satisfiable');
					}
					$this->streamStart = $c_start;
					$this->streamEnd = $c_end;
					$length = $this->streamEnd - $this->streamStart + 1;
					fseek($this->streamHandle, $this->streamStart);

					$Response->setStatus(206);
					$Response->setHeader('Content-length',$length);
					$Response->setHeader('Content-range','bytes '.$this->streamStart.'-'.$this->streamEnd.'/'.$this->streamSize);
				}

				// All of it
				else
				{
					$Response->setHeader('Content-length',$this->streamSize);
				}

				// Content-type header
				if ($this->getParam('source')=='model')
				{
					$mimeType=$Model->{$baseField.'_ctype'};
					$Response->setContentType($mimeType);
					$Response->setFileName($Model->{$baseField.'_fname'});
				}
				else
				{
					$mimeType=FlaskPHP\File\File::getMimeType($sourceFile);
					$Response->setContentType($mimeType);
					$Response->setFileName($Model->{$baseField.'_fname'});
				}
				if (preg_match('/^image\//',$mimeType) || preg_match('/^video\//',$mimeType) || preg_match('/^audio\//',$mimeType) || $mimeType=='application/pdf')
				{
					$Response->setContentDisposition('inline');
				}

				// Stream from string
				if (!empty($this->streamContent))
				{
					$c=substr($this->streamContent,$this->streamStart,$this->streamSize);
				}

				// Stream from file
				else
				{
					$c='';
					$i=$this->streamStart;
					set_time_limit(0);
					while(!feof($this->streamHandle) && $i<=$this->streamEnd)
					{
						$bytesToRead=$this->bufferSize;
						if(($i+$bytesToRead)>$this->streamEnd)
						{
							$bytesToRead=$this->streamEnd - $i + 1;
						}
						$c.=fread($this->streamHandle, $bytesToRead);
						$i+=$bytesToRead;
					}
				}

				// Return response
				$Response->setContent($c);
				return $Response;
			}
			catch (\Exception $e)
			{
				// Close file handle if open
				if (!empty($this->streamHandle))
				{
					fclose($this->streamHandle);
				}

				// Return error
				return new FlaskPHP\Response\RawResponse(
					'ERROR: '.$e->getMessage(),
					'text/plain',
					'inline'
				);
			}
		}


	}


?>
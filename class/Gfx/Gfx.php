<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The graphics routines class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Gfx;
	use Codelab\FlaskPHP as FlaskPHP;


	class Gfx
	{


		/**
		 *   Image types
		 *   @var array
		 *   @access public
		 *   @static
		 */

		public static $imageTypes=[
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'png' => 'image/png'
		];


		/**
		 *
		 *   Create canvas
		 *   -------------
		 *   @access public
		 *   @static
		 *   @param int $canvasWidth Width
		 *   @param int $canvasHeight Height
		 *   @param string $outputFormat Output format
		 *   @throws \Exception
		 *   @return resource
		 *
		 */

		public static function createCanvas( int $canvasWidth, int $canvasHeight, string $outputFormat='jpg' )
		{
			// Check
			if (!function_exists('imagecreatetruecolor')) throw new FlaskPHP\Exception\FatalException('GD functions are not available.');

			// Create
			$img=imagecreatetruecolor($canvasWidth,$canvasHeight);
			if ($outputFormat=='png')
			{
				imagecolortransparent($img,imagecolorallocatealpha($img, 0, 0, 0, 127));
				imagealphablending($img,false);
				imagesavealpha($img,true);
			}
			else
			{
				$white=imagecolorallocate($img, 255, 255, 255);
				imagefill($img, 0, 0, $white);
			}
			return $img;
		}


		/**
		 *
		 *   Resize image
		 *   ------------
		 *   @access public
		 *   @static
		 *   @param string $imgData Image binary as string
		 *   @param int $size Longer side max size
		 *   @param string $outputFormat Output format
		 *   @param string $watermark Watermark image
		 *   @param int $setWidth Image width
		 *   @param int $setHeight Image height
		 *   @param int $canvasWidth Canvas width
		 *   @param int $canvasHeight Canvas height
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public static function resizeImage( string $imgData, int $size=0, string $outputFormat='jpg', string $watermark=null, int $setWidth=null, int $setHeight=null, int $canvasWidth=null, int $canvasHeight=null )
		{
			// Check
			if (!function_exists('imagecreatefromstring')) throw new FlaskPHP\Exception\FatalException('GD functions are not available.');

			// Lubame output formatiks content-type
			$outputFormat=mb_strtolower(str_replace('image/','',$outputFormat));

			// Nothing to do?
			if (!$size && !$setWidth && !$setHeight && !$canvasWidth && !$canvasHeight && !strlen($watermark))
			{
				return $imgData;
			}

			// Init
			$src=imagecreatefromstring($imgData);
			$width=imagesx($src);
			$height=imagesy($src);
			$aspectRatio=($height>$width)?($width/$height):($height/$width);

			if ($canvasWidth && $canvasHeight)
			{
				// Create canvas
				$img=static::createCanvas($canvasWidth,$canvasHeight,$outputFormat);

				if ($width<=$canvasWidth && $height<=$canvasHeight)
				{
					$posX=floor(($canvasWidth-$width)/2);
					$posY=floor(($canvasHeight-$height)/2);
					imagecopyresampled($img,$src,$posX,$posY,0,0,$width,$height,$width,$height);
				}
				else
				{
					$rW=($canvasWidth/$width);
					$rH=($canvasHeight/$height);
					if ($rW<$rH)
					{
						$newW=$canvasWidth;
						if ($width > $height)
						{
							$newH=abs($newW * $aspectRatio);
						}
						else
						{
							$newH=abs($newW / $aspectRatio);
						}
					}
					else
					{
						$newH=$canvasHeight;
						if ($width > $height)
						{
							$newW=abs($newH / $aspectRatio);
						}
						else
						{
							$newW=abs($newH * $aspectRatio);
						}
					}
					$posX=floor(($canvasWidth-$newW)/2);
					$posY=floor(($canvasHeight-$newH)/2);
					imagecopyresampled($img,$src,$posX,$posY,0,0,$newW,$newH,$width,$height);
				}
			}
			elseif ($setWidth && $setHeight)
			{
				$img=static::createCanvas($setWidth,$setHeight,$outputFormat);
				imagecopyresampled($img,$src,0,0,0,0,$setWidth,$setHeight,$width,$height);
			}
			elseif ($setWidth)
			{
				$newW=$setWidth;
				if ($width > $height)
				{
					$newH=abs($newW * $aspectRatio);
				}
				else
				{
					$newH=abs($newW / $aspectRatio);
				}
				$img=static::createCanvas($newW,$newH,$outputFormat);
				imagecopyresampled($img,$src,0,0,0,0,$newW,$newH,$width,$height);
			}
			elseif ($setHeight)
			{
				$newH=$setHeight;
				if ($width > $height)
				{
					$newW=abs($newH / $aspectRatio);
				}
				else
				{
					$newW=abs($newH * $aspectRatio);
				}
				$img=static::createCanvas($newW,$newH,$outputFormat);
				imagecopyresampled($img,$src,0,0,0,0,$newW,$newH,$width,$height);
			}
			elseif ($size)
			{
				if ($width <= $size && $height <= $size)
				{
					$newW=$width;
					$newH=$height;
					$img=$src;
				}
				else
				{
					if ($height > $width)
					{
						$newH=$size;
						$newW=abs($newH * $aspectRatio);
					}
					else
					{
						$newW=$size;
						$newH=abs($newW * $aspectRatio);
					}
					$img=static::createCanvas($newW,$newH,$outputFormat);
					imagecopyresampled($img,$src,0,0,0,0,$newW,$newH,$width,$height);
				}
			}
			else
			{
				$img=$src;
				$newW=$width;
				$newH=$height;
			}

			// Watermark
			if (strlen($watermark))
			{
				imagealphablending($img, true);
				$wmimg=imagecreatefrompng($watermark);
				$wm_w=imagesx($wmimg);
				$wm_h=imagesy($wmimg);
				$wmpos_x=$newW-$wm_w;
				$wmpos_y=$newH-$wm_h;
				imagecopy($img,$wmimg,$wmpos_x,$wmpos_y,0,0,$wm_w,$wm_h);
			}

			// Return image
			ob_start();
			imageinterlace($img,1);
			switch($outputFormat)
			{
				case 'png':
					if (imagetypes() & IMG_PNG)
					{
						imagepng($img,NULL,5);
					}
					else
					{
						imagejpeg($img,NULL,95);
					}
					break;
				default:
					imagejpeg($img,NULL,95);
					break;
			}

			$imgsrc=ob_get_contents();
			ob_end_clean();
			imagedestroy($img);
			return $imgsrc;
		}


		/**
		 *
		 *   Crop image
		 *   ----------
		 *   @access public
		 *   @static
		 *   @param resource $imgData Image data
		 *   @param int $x1 Crop coords: X1
		 *   @param int $y1 Crop coords: Y1
		 *   @param int $x2 Crop coords: X2
		 *   @param int $y2 Crop coords: Y2
		 *   @throws \Exception
		 *   @return resource
		 *
		 */

		public static function cropImage( $imgData, $x1, $y1, $x2, $y2 )
		{
			// Check
			if (!function_exists('imagecreatefromstring')) throw new FlaskPHP\Exception\FatalException('GD functions are not available.');

			// Init
			$src=imagecreatefromstring($imgData);
			$width=($x2-$x1);
			$height=($y2-$y1);

			// Crop
			$img=imagecreatetruecolor($width,$height);
			imagecopyresampled($img,$src,0,0,$x1,$y1,$width,$height,$width,$height);

			// Return image
			ob_start();
			imageinterlace($img,1);
			imagejpeg($img,NULL,95);
			$imgsrc=ob_get_contents();
			ob_end_clean();
			imagedestroy($img);
			return $imgsrc;
		}


		/**
		 *
		 *   Crop image to square
		 *   --------------------
		 *   @access public
		 *   @static
		 *   @param resource $imgData Image data
		 *   @throws \Exception
		 *   @return resource
		 *
		 */

		public static function cropSquare( $imgData )
		{
			// Check
			if (!function_exists('imagecreatefromstring')) throw new FlaskPHP\Exception\FatalException('GD functions are not available.');

			// Init
			$src=imagecreatefromstring($imgData);
			$width=imagesx($src);
			$height=imagesy($src);

			// Calculating the part of the image to use for thumbnail
			if ($width > $height)
			{
				$thumbSize=$height;
				$x1=round($width/2-($thumbSize/2));
				$x2=$x1+$thumbSize;
				$y1=0;
				$y2=$height;
			}
			else
			{
				$thumbSize=$width;
				$x1=0;
				$x2=$width;
				$y1=round($height/2-($thumbSize/2));
				$y2=$y1+$thumbSize;
			}

			// Crop
			$img=imagecreatetruecolor($thumbSize,$thumbSize);
			imagecopyresampled($img,$src,0,0,$x1,$y1,$thumbSize,$thumbSize,$thumbSize,$thumbSize);

			// Return image
			ob_start();
			imageinterlace($img,1);
			imagejpeg($img,NULL,95);
			$imgsrc=ob_get_contents();
			ob_end_clean();
			imagedestroy($img);
			return $imgsrc;
		}

		
		/**
		 *
		 *   Get image dimensions
		 *   --------------------
		 *   @access public
		 *   @static
		 *   @param resource $imgData Image data
		 *   @throws \Exception
		 *   @return resource
		 *
		 */

		public static function imageSize( $imgData )
		{
			// Check
			if (!function_exists('imagecreatefromstring')) throw new FlaskPHP\Exception\FatalException('GD functions are not available.');

			// Get dimensions
			$src=imagecreatefromstring($imgData);
			$width=imagesx($src);
			$height=imagesy($src);
			imagedestroy($src);
			return [$width,$height];
		}


		/**
		 *
		 *   Display model image
		 *   -------------------
		 *   @access public
		 *   @static
		 *   @param FlaskPHP\Model\ModelInterface $model Model
		 *   @param string $baseField Base field
		 *   @throws \Exception
		 *   @return FlaskPHP\Response\ResponseInterface
		 *
		 */

		public static function displayModelImage( FlaskPHP\Model\ModelInterface $model, $baseField, $type=null, $param=null )
		{
			global $LAB;

			// Check
			if (!is_object($model) || !$model->_loaded || !intval($model->{$baseField.'_fsize'}))
			{
				if (Flask()->Debug->devEnvironment)
				{
					$response=new FlaskPHP\Response\RawResponse();
					$response->setContentType('text/plain');
					$response->setExpires(time());
					if (!is_object($model))
					{
						$response->setContent('$model is not an object.');
					}
					elseif (!$model->_loaded)
					{
						$response->setContent('$model not loaded.');
					}
					else
					{
						$response->setContent($baseField.'_fsize is 0');
					}
					return $response;
				}
				else
				{
					$response=new FlaskPHP\Response\RawResponse();
					$response->setContentType('text/plain');
					$response->setExpires(time());
					$response->setContent(file_get_contents(Flask()->getFlaskPath().'/static/gfx/icon-noimage.png'));
					return $response;
				}
			}

			// Cannot be resized?
			if (!in_array($model->{$baseField.'_ctype'},array('image/jpeg','image/png','image/gif')))
			{
				$response=new FlaskPHP\Response\RawResponse();
				$response->setContentType($model->{$baseField.'_ctype'});
				$response->setContentDisposition('inline');
				$response->setExpires(time()+(array_key_exists('expires',$param)?$param['expires']:(365*86400)));
				if (FlaskPHP\File\File::dbStorage())
				{
					$response->setContent($model->{$baseField});
				}
				else
				{
					$response->setContent(FlaskPHP\File\File::getFile($model->_oid,oneof($type,$model->getParam('table'))));
				}
				return $response;
			}

			// Init vars
			$size=oneof($param['size'],intval(Flask()->Request->uriVar('s')));
			$width=oneof($param['width'],intval(Flask()->Request->uriVar('w')));
			$height=oneof($param['height'],intval(Flask()->Request->uriVar('h')));
			$canvasWidth=oneof($param['canvasWidth'],intval(Flask()->Request->uriVar('cw')));
			$canvasHeight=oneof($param['canvasHeight'],intval(Flask()->Request->uriVar('ch')));
			$noCache=oneof($param['nocache'],intval(Flask()->Request->uriVar('nc')))?true:false;

			// Cache
			if (!$noCache)
			{
				if (FlaskPHP\File\File::dbStorage() && mb_strlen(Flask()->CONFIG->get('app.cachepath')))
				{
					$cacheFileName=FlaskPHP\File\File::getCacheFileName($model->_oid,oneof($type,$model->getParam('table')));
					if (!empty($size)) $cacheFileName.='.s'.intval($size);
					if (!empty($width)) $cacheFileName.='.w'.intval($width);
					if (!empty($height)) $cacheFileName.='.h'.intval($height);
					if (!empty($canvasWidth)) $cacheFileName.='.cw'.intval($canvasWidth);
					if (!empty($canvasHeight)) $cacheFileName.='.ch'.intval($canvasHeight);
				}
				elseif (!FlaskPHP\File\File::dbStorage())
				{
					$cacheFileName=FlaskPHP\File\File::getFileName($model->_oid,oneof($type,$model->getParam('table')));
					if (!empty($size)) $cacheFileName.='.s'.intval($size);
					if (!empty($width)) $cacheFileName.='.w'.intval($width);
					if (!empty($height)) $cacheFileName.='.h'.intval($height);
					if (!empty($canvasWidth)) $cacheFileName.='.cw'.intval($canvasWidth);
					if (!empty($canvasHeight)) $cacheFileName.='.ch'.intval($canvasHeight);
				}
				if (mb_strlen($cacheFileName) && file_exists($cacheFileName))
				{
					$response=new FlaskPHP\Response\RawResponse();
					$response->setContentType(oneof($param['force_ctype'],$model->{$baseField.'_ctype'}));
					$response->setContentDisposition('inline');
					$response->setExpires(time()+(array_key_exists('expires',$param)?$param['expires']:(365*86400)));
					$response->setContent(file_get_contents($cacheFileName));
					return $response;
				}
			}

			// Get image
			if (FlaskPHP\File\File::dbStorage())
			{
				$img=$model->{$baseField};
			}
			else
			{
				$img=FlaskPHP\File\File::getFile($model->_oid,oneof($type,$model->getParam('table')));
			}

			// Resize if needed
			switch ($model->{$baseField.'_ctype'})
			{
				case 'image/png':
					$outputFormat='png';
					break;
				default:
					$outputFormat='jpg';
					break;
			}
			$img=static::resizeImage($img,$size,$outputFormat,'',$width,$height,$canvasWidth,$canvasHeight);

			// Cache: write to cache
			if (!$noCache && mb_strlen($cacheFileName))
			{
				file_put_contents($cacheFileName,$img);
			}

			// Output
			$response=new FlaskPHP\Response\RawResponse();
			$response->setContentType(oneof($param['force_ctype'],$model->{$baseField.'_ctype'}));
			$response->setContentDisposition('inline');
			$response->setExpires(time()+(array_key_exists('expires',$param)?$param['expires']:(365*86400)));
			$response->setContent($img);
			return $response;
		}


	}


?>
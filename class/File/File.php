<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The file management class
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\File;
	use Codelab\FlaskPHP as FlaskPHP;


	class File
	{


		/**
		 *
		 *   Do we store files in DB?
		 *   ------------------------
		 *   @access public
		 *   @static
		 *   @throws \Exception
		 *   @return boolean
		 *
		 */

		public static function dbStorage()
		{
			return (Flask()->Config->get('app.filepath')?false:true);
		}


		/**
		 *   Do we store files in file system?
		 *   @return boolean
		 */

		public static function fileStorage()
		{
			return (Flask()->Config->get('app.filepath')?true:false);
		}


		/**
		 *
		 *   Get file data directory, make necessary paths in the process
		 *   ------------------------------------------------------------
		 *   @access public
		 *   @static
		 *   @param int $OID Object OID
		 *   @return string directory
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public static function getFileDataDir( int $OID=null )
		{
			// Check
			$fileDataDir=Flask()->Config->get('app.filepath');
			if (!mb_strlen($fileDataDir)) throw new FlaskPHP\Exception\Exception('File storage not enabled.');

			// Init
			umask(0);
			if (!file_exists($fileDataDir)) throw new FlaskPHP\Exception\Exception('File data path ('.$fileDataDir.') does not exist.');
			if (!is_writable($fileDataDir)) throw new FlaskPHP\Exception\Exception('File data path ('.$fileDataDir.') not writable by server.');

			// File dir
			if ($OID!==null)
			{
				$fileDataDir.='/'.sprintf("%03d",mb_substr(strval($OID),-3));
				if (!file_exists($fileDataDir))
				{
					mkdir($fileDataDir,0755);
				}
			}

			// Return
			return $fileDataDir;
		}


		/**
		 *
		 *   Get file cache directory, make necessary paths in the process
		 *   -------------------------------------------------------------
		 *   @access public
		 *   @static
		 *   @param int $OID Object OID
		 *   @return string directory
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public static function getFileCacheDir( int $OID=null )
		{
			global $LAB;

			// Check
			$fileDataDir=Flask()->Config->get('app.cachepath');
			if (!mb_strlen($fileDataDir)) throw new FlaskPHP\Exception\Exception('File storage not enabled.');

			// Init
			umask(0);
			$fileDataDir=$LAB->CONFIG->get('app.cachepath');
			if (!file_exists($fileDataDir)) throw new FlaskPHP\Exception\Exception('Cache path ('.$fileDataDir.') does not exist.');
			if (!is_writable($fileDataDir)) throw new FlaskPHP\Exception\Exception('Cache path ('.$fileDataDir.') not writable by web server.');

			// File dir
			if ($OID!==null)
			{
				$fileDataDir.='/'.sprintf("%03d",mb_substr($OID,-3));
				if (!file_exists($fileDataDir))
				{
					mkdir($fileDataDir,0755);
				}
			}

			// Return
			return $fileDataDir;
		}


		/**
		 *
		 *   Store file
		 *   ----------
		 *   @access public
		 *   @static
		 *   @param int $OID Object OID
		 *   @param string $type File type
		 *   @param string $fileContents File contents
		 *   @param string $subType Subtype
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public static function putFile( int $OID, string $type, string $fileContents, string $subType=null )
		{
			// Init
			umask(0);

			// If file exists, delete old one
			if (!$subType && file_exists(static::getFileName($OID,$type)))
			{
				static::deleteFile($OID,$type);
			}

			// Put file
			file_put_contents(static::getFileDataDir($OID).'/'.$OID.'.'.$type.($subType?'.'.$subType:''),$fileContents);
			chmod(static::getFileDataDir($OID).'/'.$OID.'.'.$type,0644);
		}


		/**
		 *
		 *   Move uploaded file
		 *   ------------------
		 *   @access public
		 *   @static
		 *   @param string $fileName Temp uploaded file name
		 *   @param int $OID Object OID
		 *   @param string $type File type
		 *   @throws \Exception
		 *   @return void
		 *
		 */

		public static function moveUploadedFile( string $fileName, int $OID, string $type )
		{
			umask(0);

			// If file exists, delete old one
			if (file_exists(static::getFile($OID,$type)))
			{
				static::deleteFile($OID,$type);
			}

			// Move file
			move_uploaded_file($fileName,static::getFileDataDir($OID).'/'.$OID.'.'.$type);
			chmod(static::getFileDataDir($OID).'/'.$OID.'.'.$type,0644);
		}


		/**
		 *
		 *   Get file contents
		 *   -----------------
		 *   @access public
		 *   @static
		 *   @param int $OID Object OID
		 *   @param string $type File type
		 *   @param string $subType Subtype
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public static function getFile( int $OID, string $type, string $subType=null )
		{
			return file_get_contents(static::getFileDataDir($OID).'/'.$OID.'.'.$type.($subType?'.'.$subType:''));
		}


		/**
		 *
		 *   Get file full path
		 *   ------------------
		 *   @access public
		 *   @static
		 *   @param int $OID Object OID
		 *   @param string $type File type
		 *   @param string $subType Subtype
		 *   @return string
		 */

		public static function getFileName( int $OID, string $type=null, string $subType=null )
		{
			return static::getFileDataDir($OID).'/'.$OID.($type?'.'.$type:'').($subType?'.'.$subType:'');
		}


		/**
		 *
		 *   Get file cache path
		 *   -------------------
		 *   @access public
		 *   @static
		 *   @param int $OID Object OID
		 *   @param string $type File type
		 *   @param string $subType Subtype
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public static function getCacheFileName( int $OID, string $type, string $subType=null )
		{
			return static::getFileCacheDir($OID).'/'.$OID.'.'.$type.($subType?'.'.$subType:'');
		}


		/**
		 *
		 *   Delete file
		 *   -----------
		 *   @access public
		 *   @static
		 *   @param int $OID Object OID
		 *   @param string $type File type
		 *   @param string $subType Subtype
		 *   @return void
		 */

		public static function deleteFile( int $OID, string $type=null, string $subType=null )
		{
			if ($subType)
			{
				unlink(static::getFileDataDir($OID).'/'.$OID.'.'.$type.'.'.$subType);
			}
			else
			{
				if ($type)
				{
					$fileList=glob(static::getFileName($OID).'*');
				}
				else
				{
					$fileList=glob(static::getFileName($OID,$type).'*');
				}
				foreach ($fileList as $file)
				{
					unlink($file);
				}
			}
		}


		/**
		 *
		 *   Return nice displayable file size
		 *   ---------------------------------
		 *   @access public
		 *   @static
		 *   @param int $fsize File size in bytes
		 *   @return string
		 *
		 */

		public static function niceFileSize( $fsize )
		{
			if ($fsize >= 1024*1024*1000)
				$s=sprintf("%.2f", ($fsize / (1024*1024*1024) ))." GB";
			elseif ($fsize >= 1024*1000)
				$s=sprintf("%.1f", ($fsize / (1024*1024) ))." MB";
			elseif ($fsize >= 1000)
				$s=round($fsize/1024)." KB";
			else
				$s=$fsize." bytes";
			return $s;
		}


		/**
		 *
		 *   Detect MIME type
		 *   ----------------
		 *   @access public
		 *   @static
		 *   @param string $filename Filename
		 *   @return string
		 *
		 */

		public static function getMimeType( $filename )
		{
			// Try finfo
			$finfo=finfo_open(FILEINFO_MIME_TYPE);
			$mimeType=finfo_file($finfo,$filename);
			finfo_close($finfo);
			if (!empty($mimeType)) return $mimeType;

			// Try mime.types
			$fileExtension=mb_strtolower(pathinfo($filename,PATHINFO_EXTENSION));
			$mimeTypes=file(__DIR__.'/../../data/mime/mime.types',FILE_IGNORE_NEW_LINES);
			if (empty($mimeTypes) || !is_array($mimeTypes)) return null;

			// Find MIME type
			foreach ($mimeTypes as $line)
			{
				if (empty($line) || $line[0]=='#') continue;
				$lineArr=preg_split("/\s+/",$line);
				if (empty($lineArr[1])) continue;
				for ($i=1;$i<sizeof($lineArr);++$i)
				{
					if (mb_strtolower($lineArr[$i])==$fileExtension) return $lineArr[0];
				}
			}

			// Nothing?
			return null;
		}


	}


?>
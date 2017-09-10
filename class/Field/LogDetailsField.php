<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   Log details list field
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Field;
	use Codelab\FlaskPHP as FlaskPHP;


	class LogDetailsField extends FieldInterface
	{


		/**
		 *
		 *   Get list value
		 *   --------------
		 *   @access public
		 *   @param mixed $value Value
		 *   @param array $row Row
		 *   @throws \Exception
		 *   @return mixed
		 *
		 */

		public function listValue( $value, array &$row )
		{
			// No data?
			if (!mb_strlen($value)) return '';

			// Compose details
			$value=json_decode($value);
			if ($value===null) return '';
			$logData='';
			if (!empty($value->operation))
			{
				$logData.='<div class="pb-2 mb-2" style="border-bottom: 1px #e0e1e2 solid"><b>[[ FLASK.LOG.Fld.LogData.Operation ]]:</b> '.strval($value->operation).'</div>';
			}
			if (!empty($value->data))
			{
				foreach ($value->data as $entryKey => $entryData)
				{
					$logData.='<div>';
					$logData.='<b>'.oneof($entryData->name,$entryData->id,$entryKey).': </b>';
					if (mb_strlen($entryData->old_value) || mb_strlen($entryData->new_value))
					{
						if (mb_strlen($entryData->old_value))
						{
							if (mb_strlen($entryData->old_description))
							{
								$logData.=htmlspecialchars($entryData->old_description).' ['.htmlspecialchars($entryData->old_value).']';
							}
							else
							{
								$logData.=htmlspecialchars($entryData->old_value);
							}
						}
						elseif ($value->operation!='add')
						{
							$logData.='<span style="color: #919191">[[ FLASK.LOG.Fld.LogData.Empty ]]</span>';
						}
						if (mb_strlen($entryData->old_value) || $value->operation!='add')
						{
							$logData.=' » ';
						}
						if (mb_strlen($entryData->new_value))
						{
							if (mb_strlen($entryData->new_description))
							{
								$logData.=htmlspecialchars($entryData->new_description).' ['.htmlspecialchars($entryData->new_value).']';
							}
							else
							{
								$logData.=htmlspecialchars($entryData->new_value);
							}
						}
						else
						{
							$logData.='<span style="color: #919191">[[ FLASK.LOG.Fld.LogData.Empty ]]</span>';
						}
					}
					elseif (mb_strlen($entryData->value))
					{
						if (mb_strlen($entryData->description))
						{
							$logData.=htmlspecialchars($entryData->description).' ['.htmlspecialchars($entryData->value).']';
						}
						else
						{
							$logData.=htmlspecialchars($entryData->value);
						}
					}
					else
					{
						$logData.='<span style="color: #919191">---</span>';
					}
					$logData.='</div>';
				}
			}

			// Encode
			$logData=base64_encode(FlaskPHP\Template\Template::parseContent($logData));

			// Link
			$link='<a onclick="Flask.LogData.showLogData(\''.$logData.'\')" data-tooltip="[[ FLASK.LOG.Fld.LogData.View ]]" data-inverted=""><span class="icon-log"></span></a>';
			return $link;
		}


	}


?>
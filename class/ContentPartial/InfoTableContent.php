<?php


	/**
	 *
	 *   FlaskPHP
	 *   ------------------------------
	 *   The info table content partial
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\ContentPartial;
	use Codelab\FlaskPHP as FlaskPHP;


	class InfoTableContent extends ContentPartialInterface
	{


		/**
		 *   Table rows
		 *   @var array
		 *   @access public
		 */

		public $tableRow=array();


		/**
		 *
		 *   Set table class
		 *   ---------------
		 *   @access public
		 *   @param string $tableClass Table class
		 *   @return InfoTableContent
		 *
		 */

		public function setTitle( string $tableClass )
		{
			$this->setParam('tableclass',$tableClass);
			return $this;
		}


		/**
		 *
		 *   Add table row
		 *   -------------
		 *   @access public
		 *   @param string $label Label
		 *   @param mixed $content Content
		 *   @param mixed $emptyValue Empty value
		 *   @param bool $encodeContent Encode HTML content
		 *   @return InfoTableContent
		 *
		 */

		public function addRow( string $label, $content, $emptyValue=null, bool $encodeContent=true )
		{
			$c='';
			$c.='<tr>';
			$c.='<th>'.$label.':</th>';
			$c.='<td>';
			// $c.='<div class="list-mobile-label show-on-mobile">'.$label.':</div>';
			if (mb_strlen($content))
			{
				$c.=($encodeContent?nl2br(htmlspecialchars($content)):$content);
			}
			elseif ($emptyValue!==null && $emptyValue!==false)
			{
				$c.='<span class="disabled">';
				$c.=($encodeContent?nl2br(htmlspecialchars($emptyValue)):$emptyValue);
				$c.='</span>';
			}
			$c.='</td>';
			$c.='</tr>';
			$this->tableRow[]=$c;
			return $this;
		}


		/**
		 *   Add data field as table row
		 *   @access public
		 *   @param FlaskPHP\Model\ModelInterface $model
		 *   @param string $field Field tag
		 *   @param mixed $emptyValue Empty value
		 *   @param string $label Label
		 *   @throws \Exception
		 *   @return InfoTableContent
		 */

		public function addField( FlaskPHP\Model\ModelInterface $model, string $field, $emptyValue=null, string $label=null )
		{
			// Get field
			$fieldObject=$model->getField($field);

			// Label
			if ($label===null) $label=$fieldObject->getTitle();

			// Value
			$value=$fieldObject->displayValue();

			// Add to table
			$this->addRow($label,$value,$emptyValue,false);
			return $this;
		}


		/**
		 *   Add subheader
		 *   @access public
		 *   @param string $label Label
		 *   @return InfoTableContent
		 */

		public function addHeader( $label )
		{
			$this->tableRow[]='</tbody><tbody><tr><th colspan="2" class="info-table-header">'.htmlspecialchars($label).'</td></tr></tbody><tbody>';
			return $this;
		}


		/**
		 *   Add divider
		 *   @access public
		 *   @return InfoTableContent
		 */

		public function addDivider()
		{
			$this->tableRow[]='<tr><td colspan="2" class="info-table-divider"></td></tr>';
			return $this;
		}


		/**
		 *
		 *   Render content
		 *   --------------
		 *   @access public
		 *   @throws \Exception
		 *   @return string
		 *
		 */

		public function renderContent()
		{
			// Check
			if (!sizeof($this->tableRow)) return '';

			// Table begins
			$c='<table class="info-table'.($this->hasParam('tableclass')?' '.$this->getParam('tableclass'):'').'"><tbody>';

				// Content
				$c.=join('',$this->tableRow);

			// Table ends
			$c.='</tbody></table>';
			return $c;
		}


	}


?>
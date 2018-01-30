<?php


	/**
	 *
	 *   FlaskPHP
	 *   --------
	 *   The model relation definition
	 *
	 *   @author   Codelab Solutions OÜ <codelab@codelab.ee>
	 *   @license  https://www.flaskphp.com/LICENSE MIT
	 *
	 */


	namespace Codelab\FlaskPHP\Model;
	use Codelab\FlaskPHP as FlaskPHP;


	class ModelRelation
	{


		/**
		 *   Relation ID
		 *   @var string
		 *   @access public
		 */

		public $relationID = null;


		/**
		 *   Backreference to model
		 *   @var \Codelab\FlaskPHP\Model\ModelInterface
		 *   @access public
		 */

		public $model = null;


		/**
		 *   Relation type
		 *   @var string
		 *   @access public
		 */

		public $relationType = 'onetomany';


		/**
		 *   Model field
		 *   @var string
		 *   @access public
		 */

		public $relationField = null;


		/**
		 *   Remote model
		 *   @var string
		 *   @access public
		 */

		public $relationRemoteModel = null;


		/**
		 *   Remote field
		 *   @var string
		 *   @access public
		 */

		public $relationRemoteField = null;


		/**
		 *   Relation name
		 *   @var string
		 *   @access public
		 */

		public $relationName = null;


		/**
		 *   Is a key relation?
		 *   @var bool
		 *   @access public
		 */

		public $relationKeyRelation = false;


		/**
		 *   Is relation loaded?
		 *   @var bool
		 *   @access public
		 */

		public $relationLoaded = false;


		/**
		 *   The constructor function
		 *   @access public
		 *   @param string $id Relation ID
		 *   @param string $field Field/column name
		 *   @param string $remoteModel Remote model name
		 *   @param string $remoteField Remote field name (if empty, same as in this table)
		 *   @param string $relationName Relation name
		 *   @param bool $keyRelation Is a key relation
		 *   @return \Codelab\FlaskPHP\Model\ModelRelation
		 */

		public function __construct( $id, $field, $remoteModel, $remoteField=null, $relationName=null, $keyRelation=false )
		{
			$this->relationID=$id;
			$this->relationField=$field;
			$this->relationRemoteModel=$remoteModel;
			$this->relationRemoteField=$remoteField;
			$this->relationName=$relationName;
			$this->relationKeyRelation=$keyRelation;
		}


	}


?>
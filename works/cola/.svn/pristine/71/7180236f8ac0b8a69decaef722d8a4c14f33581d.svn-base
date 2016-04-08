<?php
/* SVN FILE: $Id: model.php 7067 2011-06-09 01:39:05Z lightma $ */
/**
 * Object-relational mapper.
 *
 * DBO-backed object data model, for mapping database tables to Cola objects.
 *
 * PHP versions 5
 *
 * @package			cola
 * @subpackage		cola.core.libs.model
 * @since			ColaPHP(tm) v 0.10.0.0
 * @version			$Revision: 7067 $
 * @modifiedby		$LastChangedBy: lightma $
 * @lastmodified	$Date: 2011-06-09 09:39:05 +0800 (Âõõ, 2011-06-09) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Included libs
 */
//uses('class_registry', 'validators', 'model' . DS . 'connection_manager', 'set');
uses ( 'class_registry', 'validators', 'validation', 'model' . DS . 'model_behavior', 'model' . DS . 'connection_manager', 'set' );
/**
 * Object-relational mapper.
 *
 * DBO-backed object data model.
 * Automatically selects a database table name based on a pluralized lowercase object class name
 * (i.e. class 'User' => table 'users'; class 'Man' => table 'men')
 * The table is required to have at least 'id auto_increment', 'created datetime',
 * and 'modified datetime' fields.
 *
 * @package		cola
 * @subpackage	cola.core.libs.model
 */
class Model extends Object {

	/**
	 * The name of the DataSource connection that this Model uses
	 *
	 * @var string
	 * @access public
	 */
	var $useDbConfig = 'default';

	/**
	 * Custom database table name.
	 *
	 * @var string
	 * @access public
	 */
	var $useTable = null;

	/**
	 * Custom display field name. Display fields are used by Scaffold, in SELECT boxes' OPTION elements.
	 *
	 * @var string
	 * @access public
	 */
	var $displayField = null;

	/**
	 * Value of the primary key ID of the record that this model is currently pointing to
	 *
	 * @var string
	 * @access public
	 */
	var $id = false;

	/**
	 * Container for the data that this model gets from persistent storage (the database).
	 *
	 * @var array
	 * @access public
	 */
	var $data = array ();

	/**
	 * Table name for this Model.
	 *
	 * @var string
	 * @access public
	 */
	var $table = false;

	/**
	 * The name of the ID field for this Model.
	 *
	 * @var string
	 * @access public
	 */
	var $primaryKey = null;

	/**
	 * Table metadata
	 *
	 * @var array
	 * @access protected
	 */
	var $_tableInfo = null;

	/**
	 * List of validation rules. Append entries for validation as ('field_name' => '/^perl_compat_regexp$/')
	 * that have to match with preg_match(). Use these rules with Model::validate()
	 *
	 * @var array
	 * @access public
	 */
	var $validate = array ();

	/**
	 * Errors in validation
	 * @var array
	 * @access public
	 */
	var $validationErrors = array ();

	/**
	 * Database table prefix for tables in model.
	 *
	 * @var string
	 * @access public
	 */
	var $tablePrefix = null;

	/**
	 * Name of the model.
	 *
	 * @var string
	 * @access public
	 */
	var $name = null;

	/**
	 * Name of the current model.
	 *
	 * @var string
	 * @access public
	 */
	var $currentModel = null;

	/**
	 * List of table names included in the Model description. Used for associations.
	 *
	 * @var array
	 * @access public
	 */
	var $tableToModel = array ();

	/**
	 * List of Model names by used tables. Used for associations.
	 *
	 * @var array
	 * @access public
	 */
	var $modelToTable = array ();

	/**
	 * List of Foreign Key names to used tables. Used for associations.
	 *
	 * @var array
	 * @access public
	 */
	var $keyToTable = array ();

	/**
	 * Alias table names for model, for use in SQL JOIN statements.
	 *
	 * @var array
	 * @access public
	 */
	var $alias = array ();

	/**
	 * Holds the Behavior objects currently bound to this model.
	 *
	 * @var BehaviorCollection
	 * @access public
	 */
	var $Behaviors = null;

	/**
	 * Whether or not transactions for this model should be logged
	 *
	 * @var boolean
	 * @access public
	 */
	var $logTransactions = false;

	/**
	 * Whether or not to enable transactions for this model (i.e. BEGIN/COMMIT/ROLLBACK)
	 *
	 * @var boolean
	 * @access public
	 */
	var $transactional = false;

	/**
	 * Whether or not to cache queries for this model.  This enables in-memory
	 * caching only, the results are not stored beyond this execution.
	 *
	 * @var boolean
	 * @access public
	 */
	var $cacheQueries = true;

	/**
	 * The last inserted ID of the data that this model created
	 *
	 * @var int
	 * @access private
	 */
	var $__insertID = null;

	/**
	 * The number of records returned by the last query
	 *
	 * @var int
	 * @access private
	 */
	var $__numRows = null;

	/**
	 * The number of records affected by the last query
	 *
	 * @var int
	 * @access private
	 */
	var $__affectedRows = null;

	/**
	 * enable/disable Extention field
	 *
	 * @var boolean
	 * @access public
	 */
	var $extensible = false;

	/**
	 * Extention field name
	 *
	 * @var string
	 * @access public
	 */
	var $extentionField = 'extention';

	/**
	 * Whitelist of fields allowed to be saved.
	 *
	 * @var array
	 * @access public
	 */
	var $whitelist = array ();
	
	var $findQueryType = '';

	/**
	 * Has the datasource been configured.
	 *
	 * @var boolean
	 * @see Model::getDataSource
	 */
	protected $_sourceConfigured = false;

	/**
	 * Constructor. Binds the Model's database table to the object.
	 *
	 * @param integer $id
	 * @param string $table Name of database table to use.
	 * @param DataSource $ds DataSource connection object.
	 */
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct ();
		
		if ($this->name === null) {
			$this->name = get_class ( $this );
		}
		
		if (empty ( $this->alias )) {
			$this->alias = (isset ( $alias ) ? $alias : $this->name);
		}
		
		if ($this->primaryKey === null) {
			$this->primaryKey = 'id';
		}
		
		$this->currentModel = Inflector::underscore ( $this->name );
		
		ClassRegistry::addObject ( $this->currentModel, $this );
		
		$this->id = $id;
		
		if ($table === false) {
			$this->useTable = false;
		} else if ($table) {
			$this->useTable = $table;
		}
		
		$this->Behaviors = new BehaviorCollection ();
		
		if ($this->useTable !== false) {
			$this->setDataSource ( $ds );
			
			if ($this->useTable === null) {
				$this->useTable = Inflector::tableize ( $this->name );
			}
			
			if (in_array ( 'settableprefix', get_class_methods ( $this ) )) {
				$this->setTablePrefix ();
			}
			
			$this->setSource ( $this->useTable );
			
			if ($this->displayField == null) {
				if ($this->hasField ( 'title' )) {
					$this->displayField = 'title';
				}
				
				if ($this->hasField ( 'name' )) {
					$this->displayField = 'name';
				}
				
				if ($this->displayField == null) {
					$this->displayField = $this->primaryKey;
				}
			}
		}
		$this->Behaviors->init ( $this->alias );
	}

	/**
	 * Handles custom method calls, like findBy<field> for DB models,
	 * and custom RPC calls for remote data sources
	 *
	 * @param unknown_type $method
	 * @param array $params
	 * @return unknown
	 * @access protected
	 */
	function __call($method, $params) {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		return $db->query ( $method, $params, $this );
	}

	/**
	 * Private helper method to create associated models of given class.
	 * @param string $assoc
	 * @param string $className Class name
	 * @param string $type Type of assocation
	 * @access private
	 */
	function __constructLinkedModel($assoc, $className) {
		$colKey = Inflector::underscore ( $className );
		
		if (! class_exists ( $className )) {
			loadModel ( $className );
		}
		
		if (ClassRegistry::isKeySet ( $colKey )) {
			$this->{$assoc} = ClassRegistry::getObject ( $colKey );
			$this->{$className} = $this->{$assoc};
		} else {
			$this->{$assoc} = new $className ();
			$this->{$className} = $this->{$assoc};
		}
		
		$this->alias [$assoc] = $this->{$assoc}->table;
		$this->tableToModel [$this->{$assoc}->table] = $className;
		$this->modelToTable [$assoc] = $this->{$assoc}->table;
	}

	/**
	 * select host.
	 *
	 * @param string $host IP of the database host
	 * @access public
	 */
	function setHost($host) {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		return $db->setHost ( $host );
	}

	/**
	 * select database.
	 *
	 * @param string $database Name of the database
	 * @access public
	 */
	function setDatabase($database) {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		return $db->setDatabase ( $database );
	}

	/**
	 * Sets a custom table for your controller class. Used by your controller to select a database table.
	 *
	 * @param string $tableName Name of the custom table
	 * @access public
	 */
	function setTable($tableName) {
		return $this->setSource ( $tableName, false );
	}

	/**
	 * Sets a custom table for your controller class. Used by your controller to select a database table.
	 *
	 * @param string $tableName Name of the custom table
	 * @param boolean $fail_exit exit when failed
	 * @access public
	 */
	function setSource($tableName, $fail_exit = true) {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		
		if ($db->isInterfaceSupported ( 'listSources' )) {
			$prefix = '';
			
			if ($this->tablePrefix) {
				$prefix = $this->tablePrefix;
			}
			
			$sources = $db->listSources ();
			if (is_array ( $sources ) && ! in_array ( low ( $prefix . $tableName ), array_map ( 'low', $sources ) )) {
				if ($fail_exit) {
					return $this->colaError ( 'missingTable', array (
						array (
							'className' => $this->name, 
							'table' => $prefix . $tableName 
						) 
					) );
				} else {
					trigger_error ( "Table {$prefix}{$tableName} does not exists!", E_USER_WARNING );
					$db->clearQueryCache ();
					$this->table = $tableName;
					$this->tableToModel [$this->table] = $this->name;
					$this->loadInfo ();
					return false;
				}
			} else {
				$this->table = $tableName;
				$this->tableToModel [$this->table] = $this->name;
				$this->loadInfo ();
			}
		
		} else {
			$this->table = $tableName;
			$this->tableToModel [$this->table] = $this->name;
			$this->loadInfo ();
		}
		$db->clearQueryCache ();
		return true;
	}

	/**
	 * clear query cache
	 *
	 * @return void
	 * @author sparkwang
	 */
	function clearQueryCache() {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		return $db->clearQueryCache ();
	}

	function clearSourcesCache() {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		$filename = ConnectionManager::getSourceName ( $db ) . '_' . $db->config ['database'] . '_list';
		cache ( 'models' . DS . $filename, null, '-1 day' );
	}

	/**
	 * This function does two things: 1) it scans the array $one for the primary key,
	 * and if that's found, it sets the current id to the value of $one[id].
	 * For all other keys than 'id' the keys and values of $one are copied to the 'data' property of this object.
	 * 2) Returns an array with all of $one's keys and values.
	 * (Alternative indata: two strings, which are mangled to
	 * a one-item, two-dimensional array using $one for a key and $two as its value.)
	 *
	 * @param mixed $one Array or string of data
	 * @param string $two Value string for the alternative indata method
	 * @return array
	 * @access public
	 */
	function set($one, $two = null) {
		if (is_array ( $one )) {
			if (countdim ( $one ) == 1) {
				$data = array (
					$this->name => $one 
				);
			} else {
				$data = $one;
			}
		} else {
			$data = array (
				$this->name => array (
					$one => $two 
				) 
			);
		}
		
		foreach ( $data as $n => $v ) {
			if (is_array ( $v )) {
				
				foreach ( $v as $x => $y ) {
					if ($n == $this->name) {
						/*
						if (isset($this->validationErrors[$x])) {
							unset ($this->validationErrors[$x]);
						}
						*/
						if ($x === $this->primaryKey) {
							$this->id = $y;
						}
					}
					
					$this->data [$n] [$x] = $y;
				}
			}
		}
		return $data;
	}

	/**
	 * Returns an array of table metadata (column names and types) from the database.
	 *
	 * @return array Array of table metadata
	 * @access public
	 */
	function loadInfo() {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		
		if (! is_object ( $this->_tableInfo ) && $db->isInterfaceSupported ( 'describe' ) && $this->useTable !== false) {
			$this->_tableInfo = new Set ( $db->describe ( $this ) );
		} elseif ($this->useTable === false) {
			return new Set ();
		}
		return $this->_tableInfo;
	}

	/**
	 * Returns an associative array of field names and column types.
	 *
	 * @return array
	 * @access public
	 */
	function getColumnTypes() {
		$columns = $this->loadInfo ();
		$columns = $columns->value;
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		$cols = array ();
		
		foreach ( $columns as $col ) {
			$cols [$col ['name']] = $col ['type'];
		}
		return $cols;
	}

	/**
	 * Returns the column type of a column in the model
	 *
	 * @param string $column The name of the model column
	 * @return string
	 * @access public
	 */
	function getColumnType($column) {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		$columns = $this->loadInfo ();

		$columns = $columns->value;
		$cols = array ();
		$model = null;
		$column = str_replace ( array (
			$db->startQuote, 
			$db->endQuote 
		), '', $column );
		
		if (strpos ( $column, '.' )) {
			list ( $model, $column ) = explode ( '.', $column );
		}
		if ($model != $this->alias && isset ( $this->{$model} )) {
			return $this->{$model}->getColumnType ( $column );
		}
		foreach ( $columns as $col ) {
			if ($col ['name'] == $column) {
				return $col ['type'];
			}
		}
		return null;
	}

	/**
	 * Returns true if this Model has given field in its database table.
	 *
	 * @param string $name Name of field to look for
	 * @return boolean
	 * @access public
	 */
	function hasField($name) {
		if (is_array ( $name )) {
			foreach ( $name as $n ) {
				if ($this->hasField ( $n )) {
					return $n;
				}
			}
			return false;
		}
		
		if (empty ( $this->_tableInfo )) {
			$this->loadInfo ();
		}
		
		if ($this->_tableInfo != null) {
			return in_array ( $name, $this->_tableInfo->extract ( '{n}.name' ) );
		}
		return false;
	}

	/**
	 * Initializes the model for writing a new record.
	 *
	 * @return boolean True
	 * @access public
	 */
	function create() {
		$this->id = false;
		unset ( $this->data );
		$this->data = $this->validationErrors = array ();
		return true;
	}

	/**
	 * @deprecated
	 */
	function setId($id) {
		$this->id = $id;
	}

	/**
	 * Use query() instead.
	 * @deprecated
	 */
	function findBySql($sql) {
		return $this->query ( $sql );
	}

	/**
	 * Returns a list of fields from the database
	 *
	 * @param mixed $id The ID of the record to read
	 * @param mixed $fields String of single fieldname, or an array of fieldnames.
	 * @return array Array of database fields
	 * @access public
	 */
	function read($fields = null, $id = null) {
		$this->validationErrors = array ();
		
		if ($id != null) {
			$this->id = $id;
		}
		
		$id = $this->id;
		
		if (is_array ( $this->id )) {
			$id = $this->id [0];
		}
		
		if ($this->id !== null && $this->id !== false) {
			$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
			$field = $db->name ( $this->name ) . '.' . $db->name ( $this->primaryKey );
			return $this->find ( $field . ' = ' . $db->value ( $id, $this->getColumnType ( $this->primaryKey ) ), $fields );
		} else {
			return false;
		}
	}

	/**
	 * Returns contents of a field in a query matching given conditions.
	 *
	 * @param string $name Name of field to get
	 * @param array $conditions SQL conditions (defaults to NULL)
	 * @param string $order SQL ORDER BY fragment
	 * @return field contents
	 * @access public
	 */
	function field($name, $conditions = null, $order = null) {
		$ikan_version = isset($conditions ['ikan_version']) ? $conditions ['ikan_version'] : 2;
		if (isset($conditions ['ikan_version'])) {
		  unset ( $conditions ['ikan_version'] );
		}
		if ($conditions === null) {
			$conditions = array (
				$this->name . '.' . $this->primaryKey => $this->id 
			);
		}
		
		if ($data = $this->find ( $conditions, $name, $order, 0 )) {
			
			if (strpos ( $name, '.' ) === false) {
				if (isset ( $data [$this->name] [$name] )) {
					return $data [$this->name] [$name];
				} else {
					return false;
				}
			} else {
				$name = explode ( '.', $name );
				
				if (isset ( $data [$name [0]] [$name [1]] )) {
					return $data [$name [0]] [$name [1]];
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	/**
	 * Saves a single field to the database.
	 *
	 * @param string $name Name of the table field
	 * @param mixed $value Value of the field
	 * @param boolean $validate Whether or not this model should validate before saving (defaults to false)
	 * @return boolean True on success save
	 * @access public
	 */
	function saveField($name, $value, $validate = false) {
		return $this->save ( array (
			$this->name => array (
				$name => $value 
			) 
		), $validate );
	}

	/**
	 * Saves model data to the database.
	 * By default, validation occurs before save.
	 *
	 * @param array $data Data to save.
	 * @param boolean $validate If set, validation will be done before the save
	 * @param array $fieldList List of fields to allow to be written
	 * @return boolean success
	 * @access public
	 */
	function save($data = null, $validate = true, $fieldList = array()) {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		
		/*
		if ($data) {
			if (countdim($data) == 1) {
				$this->set(array($this->name => $data));
			} else {
				$this->set($data);
			}
		}
		*/
		
		//fix bug by oblank
		if ($data) {
			if (isset ( $data [$this->name] )) {
				$this->set ( $data );
			} else {
				$this->set ( array (
					$this->name => $data 
				) );
			}
		}
		
		if (! empty ( $data['_id'] )) {
			$this->id = $data['_id'];
		}else{
			$this->id = false;
		}
		
		$whitelist = ! (empty ( $fieldList ) || count ( $fieldList ) == 0);
		
		if (! $this->validates ()) {
			return false;
		}
		
		if (! $this->beforeSave ()) {
			return false;
		}
		$fields = $values = array ();
		$count = 0;
		
		if (count ( $this->data ) > 1) {
			$weHaveMulti = true;
			$joined = false;
		} else {
			$weHaveMulti = false;
		}

		foreach ( $this->data as $n => $v ) {
			if ($n === $this->name) {
				foreach ( array (
					'created', 
					'updated', 
					'modified' 
				) as $field ) {
					if (array_key_exists ( $field, $v ) && (empty ( $v [$field] ) || $v [$field] === null)) {
						unset ( $v [$field] );
					}
				}
				
				foreach ( $v as $x => $y ) {
					if ($this->hasField ( $x ) && ($whitelist && in_array ( $x, $fieldList ) || ! $whitelist)) {
						$fields [] = $x;
						$values [] = $y;
					}
				}
			}
			$count ++;
		}

		$exists = $this->exists ();
		
		if (! $exists && $this->hasField ( 'created' ) && ! in_array ( 'created', $fields ) && ($whitelist && in_array ( 'created', $fieldList ) || ! $whitelist)) {
			$fields [] = 'created';
			$values [] = date ( 'Y-m-d H:i:s' );
		}
		
		if ($this->hasField ( 'modified' ) && ! in_array ( 'modified', $fields ) && ($whitelist && in_array ( 'modified', $fieldList ) || ! $whitelist)) {
			$fields [] = 'modified';
			$values [] = date ( 'Y-m-d H:i:s' );
		}
		
		if ($this->hasField ( 'updated' ) && ! in_array ( 'updated', $fields ) && ($whitelist && in_array ( 'updated', $fieldList ) || ! $whitelist)) {
			$fields [] = 'updated';
			$values [] = date ( 'Y-m-d H:i:s' );
		}
		
		if (! $exists) {
			$this->id = false;
		}
		if (count ( $fields )) {
			if (! empty ( $this->id )) {
				if ($db->update ( $this, $fields, $values )) {
					$this->afterSave ();
					$this->data = false;
					$this->_clearCache ();
					return true;
				} else {
					return false;
				}
			} else {
				if ($db->create ( $this, $fields, $values )) {
					$this->afterSave ();
					$this->data = false;
					$this->_clearCache ();
					$this->validationErrors = array ();
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	/**
	 * Synonym for del().
	 *
	 * @param mixed $id
	 * @see function del
	 * @return boolean True on success
	 * @access public
	 */
	function remove($id = null) {
		return $this->del ( $id );
	}

	/**
	 * Removes record for given id. If no id is given, the current id is used. Returns true on success.
	 *
	 * @param mixed $id Id of record to delete
	 * @return boolean True on success
	 * @access public
	 */
	function del($id = null) {
		if ($id) {
			$this->id = $id;
		}
		$id = $this->id;
		
		if ($this->exists () && $this->beforeDelete ()) {
			$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
			
			$this->id = $id;
			
			if ($db->delete ( $this )) {
				$this->afterDelete ();
				$this->_clearCache ();
				$this->id = false;
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Alias for del()
	 *
	 * @param mixed $id Id of record to delete
	 * @return boolean True on success
	 * @access public
	 */
	function delete($id = null) {
		return $this->del ( $id );
	}

	/**
	 * Returns true if a record with set id exists.
	 *
	 * @return boolean True if such a record exists
	 * @access public
	 */
	function exists() {
		if ($this->id) {
			$id = $this->id;
			
			if (is_array ( $id )) {
				$id = $id [0];
			}
			$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
			return $db->hasAny ( $this, array (
				$this->primaryKey => $id 
			) );
		}
		return false;
	}

	/**
	 * Returns true if a record that meets given conditions exists
	 *
	 * @param array $conditions SQL conditions array
	 * @return boolean True if such a record exists
	 * @access public
	 */
	function hasAny($conditions = null) {
		return ($this->findCount ( $conditions ) != false);
	}

	/**
	 * Return a single row as a resultset array.
	 *
	 * @param array $conditions SQL conditions array
	 * @param mixed $fields Either a single string of a field name, or an array of field names
	 * @param string $order SQL ORDER BY conditions (e.g. "price DESC" or "name ASC")
	 * @return array Array of records
	 * @access public
	 */
	function find($conditions = null, $fields = null, $order = null) {
		$data = $this->findAll ( $conditions, $fields, $order, 1, null );
		
		if (empty ( $data [0] )) {
			return false;
		}
		
		return $data [0];
	}

	/**
	 * Returns a resultset array with specified fields from database matching given conditions.
	 *
	 * @param mixed $conditions SQL conditions as a string or as an array('field' =>'value',...)
	 * @param mixed $fields Either a single string of a field name, or an array of field names
	 * @param string $order SQL ORDER BY conditions (e.g. "price DESC" or "name ASC")
	 * @param int $limit SQL LIMIT clause, for calculating items per page.
	 * @param int $page Page number, for accessing paged data
	 * @return array Array of records
	 * @access public
	 */
	function findAll($conditions = null, $fields = null, $order = null, $limit = null, $page = 1) {
		$ikan_version = isset($conditions ['ikan_version']) ? $conditions ['ikan_version'] : 2;
		if (isset($conditions ['ikan_version'])) {
		  unset ( $conditions ['ikan_version'] );
		}
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		$this->id = $this->getID ();
		$offset = null;
		
		if ($page > 1 && $limit != null) {
			$offset = ($page - 1) * $limit;
		}
		
		if ($order == null) {
			$order = array ();
		} else {
			$order = array (
				$order 
			);
		}
		
		$queryData = array (
			'conditions' => $conditions, 
			'fields' => $fields, 
			'limit' => $limit, 
			'offset' => $offset, 
			'order' => $order 
		);
		
		$ret = $this->beforeFind ( $queryData );
		if (is_array ( $ret )) {
			$queryData = $ret;
		} elseif ($ret === false) {
			return null;
		}

		$return = $this->afterFind ( $db->read ( $this, $queryData ), $ikan_version ); //version=2ÂêêÂá∫ÁöÑÊï∞ÊçÆ‰∏ç
		
		return $return;
	}

	/**
	 * Runs a direct query against the bound DataSource, and returns the result.
	 *
	 * @param string $data Query data
	 * @return array
	 * @access public
	 */
	function execute($data) {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		$data = $db->fetchAll ( $data, $this->cacheQueries );
		
		foreach ( $data as $key => $value ) {
			foreach ( $this->tableToModel as $key1 => $value1 ) {
				if (isset ( $data [$key] [$key1] )) {
					$newData [$key] [$value1] = $data [$key] [$key1];
				}
			}
		}
		
		if (! empty ( $newData )) {
			return $newData;
		}
		
		return $data;
	}

	function runCommand($command) {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		return $db->runCommand ( $command );
	}
	
	function updateData($conditions, $fields){
	    $db = & ConnectionManager::getDataSource ( $this->useDbConfig );
	    return $db->updateAll($this, $fields,$conditions);
	}

	/**
	 * Returns number of rows matching given SQL condition.
	 *
	 * @param array $conditions SQL conditions array for findAll
	 * @param int $recursize The number of levels deep to fetch associated records
	 * @return int Number of matching rows
	 * @see Model::findAll
	 * @access public
	 */
	function findCount($conditions = null) {
		$ikan_version = isset($conditions ['ikan_version']) ? $conditions ['ikan_version'] : 2;
		if (isset($conditions ['ikan_version'])) {
		  unset ( $conditions ['ikan_version'] );
		}
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		if (get_class ( $db ) == 'DboMongodb') {
			$data = $db->findCount ( $this, $conditions );
			return $data;
		} else {
			list ( $data ) = $this->findAll ( $conditions, 'COUNT(*) AS count', null, null, 1 );
			
			if (isset ( $data [0] ['count'] )) {
				return $data [0] ['count'];
			} elseif (isset ( $data [$this->name] ['count'] )) {
				return $data [$this->name] ['count'];
			}
		}
		return false;
	}

	/**
	 * Special findAll variation for tables joined to themselves.
	 * The table needs the fields id and parent_id to work.
	 *
	 * @param array $conditions Conditions for the findAll() call
	 * @param array $fields Fields for the findAll() call
	 * @param string $sort SQL ORDER BY statement
	 * @return array
	 * @access public
	 * @todo Perhaps create a Component with this logic
	 */
	function findAllThreaded($conditions = null, $fields = null, $sort = null) {
		$ikan_version = isset($conditions ['ikan_version']) ? $conditions ['ikan_version'] : 2;
		if (isset($conditions ['ikan_version'])) {
		  unset ( $conditions ['ikan_version'] );
		}
		return $this->__doThread ( Model::findAll ( $conditions, $fields, $sort ), null );
	}

	/**
	 * Private, recursive helper method for findAllThreaded.
	 *
	 * @param array $data
	 * @param string $root NULL or id for root node of operation
	 * @return array
	 * @access private
	 * @see findAllThreaded
	 */
	function __doThread($data, $root) {
		$out = array ();
		$sizeOf = sizeof ( $data );
		
		for($ii = 0; $ii < $sizeOf; $ii ++) {
			if (($data [$ii] [$this->name] ['parent_id'] == $root) || (($root === null) && ($data [$ii] [$this->name] ['parent_id'] == '0'))) {
				$tmp = $data [$ii];
				
				if (isset ( $data [$ii] [$this->name] [$this->primaryKey] )) {
					$tmp ['children'] = $this->__doThread ( $data, $data [$ii] [$this->name] [$this->primaryKey] );
				} else {
					$tmp ['children'] = null;
				}
				
				$out [] = $tmp;
			}
		}
		
		return $out;
	}

	/**
	 * Returns an array with keys "prev" and "next" that holds the id's of neighbouring data,
	 * which is useful when creating paged lists.
	 *
	 * @param string $conditions SQL conditions for matching rows
	 * @param string $field Field name (parameter for findAll)
	 * @param unknown_type $value
	 * @return array Array with keys "prev" and "next" that holds the id's
	 * @access public
	 */
	function findNeighbours($conditions = null, $field, $value) {
		$ikan_version = isset($conditions ['ikan_version']) ? $conditions ['ikan_version'] : 2;
		if (isset($conditions ['ikan_version'])) {
		  unset ( $conditions ['ikan_version'] );
		}
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		
		if (! is_null ( $conditions )) {
			$conditions = $conditions . ' AND ';
		}
		
		@list ( $prev ) = Model::findAll ( $conditions . $field . ' < ' . $db->value ( $value ), $field, $field . ' DESC', 1, null, 0 );
		@list ( $next ) = Model::findAll ( $conditions . $field . ' > ' . $db->value ( $value ), $field, $field . ' ASC', 1, null, 0 );
		
		if (! isset ( $prev )) {
			$prev = null;
		}
		
		if (! isset ( $next )) {
			$next = null;
		}
		
		return array (
			'prev' => $prev, 
			'next' => $next 
		);
	}

	/**
	 * Returns a resultset for given SQL statement. Generic SQL queries should be made with this method.
	 *
	 * @param string $sql SQL statement
	 * @return array Resultset
	 * @access public
	 */
	function query() {
		$params = func_get_args ();
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		return call_user_func_array ( array (
			&$db, 
			'query' 
		), $params );
	}

	/**
	 * Returns true if all fields pass validation, otherwise false.
	 *
	 * @param array $data POST data
	 * @return boolean True if there are no errors
	 * @access public
	 */
	function validates($data = array()) {
		$errors = $this->invalidFields ( $data );
		return count ( $errors ) == 0;
	}

	/**
	 * Returns an array of invalid fields.
	 *
	 * @param array $data
	 * @return array Array of invalid fields or boolean case any error occurs
	 * @access public
	 */
	function _invalidFields($data = array()) {
		if (empty ( $data )) {
			$data = $this->data;
		}
		
		if (! $this->beforeValidate ()) {
			return $this->validationErrors;
		}
		
		if (! isset ( $this->validate )) {
			return $this->validationErrors;
		}
		
		if (! empty ( $data )) {
			$data = $data;
		} elseif (isset ( $this->data )) {
			$data = $this->data;
		}
		
		if (isset ( $data [$this->name] )) {
			$data = $data [$this->name];
		}
		
		foreach ( $this->validate as $field_name => $validator ) {
			if (isset ( $data [$field_name] ) && ! preg_match ( $validator, $data [$field_name] )) {
				$this->invalidate ( $field_name );
			}
		}
		return $this->validationErrors;
	}

	/**
	 * Returns an array of fields that have failed validation. On the current model.
	 *
	 * @param string $options An optional array of custom options to be made available in the beforeValidate callback
	 * @return array Array of invalid fields
	 * @see Model::validates()
	 * @access public
	 * @link http://book.cakephp.org/view/1182/Validating-Data-from-the-Controller
	 */
	function invalidFields($options = array()) {
		if (! is_object ( $this->Behaviors )) {
			$this->Behaviors = new BehaviorCollection ();
		}
		if (! $this->Behaviors->trigger ( $this, 'beforeValidate', array (
			$options 
		), array (
			'break' => true, 
			'breakOn' => false 
		) ) || $this->beforeValidate ( $options ) === false) {
			return false;
		}
		
		if (! isset ( $this->validate ) || empty ( $this->validate )) {
			return $this->validationErrors;
		}
		
		$data = $this->data;
		$methods = array_map ( 'strtolower', get_class_methods ( $this ) );
		$behaviorMethods = array_keys ( $this->Behaviors->methods () );
		
		if (isset ( $data [$this->alias] )) {
			$data = $data [$this->alias];
		} elseif (! is_array ( $data )) {
			$data = array ();
		}
		
		$Validation = & Validation::getInstance ();
		$exists = $this->exists ();
		
		$_validate = $this->validate;
		$whitelist = $this->whitelist;
		
		if (! empty ( $options ['fieldList'] )) {
			$whitelist = $options ['fieldList'];
		}
		
		if (! empty ( $whitelist )) {
			$validate = array ();
			foreach ( ( array ) $whitelist as $f ) {
				if (! empty ( $this->validate [$f] )) {
					$validate [$f] = $this->validate [$f];
				}
			}
			$this->validate = $validate;
		}
		
		foreach ( $this->validate as $fieldName => $ruleSet ) {
			if (! is_array ( $ruleSet ) || (is_array ( $ruleSet ) && isset ( $ruleSet ['rule'] ))) {
				$ruleSet = array (
					$ruleSet 
				);
			}
			$default = array (
				'allowEmpty' => true, 
				'required' => null, 
				'rule' => 'blank', 
				'last' => true, 
				'on' => null 
			);
			foreach ( $ruleSet as $index => $validator ) {
				if (! is_array ( $validator )) {
					$validator = array (
						'rule' => $validator 
					);
				}
				$validator = array_merge ( $default, $validator );
				
				if (isset ( $validator ['message'] )) {
					$message = $validator ['message'];
				} else {
					//$message = __('ËæìÂÖ•‰∏çÂêàÊ≥ï', true);
					$message = $this->_message ( $validator ['rule'] );
				}
				
				if (empty ( $validator ['on'] ) || ($validator ['on'] == 'create' && ! $exists) || ($validator ['on'] == 'update' && $exists)) {
					if (in_array ( $validator ['rule'], array (
						VALID_NOT_EMPTY, 
						'notEmpty' 
					) )) {
						$validator ['allowEmpty'] = false;
					}
					
					$required = ((! isset ( $data [$fieldName] ) && $validator ['required'] === true) || (isset ( $data [$fieldName] ) && (empty ( $data [$fieldName] ) && ! is_numeric ( $data [$fieldName] )) && $validator ['allowEmpty'] === false));
					
					if ($required) {
						$this->invalidate ( $fieldName, $message ? $message : $this->_message ( 'notEmpty' ) );
						if ($validator ['last']) {
							break;
						}
					} elseif (array_key_exists ( $fieldName, $data )) {
						if (empty ( $data [$fieldName] ) && $data [$fieldName] != '0' && $validator ['allowEmpty'] === true) {
							break;
						}
						
						if (is_array ( $validator ['rule'] )) {
							$rule = $validator ['rule'] [0];
							unset ( $validator ['rule'] [0] );
							$ruleParams = array_merge ( array (
								$data [$fieldName] 
							), array_values ( $validator ['rule'] ) );
						} else {
							$rule = $validator ['rule'];
							$ruleParams = array (
								$data [$fieldName] 
							);
						}
						$valid = true;
						
						//compatible downward, for regx
						if (preg_match ( '/^\/.*\/$/', $rule )) {
							if (isset ( $data [$fieldName] ) && ! preg_match ( $rule, $data [$fieldName] )) {
								$valid = $message;
							}
						} elseif (in_array ( strtolower ( $rule ), $methods )) {
							$ruleParams [] = $validator;
							$ruleParams [0] = array (
								$fieldName => $ruleParams [0] 
							);
							$valid = $this->dispatchMethod ( $rule, $ruleParams );
						} elseif (in_array ( $rule, $behaviorMethods ) || in_array ( strtolower ( $rule ), $behaviorMethods )) {
							$ruleParams [] = $validator;
							$ruleParams [0] = array (
								$fieldName => $ruleParams [0] 
							);
							$valid = $this->Behaviors->dispatchMethod ( $this, $rule, $ruleParams );
						} elseif (method_exists ( $Validation, $rule )) {
							$valid = $Validation->dispatchMethod ( $rule, $ruleParams );
						} elseif (! is_array ( $validator ['rule'] )) {
							$valid = preg_match ( $rule, $data [$fieldName] );
						} elseif (Configure::read ( 'debug' ) > 0) {
							trigger_error ( sprintf ( __ ( 'Could not find validation handler %s for %s', true ), $rule, $fieldName ), E_USER_WARNING );
						}
						
						if (! $valid || (is_string ( $valid ) && strlen ( $valid ) > 0)) {
							if (is_string ( $valid ) && strlen ( $valid ) > 0) {
								$validator ['message'] = $valid;
							} elseif (! isset ( $validator ['message'] )) {
								if (is_string ( $index )) {
									$validator ['message'] = $index;
								} else {
									$validator ['message'] = $message;
								}
							}
							
							$this->invalidate ( $fieldName, $validator ['message'] );
							
							if ($validator ['last']) {
								break;
							}
						}
					}
				}
			}
		}
		
		$this->validate = $_validate;
		return $this->validationErrors;
	}

	/**
	 * Sets a field as invalid
	 *
	 * @param string $field The name of the field to invalidate
	 * @return void
	 * @access public
	 */
	function invalidate($field, $value = true) {
		if (! is_array ( $this->validationErrors )) {
			$this->validationErrors = array ();
		}
		$this->validationErrors [$field] = $value;
	}

	/**
	 * Gets the display field for this model
	 *
	 * @return string The name of the display field for this Model (i.e. 'name', 'title').
	 * @access public
	 */
	function getDisplayField() {
		return $this->displayField;
	}

	/**
	 * Returns a resultset array with specified fields from database matching given conditions.
	 * Method can be used to generate option lists for SELECT elements.
	 *
	 * @param mixed $conditions SQL conditions as a string or as an array('field' =>'value',...)
	 * @param string $order SQL ORDER BY conditions (e.g. "price DESC" or "name ASC")
	 * @param int $limit SQL LIMIT clause, for calculating items per page
	 * @param string $keyPath A string path to the key, i.e. "{n}.Post.id"
	 * @param string $valuePath A string path to the value, i.e. "{n}.Post.title"
	 * @return array An associative array of records, where the id is the key, and the display field is the value
	 * @access public
	 */
	function generateList($conditions = null, $order = null, $limit = null, $keyPath = null, $valuePath = null) {
		if ($keyPath == null && $valuePath == null && $this->hasField ( $this->displayField )) {
			$fields = array (
				$this->primaryKey, 
				$this->displayField 
			);
		} else {
			$fields = null;
		}
		
		$result = $this->findAll ( $conditions, $fields, $order, $limit );
		
		if (! $result) {
			return false;
		}
		
		if ($keyPath == null) {
			$keyPath = '{n}.' . $this->name . '.' . $this->primaryKey;
		}
		
		if ($valuePath == null) {
			$valuePath = '{n}.' . $this->name . '.' . $this->displayField;
		}
		
		$keys = Set::extract ( $result, $keyPath );
		$vals = Set::extract ( $result, $valuePath );
		
		if (! empty ( $keys ) && ! empty ( $vals )) {
			$return = array_combine ( $keys, $vals );
			return $return;
		}
		return null;
	}

	/**
	 * Escapes the field name and prepends the model name. Escaping will be done according to the current database driver's rules.
	 *
	 * @param unknown_type $field
	 * @return string The name of the escaped field for this Model (i.e. id becomes `Post`.`id`).
	 * @access public
	 */
	function escapeField($field) {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		return $db->name ( $this->name ) . '.' . $db->name ( $field );
	}

	/**
	 * Returns the current record's ID
	 *
	 * @param unknown_type $list
	 * @return mixed The ID of the current record
	 * @access public
	 */
	function getID($list = 0) {
		if (! is_array ( $this->id )) {
			return $this->id;
		}
		
		if (count ( $this->id ) == 0) {
			return false;
		}
		
		if (isset ( $this->id [$list] )) {
			return $this->id [$list];
		}
		
		foreach ( $this->id as $id ) {
			return $id;
		}
		
		return false;
	}

	/**
	 * Returns the ID of the last record this Model inserted
	 *
	 * @return mixed
	 * @access public
	 */
	function getLastInsertID() {
		return $this->getInsertID ();
	}

	/**
	 * Returns the ID of the last record this Model inserted
	 *
	 * @return mixed
	 * @access public
	 */
	function getInsertID() {
		return $this->__insertID;
	}

	/**
	 * Sets the ID of the last record this Model inserted
	 *
	 * @param mixed $id
	 * @return void
	 */
	function setInsertID($id) {
		$this->__insertID = $id;
	}

	/**
	 * Returns the number of rows returned from the last query
	 *
	 * @return int
	 * @access public
	 */
	function getNumRows() {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		return $db->lastNumRows ();
	}

	/**
	 * Returns the number of rows affected by the last query
	 *
	 * @return int
	 * @access public
	 */
	function getAffectedRows() {
		$db = & ConnectionManager::getDataSource ( $this->useDbConfig );
		return $db->lastAffected ();
	}

	/**
	 * Sets the DataSource to which this model is bound
	 *
	 * @param string $dataSource The name of the DataSource, as defined in Connections.php
	 * @return boolean True on success
	 * @access public
	 */
	function setDataSource($dataSource = null) {
		if ($dataSource == null) {
			$dataSource = $this->useDbConfig;
		}
		
		$db = & ConnectionManager::getDataSource ( $dataSource );
		
		if (! empty ( $db->config ['prefix'] ) && $this->tablePrefix == null) {
			$this->tablePrefix = $db->config ['prefix'];
		}
		
		if (empty ( $db ) || $db == null || ! is_object ( $db )) {
			return $this->colaError ( 'missingConnection', array (
				array (
					'className' => $this->name 
				) 
			) );
		}
	}

	/**
	 * Gets the DataSource to which this model is bound.
	 *
	 * @return DataSource A DataSource object
	 */
	public function getDataSource() {
		if (! $this->_sourceConfigured && $this->useTable !== false) {
			$this->_sourceConfigured = true;
			$this->setSource ( $this->useTable );
		}
		return ConnectionManager::getDataSource ( $this->useDbConfig );
	}

	/**
	 * Before find callback
	 *
	 * @param array $queryData Data used to execute this query, i.e. conditions, order, etc.
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	function beforeFind(&$queryData) {
		return true;
	}

	/**
	 * After find callback. Can be used to modify any results returned by find and findAll.
	 *
	 * @param mixed $results The results of the find operation
	 * @return mixed Result of the find operation
	 * @access public
	 */
	function afterFind($results, $version = 2) {
		if ($this->extensible) {
			foreach ( $results as &$result ) {
				if (isset ( $result [$this->name] [$this->extentionField . '_'] )) {
					$result [$this->name] [$this->extentionField] = json_decode ( $result [$this->name] [$this->extentionField . '_'], true );
				}
			}
		}
		
		if ($version == 2) {
			if (! empty ( $results )) {
				foreach ( $results as $key => &$item ) {
					if (isset($item [$this->name]))
						$item = $item [$this->name];
				}
			}
		}
		
		return $results;
	}

	/**
	 * Before save callback
	 *
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	function beforeSave() {
		if ($this->extensible && $this->hasField ( $this->extentionField . '_' ) && is_array ( $this->data [$this->name] [$this->extentionField] )) {
			$this->data [$this->name] [$this->extentionField . '_'] = json_encode ( $this->data [$this->name] [$this->extentionField] );
		}
		
		return true;
	}

	/**
	 * After save callback
	 *
	 * @return boolean
	 * @access public
	 */
	function afterSave() {
		return true;
	}

	/**
	 * Before delete callback
	 *
	 * @return boolean True if the operation should continue, false if it should abort
	 * @access public
	 */
	function beforeDelete() {
		return true;
	}

	/**
	 * After delete callback
	 *
	 * @return boolean
	 * @access public
	 */
	function afterDelete() {
		return true;
	}

	/**
	 * Before validate callback
	 *
	 * @return boolean
	 * @access public
	 */
	function beforeValidate() {
		return true;
	}

	/**
	 * Private method.  Clears cache for this model
	 *
	 * @param string $type If null this deletes cached views if CACHE_CHECK is true
	 * Will be used to allow deleting query cache also
	 * @return boolean true on delete
	 * @access protected
	 */
	function _clearCache($type = null) {
		if ($type === null) {
			//add CACHE_MAPPER check by spark 090318
			if (defined ( 'CACHE_CHECK' ) && CACHE_CHECK === true && ! defined ( 'CACHE_MAPPER' )) {
				$assoc [] = strtolower ( Inflector::pluralize ( $this->name ) );
				
				clearCache ( $assoc );
				return true;
			}
		} else {
			//Will use for query cache deleting
		}
	}

	/**
	 * Called when serializing a model
	 *
	 * @return array
	 * @access public
	 */
	function __sleep() {
		$return = array_keys ( get_object_vars ( $this ) );
		return $return;
	}

	/**
	 * Called when unserializing a model
	 *
	 * @return void
	 * @access public
	 */
	function __wakeup() {
	}

	/**
	 * return message for check rule when the filed message is null
	 *
	 * @return string
	 * @access public
	 */
	function _message($rule) {
		switch ($rule) {
			case 'notEmpty' :
				return '‰∏çËÉΩ‰∏∫Á©∫';
			case 'phone' :
				return 'ËØ∑ËæìÂÖ•Ê≠£Á°ÆÁöÑÊâãÊú∫Âè∑';
			case 'tel' :
				return 'ËØ∑ËæìÂÖ•Ê≠£Á°ÆÁöÑÁîµËØùÂè∑Á†ÅÔºåÊ†ºÂºè‰∏∫010-10001000-1000';
			case 'qq' :
				return 'ËØ∑ËæìÂÖ•Ê≠£Á°ÆÁöÑQQÂè∑';
			case 'alphaNumeric' :
				return 'ËØ∑ËæìÂÖ•‰∏≠Êñá„ÄÅÊï∞Â≠ó„ÄÅÂ≠óÊØçÊàñÂÖ∂ÁªÑÂêà';
			case 'letterNumeric' :
				return 'ËØ∑ËæìÂÖ•Êï∞Â≠ó„ÄÅÂ≠óÊØçÊàñÂÖ∂ÁªÑÂêà';
			case 'date' :
				return 'ËØ∑ËæìÂÖ•Ê≠£Á°ÆÁöÑÊó•Êúü';
			case 'time' :
				return 'ËØ∑ËæìÂÖ•Ê≠£Á°ÆÁöÑÊó∂Èó¥';
			case 'boolean' :
				return 'ËØ∑ËæìÂÖ•0Êàñ1';
			case 'decimal' :
				return 'ËØ∑ËæìÂÖ•Êï∞Â≠ó';
			case 'email' :
				return 'ËØ∑ËæìÂÖ•Ê≠£Á°ÆÁöÑemail';
			case 'ip' :
				return 'ËØ∑ËæìÂÖ•Ê≠£Á°ÆÁöÑipÂú∞ÂùÄ';
			case 'postal' :
				return 'ËØ∑ËæìÂÖ•Ê≠£Á°ÆÁöÑÈÇÆÊîøÁºñÁ†Å';
			case 'url' :
				return 'ËØ∑ËæìÂÖ•Ê≠£Á°ÆÁöÑurlÂú∞ÂùÄ';
			default :
				return 'ËæìÂÖ•‰∏çÂêàÊ≥ïÔºåËØ∑ÈáçÊñ∞ËæìÂÖ•';
		}
	}
}
?>

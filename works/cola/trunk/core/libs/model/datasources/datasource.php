<?php
/* SVN FILE: $Id: datasource.php 3223 2010-08-18 03:21:37Z selerdaxia $ */
/**
 * DataSource base class
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * @package			cola
 * @subpackage		cola.core.libs.model.datasources
 * @since			ColaPHP(tm) v 0.10.5.1790
 * @version			$Revision: 3223 $
 * @modifiedby		$LastChangedBy: selerdaxia $
 * @lastmodified	$Date: 2010-08-18 11:21:37 +0800 (三, 2010-08-18) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * DataSource base class
 *
 * Long description for file
 *
 * @package		cola
 * @subpackage	cola.core.libs.model.datasources
 */
class DataSource extends Object {
/**
 * Are we connected to the DataSource?
 *
 * @var boolean
 * @access public
 */
	var $connected = false;
/**
 * Print debug info?
 *
 * @var boolean
 * @access public
 */
	var $debug = false;
/**
 * Print full query debug info?
 *
 * @var boolean
 * @access public
 */
	var $fullDebug = false;
/**
 * Error description of last query
 *
 * @var unknown_type
 * @access public
 */
	var $error = null;
/**
 * String to hold how many rows were affected by the last SQL operation.
 *
 * @var string
 * @access public
 */
	var $affected = null;
/**
 * Number of rows in current resultset
 *
 * @var int
 * @access public
 */
	var $numRows = null;
/**
 * Time the last query took
 *
 * @var int
 * @access public
 */
	var $took = null;
/**
 * Enter description here...
 *
 * @var boolean
 */
	var $cacheSources = true;
/**
 * Enter description here...
 *
 * @var array
 * @access private
 */
	var $_result = null;
/**
 * Queries count.
 *
 * @var int
 * @access private
 */
	var $_queriesCnt = 0;
/**
 * Total duration of all queries.
 *
 * @var unknown_type
 * @access private
 */
	var $_queriesTime = null;
/**
 * Log of queries executed by this DataSource
 *
 * @var unknown_type
 * @access private
 */
	var $_queriesLog = array();
/**
 * Maximum number of items in query log, to prevent query log taking over
 * too much memory on large amounts of queries -- I we've had problems at
 * >6000 queries on one system.
 *
 * @var int Maximum number of queries in the queries log.
 * @access private
 */
	var $_queriesLogMax = 200;
/**
 * Caches serialzed results of executed queries
 *
 * @var array Maximum number of queries in the queries log.
 * @access private
 */
	var $_queryCache = array();
/**
 * The default configuration of a specific DataSource
 *
 * @var array
 * @access public
 */
	var $_baseConfig = array();
/**
 * Holds references to descriptions loaded by the DataSource
 *
 * @var array
 * @access private
 */
	var $__descriptions = array();
/**
 * Holds a list of sources (tables) contained in the DataSource
 *
 * @var array
 * @access protected
 */
	var $_sources = null;
/**
 * A reference to the physical connection of this DataSource
 *
 * @var array
 * @access public
 */
	var $connection = null;
/**
 * The DataSource configuration
 *
 * @var array
 * @access public
 */
	var $config = array();
/**
 * The DataSource configuration key name
 *
 * @var string
 * @access public
 */
	var $configKeyName = null;
/**
 * Whether or not this DataSource is in the middle of a transaction
 *
 * @var boolean
 * @access protected
 */
	var $_transactionStarted = false;
/**
 * Constructor.
 */
	function __construct() {
		parent::__construct();
		if (func_num_args() > 0) {
			$this->setConfig(func_get_arg(0));
		}
	}
/**
 * Returns true if the DataSource supports the given interface (method)
 *
 * @param string $interface The name of the interface (method)
 * @return boolean True on success
 */
	function isInterfaceSupported($interface) {
		$methods = get_class_methods(get_class($this));
		$methods = strtolower(implode('|', $methods));
		$methods = explode('|', $methods);
		$return = in_array(strtolower($interface), $methods);
		return $return;
	}
/**
 * Sets the configuration for the DataSource
 *
 * @param array $config The configuration array
 * @return void
 */
	function setConfig($config) {
		if (is_array($this->_baseConfig)) {
			$this->config = $this->_baseConfig;
			foreach($config as $key => $val) {
				$this->config[$key] = $val;
			}
		}
	}
/**
 * Cache the DataSource description
 *
 * @param string $object The name of the object (model) to cache
 * @param mixed $data The description of the model, usually a string or array
 * @return void
 */
	function __cacheDescription($object, $data = null) {
		if($this->cacheSources === false){
			return null;
		}
		if (Configure::read() > 0) {
			$expires = "+15 seconds";
		} else {
			$expires = "+999 days";
		}

		if ($data !== null) {
			$this->__descriptions[$object] =& $data;
			$cache = serialize($data);
		} else {
			$cache = null;
		}
		$new = cache('models' . DS . ConnectionManager::getSourceName($this) . '_' . $object, $cache, $expires);

		if ($new != null) {
			$new = unserialize($new);
		}
		return $new;
	}
/**
 * To-be-overridden in subclasses.
 *
 * @return string
 */
	function conditions($conditions) {
		return $conditions;
	}
/**
 * To-be-overridden in subclasses.
 *
 * @param unknown_type $name
 * @return unknown
 */
	function name($name) {
		return $name;
	}
/**
 * To-be-overridden in subclasses.
 *
 * @param unknown_type $value
 * @return unknown
 */
	function value($value) {
		return $value;
	}
/**
 * Returns a Model description (metadata) or null if none found.
 *
 * @param Model $model
 * @return mixed
 */
	function describe($model) {
		if($this->cacheSources === false){
			return null;
		}

		if (isset($this->__descriptions[$model->tablePrefix.$model->table])) {
			return $this->__descriptions[$model->tablePrefix.$model->table];
		}
		$cache = $this->__cacheDescription($model->tablePrefix.$model->table);

		if ($cache !== null) {
			$this->__descriptions[$model->tablePrefix.$model->table] =& $cache;
			return $cache;
		}
		return null;
	}
/**
 * To-be-overridden in subclasses.
 *
 * @param unknown_type $model
 * @param unknown_type $fields
 * @param unknown_type $values
 * @return unknown
 */
	function create(&$model, $fields = null, $values = null) {
	//function create(Model $Model, $fields = null, $values = null) {
		return false;
	}
/**
 * To-be-overridden in subclasses.
 *
 * @param unknown_type $model
 * @param unknown_type $queryData
 * @return unknown
 */
	function read(&$model, $queryData = array()) {
	//function read(Model $Model, $query = array(), $recursive = null) {
		return false;
	}
/**
 * To-be-overridden in subclasses.
 *
 * @param unknown_type $model
 * @param unknown_type $fields
 * @param unknown_type $values
 * @return unknown
 */
	function update(&$model, $fields = null, $values = null) {
	//function update(Model $Model, $fields = null, $values = null, $conditions = null) {	
		return false;
	}
/**
 * To-be-overridden in subclasses.
 *
 * @param unknown_type $model
 * @param unknown_type $id
 */
	function delete(&$model, $id = null) {
	//function delete(Model $Model, $conditions = null) {
		if ($id == null) {
			$id = $model->id;
		}
	}
/**
 * To-be-overridden in subclasses.
 *
 * @param mixed $fields
 * @return mixed
 */
	function fields(&$model, $alias = null, $fields = array(), $quote = true) {
		return $fields;
	}
/**
 * To-be-overridden in subclasses.
 *
 * @param Model $model
 * @param unknown_type $fields
 * @return unknown
 */
	function getColumnType(&$model, $fields) {
		return false;
	}
/**
 * To-be-overridden in subclasses.
 *
 * @param unknown_type $model
 * @param unknown_type $key
 * @return unknown
 */
	function resolveKey($model, $key) {
		return $model->name . $key;
	}
	/**
	 * To-be-overridden in subclasses.
	 *
	 * @param string $database 
	 * @return unknown
	 */
	function setDatabase($database) {
		return $database;
	}
	/**
	 * To-be-overridden in subclasses.
	 *
	 * @param string $database 
	 * @return unknown
	 */
	function setHost($host) {
		return $host;
	}
	
	function clearQueryCache() {
		$this->_queryCache = array();
	}
	
	function clearSourcesCache() {
		$this->_sources = null;
	}
/**
 * Closes the current datasource.
 *
 */
	function __destruct() {
		if ($this->connected) {
			$this->close();
		}
	}
}
?>
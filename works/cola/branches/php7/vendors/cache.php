<?php
define('MEM_CACHE', 1);
define('LITE_CACHE', 2);
if(!defined('MEM_CACHED_IP')){
	define('MEM_CACHED_IP', 'localhost');
}
if(!defined('MEM_CACHED_PORT')){
	define('MEM_CACHED_PORT', 11211);
}
if(!defined('DEBUG')){
	define('DEBUG', 1);
}
if(!defined('CACHE_DEFAULT_EXPIRE')){
	define('CACHE_DEFAULT_EXPIRE', '+7 hour');
}
if(!defined('CACHE_PERMANENT_EXPIRE')){
	define('CACHE_PERMANENT_EXPIRE', '+1 year');
}
if(!defined('CACHE_DEFAULT_DIR')){
	if (defined('CACHE')) {
		if (file_exists(CACHE . DIRECTORY_SEPARATOR . 'persistents')) {
			define('CACHE_DEFAULT_DIR', CACHE . DIRECTORY_SEPARATOR . 'persistents' .DS);
		}
	} else if (defined('TMP')) {
		define('CACHE_DEFAULT_DIR', TMP);
	} else {
		define('CACHE_DEFAULT_DIR', DS . 'tmp'. DS);
	}
}
if(!defined('CACHE_PREKEY')){
	define('CACHE_PREKEY', '__');
}

require_once('Cache/cache_key_generator.php');

class Cache{
	var $cache_impl;
	var $impl;
	var $connected = true;
	
	function __construct($type = LITE_CACHE){
		$this->impl = $type;
		if($type == LITE_CACHE){
			require_once('Cache/Lite.php');
			$options = array(
				'cacheDir' => CACHE_DEFAULT_DIR,
				'lifeTime' => 3600 //one hour
			);
			
			$this->cache_impl = new Cache_Lite($options);
		}
		else if($type == MEM_CACHE){
			$this->cache_impl = new Memcached();
			$this->cache_impl->setOption(Memcached::OPT_CONNECT_TIMEOUT, 100);
			$this->cache_impl->setOption(Memcached::OPT_SEND_TIMEOUT, 10000);
			$this->cache_impl->setOption(Memcached::OPT_RECV_TIMEOUT, 10000);
			$this->cache_impl->setOption(Memcached::OPT_POLL_TIMEOUT, 1000);
			$this->cache_impl->setOption(Memcached::OPT_SERVER_FAILURE_LIMIT, 1);
			$this->cache_impl->addServer(MEM_CACHED_IP, MEM_CACHED_PORT) or trigger_error("Could not connect to memcache server", E_USER_WARNING);
			$this->connected = $this->cache_impl->getVersion() ? true : false;
		}
	}
	
	function &singleton($type = null){
		if($type){
		}
		else if(defined('CACHE_TYPE')){
			$type = CACHE_TYPE;
		}
		else{
			$type = LITE_CACHE;
		}
		
        static $cache;
        if (!isset($cache)) {
            $cache = new Cache($type);
        }
        return $cache;
    }
    
	function add($key, $value, $expire=CACHE_DEFAULT_EXPIRE) {
		try {
			$cache_key = CacheKeyGenerator::generate_key(CACHE_PREKEY, $key);
			$cache_key = Cache::compress_key($cache_key);
			return Cache::save_to_cache($cache_key, $value, $expire);
		} catch (Exception $e) {
			return null;
		}
	}
	
	function get($key, $expire=CACHE_DEFAULT_EXPIRE) {
		try {
			$cache_key = CacheKeyGenerator::generate_key(CACHE_PREKEY, $key);
			$cache_key = Cache::compress_key($cache_key);
			return Cache::load_from_cache($cache_key, $expire);
		} catch (Exception $e) {
			return null;
		}
	}
	
	function delete($key) {
		try {
			$cache_key = CacheKeyGenerator::generate_key(CACHE_PREKEY, $key);
			$cache_key = Cache::compress_key($cache_key);
			return Cache::remove_from_cache($cache_key);
		} catch (Exception $e) {
			return null;
		}
	}
	
	function compress_key($key) {
		return md5($key);
	}
	
	function clean() {
		CacheKeyGenerator::renew_cache_key_prefix(CACHE_PREKEY);
	}
	
	function save_to_cache($cache_key, $value, $expire=CACHE_DEFAULT_EXPIRE) {
		$cache = & Cache::singleton();
		if (!$cache->connected) return null;
		if($cache->impl == LITE_CACHE){
			$cache->cache_impl->setLifeTime(strtotime($expire) - time()); 
			return $cache->cache_impl->save(serialize($value), $cache_key);
		}
		else if($cache->impl == MEM_CACHE){
			return $cache->cache_impl->set($cache_key, $value, strtotime($expire)) or trigger_error("Failed to save data at the server", E_USER_WARNING);
		}
	}
	
	function load_from_cache($cache_key, $expire=CACHE_DEFAULT_EXPIRE) {
		$cache = & Cache::singleton();
		if (!$cache->connected) return null;
		if($cache->impl == LITE_CACHE){
			$cache->cache_impl->setLifeTime(strtotime($expire) - time()); 
			return unserialize($cache->cache_impl->get($cache_key));
		}	
		else if($cache->impl == MEM_CACHE){
			return $cache->cache_impl->get($cache_key);
		}
	}
	
	function remove_from_cache($cache_key) {
		$cache = & Cache::singleton();
		if (!$cache->connected) return null;
		if($cache->impl == LITE_CACHE){
			return $cache->cache_impl->remove($cache_key);
		}
		else if($cache->impl == MEM_CACHE){
			return $cache->cache_impl->delete($cache_key);
		}
	}
}

?>

<?php 
if(!defined('CACHE_MAP_KEY_PREFIX')){
	define('CACHE_MAP_KEY_PREFIX', 'CMKP');
}
class CacheKeyGenerator{
	
	function generate_key($cache_id, $arg) {
		if (!defined('CACHE_TYPE') || CACHE_TYPE == LITE_CACHE) return $cache_id . $arg;
		$cache_key_prefix = Cache::load_from_cache(CacheKeyGenerator::_get_cache_map_key($cache_id));
        if (empty($cache_key_prefix)) {
        	$cache_key_prefix = CacheKeyGenerator::renew_cache_key_prefix($cache_id);
        }
		return $cache_key_prefix . '_' . md5($arg);
	}
	
	function renew_cache_key_prefix($cache_id) {
		$cache_map_key = CacheKeyGenerator::_get_cache_map_key($cache_id);
       	$cache_key_prefix = $cache_id . rand();
   		Cache::save_to_cache($cache_map_key, $cache_key_prefix, CACHE_PERMANENT_EXPIRE);
   		return $cache_key_prefix;
	}
	
	function _get_cache_map_key($cache_id) {
		return CACHE_MAP_KEY_PREFIX . $cache_id;
	}
}
?>
<?php
namespace ElDoviz\Service;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Simple cache manager using WordPress transients with object cache fallback.
 */
class CacheManager {
    /**
     * Get cached data.
     *
     * @param string $key Cache key.
     * @param mixed  $default Default value if not set.
     * @return mixed
     */
    public function get( $key, $default = false ) {
        $value = get_transient( $key );
        return false === $value ? $default : $value;
    }

    /**
     * Set cached data.
     *
     * @param string $key   Cache key.
     * @param mixed  $value Data to cache.
     * @param int    $ttl   Time to live in seconds.
     * @return bool
     */
    public function set( $key, $value, $ttl = HOUR_IN_SECONDS ) {
        return set_transient( $key, $value, $ttl );
    }

    /**
     * Delete cached data.
     *
     * @param string $key Cache key.
     * @return bool
     */
    public function delete( $key ) {
        return delete_transient( $key );
    }
}
?>

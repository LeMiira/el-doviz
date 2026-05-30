<?php
namespace ElDoviz\Block;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers Gutenberg blocks for the plugin.
 */
class BlockRegistrar {
    /**
     * Register all blocks.
     */
    public static function register() {
        // Exchange Rates block.
        register_block_type( __DIR__ . '/exchange-rates' );
        // Ticker block.
        register_block_type( __DIR__ . '/ticker' );
        // Trend block.
        register_block_type( __DIR__ . '/trend' );
        // Privacy block.
        register_block_type( __DIR__ . '/privacy' );
    }
}
?>

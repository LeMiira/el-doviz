<?php
namespace ElDoviz\Util;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Helper class for sanitizing input and escaping output.
 */
class Sanitizer {
    /**
     * Sanitize a text field.
     *
     * @param mixed $value
     * @return string
     */
    public static function text( $value ) {
        return sanitize_text_field( wp_unslash( $value ) );
    }

    /**
     * Sanitize an integer.
     *
     * @param mixed $value
     * @return int
     */
    public static function int( $value ) {
        return absint( $value );
    }

    /**
     * Escape HTML for output.
     *
     * @param string $value
     * @return string
     */
    public static function esc_html( $value ) {
        return esc_html( $value );
    }

    /**
     * Escape attributes.
     *
     * @param string $value
     * @return string
     */
    public static function esc_attr( $value ) {
        return esc_attr( $value );
    }
}
?>

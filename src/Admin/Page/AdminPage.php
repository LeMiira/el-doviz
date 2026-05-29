<?php
namespace ElDoviz\Admin\Page;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Base class for admin pages.
 */
abstract class AdminPage {
    /**
     * Render the page content. To be implemented by child classes.
     */
    abstract public static function render();
}
?>

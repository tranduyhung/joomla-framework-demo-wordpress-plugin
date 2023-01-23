<?php
namespace JFD\App\Model;

if (!defined('ABSPATH')) exit;

/**
 * About model.
 *
 * @since  1.0
 */
class AboutModel
{
    /**
     * Get something from database for demonstration purpose.
     *
     * @return  integer
     *
     * @since   1.0
     */
    public function getPostCount(): int
    {
        global $wpdb;

        $query = "SELECT COUNT(id) FROM {$wpdb->prefix}posts";

        return $wpdb->get_var($query);
    }
}

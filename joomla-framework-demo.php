<?php
/**
 * Plugin Name: Joomla! Framework Demo
 * Description: A WordPress plugin powered by Joomla! framework.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

use Joomla\DI\Container;
use JFD\App\Service\ApplicationProvider;
use JFD\App\AdminApp;

define('JFD_DIR_PATH', plugin_dir_path(__FILE__));
define('JFD_PLUGIN_NAME', 'jfd');
define('JFD_TWIG_CACHE_DIR_PATH', JFD_DIR_PATH . 'cache');

// Stop if the dependencies are not installed.
if (!file_exists(JFD_DIR_PATH . '/vendor/autoload.php')) return;

include JFD_DIR_PATH . '/vendor/autoload.php';

// The demo only covers the admin side.
if (!is_admin()) return;

$container = (new Container)
	->registerServiceProvider(new ApplicationProvider);

$app = $container->get(AdminApp::class);
$app->execute();
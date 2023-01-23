<?php
namespace JFD\App;

use Joomla\Application\AbstractWebApplication;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Joomla\Application\Controller\ContainerControllerResolver;
use Joomla\Application\Controller\ControllerResolverInterface;
use Joomla\Router\RouterInterface;
use Joomla\Application\Web\WebClient;

if (!defined('ABSPATH')) exit;

/**
 * Admin app.
 *
 * @since  1.0
 */
class AdminApp extends AbstractWebApplication
{
    /**
     * The application's controller resolver.
     *
     * @var     ControllerResolverInterface
     *
     * @since   1.0
     */
    protected $controllerResolver;

    /**
     * The application's router.
     *
     * @var     RouterInterface
     *
     * @since   1.0
     */
    protected $router;

    /**
     * Class constructor.
     *
     * @param   ControllerResolverInterface  $controllerResolver  The application's controller resolver
     * @param   RouterInterface              $router              The application's router
     * @param   Input                        $input               An optional argument to provide dependency injection for the application's
     *                                                            input object.
     * @param   Registry                     $config              An optional argument to provide dependency injection for the application's
     *                                                            config object.
     * @param   WebClient                    $client              An optional argument to provide dependency injection for the application's
     *                                                            client object.
     * @param   ResponseInterface            $response            An optional argument to provide dependency injection for the application's
     *                                                            response object.
     *
     * @since   1.0
     */
    public function __construct(ControllerResolverInterface $controllerResolver, RouterInterface $router, Input $input = null, Registry $config = null, WebClient $client = null, ResponseInterface $response = null)
    {
        $this->controllerResolver = $controllerResolver;
        $this->router             = $router;

        parent::__construct($input, $config, $client, $response);

        // Current WordPress page. Used by the router to determine what controller is called.
        $this->set('page', $input->get('page'));
    }

    /**
     * Method to run the application and print the response.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function doExecute(): void
    {
        $route = $this->router->parseRoute($this->get('page'), $this->input->getMethod());

        foreach ($route->getRouteVariables() as $key => $value)
        {
            $this->input->def($key, $value);
        }

        \call_user_func($this->controllerResolver->resolve($route));

        echo $this->getBody();
    }

    /**
     * Listen to WordPress hooks.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function execute(): void
    {
        \add_action('admin_menu', [$this, 'addAdminMenu']);
        \add_action('admin_init', [$this, 'registerSettings']);
        \add_action('admin_menu', [$this, 'addSettingsPage']);
    }

    /**
     * Add menu items to WordPress menu.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function addAdminMenu(): void
    {
        \add_menu_page(
            'Joomla! Framework Demo',
            'JFD',
            'manage_options',
            JFD_PLUGIN_NAME . '-home',
            function() {
                $this->displayPage();
            },
            'dashicons-admin-tools'
        );

        $subMenuItems = [
            [
                'page_title'    => 'About',
                'menu_title'    => 'About',
                'menu_slug'     => JFD_PLUGIN_NAME . '-about',
            ],
            // More submenu items here.
        ];

        foreach ($subMenuItems as $index => $item)
        {
            \add_submenu_page(
                JFD_PLUGIN_NAME . '-home',
                $item['page_title'],
                $item['menu_title'],
                'manage_options',
                $item['menu_slug'],
                [$this, 'displayPage'],
                $index + 1,
            );
        }
    }

    /**
     * Print the response.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function displayPage()
    {
        $this->doExecute();
    }

    /**
     * Register WordPress configuration options.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function registerSettings(): void
    {
        \register_setting(JFD_PLUGIN_NAME, JFD_PLUGIN_NAME);
        \add_settings_section(JFD_PLUGIN_NAME . '_settings_section', null, null, JFD_PLUGIN_NAME);
        \add_settings_field('cache_twig', __('Cache Twig'), [$this, 'renderOption'], JFD_PLUGIN_NAME, JFD_PLUGIN_NAME . '_settings_section');
    }

    /**
     * Register WordPress configuration page.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function addSettingsPage(): void
    {
        \add_options_page('Joomla! Framework Demo Settings', 'Joomla! Framework Demo', 'manage_options', JFD_PLUGIN_NAME . '-config', [$this, 'renderSettingsPage']);
    }

    /**
     * Render WordPress configuration page.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function renderSettingsPage(): void
    {
        echo '<h2>Joomla! Framework Demo Plugin Settings</h2>';
        echo '<form action="options.php" method="post">';
        \settings_fields(JFD_PLUGIN_NAME);
        \do_settings_sections(JFD_PLUGIN_NAME);
        echo '<input name="submit" class="button button-primary" type="submit" value="' . \esc_attr('Save') . '" />';
        echo '</form>';
    }

    /**
     * Render the only configuration option we have.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function renderOption(): void
    {
        $options = \get_option(JFD_PLUGIN_NAME);

        $value = $options['cache_twig'] ?? null;

        $yesChecked = '';
        $noChecked = '';

        if ($value == '1')
        {
            $yesChecked = ' checked';
            $noChecked = '';
        }
        else
        {
            $yesChecked = '';
            $noChecked = ' checked';
        }

        echo '<fieldset>';
        echo '<div><label for="cacheTwigYes"><input id="cacheTwigYes" name="' . JFD_PLUGIN_NAME . '[cache_twig]" type="radio" value="1"' . $yesChecked . ' /> ' . __('Yes') . '</label></div>';
        echo '<div><label for="cacheTwigNo"><input id="cacheTwigNo" name="' . JFD_PLUGIN_NAME . '[cache_twig]" type="radio" value="0"' . $noChecked . ' /> ' . __('No') . '</label></div>';
        echo '</fieldset>';
        echo '<p>' . \sprintf(__('Twig cache is stored in %s', JFD_PLUGIN_NAME), JFD_TWIG_CACHE_DIR_PATH) . '</p>';
    }
}
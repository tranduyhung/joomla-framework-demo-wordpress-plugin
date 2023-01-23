<?php
namespace JFD\App\Service;

use Joomla\DI\ServiceProviderInterface;
use Joomla\DI\Container;
use Joomla\Input\Input;
use Joomla\Application\AbstractWebApplication;
use Joomla\Application\Controller\ContainerControllerResolver;
use Joomla\Application\Controller\ControllerResolverInterface;
use Joomla\Router\Router;
use Joomla\Router\RouterInterface;
use Joomla\Application\Web\WebClient;
use JFD\App\AdminApp;
use JFD\App\Controller\HomeController;
use JFD\App\Controller\AboutController;
use JFD\App\View\Home\HomeHtmlView;
use JFD\App\View\About\AboutHtmlView;
use JFD\App\Model\AboutModel;
use Joomla\Renderer\RendererInterface;
use Joomla\Renderer\TwigRenderer;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

if (!defined('ABSPATH')) exit;

/**
 * Service provider for the main application.
 *
 * @since  1.0
 */
class ApplicationProvider implements ServiceProviderInterface
{
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     */
    public function register(Container $container): void
    {
        // The app.
        $container
            ->alias(AdminApp::class, AbstractWebApplication::class)
            ->share(AbstractWebApplication::class, [$this, 'getWebApplicationClassService']);

        // Required packages.
        $container->share(Input::class, [$this, 'getInputClassService'], true);
        $container->share(WebClient::class, [$this, 'getWebClientService'], true);

        $container->alias(ContainerControllerResolver::class, ControllerResolverInterface::class)
            ->share(ControllerResolverInterface::class, [$this, 'getControllerResolverService']);

        // For routing.
        $container->alias(RouterInterface::class, 'application.router')
            ->alias(Router::class, 'application.router')
            ->share('application.router', [$this, 'getApplicationRouterService']);

        // For rendering HTML output.
        $container->alias(RendererInterface::class, 'renderer')
            ->alias(TwigRenderer::class, 'renderer')
            ->share('renderer', [$this, 'getRendererService'], true);

        // MVC.
        $container->alias(HomeController::class, 'controller.home')
            ->share('controller.home', [$this, 'getHomeController'], true);

        $container->alias(AboutController::class, 'controller.about')
            ->share('controller.about', [$this, 'getAboutController'], true);

        $container->alias(HomeHtmlView::class, 'view.home.html')
            ->share('view.home.html', [$this, 'getHomeHtmlView'], true);

        $container->alias(AboutHtmlView::class, 'view.about.html')
            ->share('view.about.html', [$this, 'getAboutHtmlView'], true);

        $container->alias(AboutModel::class, 'model.about')
            ->share('model.about', [$this, 'getAboutModel'], true);
    }

    /**
     * Get the main app.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  AbstractWebApplication
     *
     * @since   1.0
     */
    public function getWebApplicationClassService(Container $container): AbstractWebApplication
    {
        $application = new AdminApp($container->get(ControllerResolverInterface::class), $container->get(RouterInterface::class), $container->get(Input::class), null, $container->get(WebClient::class));

        return $application;
    }

    /**
     * Get the Input class service.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  Input
     *
     * @since   1.0
     */
    public function getInputClassService(Container $container): Input
    {
        return new Input($_REQUEST);
    }

    /**
     * Get the renderer service.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  RendererInterface
     *
     * @since   1.0
     */
    public function getRendererService(Container $container): RendererInterface
    {
        $options = \get_option(JFD_PLUGIN_NAME);

        $cacheTwig = $options['cache_twig'] ?? null;

        $cache = ($cacheTwig == '1') ? JFD_TWIG_CACHE_DIR_PATH : false;

        $loader = new FilesystemLoader(JFD_DIR_PATH . 'src/Templates');
        $environment = new Environment($loader, [
            'cache' => $cache,
        ]);

        $renderer = new TwigRenderer($environment);

        return $renderer;
    }

    /**
     * Get the Home controller.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  HomeController
     *
     * @since   1.0
     */
    public function getHomeController(Container $container): HomeController
    {
        return new HomeController($container->get(HomeHtmlView::class), $container->get(Input::class), $container->get(AdminApp::class));
    }

    /**
     * Get the About controller.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  AboutController
     *
     * @since   1.0
     */
    public function getAboutController(Container $container): AboutController
    {
        return new AboutController($container->get(AboutHtmlView::class), $container->get(Input::class), $container->get(AdminApp::class));
    }

    /**
     * Get the Home view for HTML.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  HomeHtmlView
     *
     * @since   1.0
     */
    public function getHomeHtmlView(Container $container): HomeHtmlView
    {
        $view = new HomeHtmlView($container->get('renderer'));
        $view->setLayout('home.twig');

        return $view;
    }

    /**
     * Get the About view for HTML.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  AboutHtmlView
     *
     * @since   1.0
     */
    public function getAboutHtmlView(Container $container): AboutHtmlView
    {
        $view = new AboutHtmlView($container->get('renderer'), $container->get('model.about'));
        $view->setLayout('about.twig');

        return $view;
    }

    /**
     * Get the About model.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  AboutModel
     *
     * @since   1.0
     */
    public function getAboutModel(Container $container): AboutModel
    {
        return new AboutModel;
    }

    /**
     * Get the controller resolver service.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  ControllerResolverInterface
     *
     * @since   1.0
     */
    public function getControllerResolverService(Container $container): ControllerResolverInterface
    {
        return new ContainerControllerResolver($container);
    }

    /**
     * Get the router service.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  RouterInterface
     *
     * @since   1.0
     */
    public function getApplicationRouterService(Container $container): RouterInterface
    {
        $router = new Router();

        $router->get('/' . JFD_PLUGIN_NAME . '-home', HomeController::class);
        $router->get('/' . JFD_PLUGIN_NAME . '-about', AboutController::class);

        return $router;
    }

    /**
     * Get the web client service.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  WebClient
     *
     * @since   1.0
     */
    public function getWebClientService(Container $container): WebClient
    {
        $input          = $container->get(Input::class);
        $userAgent      = $input->server->getString('HTTP_USER_AGENT', '');
        $acceptEncoding = $input->server->getString('HTTP_ACCEPT_ENCODING', '');
        $acceptLanguage = $input->server->getString('HTTP_ACCEPT_LANGUAGE', '');

        return new WebClient($userAgent, $acceptEncoding, $acceptLanguage);
    }
}
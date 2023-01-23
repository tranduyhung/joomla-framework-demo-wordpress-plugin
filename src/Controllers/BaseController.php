<?php
namespace JFD\App\Controller;

use Joomla\Application\AbstractWebApplication;
use Joomla\Input\Input;
use Joomla\Controller\AbstractController;
use JFD\App\View\Home\HomeHtmlView;
use Laminas\Diactoros\Response\HtmlResponse;
use Joomla\View\HtmlView;

if (!defined('ABSPATH')) exit;

/**
 * Base controller.
 *
 * @since  1.0
 */
class BaseController extends AbstractController
{
    /**
     * The view.
     *
     * @var HtmlView
     *
     * @since  1.0
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param   HtmlView             $view      The view object.
     * @param   Input                $input     The input object.
     * @param   AbstractApplication  $app       The application object.
     *
     * @return  void
     *
     * @since  1.0
     */
    public function __construct(HtmlView $view, Input $input = null, AbstractWebApplication $app = null)
    {
        parent::__construct($input, $app);

        $this->view = $view;
    }

    /**
     * Get view.
     *
     * @return  HtmlView
     *
     * @since  1.0
     */
    public function getView(): HtmlView
    {
        return $this->view;
    }

    /**
     * Executes the controller.
     *
     * @return  boolean
     *
     * @since  1.0
     */
    public function execute(): bool
    {
        $view = $this->getView();

        $response = new HtmlResponse($view->render());

        $this->getApplication()->setResponse($response);

        return true;
    }
}
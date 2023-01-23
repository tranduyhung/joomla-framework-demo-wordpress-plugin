<?php
namespace JFD\App\View;

use Joomla\View\HtmlView;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Joomla\Renderer\RendererInterface;

class BaseHtmlView extends HtmlView
{
	private $model;

	/**
     * Instantiate the view.
     *
     * @param   RendererInterface  $renderer      The renderer object.
     */
    public function __construct(RendererInterface $renderer, $model = null)
    {
        parent::__construct($renderer);

		$this->model = $model;
    }

	public function getModel()
	{
		return $this->model;
	}
}
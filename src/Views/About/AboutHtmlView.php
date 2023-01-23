<?php
namespace JFD\App\View\About;

use JFD\App\View\BaseHtmlView;

if (!defined('ABSPATH')) exit;

/**
 * About view.
 *
 * @since  1.0
 */
class AboutHtmlView extends BaseHtmlView
{
    /**
     * Method to render the view.
     *
     * @return  string
     *
     * @since   1.0
     */
    public function render(): string
    {
        $posts = $this->getModel()->getPostCount();

        $this->setData([
            'title' => 'About',
            'posts' => $posts,
        ]);

        return parent::render();
    }
}
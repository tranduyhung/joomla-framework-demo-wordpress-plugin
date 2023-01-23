<?php
namespace JFD\App\View\Home;

use JFD\App\View\BaseHtmlView;

if (!defined('ABSPATH')) exit;

/**
 * Home view.
 *
 * @since  1.0
 */
class HomeHtmlView extends BaseHtmlView
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
        $this->setData([
            'title'     => 'Home',
            'content'   => 'Vivamus felis diam, dapibus ac libero bibendum, semper sodales nunc. Cras ipsum lectus, tempus in metus nec, finibus euismod eros. In eget facilisis nunc, quis molestie odio.'
        ]);

        return parent::render();
    }
}
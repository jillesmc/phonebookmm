<?php

namespace Core;


/**
 * Class BaseController
 * @package Core
 */
abstract class BaseController
{
    protected $view;
    private $viewPath;
    private $layoutPath;
    private $pageTitle;

    public function __construct()
    {
        $this->view = new \stdClass;
    }


    /**
     * @param string $viewPath
     * @param string $layoutPath
     * @return mixed
     */
    protected function renderView(string $viewPath, string $layoutPath = null)
    {
        $this->viewPath = $viewPath;
        $this->layoutPath = $layoutPath;
        if ($layoutPath) {
            return $this->layout();
        } else {
            return $this->content();
        }
    }

    protected function content()
    {
        if (file_exists(__DIR__ . "/../app/Views/{$this->viewPath}.phtml")) {
            return include __DIR__ . "/../app/Views/{$this->viewPath}.phtml";
        } else {
            echo "Error: View path not found!";
        }
    }

    protected function layout()
    {
        if (file_exists(__DIR__ . "/../app/Views/{$this->layoutPath}.phtml")) {
            return include __DIR__ . "/../app/Views/{$this->layoutPath}.phtml";
        } else {
            echo "Error: Layout path not found!";
        }
    }

    /**
     * @param $pageTitle
     */
    protected function setPageTitle(string $pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * @param string $separator
     * @return string
     */
    protected function getPageTitle(string $separator = null)
    {
        if ($separator) {
            return $this->pageTitle . " " . $separator . " ";
        } else {
            return $this->pageTitle;
        }
    }
}
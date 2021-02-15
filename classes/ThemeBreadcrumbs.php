<?php

class ThemeBreadcrumbs extends ObjectModel
{
    protected $breadcrumbs = [];
    public $pageName;

    function __construct() {
        $this->context = Context::getContext();
        $this->pageName = $this->context->controller->getPageName();
        $this->getAvailableBreadcrumbs();
    }

    protected function getAvailableBreadcrumbs()
    {
        $this->breadcrumbs = $this->getCommonBreadcrumbs();
    }

    public function getBreadcrumb()
    {
        $breadcrumb = [];
        $breadcrumb['links'] = $this->getBreadcrumbByPageName();
        $breadcrumb['count'] = count($breadcrumb['links']);

        return $breadcrumb;
    }

    public function getBreadcrumbByPageName()
    {
        $breadcrumb = [];

        if (isset($this->breadcrumbs[$this->pageName])) {
            $breadcrumb = $this->breadcrumbs[$this->pageName];
        }

        return $breadcrumb;
    }

    protected function getCommonBreadcrumbs()
    {
        $pages = [
            [
                'controller' => 'cart',
                'name' => $this->trans('Your cart', array(), 'Shop.Theme.Global'),
            ],
            [
                'controller' => 'pagenotfound',
                'name' => $this->trans('404', array(), 'Shop.Theme.Global'),
            ],
            [
                'controller' => 'stores',
                'name' => $this->trans('Our stores', array(), 'Shop.Theme.Global'),
            ],
            [
                'controller' => 'sitemap',
                'name' => $this->trans('Sitemap', array(), 'Shop.Theme.Global'),
            ],
        ];

        $breadcrumbs = [];
        foreach ($pages as $page) {
            $breadcrumbs[$page['controller']] = [
                [
                    'url' => $this->context->link->getPageLink('index'),
                    'title' => $this->trans('Home', array(), 'Shop.Theme.Global')
                ],
                [
                    'url' => $this->context->link->getPageLink($page['controller']),
                    'title' => $page['name']
                ]
          ];
        }

        return $breadcrumbs;
    }
}

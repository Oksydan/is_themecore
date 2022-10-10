<?php

namespace Oksydan\Module\IsThemeCore\Core\Breadcrumbs;

class ThemeBreadcrumbs
{
    protected $breadcrumbs = [];
    public $pageName;
    public $translator;

    public function __construct()
    {
        $this->context = \Context::getContext();
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
                'name' => $this->context->getTranslator()->trans('Shopping Cart', [], 'Shop.Theme.Checkout'),
            ],
            [
                'controller' => 'pagenotfound',
                'name' => $this->context->getTranslator()->trans('404', [], 'Shop.Theme.Global'),
            ],
            [
                'controller' => 'stores',
                'name' => $this->context->getTranslator()->trans('Our stores', [], 'Shop.Theme.Global'),
            ],
            [
                'controller' => 'sitemap',
                'name' => $this->context->getTranslator()->trans('Sitemap', [], 'Shop.Theme.Global'),
            ],
        ];

        $breadcrumbs = [];
        foreach ($pages as $page) {
            $breadcrumbs[$page['controller']] = [
                [
                    'url' => $this->context->link->getPageLink('index'),
                    'title' => $this->context->getTranslator()->trans('Home', [], 'Shop.Theme.Global'),
                ],
                [
                    'url' => $this->context->link->getPageLink($page['controller']),
                    'title' => $page['name'],
                ],
            ];
        }

        return $breadcrumbs;
    }
}

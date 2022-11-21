<?php

use Oksydan\Module\IsThemeCore\Core\ListingDisplay\ThemeListDisplay;

class Is_themecoreAjaxThemeModuleFrontController extends ModuleFrontController
{
    public $displayType;

    public function init()
    {
        parent::init();

        $this->displayType = Tools::getValue('displayType');
    }

    public function initContent()
    {
        parent::initContent();

        $themeDisplay = new ThemeListDisplay();

        if ($this->displayType) {
            $themeDisplay->setDisplay($this->displayType);
            $this->ajaxRender(json_encode([
                'hasError' => false,
                'success' => true,
            ]));
        } else {
            $this->ajaxRender(json_encode([
                'hasError' => true,
                'success' => false,
            ]));
        }
    }
}

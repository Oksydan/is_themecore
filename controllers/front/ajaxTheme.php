<?php

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

        if($this->displayType) {
            ThemeListDisplay::setDisplay($this->displayType);
            $this->ajaxRender(json_encode([
                'hasError' => false,
                'success' => true
            ]));
        } else {
            $this->ajaxRender(json_encode([
                'hasError' => true,
                'success' => false
            ]));
        }

        $this->ajaxDie();
    }

}

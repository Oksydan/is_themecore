<?php

class AdminThemeCoreConfigurationController extends ModuleAdminController {
  public $bootstrap = true;

  public function __construct()
  {
    parent::__construct();

    $this->toolbar_title = $this->l('Configuration');
  }

  public function postProcess()
  {
    $this->initOptions();
    return parent::postProcess();
  }

  public function initOptions()
  {
    $this->fields_options['settings'] = [
      'title' => $this->l('Settings'),
      'icon' => 'icon-cogs',
      'fields' => [
        'THEMECORE_DISPLAY_LIST' => [
            'type' => 'select',
            'title' => $this->l('Default list display'),
            'validation' => 'isAnything',
            'required' => true,
            'identifier' => 'displayType',
            'list' => [
                [
                  'displayType' => 'grid',
                  'name' => $this->l('Grid')
                ],
                [
                  'displayType' => 'list',
                  'name' => $this->l('List')
                ],
            ],
        ],
      ],
      'submit' => ['title' => $this->l('Save')],
    ];
  }
}

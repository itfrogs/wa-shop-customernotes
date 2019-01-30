<?php

/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 3/6/16
 * Time: 12:19 PM
 */
class shopCustomernotesPluginSettingsAction extends waViewAction
{
    /**
     * @var shopCustomernotesPlugin $plugin
     */
    private static $plugin;

    /**
     * shopCustomernotesPluginSettingsAction constructor.
     * @param null $params
     * @throws waException
     */
    public function __construct($params = null)
    {
        $plugin = wa('shop')->getPlugin('customernotes');
        self::$plugin = $plugin;
        parent::__construct($params);
    }

    /**
     *
     */
    public function execute()
    {
        $control_params = array(
            'id' => waRequest::get('id'),
            'namespace' => 'shop_customernotes',
            'title_wrapper' => '%s',
            'description_wrapper' => '<br><span class="hint">%s</span>',
            'control_wrapper' => '<div class="name">%s</div><div class="value">%s %s</div>'
        );

        $settings = self::$plugin->getSettings();
        $this->view->assign('settings', $settings);
        $this->view->assign('plugin_id', 'customernotes');
        $this->view->assign('tabs', shopCustomernotesPlugin::getTabs());
        $this->view->assign('plugin_settings_controls', $this->getPluginSettingsControls($control_params));
    }


    /**
     * Возвращает элементы формы для вкладки Samples Settings
     * @param $params
     * @return array
     */
    private function getPluginSettingsControls($params)
    {
        $controls = array(
            'basic'     => self::$plugin->getControls($params + array('subject' => 'basic_settings')),
            'api'       => self::$plugin->getControls($params + array('subject' => 'api_settings')),
            'info'      => self::$plugin->getControls($params + array('subject' => 'info_settings')),
        );
        return $controls;
    }
}
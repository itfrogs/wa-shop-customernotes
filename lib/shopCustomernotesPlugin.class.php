<?php

class shopCustomernotesPlugin extends shopPlugin
{
    /**
     * @var waView $view
     */
    private static $view;
    private static function getView()
    {
        if (!isset(self::$view)) {
            self::$view = waSystem::getInstance()->getView();
        }
        return self::$view;
    }

    /**
     * @var shopCustomernotesPlugin $plugin
     */
    private static $plugin;
    private static function getPlugin()
    {
        if (!isset(self::$plugin)) {
            self::$plugin = wa()->getPlugin('customernotes');
        }
        return self::$plugin;
    }

    public function backendOrder($order) {
        $view = self::getView();
        $rm = new shopCustomernotesNotesModel();

        $view->assign('contact_id', $order['contact_id']);
        $view->assign('order_id', $order['id']);
        $view->assign('notes', $rm->getNotesByContactId($order['contact_id']));
        $view->assign('settings', $this->getSettings());

        return array(
            'info_section' => $view->fetch($this->path . '/templates/orderInfoSection.html'),
        );
    }

    public function getPluginUrl() {
        return $this->getPluginStaticUrl(false);
    }

    public function getPluginPath() {
        return $this->path;
    }

    public static function settingCustomControlLrc()
    {
        $plugin = self::getPlugin();
        $view = self::getView();
        $settings = $plugin->getSettings();
        $view->assign('settings', $settings);

        return $view->fetch($plugin->getPluginPath() . '/templates/SettingsCustomControlLrc.html');
    }

    public static function settingCustomControlLrcLink()
    {
        $plugin = self::getPlugin();
        $view = self::getView();
        $asm = new waAppSettingsModel();
        $redirect_uri = wa()->getUrl(true). '?action=plugins#/customernotes/';

        $referer = parse_url(waRequest::server('HTTP_REFERER'));
        parse_str($referer['query'], $vars);

        if (isset($vars['code'])) {
            $postdata = http_build_query(
                array(
                    "grant_type" => "authorization_code",
                    "client_id" => "l-r-c.info",
                    'code' => $vars['code'],
                )
            );

            $url = 'http://webasyst.itfrogs.ru/api.php/token/?';
            $options = array(
                "format" => "JSON"
            );
            $url .= http_build_query($options,'','&');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

            $data = json_decode(curl_exec($ch));
            curl_close($ch);
            if (isset($data->access_token)) {
                $asm->set(array('shop', 'customernotes'), 'token', $data->access_token);
            }
        }

        $token = $asm->get(array('shop', 'customernotes'), 'token');
        $view->assign('redirect_uri', $redirect_uri);
        $view->assign('token', $token);

        return $view->fetch($plugin->getPluginPath() . '/templates/SettingsCustomControlLrcLink.html');
    }
}
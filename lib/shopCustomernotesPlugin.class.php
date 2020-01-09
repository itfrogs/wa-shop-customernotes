<?php

/**
 * Class shopCustomernotesPlugin
 */
class shopCustomernotesPlugin extends shopPlugin
{
    /**
     * @var waView $view
     */
    private static $view;
    /**
     * @var shopCustomernotesPlugin $plugin
     */
    private static $plugin;

    /**
     * @return shopCustomernotesPlugin|waPlugin
     * @throws waException
     */
    private static function getPlugin()
    {
        if (!isset(self::$plugin)) {
            self::$plugin = wa()->getPlugin('customernotes');
        }
        return self::$plugin;
    }

    /**
     * @return waSmarty3View|waView
     * @throws waException
     */
    private static function getView()
    {
        if (!isset(self::$view)) {
            self::$view = waSystem::getInstance()->getView();
        }
        return self::$view;
    }


    /**
     * @return string
     * @throws waException
     */
    public static function getPluginPath()
    {
        $plugin = self::getPlugin();
        return $plugin->path;
    }

    /**
     * @param $order
     * @return array
     * @throws waException
     */
    public function backendOrder($order) {
        $view = self::getView();
        $rm = new shopCustomernotesNotesModel();

        $view->assign('contact_id', $order['contact_id']);
        $view->assign('order_id', $order['id']);
        $view->assign('notes', $rm->getNotesByContactId($order['contact_id']));
        $view->assign('note', $note  = $rm->getById($order['id']));
        $view->assign('settings', $this->getSettings());

        if ($this->settings['get_uuids']) {
            $api = new shopCustomernotesApi();
            $api_customer_model = new shopCustomernotesCustomerModel();

            $customer = $api_customer_model->getById($order['contact_id']);

            if (empty($customer)) {
                try {
                    $uuids = $api->getUuid($order['contact_id']);
                    //waLog::dump($uuids, 'uuids.log');
                    if (! empty($uuids) && count($uuids) == 1) {
                        $uuid = reset($uuids);
                        $uuid = $api->updateUuid($uuid->uuid, $order['contact_id']);
                        if (isset($uuid['uuid'])) {
                            $customer = $api->saveCustomer($order['contact_id'], $uuid);
                            if (!empty($customer['country'])) {
                                $customer['country'] = waCountryModel::getInstance()->name(ifempty($customer['country']));
                            }
                            $view->assign('api_contact_id', $order['contact_id']);
                            $view->assign('api_customer', $customer);
                        }
                    }
                    else {
                        $view->assign('uuids', (array) $uuids);
                    }
                } catch (waException $e) {
                    if (waSystemConfig::isDebug()) {
                        waLog::log($e->getMessage(), 'customernotes-api-error.log');
                    }
                }
            }
            else {
                if (!empty($customer['country'])) {
                    $customer['country'] = waCountryModel::getInstance()->name(ifempty($customer['country']));
                }
                $view->assign('api_contact_id', $order['contact_id']);
                $view->assign('api_customer', $customer);
            }
        }

        return array(
            'info_section' => $view->fetch($this->path . '/templates/hooks/backend_order/info_section.html'),
        );
    }


    /**
     * @return string
     */
    public function getPluginUrl() {
        return $this->getPluginStaticUrl(false);
    }


    /**
     * @return string
     * @throws waException
     */
    public static function getFeedbackControl()
    {
        $view = self::getView();
        $plugin = self::getPlugin();
        return $view->fetch($plugin->getPluginPath() . '/templates/controls/feedbackControl.html');
    }

    /**
     * @return array
     */
    public static function getTabs()
    {
        $tabs = array(
            'api' => array(
                'name' => _wp('External base connect'),
                'template' => 'Api.html',
            ),
            'info' => array(
                'name' => _wp('Information'),
                'template' => 'Info.html',
            ),
        );
        return $tabs;
    }

    /**
     * @return string
     * @throws waException
     */
    public static function getAuthControl()
    {
        $view = self::getView();
        $plugin = self::getPlugin();
        $settings = $plugin->getSettings();

        $errors = array();

        $errors_template = '';

        $view->assign('errors_template', $errors_template);

        $redirect_uri = wa()->getUrl(true) . '?action=plugins#/customernotes';

        $login_url = $settings['api_url'] . '/api.php/auth?client_id=bstats.ru&client_name=dossier&response_type=code&scope=dossier&format=json&redirect_uri='.$redirect_uri;
        $view->assign('login_url', $login_url);

        $view->assign('errors', $errors);
        $view->assign('settings', $settings);

        $login_template = $view->fetch(self::getPluginPath() . '/templates/actions/backend/auth/login.html');
        $view->assign('login_template', $login_template);

        return $view->fetch(self::getPluginPath() . '/templates/controls/authControl.html');
    }

    /**
     * @return string
     * @throws waException
     */
    public static function getAuthHint()
    {
        $view = self::getView();
        return $view->fetch(self::getPluginPath() . '/templates/controls/authHint.html');
    }
}
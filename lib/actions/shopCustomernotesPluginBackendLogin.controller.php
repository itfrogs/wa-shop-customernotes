<?php

/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 1/9/16
 * Time: 8:03 PM
 */
class shopCustomernotesPluginBackendLoginController extends waJsonController
{
    /**
     * @var blogWallPlugin $plugin
     */
    private static $plugin;

    /**
     * @return blogWallPlugin|waPlugin
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
     * @var waView $view
     */
    private static $view;

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
     * @throws waException
     */
    public function execute()
    {
        if (wa()->getUser()->getRights('shop', 'settings')) {

            $plugin = self::getPlugin();
            $settings = $plugin->getSettings();
            $asm = new waAppSettingsModel();
            $redirect_uri = wa()->getUrl(true) . '?action=plugins#/customernotes';

            $view = self::getView();

            $code = waRequest::post('code', '', 'string');
            $logout = waRequest::post('logout', 0, 'int');
            $errors = array();
            $data = array();

            $errors_template = '';

            if ($logout == 1) {
                $asm->del(array('shop', 'customernotes'), 'dossier_token');
            } else {
                if (!empty($code)) {
                    $request_data = array(
                        'code'          => $code,
                        'client_id'     => 'dossier.com',
                        'grant_type'    => 'authorization_code',
                    );

                    try {
                        $net = new waNet();
                        $url = $settings['api_url'] . "/api.php/token/?format=JSON";
                        $net->query($url, $request_data, waNet::METHOD_POST);

                         $result = json_decode($net->getResponse());
                        if (isset($result->error)) {
                            if (waSystemConfig::isDebug()) {
                                waLog::log($url, 'customernotes-auth-error.log');
                                waLog::log('Unable to login: '.wa_dump_helper($result), 'customernotes-auth-error.log');
                            }

                            $errors = array(
                                array(
                                    'error' => _wp('Settings error'),
                                    'error_description' => _wp('Unable to login. See for more details in customernotes-auth-error.log')
                                ),
                            );
                            $view->assign('errors', $errors);
                            $errors_template = $view->fetch(shopCustomernotesPlugin::getPluginPath() . '/templates/actions/backend/auth/errors.html');
                        }
                        else {
                            $asm->set(array('shop', 'customernotes'), 'dossier_token', $result->access_token);
                            $settings['dossier_token'] = $result->access_token;
                        }
                    } catch (Exception $ex) {
                        if (waSystemConfig::isDebug()) {
                            $result = $ex->getMessage();
                            waLog::log($url, 'customernotes-auth-error.log');
                            waLog::dump($request_data, 'customernotes-auth-error.log');
                            waLog::log('Unable to send login information: '.$result, 'customernotes-auth-error.log');
                        }
                    }
                }
            }

            $login_url = $settings['api_url'] . '/api.php/auth?client_id=dossier.com&client_name=dossier&response_type=code&scope=dossier&format=json&redirect_uri='.$redirect_uri;
            $view->assign('login_url', $login_url);
            $view->assign('errors_template', $errors_template);

            $view->assign('errors', $errors);
            $login_template = $view->fetch(shopCustomernotesPlugin::getPluginPath() . '/templates/actions/backend/auth/login.html');
            $view->assign('login_template', $login_template);

            $view->assign('settings', $settings);

            $this->response = array(
                'login_template' => $login_template,
                'errors_template' => $errors_template,
                'errors' => $errors,
                'data' => $data,
                'url' => wa()->getUrl(true) . '?module=plugins#/wall',
            );
        } else {
            $this->setError('Access denied');
        }
    }
}
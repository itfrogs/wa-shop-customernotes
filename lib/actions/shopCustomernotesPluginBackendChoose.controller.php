<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 1/19/19
 * Time: 10:29 PM
 */

class shopCustomernotesPluginBackendChooseController extends waJsonController
{

    /**
     * $var shopCustomernotesPlugin|waPlugin $plugin
     * @throws waException
     */
    public function execute()
    {
        if (wa()->getUser()->getRights('shop', 'orders')) {
            $plugin = wa()->getPlugin('customernotes');
            $settings = $plugin->getSettings();
            $contact_id = waRequest::post('contact_id', 0, 'int');
            $customer = waRequest::post('customer', array(), waRequest::TYPE_ARRAY);
            $api = new shopCustomernotesApi();

            if (isset($customer['uuid'])) {
                $customer = $api->updateUuid($customer['uuid'], $contact_id);
                $customer = $api->saveCustomer($contact_id, $customer);
                if (!empty($customer['country'])) {
                    $customer['country'] = waCountryModel::getInstance()->name(ifempty($customer['country']));
                }
                $view = wa()->getView();
                $view->assign('api_contact_id', $contact_id);
                $view->assign('api_customer', $customer);
                $view->assign('settings', $settings);
                $this->response = array(
                    'customer' => $view->fetch(shopCustomernotesPlugin::getPluginPath() . '/templates/hooks/backend_order/info_section.api_customer.html')
                );
            }
        }
        else {
            $this->setError(_wp('Access denied'));
        }
    }
}
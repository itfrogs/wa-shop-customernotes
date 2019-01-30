<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 1/19/19
 * Time: 5:32 PM
 */

class shopCustomernotesPluginBackendContactcheckController extends waJsonController
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
            $api = new shopCustomernotesApi();
            $api_customer_model = new shopCustomernotesCustomerModel();

            $uuid = $api_customer_model->getById($contact_id);
            $customer = $api_customer_model->getCustomer($contact_id);
            $customer = $api->hashCustomer($customer);
            if ($api->hasNewData($customer)) {
                $customer = $api->addNewData($customer);
            }

            if (!empty($uuid)) {
                if (isset($customer['emails'])) {
                    $uuid['emails'] = $customer['emails'];
                }
                if (isset($customer['phones'])) {
                    $uuid['phones'] = $customer['phones'];
                }

                $uuid = $api->updateUuid($uuid['uuid'], $contact_id);
                if (isset($uuid['uuid'])) {
                    $customer = $api->saveCustomer($contact_id, $uuid);
                    if (!empty($customer['country'])) {
                        $customer['country'] = waCountryModel::getInstance()->name(ifempty($customer['country']));
                    }
                    $view = wa()->getView();
                    $view->assign('api_contact_id', $contact_id);
                    $view->assign('api_customer', $customer);
                    $view->assign('settings', $settings);
                    $this->response = array(
                        'customer'  => $view->fetch(shopCustomernotesPlugin::getPluginPath() . '/templates/hooks/backend_order/info_section.api_customer.html')
                    );
                }
            }

        }
        else {
            $this->setError(_wp('Access denied'));
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 5/26/15
 * Time: 11:50 PM
 */

class shopCustomernotesPluginBackendRateController extends waJsonController
{
    /**
     * @var waView $view
     */
    private $view;

    /**
     * @var shopCustomernotesPlugin $plugin
     */
    private $plugin;

    /**
     * shopCustomernotesPluginBackendRateController constructor.
     * @throws waException
     */
    function __construct()
    {
        $this->view = waSystem::getInstance()->getView();
        $this->plugin = wa()->getPlugin('customernotes');
    }

    /**
     * @throws waException
     */
    public function execute()
    {
        if (wa()->getUser()->getRights('shop', 'orders')) {
            $type = waRequest::request('type', 'up', 'string');
            $order_id = waRequest::request('order_id', 0, 'int');

            $settings = $this->plugin->getSettings();

            $rate = $type === 'up' ? 1 : -1;

            $order_model = new shopOrderModel();
            $order = $order_model->getById($order_id);

            $nm = new shopCustomernotesNotesModel();

            $note = array();
            $notes = array();
            if (!empty($order)) {
                $nm->rateCustomer($order_id, $rate);
                $notes  = $nm->getNotesByContactId($order['contact_id']);
                $note  = $nm->getById($order['id']);
            }

            if ($settings['get_uuids']) {
                $api = new shopCustomernotesApi();
                $api_customer_model = new shopCustomernotesCustomerModel();

                $customer = $api_customer_model->getById($order['contact_id']);

                if (empty($customer)) {
                    try {
                        $uuids = $api->getUuid($order['contact_id']);
                        //waLog::dump($uuids, 'uuids.log');
                        if (count($uuids) == 1) {
                            $uuid = reset($uuids);
                            $uuid = $api->updateUuid($uuid->uuid, $order['contact_id']);
                            if (isset($uuid['uuid'])) {
                                $customer = $api->saveCustomer($order['contact_id'], $uuid);
                                if (!empty($customer['country'])) {
                                    $customer['country'] = waCountryModel::getInstance()->name(ifempty($customer['country']));
                                }
                                $this->view->assign('api_contact_id', $order['contact_id']);
                                $this->view->assign('api_customer', $customer);
                            }
                        }
                        else {
                            $this->view->assign('uuids', (array) $uuids);
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
                    $this->view->assign('api_contact_id', $order['contact_id']);
                    $this->view->assign('api_customer', $customer);
                }
            }

            $this->view->assign('settings', $settings);
            $this->view->assign('note', $note);
            $this->view->assign('notes', $notes);
            $this->view->assign('order_id', $order_id);
            $this->response = array(
                'notes_template'    => $this->view->fetch(shopCustomernotesPlugin::getPluginPath() . '/templates/hooks/backend_order/info_section.notes.html'),
                'form_template'     => $this->view->fetch(shopCustomernotesPlugin::getPluginPath() . '/templates/hooks/backend_order/info_section.form.html'),
                'rating_template'   => $this->view->fetch(shopCustomernotesPlugin::getPluginPath() . '/templates/hooks/backend_order/info_section.rating.html'),
            );
        }
    }
}


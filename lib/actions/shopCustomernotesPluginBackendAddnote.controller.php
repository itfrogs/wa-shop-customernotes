<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 5/26/15
 * Time: 11:50 PM
  */

class shopCustomernotesPluginBackendAddnoteController extends waJsonController
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
     * shopCustomernotesPluginBackendAddnoteController constructor.
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
            $text = waRequest::request('customernotesNote', '', 'string');
            $order_id = waRequest::request('customernotesNoteOrderId', 0, 'int');
            $plugin = $this->plugin;
            $settings = $plugin->getSettings();

            $order_model = new shopOrderModel();
            $order = $order_model->getById($order_id);

            $nm = new shopCustomernotesNotesModel();

            $notes = array();
            if (!empty($order)) {
                $note   = $nm->getById($order['id']);
                if (!empty($note)) {
                    $note['note'] = $text;
                    $nm->updateById($order['id'], $note);
                }
                else {
                    $note = array(
                        'order_id'      => $order['id'],
                        'contact_id'    => $order['contact_id'],
                        'datetime'      => date('Y-m-d H:i:s'),
                        'rate'          => 0,
                        'note'          => $text,
                    );
                    $nm->insert($note);
                }

                $notes  = $nm->getNotesByContactId($order['contact_id']);
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
            $this->view->assign('notes', $notes);
            $this->view->assign('order_id', $order_id);
            $this->response = array(
                'notes_template' => $this->view->fetch($plugin::getPluginPath() . '/templates/hooks/backend_order/info_section.notes.html'),
            );
        }
    }
}
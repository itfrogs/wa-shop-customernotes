<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 1/30/19
 * Time: 8:19 PM
 */

class shopCustomernotesPluginBackendGetcommentsController extends waJsonController
{
    /**
     * @throws waException
     */
    public function execute()
    {
        if (wa()->getUser()->getRights('shop', 'orders')) {
            $order_id = waRequest::post('order_id', 0, 'int');

            $api = new shopCustomernotesApi();
            $api_customer_model = new shopCustomernotesCustomerModel();

            $om = new shopOrderModel();
            $order  = $om->getById($order_id);

            $customer = $api_customer_model->getById($order['contact_id']);

            if (!empty($customer)) {
                $data = array(
                    'uuid'          => $customer['uuid'],
                );

                $response = $api->request('customer.getcomments', $data);

                if (isset($response->error)) {
                    $this->setError($response->error);
                }
                else {
                    $view = wa()->getView();
                    $view->assign('notes', $response);
                    $this->response = array(
                        'notes_template' => $view->fetch(shopCustomernotesPlugin::getPluginPath() . '/templates/hooks/backend_order/info_section.bstats_notes.html'),
                    );
                }
            }
        }
        else {
            $this->setError(_wp('Access denied'));
        }
    }
}
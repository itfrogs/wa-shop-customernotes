<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 1/30/19
 * Time: 1:50 PM
 */

class shopCustomernotesPluginBackendSendcommentController extends waJsonController
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

            $nm = new shopCustomernotesNotesModel();
            $note  = $nm->getById($order_id);

            $customer = $api_customer_model->getById($note['contact_id']);

            if (!empty($customer)) {
                $data = array(
                    'uuid'          => $customer['uuid'],
                    'order_id'      => $order_id,
                    'rate'          => $note['rate'],
                    'comment'       => $note['note'],
                );
                waLog::dump($data, 'datlog.log');
                $response = $api->request('customer.addcomment', $data);

                if (isset($response->error)) {
                    $this->setError($response->error);
                }

            }
        }
        else {
            $this->setError(_wp('Access denied'));
        }
    }
}
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


<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 8/7/15
 * Time: 9:00 PM
  */

class shopCustomernotesPluginBackendDeletenoteController extends waJsonController
{
    /**
     * @var waView $view
     */
    private $view;

    /**
     * shopCustomernotesPluginBackendDeletenoteController constructor.
     * @throws waException
     */
    function __construct()
    {
        $this->view = waSystem::getInstance()->getView();
    }

    /**
     * @throws waException
     */
    public function execute()
    {
        if (wa()->getUser()->getRights('shop', 'orders')) {
            $order_id = waRequest::request('order_id', '0', 'int');
            $nm = new shopCustomernotesNotesModel();
            $note = $nm->getById($order_id);

            $this->view->assign('contact_id', $note['contact_id']);
            $this->view->assign('order_id', $note['order_id']);

            if ($nm->deleteById($order_id)) {
                $note = array();
                $this->view->assign('note', $note);
                $this->response = array(
                    'form_template'     => $this->view->fetch(shopCustomernotesPlugin::getPluginPath() . '/templates/hooks/backend_order/info_section.form.html'),
                    'rating_template'   => $this->view->fetch(shopCustomernotesPlugin::getPluginPath() . '/templates/hooks/backend_order/info_section.rating.html'),
                );
            }
            else {
                $this->setError(_wp('Note can not be deleted'));
            }
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 12/19/18
 * Time: 1:36 PM
 */

class shopCustomernotesPluginBackendShownotesController extends waJsonController
{
    /**
     * @throws waException
     */
    public function execute()
    {
        if (wa()->getUser()->getRights('shop', 'orders')) {
            $enabled = waRequest::request('enabled');
            $asm = new waAppSettingsModel();

            $asm->set(array('shop', 'customernotes'), 'show_notes', $enabled);

        }
        else {
            $this->setError(_wp('Access denied'));
        }
    }
}
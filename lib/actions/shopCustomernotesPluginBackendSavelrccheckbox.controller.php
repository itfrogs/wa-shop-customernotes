<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 8/23/15
 * Time: 5:32 PM
  */

class shopCustomernotesPluginBackendSavelrccheckboxController extends waJsonController
{
    public function execute()
    {
        if (wa()->getUser()->getRights('shop', 'orders')) {
            $state = waRequest::request('state', 0, 'int');
            $asm = new waAppSettingsModel();
            if ($state == 1) {
                $asm->set(array('shop', 'customernotes'), 'use_lrc', 1);
            }
            else {
                $asm->del(array('shop', 'customernotes'), 'use_lrc');
            }
        }
        else {
            $this->setError(_wp('Access denied'));
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 1/4/19
 * Time: 9:05 PM
 */

class shopCustomernotesPhoneModel extends waModel
{

    /**
     * Primary key of the table
     * @var string
     */
    protected $id = array('contact_id', 'phone');

    protected $table = 'shop_customernotes_phone';
}
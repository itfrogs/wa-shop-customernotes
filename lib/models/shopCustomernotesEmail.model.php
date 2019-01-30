<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 1/4/19
 * Time: 9:02 PM
 */

class shopCustomernotesEmailModel extends waModel
{
    /**
     * Primary key of the table
     * @var string
     */
    protected $id = array('contact_id', 'email');

    protected $table = 'shop_customernotes_email';
}
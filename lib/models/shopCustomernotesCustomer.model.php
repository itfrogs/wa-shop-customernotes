<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 1/4/19
 * Time: 8:57 PM
 */

class shopCustomernotesCustomerModel extends waModel
{

    /**
     * Primary key of the table
     * @var string
     */
    protected $id = 'contact_id';

    /**
     * @var string
     */
    protected $table = 'shop_customernotes_customer';

    /**
     * @param $contact_id
     * @return array
     * @throws waException
     */
    public function getCustomer($contact_id) {
        $contact = new waContact($contact_id);
        $customer = array(
            'contact_id'    => $contact_id, //Этот id нужен только для внутреннего использования и поиска совпадений.
            'emails'        => array(),
            'city'          => null,
            'country'       => null,
            'phones'        => array(),
        );

        $city = $contact->get('address:city');
        if (!empty($city)) $customer['city'] = $city[0]['value'];

        $country = $contact->get('address:country');
        if (!empty($country)) $customer['country'] = $country[0]['value'];

        $emails = $contact->get('email');
        foreach ($emails as $e) {
            if (shopCustomernotesPluginViewHelper::validateEmail($e['value'])) {
                $customer['emails'][] = $e['value'];
            }
        }

        $phones = $contact->get('phone');
        foreach ($phones as $p) {
            if ($phone = shopCustomernotesPluginViewHelper::clearPhoneNumber($p['value'])) {
                $customer['phones'][] = $phone;
            }
        }
        return $customer;
    }
}
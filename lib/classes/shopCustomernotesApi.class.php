<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 12/18/18
 * Time: 5:38 PM
 */

class shopCustomernotesApi
{
    /**
     * @var shopCustomernotesPlugin $plugin
     */
    private static $plugin;

    /**
     * @return shopCustomernotesPlugin|waPlugin
     * @throws waException
     */
    private static function getPlugin()
    {
        if (!isset(self::$plugin)) {
            self::$plugin = wa()->getPlugin('customernotes');
        }
        return self::$plugin;
    }

    /**
     * @var mixed
     */
    private $settings;

    /**
     * @var
     */
    private $token;

    /**
     * @var waNet
     */
    private $net;

    /**
     * @var
     */
    private $url;

    /**
     * shopCustomernotesApi constructor.
     * @throws waException
     */
    public function __construct()
    {
        self::$plugin = self::getPlugin();
        $this->settings = self::$plugin->getSettings();
        $this->token = $this->settings['dossier_token'];
        if (!$this->token) {
            waLog::log(_wp('The plugin is not authorized on the external server.'), 'customernotes-api-error.log');
            waLog::log('token: ' . var_dump($this->settings['dossier_token']), 'customernotes-api-error.log');
        }

        $this->net = new waNet();
        $this->url = $this->settings['api_url'];
    }


    /**
     * @param $method
     * @param array $postfields
     * @return mixed|null
     * @throws waException
     */
    public function request($method, $postfields = array())
    {
        $url = $this->url . '/api.php/?app=dossier&method='.$method.'&access_token='.$this->token;
        try {
            $this->net->query($url, $postfields, waNet::METHOD_POST);
            $result = json_decode($this->net->getResponse());
            if (isset($result->error) && waSystemConfig::isDebug()) {
                waLog::log('request error:', 'customernotes-api-error.log');
                waLog::dump($result, 'customernotes-api-error.log');
                return null;
            }
            else return $result;
        } catch (Exception $ex) {
            if (waSystemConfig::isDebug()) {
                waLog::log('request catch:', 'customernotes-api-error.log');
                waLog::log($ex->getMessage(), 'customernotes-api-error.log');
                //throw new waException($ex->getMessage());
            }
            return null;
        }
    }

    /**
     * @param $contact_id
     * @return mixed
     * @throws waException
     */
    public function getUuid($contact_id) {
        if (is_numeric($contact_id) && $contact_id > 0) {
            $customer_model = new shopCustomernotesCustomerModel();
            $customer = $customer_model->getCustomer($contact_id);

            $customer_row = $customer_model->getById($contact_id);

            //Если нет данных о покупателе, то и получить мы о нем ничего не можем
            if (empty($customer_row) &&
                empty($customer['emails']) &&
                empty($customer['phones'])
            ) return null;

            if (empty($customer_row)) {
                //Хешируем данные. Теперь они не имеют никакой персональной ценности.
                $customer = $this->hashCustomer($customer);

                //Удаляем лишнее
                unset($customer['contact_id']);

                //Получаем uuid
                $uuids = $this->request('customer.getuuid', array('customer' => $customer));
                return $uuids;
            }
            else {
                if ($this->hasNewData($customer)) {
                    //Удаляем лишнее
                    $customer = $this->addCounts($customer);
                    unset($customer['contact_id']);

                    $uuid = $this->request('customer.update', array('customer' => $customer, 'uuid' => $customer_row['uuid']));
                    if (isset($uuid['uuid']) && !empty($uuid['uuid'])) {
                        $this->addNewData($customer);
                        return array(0 => $uuid);
                    }
                    else return null;
                }
            }
        }
        else return null;
    }

    /**
     * @param $customer
     * @return mixed
     */
    public function hashCustomer($customer) {
        foreach ($customer['emails'] as $i => $email) {
            $customer['emails'][$i] = md5($email);
        }
        foreach ($customer['phones'] as $i => $phone) {
            $customer['phones'][$i] = md5($phone);
        }
        return $customer;
    }

    /**
     * @param $customer
     * @return bool
     * @throws waException
     */
    public function hasNewData($customer) {
        $phone_model = new shopCustomernotesPhoneModel();
        $email_model = new shopCustomernotesEmailModel();

        foreach ($customer['emails'] as $i => $email) {
            $row = $email_model->getByField(array('contact_id' => $customer['contact_id'], 'email' => $email));
            if (empty($row)) {
                return true;
            }
        }
        foreach ($customer['phones'] as $i => $phone) {
            $row = $phone_model->getByField(array('contact_id' => $customer['contact_id'], 'phone' => $phone));
            if (empty($row)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $customer
     * @return array
     * @throws waException
     */
    public function addNewData($customer) {
        if (isset($customer['emails']) && !empty($customer['emails'])) {
            $email_model = new shopCustomernotesEmailModel();
            foreach ($customer['emails'] as $i => $email) {
                $row = $email_model->getByField(array('contact_id' => $customer['contact_id'], 'email' => $email));
                if (empty($row)) {
                    $email_model->insert(array('contact_id' => $customer['contact_id'], 'email' => $email));
                }
            }
        }
        if (isset($customer['phones']) && !empty($customer['emails'])) {
            $phone_model = new shopCustomernotesPhoneModel();
            foreach ($customer['phones'] as $i => $phone) {
                $row = $phone_model->getByField(array('contact_id' => $customer['contact_id'], 'phone' => $phone));
                if (empty($row)) {
                    $phone_model->insert(array('contact_id' => $customer['contact_id'], 'phone' => $phone));
                }
            }
        }
        return $customer;
    }

    /**
     * @param $customer
     * @return array
     * @throws waException
     */
    public function getData($customer) {
        if (isset($customer['emails']) && !empty($customer['emails'])) {
            $email_model = new shopCustomernotesEmailModel();
            foreach ($customer['emails'] as $i => $email) {
                $row = $email_model->getByField(array('contact_id' => $customer['contact_id'], 'email' => $email));
                if (empty($row)) {
                    $email_model->insert(array('contact_id' => $customer['contact_id'], 'email' => $email));
                }
            }
        }
        if (isset($customer['phones']) && !empty($customer['emails'])) {
            $phone_model = new shopCustomernotesPhoneModel();
            foreach ($customer['phones'] as $i => $phone) {
                $row = $phone_model->getByField(array('contact_id' => $customer['contact_id'], 'phone' => $phone));
                if (empty($row)) {
                    $phone_model->insert(array('contact_id' => $customer['contact_id'], 'phone' => $phone));
                }
            }
        }
        return $customer;
    }

    /**
     * @param $uuid
     * @param $contact_id
     * @return array
     * @throws waException
     */
    public function updateUuid($uuid, $contact_id) {
        $api_customer_model = new shopCustomernotesCustomerModel();
        $customer = $api_customer_model->getCustomer($contact_id);
        $customer = $this->hashCustomer($customer);
        $customer = $this->addCounts($customer);
        $customer['uuid'] = $uuid;

        $uuid = $this->request('customer.update', array('customer' => $customer));
        if (is_object($uuid) && !empty($uuid)) {
            $customer = (array) $uuid;
            return $customer;
        }
        else return array();
    }

    /**
     * @param $customer
     * @return mixed
     */
    public function addCounts($customer) {
        $order_model = new shopOrderModel();
        if ($this->settings['send_pay_stats']) {
            $customer['payments'] = $order_model->select('COUNT(id)')->where('contact_id = ' . intval($customer['contact_id']) . ' AND paid_date IS NOT NULL')->fetchField();
        }

        if ($this->settings['send_refund_stats']) {
            $customer['refunds'] = $order_model->select('COUNT(id)')->where('contact_id = ' . intval($customer['contact_id']) . ' AND state_id = "refunded"')->fetchField();
        }

        if ($this->settings['send_rating_stats']) {
            $notes_model = new shopCustomernotesNotesModel();
            $customer['rating'] = $notes_model->getRatingSumm($customer['contact_id']);
        }

        return $customer;
    }


    /**
     * @param $contact_id
     * @param $customer
     * @return array
     * @throws waException
     */
    public function saveCustomer($contact_id, $customer) {
        $customer['contact_id'] = $contact_id;
        $customer = $this->addNewData($customer);

        $customer_model = new shopCustomernotesCustomerModel();
        $customer_model->insert($customer, 1);
        return $customer;
    }
}
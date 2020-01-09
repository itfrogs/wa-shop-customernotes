<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 2/21/15
 * Time: 6:24 PM
  */

class shopCustomernotesNotesModel extends waModel {

    /**
     * Primary key of the table
     * @var string
     */
    protected $id = 'order_id';

    protected $table = 'shop_customernotes_notes';

    public function getCustomerRating($order_id) {
        $om = new shopOrderModel();
        $order = $om->getById($order_id);

        $rating = array(
            'up' => $this->query(
                    "SELECT SUM(rate) FROM ".$this->table." WHERE contact_id = i:id AND rate = 1",
                    array('id' => $order['contact_id'])
            )->fetchField(),
            'down' => $this->query(
                "SELECT SUM(rate) FROM ".$this->table." WHERE contact_id = i:id AND rate = -1",
                array('id' => $order['contact_id'])
            )->fetchField(),
            'sum' => $this->query(
                "SELECT SUM(rate) FROM ".$this->table." WHERE contact_id = i:id",
                array('id' => $order['contact_id'])
            )->fetchField(),
        );

        $rating['down'] = abs($rating['down']);

        foreach ($rating as &$r) {
            if ($r === null) $r = 0;
        }

        return $rating;
    }

    public function getRatingSumm($contact_id) {
        $rating = $this->query(
            "SELECT SUM(rate) FROM ".$this->table." WHERE contact_id = i:id",
            array('id' => $contact_id)
        )->fetchField();
        return $rating;
    }

    public function rateCustomer($order_id, $rate) {
        $currentRate = $this->query(
            "SELECT * FROM ".$this->table." WHERE order_id = i:order_id",
            array(
                'order_id' => $order_id,
            ))->fetch();

        $om = new shopOrderModel();
        $order = $om->getById($order_id);

        if (empty($currentRate)) {
            $data = array(
                'order_id'          => $order_id,
                'contact_id'   => $order['contact_id'],
                'datetime'   => date('Y-m-d H:i:s'),
                'rate'              => $rate,
            );
            $data['id'] = $this->insert($data);
        }
        else {
            $data = $currentRate;
            $data['rate'] = intval($rate);
            $data['datetime'] = date('Y-m-d H:i:s');
            $this->updateById($order_id, $data);
        }

        return $this->getCustomerRating($order['id']);
    }

    public function noteCustomerByOrderId($order_id, $note)
    {
        $currentRate = $this->query(
            "SELECT * FROM ".$this->table." WHERE order_id = i:order_id",
            array(
                'order_id' => $order_id,
            ))->fetch();

        $om = new shopOrderModel();
        $order = $om->getById($order_id);

        if (empty($currentRate)) {
            $data = array(
                'order_id'      => $order_id,
                'contact_id'    => $order['contact_id'],
                'datetime'      => date('Y-m-d H:i:s'),
                'rate'          => 0,
                'note'          => $note,
            );
            $this->insert($data);
        }
        else {
            $data = $currentRate;
            $data['datetime'] = date('Y-m-d H:i:s');
            $data['note'] = $note;
            $this->updateByField('order_id', $order_id, $data);
        }

        $data['note'] = htmlspecialchars($data['note']);
        $data['order_id'] = $order_id;

        return $data;
    }

    public function getNotesByContactId($contact_id) {
        return $this->select('*')
            ->where('contact_id = ' . intval($contact_id))
            ->order('datetime DESC')
            ->fetchAll();
    }
}
<?php
return array(
    'shop_customernotes_customer' => array(
        'contact_id' => array('int', 11, 'null' => 0),
        'uuid' => array('varchar', 36),
        'city' => array('varchar', 50),
        'country' => array('varchar', 10),
        'total_rating' => array('int', 11, 'null' => 0, 'default' => '0'),
        'payments' => array('int', 11, 'null' => 0, 'default' => '0'),
        'refunds' => array('int', 11, 'null' => 0, 'default' => '0'),
        ':keys' => array(
            'PRIMARY' => 'contact_id',
        ),
    ),
    'shop_customernotes_email' => array(
        'contact_id' => array('int', 11, 'null' => 0),
        'email' => array('varchar', 50, 'null' => 0),
        ':keys' => array(
            'PRIMARY' => array('contact_id', 'email'),
        ),
    ),
    'shop_customernotes_notes' => array(
        'order_id' => array('int', 11, 'null' => 0),
        'contact_id' => array('int', 11, 'null' => 0),
        'datetime' => array('datetime'),
        'note' => array('text'),
        'rate' => array('tinyint', 1, 'null' => 0, 'default' => '0'),
        ':keys' => array(
            'PRIMARY' => 'order_id',
        ),
    ),
    'shop_customernotes_phone' => array(
        'contact_id' => array('int', 11, 'null' => 0),
        'phone' => array('varchar', 20, 'null' => 0),
        ':keys' => array(
            'PRIMARY' => array('contact_id', 'phone'),
        ),
    ),
);

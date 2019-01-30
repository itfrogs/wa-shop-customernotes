<?php

return array(
    'show_notes' => array(
        'title' => _wp('Show notes in the order'),
        'description' => _wp('Shows the note in order by default'),
        'control_type' => waHtmlControl::CHECKBOX,
        'value' => 1,
        'subject' => 'basic_settings',
    ),
    'api_hint' => array(
        'value' => 'Данная опция позволяет обмениваться статистикой о действиях клиентов между магазинами. Никаких персональных данных не передается. На сервер отправляются только хеши телефонов и е-мэйлов клиентов, количество оплат, покупок и возвратов. Так же, по желанию, можно отправлять и отзывы о клиенте. Обмениваясь такой информацией вы сможете оценить репутацию покупателя и будете предупреждены о возможных неприятностях, которые происходили ранее у этого же покупателя с другими магазинами.',
        'control_type' => waHtmlControl::HELP,
        'subject' => 'api_settings',
    ),
    'auth' => array(
        'title' => _wp('Autorization'),
        'control_type' => waHtmlControl::CUSTOM . ' ' . 'shopCustomernotesPlugin::getAuthControl',
        'subject' => 'api_settings',
    ),
    'dossier_token' => array(
        'value' => null,
        'control_type' => waHtmlControl::HIDDEN,
        'subject' => 'api_settings',
    ),
    'feedback' => array(
        'title' => _wp('Ask for technical support'),
        'description' => _wp('Click on the link to contact the developer.'),
        'control_type' => waHtmlControl::CUSTOM . ' ' . 'shopCustomernotesPlugin::getFeedbackControl',
        'subject' => 'info_settings',
    ),
    'api_url' => array(
        'value' => 'https://bstats.ru',
        'control_type' => waHtmlControl::HIDDEN,
        'subject' => 'info_settings',
    ),
    'get_uuids' => array(
        'value' => 0,
        'control_type' => waHtmlControl::CHECKBOX,
        'subject' => 'hidden_settings',
    ),
    'send_pay_stats' => array(
        'value' => 0,
        'control_type' => waHtmlControl::CHECKBOX,
        'subject' => 'hidden_settings',
    ),
    'send_refund_stats' => array(
        'value' => 0,
        'control_type' => waHtmlControl::CHECKBOX,
        'subject' => 'hidden_settings',
    ),
    'send_rating_stats' => array(
        'value' => 0,
        'control_type' => waHtmlControl::CHECKBOX,
        'subject' => 'hidden_settings',
    ),
);

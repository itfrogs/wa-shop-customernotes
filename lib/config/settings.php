<?php

return array(
    'hide_notes' => array(
        'title' => _wp('Hide notes in the order'),
        'description' => _wp('Hides the note in order by default'),
        'control_type' => waHtmlControl::CHECKBOX,
        'value' => 0,
    ),
    'use_lrc' => array(
        'title' => _wp('Use LRC service'),
        'description' => _wp('Service LRC is a common database of customer reviews online stores.'),
        'control_type' => waHtmlControl::CUSTOM . ' ' . 'shopCustomernotesPlugin::settingCustomControlLrc',

    ),
    'token' => array(
        'title' => _wp('LRC Token'),
        'description' => _wp('Access token for usage LRC service. Follow the link to get a new LRC Token.'),
        'control_type' => waHtmlControl::CUSTOM . ' ' . 'shopCustomernotesPlugin::settingCustomControlLrcLink',
    ),

);

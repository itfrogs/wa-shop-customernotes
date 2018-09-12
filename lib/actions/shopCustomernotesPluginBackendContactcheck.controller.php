<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 8/23/15
 * Time: 11:08 PM
  */

class shopCustomernotesPluginBackendContactcheckController extends waJsonController
{
    /**
     * @var waView $view
     */
    private $view;

    /**
     * @var shopCustomernotesPlugin $plugin
     */
    private $plugin;

    function __construct()
    {
        $this->view = waSystem::getInstance()->getView();
        $this->plugin = wa()->getPlugin('customernotes');
    }

    public function execute()
    {
        if (wa()->getUser()->getRights('shop', 'orders')) {
            $contact_id = waRequest::request('contact_id', 'contact_id', 'int');
            $contact = new waContact($contact_id);
            $settings = $this->plugin->getSettings();

            if ($contact->getId() > 0 && isset($settings['token']) && strlen($settings['token']) > 0) {
                if ($city = $contact->get('address:city')) {
                    $city = reset($city);
                    $city = $city['value'];
                }
                else $city = '';

                if ($phone = $contact->get('phone')) {
                    $phone = reset($phone);
                    $phone = $phone['value'];
                }
                else $phone = '';

                $options = array(
                    'access_token' => $settings['token'],
                    'format' => 'JSON',
                );

                $postdata = http_build_query(
                    array(
                        'contact' => array(
                            'login' => $contact->get('login'),
                            'firstname' => $contact->get('firstname'),
                            'middlename' => $contact->get('middlename'),
                            'lastname' => $contact->get('lastname'),
                            'city' => $city,
                            'phone' => $phone,
                        ),
                    )
                );

                $url = 'http://webasyst.itfrogs.ru/api.php?app=lrc&method=contact.check&';
                $url .= http_build_query($options,'','&');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                $data = json_decode(curl_exec($ch));
                curl_close($ch);

                var_dump($data);

//                http://webasyst.itfrogs.ru/api.php?app=APP_ID&method=METHOD&PARAMS&access_token=ACCESS_TOKEN&format=FORMAT


                $this->response = array(
//                    'note_id' =>  $note_id,
//                    'form_template' => $this->view->fetch($this->plugin->getPluginPath() . '/templates/notesForm.html'),
                );
            }
            else {
                $this->setError(_wp('Contact not found'));
            }
        }
    }
}
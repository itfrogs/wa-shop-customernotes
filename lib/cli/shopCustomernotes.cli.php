<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 12/18/17
 * Time: 9:18 PM
 */

class shopCustomernotesCli extends waCliController
{
    /**
     * @throws waException
     */
    public function execute()
    {
        $arg = waRequest::param(0);

        if ($arg == 'updatedb') {
            try {
                $nm = new shopCustomernotesNotesModel();
                if(!$nm->fieldExists('datetime')) {
                    $orders = $nm->query(
                        'SELECT order_id FROM shop_customernotes_notes GROUP BY order_id '
                    )->fetchAll('order_id');

                    foreach ($orders as $order_id => $order) {
                        $notes = $nm->select('*')->where('order_id = ' . intval($order_id))->fetchAll();

                        if (count($notes) > 1) {
                            $old_note = $notes[0];
                            foreach ($notes as $note) {
                                if ($note['id'] != $old_note['id']) {
                                    $old_note['note'] .= PHP_EOL . $note['note'];
                                    $old_note['rate'] = $old_note['rate'] + $note['rate'];

                                    $nm->deleteByField('id', $note['id']);
                                }
                            }

                            if ($old_note['rate'] > 0) $old_note['rate'] = 1;
                            if ($old_note['rate'] < 0) $old_note['rate'] = -1;

                            $nm->updateByField('id', $old_note['id'], $old_note);
                        }
                    }

                    $nm->exec(
                        "ALTER TABLE `shop_customernotes_notes` CHANGE COLUMN `order_id` `order_id` INT(11) NOT NULL FIRST, CHANGE COLUMN `update_datetime` `datetime` DATETIME NULL DEFAULT NULL AFTER `create_contact_id`, DROP COLUMN `id`, DROP COLUMN `create_datetime`, DROP COLUMN `pro_note_id`, DROP PRIMARY KEY, ADD PRIMARY KEY (`order_id`)"
                    );

                    $nm->exec(
                        "ALTER TABLE `shop_customernotes_notes` DROP COLUMN `create_contact_id`;"
                    );

                    $nm->exec(
                        "CREATE TABLE `shop_customernotes_customer` (`contact_id` INT(11) NOT NULL, `uuid` VARCHAR(36) NULL DEFAULT NULL, `city` VARCHAR(50) NULL DEFAULT NULL, `country` VARCHAR(10) NULL DEFAULT NULL, `total_rating` INT(11) NOT NULL DEFAULT '0', `payments` INT(11) NOT NULL DEFAULT '0', `refunds` INT(11) NOT NULL DEFAULT '0', PRIMARY KEY (`contact_id`));"
                    );

                    $nm->exec(
                        "CREATE TABLE `shop_customernotes_phone` (`contact_id` INT(11) NOT NULL, `phone` VARCHAR(20) NOT NULL, PRIMARY KEY (`contact_id`, `phone`));"
                    );

                    $nm->exec(
                        "CREATE TABLE `shop_customernotes_email` (`contact_id` INT(11) NOT NULL, `email` VARCHAR(50) NOT NULL, PRIMARY KEY (`contact_id`, `email`));"
                    );


                }
            }
            catch (waException $e) {

            }
        }

    }

}
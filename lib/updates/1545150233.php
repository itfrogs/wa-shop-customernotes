<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 12/18/18
 * Time: 7:24 PM
 */

try {
    $nm = new shopCustomernotesNotesModel();
    if(!$nm->fieldExists('datetime')) {
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
<?php

/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 14.09.14
 * Time: 23:27
 */
class shopCustomernotesPluginViewHelper extends waViewHelper
{

    /**
     * @param $order_id
     * @param int $rate_id
     * @return array
     */
    public static function getCustomerRating($order_id, $rate_id = 0) {
        $nm = new shopCustomernotesNotesModel();
        $rating = $nm->getCustomerRating($order_id);
        return $rating;
    }

    /**
     * @param $rate
     * @return string
     */
    public static function getRatePic($rate) {
        $output = '';
        if ($rate > 0) {
            $output = '<i class="icon16 plus"></i>';
        }
        elseif ($rate < 0) {
            $output = '<i class="icon16 minus"></i>';
        }
        else {
            $output = '<i class="icon16 status-gray"></i>';
        }
        return $output;
    }

    public static function clearPhoneNumber($phone) {

        $phone = preg_replace("#[^\d]#", "", $phone);

        if (strlen($phone) < 11) return false;

        if (strlen($phone) == 11 && $phone[0] == 8) {
            $phone[0] = 7;
        }
        return $phone;
    }

    public static function validateEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        else return false;
    }
}
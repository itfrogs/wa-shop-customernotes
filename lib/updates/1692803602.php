<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 23.08.2023
 * Time: 18:13
 */

try {
    $path = wa()->getAppPath(null, 'shop') . '/plugins/customernotes/js/customernotes.min.min.js';
    waFiles::delete($path);
    $path = wa()->getAppPath(null, 'shop') . '/plugins/customernotes/lib/updates/customernotes_settings.min.min.js';
    waFiles::delete($path);
}
catch (waException $e) {

}
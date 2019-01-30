<?php
/**
 * Created by PhpStorm.
 * User: snark | itfrogs.ru
 * Date: 12/18/18
 * Time: 6:47 PM
 */
try {
    $path = wa()->getAppPath(null, 'shop') . '/plugins/customernotes/templates/';
    $files = array(
        'notesForm.html',
        'oneNote.html',
        'orderInfoSection.html',
    );
    foreach ($files as $file) {
        waFiles::delete($path . $file);
    }

    $path = wa()->getAppPath(null, 'shop') . '/plugins/customernotes/img/';
    $files = array(
        'down24.png',
        'sprite.png',
        'up24.png',
    );
    foreach ($files as $file) {
        waFiles::delete($path . $file);
    }
}
catch (waException $e) {

}
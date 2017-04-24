<?php
/**
 * Created by PhpStorm.
 * User: Pathologic
 * Date: 22.05.2015
 * Time: 20:43
 */

if (!IN_MANAGER_MODE) {
    die('<h1>ERROR:</h1><p>Please use the MODx Content Manager instead of accessing this file directly.</p>');
}
global $content;

include_once(MODX_BASE_PATH.'assets/tvs/selector/lib/selector.class.php');
$documentData = array(
    'id' => isset($content['id']) ? (int)$content['id'] : 0,
    'template' => (int)$content['template'],
    'parent' => (int)$content['parent'],
);

$selector = new \Selector\Selector (
    $modx,
    $row,
    $documentData
);

echo $selector->render();

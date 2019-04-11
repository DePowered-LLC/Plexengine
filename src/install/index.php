<?php
/*
@copy
 */

namespace pe\engine;

$base_url = 'http://plexengine.ru/cdn/installer';
$actual_version = @file_get_contents($base_url.'/version');

$ctx = [
    'steps' => [
        'Лицензия',
        'Проверка',
        'Конфигурация',
        'Готово'
    ],
    'actual_version' => $actual_version,
    'version' => '0.4.3'
];

$step = isset($_GET['step']) ? $_GET['step'] : 0;
$ctx['step'] = $step;
$steps = (require_once 'steps.php');
ob_start();
$ctx['status'] = $steps[$step]($ctx);
if (in_array('done', $ctx['status'])) {
    if ($step == count($steps)) header('Location: /');
    else header('Location: ?step='.($step + 1));
    exit;
}
$ctx['step_result'] = ob_get_clean();

View::load('*layout', $ctx);
?>

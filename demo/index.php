<?php
require_once '../vendor/autoload.php';

use Templator\Exceptions\InvalidTemplateException;
use Templator\Exceptions\ResultTemplateMismatchException;
use Templator\Templator;

$templator = new Templator();
$templator->setDefaultDirTemplates(__DIR__ . "/public/");

try {
    $templator->loadTemplate("index.tpl");
} catch (Exception $e) {
    echo $e->getMessage();
}

$resultRender = $templator->render(
    [
        'name' => 'Juni',
        'title_task' => '<ReverseTemplator>',
        'object' => '<robot>',
        'item' => '<cyborg>',
        'occupation' => 'templator'
    ]
);

var_dump($resultRender);

try {
    var_dump($templator->reverseTemplate($resultRender));
} catch (InvalidTemplateException | ResultTemplateMismatchException $e) {
    echo $e->getMessage();
}

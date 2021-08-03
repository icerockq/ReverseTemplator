# ReverseTemplator

Clone repository
```bash
https://github.com/icerockq/ReverseTemplator.git
```
Go to the project folder

Install dependencies
```bash
composer install
```

Go to demo folder
```bash
cd demo
```
Run the command
```bash
php index.php
```

Basic Usage

```php
require_once '../vendor/autoload.php';

//use the Templator to init template engine
$templator = new Templator();
//specify the directory with templates
$templator->setDefaultDirTemplates(__DIR__ . "/public/");
//load template
$templator->loadTemplate("index.tpl");
//use render to get result
$resultRender = $templator->render(['name' => 'Juni']);
//use method reverseTemplate to restore variables
$arrayTemplateValue = $templator->reverseTemplate($resultRender);
```

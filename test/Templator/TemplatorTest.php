<?php

namespace Templator\Test;

use Templator\Exceptions\InvalidTemplateException;
use Templator\Exceptions\ResultTemplateMismatchException;
use Templator\Templator;
use PHPUnit\Framework\TestCase;

class TemplatorTest extends TestCase
{

    private string $dirTemplate = __DIR__.'/extra/';

    public function testTextProcessing()
    {
        $text = "    Test    test, test     test   test    ";
        $expected = "Test test, test test test";

        $templator = new Templator();
        $result = $templator->textProcessing($text);
        self::assertEquals($expected, $result);
    }

    public function testLoadTemplate()
    {
        $templator = new Templator();
        $templator->setDefaultDirTemplates($this->dirTemplate);
        $templator->loadTemplate("test_template_load.tpl");
        self::assertEquals("Hello, my name is {{name}}.", $templator->getTextTemplate());
    }
    /**
     * @dataProvider caseBasicDataReverse
     */
    public function testReverseTemplate($templateFile, $result, $reverseVariable)
    {
        $templator = new Templator();
        $templator->setDefaultDirTemplates($this->dirTemplate);
        $templator->loadTemplate($templateFile);
        self::assertEquals($templator->reverseTemplate($result), $reverseVariable);
    }

    public function caseBasicDataReverse(): array
    {
        return [
            ['test_template_case_1.tpl', 'Hello, my name is Juni.', ['name' => "Juni"]],
            ['test_template_case_1.tpl', 'Hello, my name is .', ['name' => ""]],
            ['test_template_case_2.tpl', 'Hello, my name is <robot>.', ['name' => "<robot>"]],
            ['test_template_case_1.tpl', 'Hello, my name is &lt;robot&gt;.', ['name' => "<robot>"]],
        ];
    }

    public function testRender()
    {
        $templator = new Templator();
        $templator->setDefaultDirTemplates($this->dirTemplate);
        $templator->loadTemplate("test_template_render.tpl");
        $result = $templator->render(["name" => "Tom", "phone_number" => '<a href="tel:8(999)-999-99-99"></a>']);
        self::assertEquals('Hello, my name is Tom <a href="tel:8(999)-999-99-99"></a>.', $result);
    }

    /**
     * @dataProvider caseBasicDataClear
     */
    public function testTextClear(string $input, string $output)
    {
        $templator = new Templator();
        $templator->setDefaultDirTemplates($this->dirTemplate);
        self::assertEquals($templator->textClear($input), $output);
    }

    public function caseBasicDataClear(): array
    {
        $result = 'Hello, my name is Tom';
        return [
            ['Hello, my name is Tom.', $result, ['name' => "Juni"]],
            ['Hello, my name is Tom...', $result, ['name' => ""]]
        ];
    }

    /**
     * @dataProvider caseBasicDataExceptions
     */
    public function testExceptions($templateFile, $result, $exceptionClass)
    {
        $this->expectException($exceptionClass);
        $templator = new Templator();
        $templator->setDefaultDirTemplates($this->dirTemplate);
        $templator->loadTemplate($templateFile);
        self::assertEquals($templator->reverseTemplate($result), $result);
    }

    public function caseBasicDataExceptions(): array
    {
        return [
            ['test_template_error1.tpl', 'Hello, my name is Juni.', InvalidTemplateException::class],
            ['test_template_case_1.tpl', 'Hello, my lastname is Juni.', ResultTemplateMismatchException::class],
        ];
    }
}

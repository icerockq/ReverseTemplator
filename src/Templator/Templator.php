<?php

namespace Templator;

use DOMDocument;
use Templator\Exceptions\InvalidTemplateException;
use Templator\Exceptions\ResultTemplateMismatchException;

class Templator
{
    private string $textTemplate;
    private string $defaultDirTemplates = "/";

    public function loadTemplate(string $pathToTemplate)
    {
        $fullPathToTemplate = $this->defaultDirTemplates . $pathToTemplate;

        if (file_exists($fullPathToTemplate)) {
            $textFile = file_get_contents($fullPathToTemplate);
            $this->textTemplate = $this->textProcessing($textFile);
            return true;
        }

        throw new \Exception("File not found to path - " . $fullPathToTemplate);
    }

    public function textClear(string $text)
    {
        return preg_replace('/[\.]\n*/m', '', $text);
    }

    public function render(array $data)
    {
        $arrayPattern = [];
        $arrayReplacement = [];

        preg_match_all('/({{|{)(.*?)(}}|})/', $this->textTemplate, $matches);
        $arrayTemplateVars = $matches[0];

        foreach ($arrayTemplateVars as $var) {
            $countSeparator = substr_count($var, "{");
            $varWithoutSeparator = preg_replace('/[\{\}]/i', '', $var);
            $arrayPattern[] = $var;
            if($countSeparator == 1) {
                $arrayReplacement[] = $data[$varWithoutSeparator];
            } else {
                $arrayReplacement[] = htmlspecialchars($data[$varWithoutSeparator]);
            }
        }

        return str_replace($arrayPattern, $arrayReplacement, $this->textTemplate);
    }

    public function textProcessing($text): string
    {
        $output = preg_replace('/\s+/', ' ', $text);
        $output = trim($output);
        return $output;
    }

    public function reverseTemplate(string $textResult)
    {

        $arrayWords = mb_split(" ", $this->textClear($this->textTemplate));
        $arrayWordsResult = mb_split(" ", $this->textClear($textResult));


        $array = [];
        foreach ($arrayWords as $key => $word) {
            preg_match_all('/{{|{|}}|}/', $word, $matches);
            if ($matches[0]) {
                $separators = (array)$matches[0];
                if (($separators[0] and $separators[1])) {
                    $word = preg_replace('/[\{\}]/i', '', $word);
                    if ((strlen($separators[0]) != strlen($separators[1]) or $separators[0] == $separators[1])) {
                        throw new InvalidTemplateException("Invalid template.");
                    } elseif (strlen($separators[0]) == 1) {
                        $array[$word] = $arrayWordsResult[$key];
                    } elseif (strlen($separators[0]) == 2) {
                        $array[$word] = html_entity_decode($arrayWordsResult[$key]);
                    }
                }
            } else {
                if ($arrayWordsResult[$key] != $word) {
                    throw new ResultTemplateMismatchException("Result not matches original template.");
                }
            }
        }

        return $array;
    }

    /**
     * @param string $defaultDirTemplates
     */
    public function setDefaultDirTemplates(string $defaultDirTemplates): void
    {
        $this->defaultDirTemplates = $defaultDirTemplates;
    }

    /**
     * @return string
     */
    public function getTextTemplate(): string
    {
        return $this->textTemplate;
    }
}

<?php

declare(strict_types=1);

namespace Core\Config\Generator\Util;

final class TemplateRenderer
{
    /**
     * @param string $templatePath
     * @param array<string,string> $vars
     * @return string
     */
    public function render(string $templatePath, array $vars): string
    {
        $template = require $templatePath;

        if (!is_string($template)) {
            throw new \RuntimeException("Template at {$templatePath} must return a string.");
        }

        foreach ($vars as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }

        return $template;
    }
}

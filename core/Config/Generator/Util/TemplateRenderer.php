<?php

declare(strict_types=1);

namespace Core\Config\Generator\Util;

final class TemplateRenderer
{
    public function render(string $templatePath, array $vars): string
    {
        $template = require $templatePath;
        foreach ($vars as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }
        return $template;
    }
}

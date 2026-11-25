<?php

declare(strict_types=1);

namespace App;

final class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layout'): void
    {
        $content = self::fetch($template, $data);
        if ($layout === null) {
            echo $content;
            return;
        }
        echo self::fetch($layout, $data + ['content' => $content]);
    }

    public static function fetch(string $template, array $data = []): string
    {
        $file = App::root() . '/templates/' . $template . '.php';
        if (!preg_match('#^[a-z0-9/_-]+$#', $template) || !is_file($file)) {
            throw new \RuntimeException('Template not found: ' . $template);
        }
        extract($data, EXTR_SKIP);
        ob_start();
        try {
            require $file;
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return (string) ob_get_clean();
    }

    public static function partial(string $template, array $data = []): string
    {
        return self::fetch('partials/' . $template, $data);
    }
}

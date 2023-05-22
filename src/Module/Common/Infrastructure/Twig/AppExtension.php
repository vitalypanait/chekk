<?php

declare(strict_types=1);

namespace App\Module\Common\Infrastructure\Twig;

use LogicException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('encore_tags', [$this, 'renderTags'], ['is_safe' => ['html']]),
        ];
    }

    public function renderTags(): string
    {
        $filename = 'manifest.json';
        $json = [];

        if (file_exists($filename)) {
            $json = json_decode(file_get_contents($filename), true);

            if (json_last_error() !== 0) {
                throw new LogicException(sprintf('File [%s] has invalid JSON', $filename));
            }
        }

        if (empty($json)) {
            return '';
        }

        $path = 'src/main.js';
        $vendors = array_map(function (string $file) {
            $parts = explode('_', $file);

            return sprintf('<link rel="modulepreload" crossorigin href="/%s">', 'assets/' . $parts[1]);
        }, $json[$path]['imports']);

        $mainStyles = array_map(function (string $css) {
            return sprintf('<link rel="stylesheet" href="/%s">', $css);
        }, $json[$path]['css']);

        return implode(' ', array_merge(
            [sprintf('<script type="module" crossorigin src="/%s"></script>', $json[$path]['file'])],
            $vendors,
            [sprintf('<link rel="stylesheet" href="/%s">', $json['vendor.css']['file'])],
            $mainStyles
        ));
    }
}
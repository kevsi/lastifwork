<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IconColorExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getIconColor', [$this, 'getIconColor']),
            new TwigFunction('getFormatColor', [$this, 'getFormatColor']),
        ];
    }

    public function getIconColor(string $type): string
    {
        return match(strtolower($type)) {
            'rapport' => 'info',
            'contrat' => 'success',
            'facture' => 'warning',
            'procedure' => 'primary',
            'note' => 'danger',
            default => 'secondary'
        };
    }

    public function getFormatColor(string $format): string
    {
        return match(strtolower($format)) {
            'pdf' => 'success',
            'docx', 'doc' => 'info',
            'xlsx', 'xls' => 'warning',
            'txt' => 'danger',
            'png', 'jpg', 'jpeg' => 'primary',
            default => 'secondary'
        };
    }
}
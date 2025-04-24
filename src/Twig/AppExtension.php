<?php

namespace App\Twig;

use App\Controller\FileIconExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_file_icon', [FileIconExtension::class, 'getFileIcon']),
        ];
    }
}
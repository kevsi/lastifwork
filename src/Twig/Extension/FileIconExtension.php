<?php
// src/Twig/Extension/FileIconExtension.php
namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FileIconExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_file_icon', [$this, 'getFileIcon']),
        ];
    }

    public function getFileIcon(string $mimeType): string
    {
        $iconMap = [
            'application/pdf' => 'picture_as_pdf',
            'application/msword' => 'description',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'description',
            'application/vnd.ms-excel' => 'table_view',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'table_view',
            'application/vnd.ms-powerpoint' => 'slideshow',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'slideshow',
            'image/jpeg' => 'image',
            'image/png' => 'image',
            'image/gif' => 'gif',
            'text/plain' => 'article',
            'application/zip' => 'folder_zip',
            'application/x-rar-compressed' => 'folder_zip',
            'video/mp4' => 'movie',
            'audio/mpeg' => 'audio_file',
        ];
        
        // Extraire le type principal
        $mainType = explode('/', $mimeType)[0];
        
        if (isset($iconMap[$mimeType])) {
            return $iconMap[$mimeType];
        } elseif ($mainType === 'image') {
            return 'image';
        } elseif ($mainType === 'video') {
            return 'movie';
        } elseif ($mainType === 'audio') {
            return 'audio_file';
        }
        
        return 'insert_drive_file';
    }
}
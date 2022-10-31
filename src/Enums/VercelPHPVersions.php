<?php

namespace Mridhulka\LaravelVercel\Enums;

enum VercelPHPVersions: string
{
    case PHP81 = 'vercel-php@0.5.1 - PHP 8.1.x';
    case PHP8 = 'vercel-php@0.4.0 - PHP 8.0.x';
    case PHP74 = 'vercel-php@0.3.2 - PHP 7.4.x';
}
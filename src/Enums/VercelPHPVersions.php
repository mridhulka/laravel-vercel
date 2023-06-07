<?php

namespace Mridhulka\LaravelVercel\Enums;

enum VercelPHPVersions: string
{
    case PHP82 = 'vercel-php@0.6.0 - PHP 8.2.x';
    case PHP81 = 'vercel-php@0.5.3 - PHP 8.1.x';
    case PHP8 = 'vercel-php@0.4.1 - PHP 8.0.x';
    case PHP74 = 'vercel-php@0.3.3 - PHP 7.4.x';
}

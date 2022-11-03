# Laravel Vercel

[![Latest Version](https://img.shields.io/github/release/mridhulka/laravel-vercel.svg)](https://github.com/mridhulka/laravel-vercel/releases)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/mridhulka/laravel-vercel/Run%20tests)
![License](https://img.shields.io/github/license/mridhulka/laravel-vercel)

Publish assets for Laravel deployment on Vercel

## Installation

Require this package with composer. It is recommended to only require the package as a dev dependency.

```shell
composer require mridhulka/laravel-vercel --dev
```

### Publish the assets

```shell
php artisan vercel:install
```

## Testing

You can run the tests with:

```shell
./vendor/bin/pest
```

## More info

**[Customizing serverless functions - Vercel](https://vercel.com/blog/customizing-serverless-functions)**
**[Function details - Vercel](https://vercel.com/docs/project-configuration#project-configuration/functions)**

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

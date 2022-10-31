<?php

namespace Mridhulka\LaravelVercel\Tests\Feature;

use Mridhulka\LaravelVercel\Tests\TestCase;
use Mridhulka\LaravelVercel\Enums\VercelPHPVersions;


class InstallCommandTest extends TestCase
{
    /** @test */
    public function it_overwrites_assets_when_instructed()
    {
        file_put_contents(base_path('vercel.json'), 'test contents');
        file_put_contents(base_path('.vercelignore'), 'test contents');

        if (!is_dir(base_path('api'))) {
            mkdir(base_path('api'), 0777, true);
        }
        file_put_contents(base_path('api/index.php'), 'test contents');

        $this->artisan('vercel:install')
            ->expectsConfirmation(
                'Asset files for Vercel already exist. Do you want to continue?',
                'yes'
            )
            ->expectsOutput('Warning: Overwriting assets...')
            ->expectsQuestion('Select Vercel PHP version?', VercelPHPVersions::PHP81->value)
            ->expectsQuestion('Do you want to set custom serverless function properties?', 'y')
            ->expectsQuestion('What should be the maximum allotted memory (in MB) for the serverless function?', '1024')
            ->expectsQuestion('What should be the maximum allotted time (in seconds) for the serverless function?', '10')
            ->assertSuccessful();



        $this->assertJsonFileEqualsJsonFile(
            'tests/Stubs/vercel.json',
            base_path('vercel.json')
        );
    }

    /** @test */
    public function it_does_not_overwrite_assets_when_instructed()
    {

        file_put_contents(base_path('vercel.json'), 'test contents');
        file_put_contents(base_path('.vercelignore'), 'test contents');

        if (!is_dir(base_path('api'))) {
            mkdir(base_path('api'), 0777, true);
        }
        file_put_contents(base_path('api/index.php'), 'test contents');

        $this->assertTrue(file_exists(base_path('vercel.json')));

        $command = $this->artisan('vercel:install');
        $command->expectsConfirmation(
            'Asset files for Vercel already exist. Do you want to continue?',
            'no'
        );

        $command->expectsOutput('Exiting without overwriting asset files');

        $this->assertEquals('test contents', file_get_contents(base_path('vercel.json')));
        $this->assertEquals('test contents', file_get_contents(base_path('.vercelignore')));
        $this->assertEquals('test contents', file_get_contents(base_path('api/index.php')));
    }

    /** @test */
    public function it_publishes_the_asset_files()
    {

        $this->artisan('vercel:install')
            ->expectsQuestion('Select Vercel PHP version?', VercelPHPVersions::PHP81->value)
            ->expectsQuestion('Do you want to set custom serverless function properties?', 'y')
            ->expectsQuestion('What should be the maximum allotted memory (in MB) for the serverless function?', '1024')
            ->expectsQuestion('What should be the maximum allotted time (in seconds) for the serverless function?', '10')
            ->assertSuccessful();
        $this->assertJsonFileEqualsJsonFile(
            'tests/Stubs/vercel.json',
            base_path('vercel.json')
        );
    }

    /** @test */
    public function it_publishes_assets_with_default_values_if_custom_values_are_not_set()
    {

        $this->artisan('vercel:install')
            ->expectsQuestion('Select Vercel PHP version?', VercelPHPVersions::PHP81->value)
            ->expectsConfirmation('Do you want to set custom serverless function properties?', 'no')
            ->assertSuccessful();
        $this->assertJsonFileEqualsJsonFile(
            'tests/Stubs/vercel.json',
            base_path('vercel.json')
        );
    }





    public function tearDown(): void
    {
        unlink(base_path('vercel.json'));
        unlink(base_path('.vercelignore'));
        unlink(base_path('api/index.php'));
    }
}

<?php

namespace Mridhulka\LaravelVercel\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Mridhulka\LaravelVercel\Enums\VercelPHPVersions;

class InstallCommand extends Command
{

    protected $signature = 'vercel:install';

    protected $description = 'Publish assets required for deploying laravel on vercel.';

    private $vercelMaxMemory;
    private $vercelMaxDuration;

    public function handle()
    {
        $this->info('-----------------------------------------');
        $this->info('Publishing the Vercel deployment files...');
        $this->info('-----------------------------------------');

        $this->newLine();


        if (!$this->assetsExist()) {
            return $this->publishAssets();
        }

        if ($this->overwriteAssets()) {
            $this->warn('Warning: Overwriting assets...');
            return $this->publishAssets(true);
        }
        $this->warn('Exiting without overwriting asset files');
    }

    private function assetsExist()
    {
        return file_exists(base_path('vercel.json'))
            or file_exists(base_path('.vercelignore'))
            or file_exists(base_path('api/index.php'));
    }

    private function overwriteAssets()
    {
        return $this->confirm(
            'Asset files for Vercel already exist. Do you want to continue?'
        );
    }

    private function showBasicInfo()
    {
        $this->newLine();
        $this->info('More info at:');
        $this->newLine();
        $this->info('https://vercel.com/blog/customizing-serverless-functions');
        $this->info('https://vercel.com/docs/project-configuration#project-configuration/functions');
    }

    private function validateInputs($vercelMaxMemory, $vercelMaxDuration)
    {
        $validator = Validator::make([
            'vercelMaxMemory' => $vercelMaxMemory,
            'vercelMaxDuration' => $vercelMaxDuration,
        ], [
            'vercelMaxMemory' => [
                'integer',
                'between:128,3008',
                function ($vercelMaxMemory, $value, $fail) {
                    if (is_numeric($value)) {
                        if ($value % 64 != 0) {
                            $fail('The ' . $vercelMaxMemory . ' must between 128 and 3008, in intervals of 64.');
                        }
                    }
                },
            ],
            'vercelMaxDuration' => [
                'integer',
                'min:1',
            ],
        ]);

        if ($validator->fails()) {
            $this->error('Invalid input. Please try again.');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            exit;
        }
    }

    public function setCustomServerlessFunctionProperties()
    {
        if ($this->confirm('Do you want to set custom serverless function properties?')) {
            $this->showBasicInfo();

            $vercelMaxMemory = $this->ask('What should be the maximum allotted memory (in MB) for the serverless function?');
            $vercelMaxDuration = $this->ask('What should be the maximum allotted time (in seconds) for the serverless function?');

            $this->validateInputs($vercelMaxMemory, $vercelMaxDuration);

            $this->vercelMaxDuration = $vercelMaxDuration;
            $this->vercelMaxMemory = $vercelMaxMemory;
        } else {
            $this->vercelMaxMemory = 1024;
            $this->vercelMaxDuration = 5;
        }
    }

    private function infoTable($vercelPHPVersion, $vercelMaxMemory, $vercelMaxDuration)
    {
        $headers = ['Property', 'Value'];

        $data = [
            [
                'Property' => 'PHP version',
                'Value' => $vercelPHPVersion,
            ],
            [
                'Property' => 'Max Memory',
                'Value' => $vercelMaxMemory . ' MB',
            ],
            [
                'Property' => 'Max Duration',
                'Value' => $vercelMaxDuration . ' seconds',
            ],
        ];
        $this->newLine();
        $this->table($headers, $data);
    }

    private function choosePHPVersion()
    {
        $vercelPHPVersions = array_column(VercelPHPVersions::cases(), 'value');

        $this->vercelPHPVersionOption = $this->choice(
            'Select Vercel PHP version?',
            $vercelPHPVersions,
            1
        );

        $this->vercelPHPVersion = strstr($this->vercelPHPVersionOption,  ' ', true);
    }

    private function publishAssets($forcePublish = false)
    {
        try {
            $this->choosePHPVersion();

            $this->setCustomServerlessFunctionProperties();

            $stubContents = file_get_contents(__DIR__ . '/../../assets/vercel.json.stub');

            $vars = ['{{ vercelPHPVersion }}', '"{{ vercelMaxMemory }}"', '"{{ vercelMaxDuration }}"'];
            $values = [$this->vercelPHPVersion, $this->vercelMaxMemory, $this->vercelMaxDuration];
            $contents = str_replace($vars, $values, $stubContents);


            file_put_contents(base_path('vercel.json'), $contents);

            $params = [
                '--provider' => "Mridhulka\LaravelVercel\LaravelVercelServiceProvider",
                '--tag' => "assets"
            ];

            if ($forcePublish === true) {
                $params['--force'] = true;
            }

            $this->call('vendor:publish', $params);
            $this->infoTable($this->vercelPHPVersionOption, $this->vercelMaxMemory, $this->vercelMaxDuration);
            $this->info('Installation successfull');
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

<?php

namespace Naveed\BreezeNext\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'breeze-next:setup')]
class SetUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'breeze-next:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the project to act as a stateless api for a backend NextJs application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Updating controllers...");
        \Artisan::call('vendor:publish', ['--tag' => 'breeze-next', '--force' => true]);

        $this->updateCsrfMiddleware();

        $this->updateAuthRoutes();

        $this->updateAuthMiddleware();

        $this->updateEmailVerificationRequest();

        $this->warn("Please make sure that your User model uses Laravel\Sanctum\HasApiTokens trait.");

        return 0;
    }

    private function updateEmailVerificationRequest(): void
    {
        $str = 'use Illuminate\Foundation\Auth\EmailVerificationRequest;';
        $replacement = 'use Naveed\BreezeNext\Requests\EmailVerificationRequest;';
        $this->info("Updating email verification request from {$str} to {$replacement}...");
        $file = app_path("Http/Controllers/Auth/VerifyEmailController.php");
        $contents = file_get_contents($file);
        $contents = str_replace($str, $replacement, $contents);
        file_put_contents($file, $contents);
    }

    private function updateAuthMiddleware(): void
    {
        $file = base_path('routes/auth.php');
        $this->info("Updating middleware in {$file} from auth to auth:sanctum...");
        $content = file_get_contents($file);
        $regex = '/(middleware\(.*)\'auth\'(.*\))/';
        $newContent = preg_replace($regex, "$1'auth:sanctum'$2", $content);
        // remove auth from email verification link
        $regex = '/middleware\(.+auth:sanctum.+signed.+throttle:6,1.+\)/';
        $newContent = preg_replace($regex, "middleware(['signed', 'throttle:6,1'])", $newContent);
        file_put_contents($file, $newContent);
    }

    private function updateAuthRoutes(): void
    {
        $this->info("Moving auth routes from routes/web.php to routes/api.php...");
        $file = base_path('routes/web.php');
        $content = file_get_contents($file);
        $regex = '/require\s+__DIR__\s*\.\s*\'\/auth\.php\';/';
        $newContent = preg_replace($regex, "", $content);
        file_put_contents($file, $newContent);

        $file = base_path('routes/api.php');
        $content = file_get_contents($file);
        if (!preg_match($regex, $content)) {
            $line = "require __DIR__ . '/auth.php';";
            $newContent = $content . "\n" . $line . "\n";
            file_put_contents($file, $newContent);
        }
    }

    private function updateCsrfMiddleware(): void
    {
        $file = config_path('sanctum.php');
        $this->info("Updating csrf middleware in {$file}...");
        $content = file_get_contents($file);
        $newContent = preg_replace("/'validate_csrf_token' => (.+),/", "'validate_csrf_token' => Naveed\BreezeNext\Middlewares\ValidateCsrfToken::class,", $content);
        file_put_contents($file, $newContent);

        $envFile = base_path('.env');
        $content = file_get_contents($envFile);
        if (Str::of($content)->contains('BREEZE_NEXT_CSRF_KEY=')) {
            $this->warn("BREEZE_NEXT_CSRF_KEY already set in .env file. Please make sure that the same key exists in the .env file of your NextJs application.");
            return;
        }
        $line = "BREEZE_NEXT_CSRF_KEY=" . Str::random(32);
        $newContent = $content . "\n" . $line . "\n";
        file_put_contents($envFile, $newContent);
        $this->warn("Added `{$line}` in {$envFile}. Please add the same line in the .env file of your NextJs application.");
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('naira', function (string $expression) {
            $expression = trim($expression);
            if ($expression === '') {
                $expression = '0';
            }

            if (!str_contains($expression, ',')) {
                $expression .= ', 2';
            }

            return "<?php echo \\App\\Support\\Money::format($expression); ?>";
        });
    }
}

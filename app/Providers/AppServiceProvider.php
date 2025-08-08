<?php

namespace App\Providers;

use App\Interfaces\AffiliatesRepositoryInterface;
use App\Interfaces\CourseRepositoryInterface;
use App\Interfaces\ModuleRepositoryInterface;
use App\Interfaces\QuizRepositoryInterface;
use App\Interfaces\BundleRepositoryInterface;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\CertificateRepositoryInterface;
use App\Interfaces\CommissionRepositoryInterface;
use App\Interfaces\GrantCodeRepositoryInterface;
use App\Interfaces\LessonProgressRepositoryInterface;
use App\Interfaces\LessonRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\StudentRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\StudentGradingRepositoryInterface;
use App\Interfaces\WalletRepositoryInterface;
use App\Interfaces\WalletTransactionRepositoryInterface;
use App\Repositories\AffiliatesRepository;
use App\Repositories\BundleRepository;
use App\Repositories\CartRepository;
use App\Repositories\CertificateRepository;
use App\Repositories\CommissionRepository;
use App\Repositories\CourseRepository;
use App\Repositories\GrantCodeRepository;
use App\Repositories\LessonProgressRepository;
use App\Repositories\LessonRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\QuizRepository;
use App\Repositories\StudentGradingRepository;
use App\Repositories\StudentRepository;
use App\Repositories\WalletRepository;
use App\Repositories\WalletTransactionRepository;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(BundleRepositoryInterface::class, BundleRepository::class);
        $this->app->bind(ModuleRepositoryInterface::class, ModuleRepository::class);
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->bind(QuizRepositoryInterface::class, QuizRepository::class);
        $this->app->bind(LessonRepositoryInterface::class,LessonRepository::class);
        $this->app->bind(CartRepositoryInterface::class,CartRepository::class);
        $this->app->bind(OrderRepositoryInterface::class,OrderRepository::class);
        $this->app->bind(StudentRepositoryInterface::class,StudentRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class,PaymentRepository::class);
        $this->app->bind(StudentGradingRepositoryInterface::class,StudentGradingRepository::class);
        $this->app->bind(LessonProgressRepositoryInterface::class,LessonProgressRepository::class);
        $this->app->bind(GrantCodeRepositoryInterface::class,GrantCodeRepository::class);
        $this->app->bind(CommissionRepositoryInterface::class,CommissionRepository::class);
        $this->app->bind(AffiliatesRepositoryInterface::class,AffiliatesRepository::class);
        $this->app->bind(CertificateRepositoryInterface::class,CertificateRepository::class);
        $this->app->bind(WalletRepositoryInterface::class,WalletRepository::class);
        $this->app->bind(WalletTransactionRepositoryInterface::class,WalletTransactionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

    }
}

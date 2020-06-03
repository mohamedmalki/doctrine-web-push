<?php

namespace DoctrineWeb\WebPush;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Minishlink\WebPush\WebPush;

class DoctrineWebPushServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([VapidKeysGenerateCommand::class]);

        $this->mergeConfigFrom(__DIR__.'/../config/doctrinewebpush.php', 'doctrinewebpush');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->when(WebPushNotification::class)
            ->needs(WebPush::class)
            ->give(function () {
                $webPush = new WebPush($this->webPushConfig());
                $webPush->setReuseVAPIDHeaders(true);

                return $webPush;
            });

        $this->app->when(WebPushNotification::class)
            ->needs(ReportWebPushMessageInterface::class)
            ->give(ReportWebPushMessage::class);

        if ($this->app->runningInConsole()) {
            $this->definePublishing();
        }
    }

    /**
     * @return array
     */
    protected function webPushConfig()
    {
        $config = [];
        $webpush = config('doctrinewebpush');
        $publicKey = $webpush['vapid']['public_key'];
        $privateKey = $webpush['vapid']['private_key'];

        if (! empty($webpush['gcm']['key'])) {
            $config['GCM'] = $webpush['gcm']['key'];
        }

        if (empty($publicKey) || empty($privateKey)) {
            return $config;
        }

        $config['VAPID'] = compact('publicKey', 'privateKey');
        $config['VAPID']['subject'] = $webpush['vapid']['subject'];

        if (empty($config['VAPID']['subject'])) {
            $config['VAPID']['subject'] = url('/');
        }

        if (! empty($webpush['vapid']['pem_file'])) {
            $config['VAPID']['pemFile'] = $webpush['vapid']['pem_file'];

            if (Str::startsWith($config['VAPID']['pemFile'], 'storage')) {
                $config['VAPID']['pemFile'] = base_path($config['VAPID']['pemFile']);
            }
        }

        return $config;
    }

    /**
     * Define the publishable migrations and resources.
     *
     * @return void
     */
    protected function definePublishing()
    {
        $this->publishes([
            __DIR__.'/../config/doctrinewebpush.php' => config_path('doctrinewebpush.php'),
        ], 'config');

        if (! class_exists('CreatePushSubscriptionsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../migrations/create_push_subscriptions_table.php.stub' => database_path("migrations/{$timestamp}_create_push_subscriptions_table.php"),
            ], 'migrations');
        }
    }
}

<?php

namespace Atomic\Mail;

use Parsedown as Markdown;
use PHPMailer\PHPMailer\PHPMailer;
use Atomic\Foundation\Application;
use Atomic\Support\Arr;
use Atomic\Support\Facades\Event;
use Atomic\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('mailer', function (Application $app) {
            $mailer = new Mailer($app['events']);

            // Next we will set all of the global addresses on this mailer, which allows
            // for easy unification of all "from" addresses as well as easy debugging
            // of sent messages since these will be sent to a single email address.
            foreach (['from', 'replyTo', 'to'] as $type) {
                $this->setGlobalAddress($mailer, $app['config']['mail'], $type);
            }

            return $mailer;
        });

        $this->app->singleton('markdown', function () {
            return new Markdown();
        });
    }

    /**
     * Set a global address on the mailer by type.
     *
     * @param  \Atomic\Mail\Mailer  $mailer
     * @param  array  $config
     * @param  string  $type
     * @return void
     */
    protected function setGlobalAddress($mailer, array $config, string $type)
    {
        $address = Arr::get($config, $type, $this->app['config']['mail.' . $type]);

        if (is_array($address) && isset($address['address'])) {
            $mailer->{'always' . ucfirst($type)}($address['address'], $address['name']);
        }
    }

    /**
     * Boot the service provider
     *
     * @return void
     */
    public function boot(): void
    {
        Event::listen('phpmailer_init', function (PHPMailer $mailer) {
            $mailer->CharSet = 'UTF-8';
            $mailer->Encoding = 'base64';
        });
    }
}

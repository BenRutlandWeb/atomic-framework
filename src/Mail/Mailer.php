<?php

namespace Atomic\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use Atomic\Events\Dispatcher;

class Mailer
{
    /**
     * The event dispatcher instance.
     *
     * @var \Atomic\Events\Dispatcher
     */
    protected $events;

    /**
     * The global from address and name.
     *
     * @var array
     */
    protected $from;

    /**
     * The global reply-to address and name.
     *
     * @var array
     */
    protected $replyTo;

    /**
     * The global to address and name.
     *
     * @var array
     */
    protected $to;

    /**
     * Create a new Mailer instance.
     *
     * @param  \Atomic\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Set the global from address and name.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return void
     */
    public function alwaysFrom(string $address, ?string $name = null): void
    {
        $this->from = compact('address', 'name');
    }

    /**
     * Set the global reply-to address and name.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return void
     */
    public function alwaysReplyTo(string $address, ?string $name = null): void
    {
        $this->replyTo = compact('address', 'name');
    }

    /**
     * Set the global to address and name.
     *
     * @param  string  $address
     * @param  string|null  $name
     * @return void
     */
    public function alwaysTo(string $address, ?string $name = null): void
    {
        $this->to = compact('address', 'name');
    }

    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param  mixed  $users
     * @return \Atomic\Mail\PendingMail
     */
    public function to($users): PendingMail
    {
        return (new PendingMail($this))->to($users);
    }

    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param  mixed  $users
     * @return \Atomic\Mail\PendingMail
     */
    public function cc($users): PendingMail
    {
        return (new PendingMail($this))->cc($users);
    }

    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param  mixed  $users
     * @return \Atomic\Mail\PendingMail
     */
    public function bcc($users): PendingMail
    {
        return (new PendingMail($this))->bcc($users);
    }

    /**
     * Render the given message as a view.
     *
     * @param  \Atomic\Mail\Mailable $mailable
     * @return string
     */
    public function render(Mailable $mailable): string
    {
        return $mailable->render();
    }

    /**
     * Send the mail
     *
     * @param \Atomic\Mail\Mailable $mailable
     * @return bool
     */
    public function send(Mailable $mailable): bool
    {
        $mailable->build();

        // If a global from address has been specified we will set it on every message
        // instance so the developer does not have to repeat themselves every time
        // they create a new message. We'll just go ahead and push this address.
        if (!empty($this->from['address'])) {
            $mailable->from($this->from['address'], $this->from['name']);
        }

        // When a global reply address was specified we will set this on every message
        // instance so the developer does not have to repeat themselves every time
        // they create a new message. We will just go ahead and push this address.
        if (!empty($this->replyTo['address'])) {
            $mailable->replyTo($this->replyTo['address'], $this->replyTo['name']);
        }


        $this->events->listen('phpmailer_init', function (PHPMailer $phpmailer) use ($mailable) {
            if ($mailable->hasHtml()) {
                $phpmailer->AltBody = $mailable->buildText();
            }
        });

        // If a global "to" address has been set, we will set that address on the mail
        // message. This is primarily useful during local development in which each
        // message should be delivered into a single mail address for inspection.
        if (isset($this->to['address'])) {
            $this->setGlobalToAndRemoveCcAndBcc($mailable);
        }

        return wp_mail(
            $mailable->buildTo(),
            $mailable->buildSubject(),
            $mailable->buildHtml(),
            $mailable->buildHeaders(),
            $mailable->buildAttachments()
        );
    }

    /**
     * Set the global "to" address on the given message.
     *
     * @param  \Atomic\Mail\PendingMail $mailable
     * @return void
     */
    protected function setGlobalToAndRemoveCcAndBcc(Mailable $mailable): void
    {
        $mailable->unsetRecipients()
            ->to($this->to['address'], $this->to['name']);
    }
}

<?php

namespace Atomic\Mail;

class PendingMail
{
    /**
     * The mailer instance.
     *
     * @var \Atomic\Mail\Mailer
     */
    protected $mailer;

    /**
     * The "to" recipients of the message.
     *
     * @var mixed
     */
    protected $to = [];

    /**
     * The "cc" recipients of the message.
     *
     * @var mixed
     */
    protected $cc = [];

    /**
     * The "bcc" recipients of the message.
     *
     * @var mixed
     */
    protected $bcc = [];

    /**
     * Create a new mailable mailer instance.
     *
     * @param \Atomic\Mail\Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Set the recipients of the message.
     *
     * @param mixed $users
     * @return self
     */
    public function to($users): self
    {
        $this->to = $users;

        return $this;
    }

    /**
     * Set the recipients of the message.
     *
     * @param mixed $users
     * @return self
     */
    public function cc($users): self
    {
        $this->cc = $users;

        return $this;
    }

    /**
     * Set the recipients of the message.
     *
     * @param mixed $users
     * @return self
     */
    public function bcc($users): self
    {
        $this->bcc = $users;

        return $this;
    }

    /**
     * Send a new mailable message instance.
     *
     * @param \Atomic\Mail\Mailable $mailable
     * @return bool
     */
    public function send(Mailable $mailable): bool
    {
        return $this->mailer->send($this->fill($mailable));
    }

    /**
     * Populate the mailable with the addresses.
     *
     * @param \Atomic\Mail\Mailable $mailable
     * @return \Atomic\Mail\Mailable
     */
    protected function fill(Mailable $mailable): Mailable
    {
        return $mailable->to($this->to)->cc($this->cc)->bcc($this->bcc);
    }
}

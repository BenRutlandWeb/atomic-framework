<?php

namespace Atomic\Console;

use function WP_CLI\Utils\make_progress_bar as wp_cli_make_progress_bar;

class ProgressBar
{
    /**
     * The progress bar count
     *
     * @var integer
     */
    protected $count = 0;

    /**
     * The progress bar message
     *
     * @var string
     */
    protected $message = '';

    /**
     * Assign the count
     *
     * @param integer $count
     */
    public function __construct(int $count)
    {
        $this->count = $count;
    }

    /**
     * Set a progress bar message.
     *
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Start the progressbar
     *
     * @return void
     */
    public function start(): void
    {
        $this->bar = wp_cli_make_progress_bar($this->message, $this->count);
    }

    /**
     * Increment the progress bar
     *
     * @return void
     */
    public function advance(): void
    {
        $this->bar->tick();
    }
    /**
     * Finish the progressbar
     *
     * @return void
     */
    public function finish(): void
    {
        $this->bar->finish();
    }
}

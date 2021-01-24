<?php

namespace Atomic\WordPress;

use BadMethodCallException;
use Atomic\Foundation\Application;

abstract class Shortcode
{
    /**
     * The shortcode tag
     *
     * @var string
     */
    protected $tag;

    /**
     * An array of allowed attributes and defaults
     *
     * @var array
     */
    protected $defaultAttributes = [];

    /**
     * The shortcode attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The shortcode content
     *
     * @var string
     */
    protected $content = '';

    /**
     * The application instance
     *
     * @var \Atomic\Foundation\Application
     */
    protected $app;

    /**
     * Create the shortcode instance
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register the shortcode
     *
     * @return void
     */
    public function register(): void
    {
        add_shortcode($this->tag, [$this, 'resolveShortcode']);
    }

    /**
     * Handle the shortcode
     *
     * @param array|string|null $attrs
     * @param string|null $content
     * @return mixed
     */
    public function resolveShortcode($attrs, string $content = '')
    {
        $this->attributes = shortcode_atts($this->defaultAttributes, $attrs, $this->tag);
        $this->content = $content;

        return $this->callhandler();
    }

    /**
     * Call the handle method
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function callhandler()
    {
        if (method_exists($this, 'handle')) {
            return $this->app->call([$this, 'handle']);
        }

        throw new BadMethodCallException(sprintf('Method %s::handle does not exist.', static::class));
    }

    /**
     * get the attributes
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get the content
     *
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * Get an attribute
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically get the shortcode attributes
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }
}

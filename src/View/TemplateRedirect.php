<?php

namespace Atomic\View;

use Atomic\Events\Dispatcher;
use Atomic\Foundation\Application;

class TemplateRedirect
{
    /**
     * The app instance
     *
     * @var \Atomic\Foundation\Application
     */
    protected $app;

    /**
     * The templates that WordPress looks for in the root of the theme.
     *
     * @var array
     */
    protected $templateHierarchy = [
        'index',
        '404',
        'archive',
        'author',
        'category',
        'tag',
        'taxonomy',
        'date',
        'embed',
        'home',
        'frontpage',
        'privacypolicy',
        'page',
        'paged',
        'search',
        'single',
        'singular',
        'attachment',
    ];

    /**
     * Filter the template heirarchy.
     *
     * @param \Atomic\Foundation\Application $app
     * @param \Atomic\Events\Dispatcher $events
     */
    public function __construct(Application $app, Dispatcher $events)
    {
        $this->app = $app;

        foreach ($this->templateHierarchy as $type) {
            $events->listen("{$type}_template_hierarchy", [$this, 'filterTemplates']);
        };
    }

    /**
     * Filter the WordPress hierarchy to look for templates in views before
     * looking in the root of the theme.
     *
     * @param array $templates
     * @return array
     */
    public function filterTemplates(array $templates): array
    {
        $path = str_replace(
            $this->app->basePath() . DIRECTORY_SEPARATOR,
            '',
            $this->app['config']['view.path']
        );

        return collect($templates)
            ->map(function ($template) use ($path) {
                return ["$path/$template", $template];
            })
            ->flatten()
            ->toArray();
    }
}

<?php namespace BizMark\Streaming;

use Backend, Event;
use System\Classes\PluginBase;

/**
 * Streaming Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Streaming',
            'description' => 'No description provided yet...',
            'author'      => 'BizMark',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Registers any mail templates implemented in this plugin.
     *
     * @return array
     */
    public function registerMailTemplates()
    {
        return [
            'bizmark.streaming::mail.user_activate',
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
           'BizMark\Streaming\Components\ChatWindow' => 'ChatWindow',
           'BizMark\Streaming\Components\Auth' => 'Auth',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'bizmark.streaming.access_persons' => [
                'tab' => 'Streaming',
                'label' => 'Managing persons'
            ],
            'bizmark.streaming.access_messages' => [
                'tab' => 'Streaming',
                'label' => 'Managing messages'
            ],
            'bizmark.streaming.access_photos' => [
                'tab' => 'Streaming',
                'label' => 'Managing photos'
            ],
            'bizmark.streaming.access_settings' => [
                'tab' => 'Streaming',
                'label' => 'Managing settings'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'streaming-persons' => [
                'label'       => 'Persons',
                'url'         => Backend::url('bizmark/streaming/persons'),
                'icon'        => 'icon-user',
                'permissions' => ['bizmark.streaming.access_persons'],
                'order'       => 500,
            ],
            'streaming-messages' => [
                'label'       => 'Messages',
                'url'         => Backend::url('bizmark/streaming/messages'),
                'icon'        => 'icon-comment',
                'permissions' => ['bizmark.streaming.access_messages'],
                'order'       => 500,
            ],
            'streaming-photos' => [
                'label'       => 'Photos',
                'url'         => Backend::url('bizmark/streaming/photos'),
                'icon'        => 'icon-file-image-o',
                'permissions' => ['bizmark.streaming.access_photos'],
                'order'       => 500,
            ],
        ];
    }

    /**
     * Registers back-end settings items for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Chat settings',
                'description' => 'Managing chat settings',
                'category'    => 'Chat system',
                'icon'        => 'icon-cog',
                'class'       => 'BizMark\Streaming\Models\Settings',
                'order'       => 500,
                'keywords'    => 'chatting settings',
                'permissions' => ['bizmark.streaming.access_settings'],
            ]
        ];
    }
}

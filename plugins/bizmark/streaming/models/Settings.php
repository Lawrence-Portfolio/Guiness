<?php namespace BizMark\Streaming\Models;

use Model;

/**
 * Settings Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'bizmark_streaming_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}

<?php namespace BizMark\Streaming\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use BizMark\Streaming\Models\Settings;

/**
 * Photos Back-end Controller
 */
class Photos extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('BizMark.Streaming', 'streaming-photos', 'photos');
    }

    public function getMessagesInterval()
    {
        return Settings::get('moderator_update_time_interval');
    }

    public function index_onRefresh()
    {
        return $this->listRefresh();
    }
}

<?php namespace BizMark\Streaming\Controllers;

use BackendMenu, Redirect;
use Backend\Classes\Controller;
use BizMark\Streaming\Models\Message;
use BizMark\Streaming\Models\Person;
use BizMark\Streaming\Models\Settings;

/**
 * Messages Back-end Controller
 */
class Messages extends Controller
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

        BackendMenu::setContext('BizMark.Streaming', 'streaming-messages', 'messages');
    }

    public function index_onRefresh()
    {
        return $this->listRefresh();
    }

    public function getMessagesInterval()
    {
        return Settings::get('moderator_update_time_interval');
    }

    public function getStreamUrl()
    {
        return Settings::get('stream_url');
    }

    public function onDownloadMessages()
    {
        $fileUrl = Message::generateLinkToZipWithMessages();
        if ($fileUrl) {
            return Redirect::to($fileUrl);
        }

        Flash::error('Нет сообщений для генерации архива');
        return null;
    }
}

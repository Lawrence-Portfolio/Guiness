<?php namespace BizMark\Streaming\Components;

use BizMark\Streaming\Models\Person;
use BizMark\Streaming\Models\Settings;
use October\Rain\Exception\AjaxException;
use Session, Input;
use Cms\Classes\ComponentBase;
use BizMark\Streaming\Models\Message;
use System\Models\File;

class ChatWindow extends ComponentBase
{
    public $messages;
    public $check_interval;
    public $stream_url;

    public function componentDetails()
    {
        return [
            'name'        => 'ChatWindow Component',
            'description' => 'No description provided yet...'
        ];
    }

    /**
     * @return int|null
     */
    public function getPerson()
    {
        if (Session::has('participant')) {
            return Session::get('participant');
        }

        return null;
    }

    /**
     * @return Person|Person[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getPersonObject()
    {
        if (Session::has('participant')) {
            return Person::notBanned()->find(Session::get('participant'));
        }

        return null;
    }

    public function onRun()
    {
        $this->messages = $this->getLastMessages();
        $this->check_interval = Settings::get('participant_update_time_interval');
        $this->stream_url = Settings::get('stream_url');
    }

    protected function getLastMessages()
    {
        return Message::orderBy('created_at', 'desc')->with('person')->take(25)->get();
    }

    public function onCheckMessages()
    {
        return [
            '#messagesWrap' => $this->renderPartial('chat/messages', ['messages' => $this->getLastMessages()])
        ];
    }

    public function onSubmit()
    {
        $data = Input::all();

        if (Session::token() != e(array_get($data, '_token'))) {
            throw new AjaxException(['X_OCTOBER_ERROR_MESSAGE' => 'Token mismatch.']);
        }

        $content = trim(e(array_get($data,'message')));

        if (!Input::has('files') && empty($content)) {
            throw new AjaxException(['X_OCTOBER_ERROR_MESSAGE' => 'Message can\'t be empty.']);
        }

        $person = $this->getPersonObject();

        if (empty($person)) {
            throw new AjaxException(['X_OCTOBER_ERROR_MESSAGE' => 'Server error occurred. Reload page']);
        }

        if ($person->is_blocked) {
            throw new AjaxException(['X_OCTOBER_ERROR_MESSAGE' => 'You have been banned from chat.']);
        }

        $message = new Message();
        $message->person_id = $person->id;
        $message->content = $content;

        if (Input::has('files')) {
            $message->is_image = true;
            $uploads = Input::file('files');

            foreach ($uploads as $upload) {
                if ($upload->getSize() > 10485760) {
                    throw new AjaxException(['X_OCTOBER_ERROR_MESSAGE' => 'You can\'t upload file more than 10mb']);
                }

                $file = new File();
                $file->fromFile($upload);
                $file->save();

                $person->images()->add($file);
                $person->save();
            }
        }

        $message->save();

        return [
            '#messagesWrap' => $this->renderPartial('chat/messages', ['messages' => $this->getLastMessages()])
        ];
    }
}

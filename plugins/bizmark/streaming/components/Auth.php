<?php namespace BizMark\Streaming\Components;

use Input, Session, Redirect, Mail;
use Cms\Classes\ComponentBase;
use October\Rain\Argon\Argon;
use Cms\Classes\Page as CmsPage;
use October\Rain\Exception\AjaxException;

use BizMark\Streaming\Models\Person;

class Auth extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Auth Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function getPersonObject()
    {
        if (Session::has('participant')) {
            return Person::notBanned()->find(Session::get('participant'));
        }

        return null;
    }

    public function onLogin()
    {
        $data = Input::all();

        if (Session::token() != e(array_get($data, '_token'))) {
            throw new AjaxException(['X_OCTOBER_ERROR_MESSAGE' => 'Token mismatch.']);
        }

        if (Session::has('participant')) {
            throw new AjaxException(['X_OCTOBER_ERROR_MESSAGE' => 'You are already logged in.']);
        }

        $username = trim(e(array_get($data, 'username')));
        $email = trim(e(array_get($data, 'email')));

        $person = Person::where('username', $username)
            ->where('email', $email)
            ->first();

        if (!empty($person)) {
            if ($this->auth($person)) {
                return Redirect::refresh();
            } else {
                throw new AjaxException(['X_OCTOBER_ERROR_MESSAGE' => 'Unsuccessful login attempt.']);
            }
        } else {
            if ($this->register($username, $email)) {
                return Redirect::refresh();
            } else {
                throw new AjaxException(['X_OCTOBER_ERROR_MESSAGE' => 'Unsuccessful registration attempt.'. PHP_EOL.'This email may already be registered with a different nickname.']);
            }
        }
    }

    public function onLogOut()
    {
        Session::forget('participant');
        Session::forget('person');
        return Redirect::refresh();
    }

    /**
     * Auth user
     * @param Person $person
     * @return boolean
     */
    protected function auth(Person $person)
    {
        try {
            Session::put('participant', $person->id);
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Register new user
     * @param string $username
     * @param string $email
     * @return boolean
     */
    protected function register(string $username, string $email)
    {
        try {
            $current_time = Argon::now();

            $person = new Person;
            $person->username = $username;
            $person->email = $email;
            $person->is_activated = true;
//            $person->activation_sent_at = $current_time;
            $person->save();

//            $params = [
//                'username' => $person->username,
//                'link' => Redirect::to(CmsPage::url('index').'?code='.$person->activation_code)
//            ];
//
//            Mail::sendTo($person->email, 'bizmark.streaming::mail.user_activate', $params);

            Session::put('participant', $person->id);
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function onResendEmail()
    {
        $person = $this->getPersonObject();

//        $person->ac
    }
}

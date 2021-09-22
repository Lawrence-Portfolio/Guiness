<?php namespace BizMark\Streaming\Controllers;

use Flash, Redirect;
use BackendMenu;
use Backend\Classes\Controller;
use BizMark\Streaming\Models\Person;

/**
 * Persons Back-end Controller
 */
class Persons extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
    ];

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var string Configuration file for the `RelationController` behavior.
     */
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('BizMark.Streaming', 'streaming-persons', 'persons');
    }

    public function index()
    {
        $this->addJs('/plugins/bizmark/streaming/assets/js/bulk-actions.js');

        $this->asExtension('ListController')->index();
    }

    /**
     * {@inheritDoc}
     */
    public function listInjectRowClass($record, $definition = null)
    {
        $classes = [];

        if ($record->is_blocked) {
            $classes[] = 'negative';
        }

        if (!$record->is_activated) {
            $classes[] = 'disabled';
        }

        if (count($classes) > 0) {
            return join(' ', $classes);
        }
    }

    /**
     * Perform bulk action on selected users
     */
    public function index_onBulkAction()
    {
        if (
            ($bulkAction = post('action')) &&
            ($checkedIds = post('checked')) &&
            is_array($checkedIds) &&
            count($checkedIds)
        ) {

            foreach ($checkedIds as $userId) {
                if (!$person = Person::find($userId)) {
                    continue;
                }

                switch ($bulkAction) {
                    case 'delete':
                        $person->forceDelete();
                        break;

                    case 'activate':
                        $person->activate($person->activation_code);
                        break;

                    case 'deactivate':
                        $person->deactivate();
                        break;

                    case 'ban':
                        $person->ban();
                        break;

                    case 'unban':
                        $person->unban();
                        break;
                }
            }

            Flash::success('Действие успешно выполнено');
        }
        else {
            Flash::error('Произошла ошибка');
        }

        return $this->listRefresh();
    }

    public function onActivate()
    {
        $id = input('id');
        $person = Person::find($id);
        $person->activate($person->activation_code);

        Flash::success('Успешно активировано!');

        return Redirect::refresh();
    }

    public function onDeactivate()
    {
        $id = input('id');
        $person = Person::find($id);
        $person->deactivate();

        Flash::success('Успешно деактивировано!');

        return Redirect::refresh();
    }

    public function onBan()
    {
        $id = input('id');
        $person = Person::find($id);
        $person->ban();

        Flash::success('Успешно заблокировано!');

        return Redirect::refresh();
    }

    public function onUnBan()
    {
        $id = input('id');
        $person = Person::find($id);
        $person->unban();

        Flash::success('Успешно разблокировано!');

        return Redirect::refresh();
    }

    public function index_onDownloadImages()
    {
        $fileUrl = Person::generateLinkToZipWithImages();
        if ($fileUrl) {
            return Redirect::to($fileUrl);
        }

        Flash::error('Нет картинок для генерации архива');
        return null;
    }
}

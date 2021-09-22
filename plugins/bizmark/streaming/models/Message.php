<?php namespace BizMark\Streaming\Models;

use Storage;
use October\Rain\Database\Model;
use October\Rain\Filesystem\Zip;

/**
 * Message Model
 * @package BizMark\Streaming\Models
 * @author Nick Khaetsky at Biz-Mark, nick@biz-mark.ru, https://biz-mark.ru
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                                             $id
 * @property string                                          $content
 * @property boolean                                         $is_image
 * @property \October\Rain\Argon\Argon                       $created_at
 * @property \October\Rain\Argon\Argon                       $updated_at
 *
 * Relations
 * @property int                                             $person_id
 * @property Person                                          $person
 * @method \October\Rain\Database\Relations\BelongsTo|Person person()
 */
class Message extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bizmark_streaming_messages';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'person' => Person::class
    ];

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string|boolean
     */
    public static function generateLinkToZipWithMessages()
    {
        $LogfileName = 'messages_history.txt';
        $messages = self::orderBy('created_at', 'DESC')->get();

        if ($messages->count() > 0) {
            Storage::delete($LogfileName);
            Storage::put($LogfileName, ' ');

            foreach ($messages as $message) {
                if (!empty($message->person)) {
                    $username = $message->person->username;
                    $email = $message->person->email;
                } else {
                    $username = 'MODERATOR';
                    $email = '';
                }

                Storage::append($LogfileName, sprintf("[%s] %s - %s: %s", $message->created_at->timezone('Europe/Moscow')->format('H:i:s'), $username, $email, $message->content));
            }

            Zip::make(storage_path('app/media/messages_history.zip'), function ($zip) use ($LogfileName){
                $zip->addFile(storage_path('app/messages_history.txt'), 'messages_history.txt');
            });
            return url('storage/app/media/messages_history.zip');
        }

        return false;
    }

    public static function send()
    {

    }
}

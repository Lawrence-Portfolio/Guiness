<?php namespace BizMark\Streaming\Models;

use October\Rain\Argon\Argon;
use ValidationException;
use October\Rain\Database\Model;
use October\Rain\Filesystem\Zip;

/**
 * Person Model
 * @package BizMark\Streaming\Models
 * @author Nick Khaetsky at Biz-Mark, nick@biz-mark.ru, https://biz-mark.ru
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                                                           $id
 * @property string                                                        $username
 * @property string                                                        $email
 * @property string                                                        $activation_code
 * @property boolean                                                       $is_activated
 * @property boolean                                                       $is_blocked
 * @property \October\Rain\Argon\Argon                                     $activation_sent_at
 * @property \October\Rain\Argon\Argon                                     $created_at
 * @property \October\Rain\Argon\Argon                                     $updated_at
 *
 * Relations
 * @property Message[]                                                     $messages
 * @method \October\Rain\Database\Relations\HasMany|Message                messages()
 *
 * @property \System\Models\File[]                                         $images
 * @method \October\Rain\Database\Relations\AttachMany|\System\Models\File images()
 *
 * Scopes
 * @method static $this                                                    activated()
 * @method static $this                                                    notActivated()
 * @method static $this                                                    banned()
 * @method static $this                                                    notBanned()
 */
class Person extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bizmark_streaming_persons';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'email' => 'required|email|unique:bizmark_streaming_persons',
        'images.*' => 'image|max:10240',
        'username' => 'required',
    ];

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
        'activation_sent_at',
        'created_at',
        'updated_at'
    ];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'messages' => [
            Message::class,
            'key' => 'person_id',
            'delete' => true
        ]
    ];
    public $attachMany = [
        'images' => [
            \System\Models\File::class,
            'delete' => true
        ]
    ];

    public function beforeCreate()
    {
        if (empty($this->activation_code)) {
            $this->activation_code = $this->generateActivationCode();
        }
    }

    public function scopeActivated($query)
    {
        return $query->where('is_activated', true);
    }

    public function scopeNotActivated($query)
    {
        return $query->where('is_activated', false);
    }

    public function scopeBanned($query)
    {
        return $query->where('is_blocked', true);
    }

    public function scopeNotBanned($query)
    {
        return $query->where('is_blocked', false);
    }

    /**
     * Activates person
     * @param $activation_code
     */
    public function activate($activation_code)
    {
        if ($this->is_activated != true) {
            if ($this->activation_code == $activation_code) {
                $this->is_activated = true;
                $this->activation_code = null;
                $this->save();
            } else {
                throw new ValidationException(['activation_code' => 'Не правильный код валидации']);
            }
        }
    }

    /**
     * Deactivates person.
     */
    public function deactivate()
    {
        if ($this->is_activated != false) {
            $this->is_activated = false;
            $this->activation_code = $this->generateActivationCode();
            $this->save();
        }
    }

    /**
     * Blocks user from auth and participating in system
     */
    public function ban()
    {
        $this->is_blocked = true;
        $this->save();
    }

    /**
     * Unblocks user
     */
    public function unban()
    {
        $this->is_blocked = false;
        $this->save();
    }

    /**
     * Generator of random string for person activation
     * @param int $length
     * @return string
     */
    protected function generateActivationCode($length = 6)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\UrlGenerator|string|boolean
     */
    public static function generateLinkToZipWithImages()
    {
        $persons = self::activated()->notBanned()->whereHas('images', function ($query) {
            $query->whereBetween('created_at', ['2020-12-05 16:00:00', '2020-12-05 18:34:00']);
        })->get();
        $current_timestamp = Argon::now()->format('Ymd-his');

        if ($persons->count() > 0) {
            Zip::make(storage_path('app/media/persons_images'.$current_timestamp.'.zip'), function ($zip) use ($persons, $current_timestamp) {
                foreach ($persons as $person) {
                    foreach ($person->images as $key => $image) {
                        $extension = explode('/', $image->content_type);
                        $extension = end($extension);
//                        $iterator = $key + 1;

                        $zip->addFile(
                            storage_path('app/' . $image->getDiskPath()),
                            e($person->username) . ' - ' . e($person->email) .' - '.$current_timestamp.'.' . $extension
                        );
                    }
                }
            });

            return url('storage/app/media/persons_images'.$current_timestamp.'.zip');
        }

        return false;
    }
}

<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Entrust\HasRole;
use Dingo\Api\Transformer\TransformableInterface;

/**
 * User
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Config::get('entrust::role[] $roles
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $confirmation_code
 * @property boolean $confirmed
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereConfirmationCode($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereConfirmed($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereUpdatedAt($value)
 */
class User extends ConfideUser implements TransformableInterface
{
    use HasRole;

    public static $relationsData = array(
        'activities' => array(self::HAS_MANY, 'Activity'),
        'events_created' => array(self::HAS_MANY, 'CalendarEvent'),
        'registrations' => array(self::HAS_MANY, 'EventRegistration'),
    );

    public function getTransformer()
    {
        return new UserTransformer;
    }
}

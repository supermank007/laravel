<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public $primaryKey = 'id';

    public $incrementing = false;

    public function roles() {
        return $this->belongsToMany('App\Role', 'role_user', 'user_id', 'role_id');
    }

    public function program() {
        return $this->belongsTo('App\Program');
    }

    public function registrations() {
        return $this->hasMany('App\UserRegistration');
    }

    public function created_forms() {
        return $this->hasMany('App\Form', 'creator_user_id');
    }

    public function edited_forms() {
        return $this->hasMany('App\Form', 'editor_user_id');
    }

    public function assigner_form_assignments() {
        return $this->hasMany('App\FormAssignment', 'assigner_user_id');
    }

    public function hasRoles(...$roles) {
        return $this->roles()->whereIn('role', $roles)->count() > 0;
    }

     public function scopeActive($query)
    {
        return $query->where('active', '1')
                     ->whereHas('program', function($query) {
                        $query->where('deleted_at', '=', null);
                     });
    }

    public function fullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function isSameAs(User $user) {
        return $this->getKey() == $user->getKey();
    }

    public static function getUserRegistration() {
        return UserRegistration::where('registration_number', session('registration_number') )->first();
    }
    
}

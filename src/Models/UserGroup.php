<?php
namespace App\Semlohe\Models;

class UserGroup extends Model
{
    /**
     * Table Definition
     *
     * var @string
     */
    protected $table = 'user_group';

    /**
     * PK Definition
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Disable Autoincrement
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * Disable created_at and updated_at for eloquent
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Has many user relation
     */
    public function users()
    {
        return $this->hasMany('App\Semlohe\Models\User');
    }

    /**
     * Scope Lower or Equal Tier
     *
     * @param $query
     * @return string
     */
    public function scopeLowerOrEqualTier($query, $tier)
    {
        return $query
        ->where('tier', '>=', $tier);
    }

    /**
     * Scope Active 
     *
     * @param $query
     * @return string
     */
    public function scopeActive($query)
    {
        return $query->where('status', '=', '1');
    }

    /**
     * Scope Not Deleted
     *
     * @param $query
     * @return string
     */
    public function scopeNotDeleted($query)
    {
        return $query
        ->where('deleted', '=', '0');
    }
}

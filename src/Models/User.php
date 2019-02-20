<?php
namespace App\Semlohe\Models;

class User extends Model
{
    /**
     * Table Definition
     *
     * var @string
     */
    protected $table = 'user';

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
     * Belongs to user group relation
     */
    public function userGroup()
    {
        return $this->belongsTo('App\Semlohe\Models\UserGroup');
    }

    /**
     * Filter by lower or equal tier
     * 
     * @param $query
     * @param integer $tier
     */
    public function scopeLowerOrEqualTier($query, $tier)
    {
        return $query->whereHas(
            'userGroup',
            function ($q) use ($tier) {
                $q->lowerOrEqualTier($tier);
            }
        );
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

    /**
     * Scope Active - Mengembalikan artikel hanya yang statusnya aktif
     *
     * @param $query
     * @return string
     */
    public function scopeActive($query)
    {
        return $query->where('status', '=', '1');
    } 
}

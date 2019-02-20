<?php
namespace App\Semlohe\Models;

class Client extends Model
{
    /**
     * Table Definition
     *
     * var @string
     */
    protected $table = 'client';

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
    public $timestamps = false;
}

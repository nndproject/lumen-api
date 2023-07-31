<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MasterCertificate extends Model
{
    use SoftDeletes;

    protected $connection   = 'mysql';
    protected $table        = 'master_certificate';
    protected $dates        = ['deleted_at'];
    protected $primaryKey   = 'id';
    protected $fillable = [
        'event_name',
        'event_description', 
        'event_date',
        'event_signed',
        'status',
        'post_by',
    ];
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    // If the table name is not 'sessions', specify the table name
    protected $table = 'sessions';

    // Disable timestamps if they are not used in this table
    public $timestamps = false;

    // Allow mass assignment for the following fields
    protected $fillable = ['id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity', 'device_id'];
}

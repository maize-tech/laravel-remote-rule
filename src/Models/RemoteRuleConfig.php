<?php

namespace Maize\RemoteRule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maize\RemoteRule\Models\Concerns\CastsAttributes;

class RemoteRuleConfig extends Model
{
    use HasFactory;
    use CastsAttributes;

    protected $fillable = [
        'name',
        'url',
        'method',
        'headers',
        'json',
        'timeout',
    ];
}

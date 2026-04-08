<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *  MODEL: NpvCashFlow
 *  Layer  : Data / Model Layer
 *
 * @property int    $id
 * @property int    $npv_project_id
 * @property int    $year
 * @property float  $cash_flow
 * @property float  $discount_factor
 * @property float  $present_value
 */
class NpvCashFlow extends Model
{
    use HasFactory;

    protected $table = 'npv_cash_flows';

    protected $fillable = [
        'npv_project_id',
        'year',
        'cash_flow',
        'discount_factor',
        'present_value',
    ];

    protected $casts = [
        'cash_flow'       => 'float',
        'discount_factor' => 'float',
        'present_value'   => 'float',
        'year'            => 'integer',
    ];


    public function project(): BelongsTo
    {
        return $this->belongsTo(NpvProject::class, 'npv_project_id');
    }
}
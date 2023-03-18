<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderService extends Model
{
    protected $table = 'provider_sub_service';

    protected $fillable = [
        'provider_id', 'sub_service_id', 'hour', 'job'
    ];
    public $timestamps = false;

    /**
     * Get the provider that owns the ProviderService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'id', 'provider_id');
    }

    /**
     * Get the subService te ProviderService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subService()
    {
        return $this->belongsTo(SubService::class, 'sub_service_id', 'id');
    }
}

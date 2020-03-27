<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Tenancy\Identification\Concerns\AllowsTenantIdentification;
use Tenancy\Identification\Contracts\Tenant;
use Tenancy\Identification\Drivers\Http\Contracts\IdentifiesByHttp;
use Tenancy\Identification\Drivers\Console\Contracts\IdentifiesByConsole;
use Symfony\Component\Console\Input\InputInterface;

class Customer extends Model implements Tenant, IdentifiesByHttp, IdentifiesByConsole
{
    use AllowsTenantIdentification;

    protected $fillable = [
        'slug',
        'portal_hostname',
        'portal_path'
    ];
    /**
     * Specify whether the tenant model is matching the request.
     *
     * @param Request $request
     * @return Tenant
     */
    public function tenantIdentificationByHttp(Request $request): ?Tenant
    {
        return $this->query()
            ->where('portal_hostname', $request->getHttpHost())
            ->where('portal_path', $request->path())
            ->first();
    }

    public function tenantIdentificationByConsole(InputInterface $input): ?Tenant
    {
        dump(__CLASS__);
        if (app()->runningInConsole() && $input->hasParameterOption('--tenant')) {
            return $this->query()
                ->where('slug', $input->getParameterOption('--tenant'))
                ->first();
        }

        return null;
    }

    /**
     * The attribute of the Model to use for the key.
     *
     * @return string
     */
    public function getTenantKeyName(): string
    {
        return 'id';
    }

    /**
     * The actual value of the key for the tenant Model.
     *
     * @return string|int
     */
    public function getTenantKey()
    {
        return $this->id;
    }

    /**
     * A unique identifier, eg class or table to distinguish this tenant Model.
     *
     * @return string
     */
    public function getTenantIdentifier(): string
    {
        return get_class($this);
    }
}

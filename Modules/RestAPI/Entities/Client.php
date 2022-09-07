<?php

namespace Modules\RestAPI\Entities;

use App\Scopes\CompanyScope;

class Client extends User
{
    protected $table = 'users';
    protected $default = [
        'id',
        'name',
        'email',
        'status',
        'client_details'
    ];

    public function scopeVisibility($query)
    {

        if (api_user()) {
            // If employee or client show projects assigned
            $query->withoutGlobalScope(CompanyScope::class);
            $query->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->leftJoin('client_details', 'users.id', '=', 'client_details.user_id')
                ->where('client_details.company_id', '=', company()->id)
                ->select(
                    'users.id',
                    'users.name as name',
                    'users.email',
                    'users.created_at',
                    'client_details.company_name',
                    'users.image'
                )
                ->where('roles.name', 'client');

            return $query;
        }
        return $query;
    }
}

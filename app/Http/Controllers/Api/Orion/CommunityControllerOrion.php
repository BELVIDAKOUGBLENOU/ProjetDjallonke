<?php

namespace App\Http\Controllers\Api\Orion;

use App\Models\Community;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Orion\Concerns\DisableAuthorization;
use Orion\Http\Controllers\Controller;

class CommunityControllerOrion extends Controller
{
    use DisableAuthorization;

    protected $request = \App\Http\Requests\CommunityRequest::class;

    protected $model = Community::class;

    /**
     * Retrieve the user that is performing the action.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function resolveUser()
    {
        return request()->user();
    }

    /**
     * The attributes that are used for searching.
     */
    public function searchableBy(): array
    {
        return ['name', 'country.name', 'creation_date'];
    }

    /**
     * The attributes that are used for sorting.
     */
    public function sortableBy(): array
    {
        return ['name', 'creation_date', 'created_at', 'id'];
    }

    /**
     * The relations that are allowed to be included in the response.
     */
    public function includes(): array
    {
        return ['creator', 'country', 'users'];
    }

    /**
     * The relations that are always included in the response.
     */
    public function alwaysIncludes(): array
    {
        return ['country', 'creator'];
    }

    /**
     * Scope the query for the index action to filter by user's communities.
     */
    protected function buildIndexFetchQuery(Request $request, array $requestedRelations): Builder
    {
        // Mimic the logic: setPermissionsTeamId(null);
        // We do this to ensure we are looking at communities from a global context


        if (!self::canGlobal('list communities')) {
            abort(403, 'Unauthorized action');

        }

        $query = parent::buildIndexFetchQuery($request, $requestedRelations);
        return $query;
    }

    protected function performStore(Request $request, Model $community, array $attributes): void
    {
        if (!self::canGlobal('create communities')) {
            abort(403, 'Unauthorized action');
        }
        // Force updated attributes
        // Original controller: $data['creation_date'] = now();
        $attributes['created_by'] = $request->user()->id;
        if (!isset($attributes['creation_date'])) {
            $attributes['creation_date'] = now();
        }

        $community->fill($attributes);
        $community->save();
    }

    protected function performUpdate(Request $request, Model $community, array $attributes): void
    {
        if (!self::canGlobal('update communities')) {
            abort(403, 'Unauthorized update');
        }
        $community->fill($attributes);
        $community->save();
    }

    protected function performDestroy(Model $community): void
    {
        if (!self::canGlobal('delete communities')) {
            abort(403, 'Unauthorized deletion');
        }
        $community->delete();
    }

    protected function buildShowFetchQuery(Request $request, array $requestedRelations): Builder
    {
        if (!self::canGlobal('view communities')) {
            abort(403, 'Unauthorized view');
        }
        return parent::buildShowFetchQuery($request, $requestedRelations);
    }

    static function canGlobal(string $permission)
    {
        // Log::info("Checking global permission: {$permission}");
        $registrar = app(\Spatie\Permission\PermissionRegistrar::class);
        $originalTeamId = $registrar->getPermissionsTeamId();

        $registrar->setPermissionsTeamId(0);

        $user = request()->user();

        if (!$user) {
            // Log::warning("No user found in canGlobal");
            $registrar->setPermissionsTeamId($originalTeamId);
            return false;
        }

        // Log::info("User found: {$user->id}", ['roles' => $user->getRoleNames()->toArray()]);
        // // The user added a log here previously, I'll keep it but formatted
        // Log::info("All permissions", ['all_perms' => $user->getAllPermissions()->pluck('name')->toArray()]);

        try {
            $resp = $user->hasPermissionTo($permission);
        } catch (\Throwable $e) {
            Log::error("Error checking permission: " . $e->getMessage());
            $resp = false;
        }

        $registrar->setPermissionsTeamId($originalTeamId);
        // Log::info("Permission {$permission} result: " . ($resp ? 'true' : 'false'));
        return $resp;
    }
}

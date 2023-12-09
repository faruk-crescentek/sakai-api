<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $this->authorize('permissions-list');

        $permissions = Permission::all();

        return PermissionResource::collection($permissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePermissionRequest $request
     * @return PermissionResource
     * @throws AuthorizationException
     */
    public function store(StorePermissionRequest $request)
    {
        $this->authorize('add-permission');

        $permission = new Permission();
        $permission->name = $request->name;
        $permission->guard_name = 'web';

        if ($permission->save()) {
            return new PermissionResource($permission);
        }

        return response()->json(['status' => 405, 'success' => false]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Permission $permission
     * @param StorePermissionRequest $request
     * @return JsonResponse|PermissionResource
     * @throws AuthorizationException
     */
    public function update(Permission $permission, StorePermissionRequest $request)
    {
        $this->authorize('edit-permission');

        $permission->name = $request->name;

        if ($permission->save()) {
            return new PermissionResource($permission);
        }

        return response()->json(['status' => 405, 'success' => false]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $this->authorize('delete-permission');
        $permission->delete();

        return response()->noContent();
    }

    public function getRolePermissions($id)
    {
        $this->authorize('edit-permission');
        $permissions = Role::findById($id, 'web')->permissions;
        return PermissionResource::collection($permissions);
    }

    public function updateRolePermissions(Request $request)
    {
        $this->authorize('edit-permission');

        $permissions = json_decode($request->permissions, true);
        $permissions_where = Permission::whereIn('id', $permissions)->get();
        $role = Role::findById($request->role_id, 'web');
        $role->syncPermissions($permissions_where);
        return PermissionResource::collection($permissions_where);
    }
}

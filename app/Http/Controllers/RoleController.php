<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $this->authorize('role-list');

        $roles = Role::all();
        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RoleResource
     */
    public function store(StoreRoleRequest $request)
    {
        $this->authorize('add-role');

        $role = new Role();
        $role->name = $request->name;
        $role->guard_name = 'web';

        if ($role->save()) {
            return new RoleResource($role);
        }

        return response()->json(['status' => 405, 'success' => false]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Role $role
     * @param StoreRoleRequest $request
     * @return RoleResource
     * @throws AuthorizationException
     */
    public function update(Role $role, StoreRoleRequest $request)
    {
        $this->authorize('edit-role');

        $role->name = $request->name;

        if ($role->save()) {
            return new RoleResource($role);
        }

        return response()->json(['status' => 405, 'success' => false]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
       $this->authorize('delete-role');
        $role->delete();

        return response()->noContent();
    }

    public function getList()
    {
        return RoleResource::collection(Role::all());
    }
}

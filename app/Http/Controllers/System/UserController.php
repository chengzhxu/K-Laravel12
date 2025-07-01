<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\UserRequest;
use App\Models\User;
use App\Services\System\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * 用户列表
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 15), 50);
        $page = $request->input('page', 1);
        $users = User::with('roles');
        $users = UserService::getInstance()->handleSearch($users, $request);
        $users = $users->paginate($limit, ['*'], 'page', $page);
        return $this->successPaginated($users);
    }

    /**
     * 查看用户信息
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = User::with('roles')->findOrFail($id);
        return $this->success($user);
    }

    /**
     * 更新用户
     * @param UserRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(UserRequest $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $validated = $request->validated();

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        // 如果请求中包含角色，则同步角色
        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles'));
        }

        return $this->success($user, '用户更新成功');
    }

    /**
     * 创建用户
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        // 如果请求中包含角色，则分配角色
        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles'));
        }
        return $this->success($user, '用户创建成功');
    }

    /**
     * 重置密码
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function resetPassword(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $password = $request->input('password');
        $confirm_password = $request->input('confirm_password');
        if (empty($password) || empty($confirm_password)) {
            return $this->fail(422, '密码更新失败');
        }
        if ($password !== $confirm_password) {
            return $this->fail(422, '两次输入的密码不一致');
        }
        $user->password = Hash::make($password);
        $user->save();
        return $this->success($user, '密码更新成功');
    }

    /**
     * 删除用户
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);
        // 不允许删除自己
        if ((string)auth()->id() === (string)$id) {
            return $this->fail(422, '不能删除当前登录用户');
        }
        $user->delete();
        return $this->success($user, '用户删除成功');
    }

    /**
     * 批量删除
     * @param Request $request
     * @return JsonResponse
     */
    public function batchDestroy(Request $request): JsonResponse
    {
        // 验证请求数据
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->fail(422, $validator->errors()->first());
        }
        $ids = $request->input('ids');
        // 批量软删除
        User::whereIn('id', $ids)->delete();
        return $this->success($ids, '删除成功');
    }

    /**
     * 分配角色给用户
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function assignRoles(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);
        $user->syncRoles($validated['roles']);
        return $this->success($user, '角色分配成功');
    }
}

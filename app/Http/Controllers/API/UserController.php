<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\traits\ApiResponse;

class UserController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $users = User::all();
            return $this->sendResponse($users, 'Daftar User Berhasil Diambil');
        } catch (\Exception $e) {
            return $this->sendError('Gagal mengambil daftar user', 500, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'username' => 'required|string|max:50|unique:users,username',
                'name' => 'required|string|max:100',
                'password' => 'required|string|min:8|max:60',
            ]);

            $validatedData['password'] = Hash::make($validatedData['password']);

            $user = User::create($validatedData);

            return $this->sendResponse($user, 'User Berhasil Dibuat', 201);
        } catch (\Exception $e) {
            return $this->sendError('Gagal membuat user', 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return $this->sendResponse($user, 'User Berhasil Diambil');
        } catch (\Exception $e) {
            return $this->sendError('User tidak ditemukan', 404, $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validatedData = $request->validate([
                'username' => 'required|string|max:50|unique:users,username,' . $id,
                'name' => 'required|string|max:100',
                'password' => 'nullable|string|min:8|max:60',
                'role' => 'nullable|string|in:admin,user',
            ]);

            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            $user->update($validatedData);

            return $this->sendResponse($user, 'User berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->sendError('Gagal memperbarui user', 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return $this->sendResponse(null, 'User Berhasil Dihapus');
        } catch (\Exception $e) {
            return $this->sendError('Gagal menghapus user', 500, $e->getMessage());
        }
    }
}
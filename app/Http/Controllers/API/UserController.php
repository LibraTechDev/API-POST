<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    private function respondWithJson($code, $message, $data = null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function index()
    {
        return $this->respondWithJson(200, 'Daftar User Berhasil Diambil', User::all());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'name' => 'required|string|max:100',
            'password' => 'required|string|min:8|max:60',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return $this->respondWithJson(201, 'User Berhasil Dibuat', $user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return $this->respondWithJson(200, 'User Berhasil Diambil', $user);
    }

    public function update(Request $request, $id)
    {
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

        return $this->respondWithJson(200, 'User updated successfully', $user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return $this->respondWithJson(200, 'User Berhasil Dihapus');
    }
}
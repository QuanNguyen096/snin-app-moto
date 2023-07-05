<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function getUserInfo(Request $request)
    {
        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (!auth()->check()) {
            return response()->json(['message' => 'Bạn chưa đăng nhập', 'status' => 401], 401);
        }

        // Lấy thông tin user
        $user = auth()->user();

        // Tải ảnh của user và trả về URL của ảnh
        $image_url = null;
        if ($user->image) {
            $image_url = Storage::url("user/{$user->image}");
        }

        // Trả về thông tin user và URL của ảnh
        return response()->json([
            'message' => 'Successful',
            'status' => 200,
            'data' => [
                'id' => $user->id,
                'address' => $user->address,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'image' => $image_url,
                'score' => $user->score,
                'gender' => $user->gender
            ]
        ]);
    }


    public function updateScore(Request $request)
    {
        $request->validate([
            'score' => 'required|integer',
        ]);

        $user = Auth::user();
        $user->score += $request->score;
        $user->save();

        return response()->json([
            'message' => 'Score updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'phone' => ['required', 'numeric', 'regex:/^(0[2-9]|(1[2-9]|3[2-9]|5[6|8|9]|7[0|6-9]|8[1-5]|9[0-9]))\d{7}$/'],
                'c_password' => 'required|same:password',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
            $path = public_path('storage/user');
            Storage::disk('public')->put("/user/{$imageName}", file_get_contents($request->image));

            User::create([
                'name' => $request->name,
                'image' => $imageName,
                'gender' => $request->input('gender', 1),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'password' => Hash::make($request->input('password')),
            ]);
            return response()->json([
                'message' => 'create successful'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went really wrong !'
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {
            $user = auth()->user();
            $user->name = $request->input('name', $user->name);
            // $user->email = $request->input('email', $user->email);
            $user->phone = $request->input('phone', $user->phone);
            $user->gender = $request->input('gender', $user->gender);
            $user->address = $request->input('address', $user->address);

            if ($request->hasFile('image')) {
                $storege = Storage::disk('public');

                if ($storege->exists($user->image)) {
                    $storege->delete($user->image);
                }
                $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
                $storege->put("/user/{$imageName}", file_get_contents($request->image));
                $user->image = $imageName;
            }
            $user->save();

            return response()->json([
                'message' => 'Update successful',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'image' => $user->image,
                    'gender' => $user->gender,
                    'address' => $user->address
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went really wrong !'
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}

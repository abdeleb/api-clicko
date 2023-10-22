<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserStoreRequest;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function users()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function topDomains()
    {
        $userEmails = User::pluck('email');

        // Extract domains from emails
        $domains = [];
        foreach ($userEmails as $email) {
            //Splits the email string into two parts based on the '@' character with explode function
            [$username, $domain] = explode('@', $email);
            $domains[] = $domain;
        }

        $domainCounts = array_count_values($domains);
        arsort($domainCounts); // Sort descending

        $topDomains = array_slice($domainCounts, 0, 3);

        return response()->json($topDomains);
    }

    public function createUser(UserStoreRequest $request)
    {
        $data = $request->getContent();
        $userData = json_decode($data, true);

        // Using UserStoreRequest to validate data
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
        ]);

        $response = [
            'status' => 1,
            'msg' => 'User created successfully',
            'user' => $user,
        ];

        return response()->json($response, 201); // 201 = "Created"


    }

    public function showUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Ups! User not found anywhere :('], 404);
        }

        return response()->json(['user' => $user], 200);
    }

    public function updateUser(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Ups! User not found',
            ], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => 1,
            'message' => 'User successfully updated',
            'user' => $user,
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Ups! User not found',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 1,
            'message' => 'User successfully deleted',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

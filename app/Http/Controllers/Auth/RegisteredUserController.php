<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        Auth::login($user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]));
        
        auth()->user()->wallet()->create(['amount' => 0]);

        $this->promote();

        event(new Registered($user));

        return redirect(RouteServiceProvider::HOME);
    }

    public function promote()
    {
        $first_user = User::first();
        
        if (! empty($first_user))
        {
            $first_user_pool = $first_user->pool;

            if ($first_user_pool == 1)
            {
                $pool1_users = User::where('created_at', '>', $first_user->created_at)
                                    ->where('pool', 1)
                                    ->count();
        
                if ($pool1_users >= 14)
                {
                    User::where('id', $first_user->id)->update(['pool' => 2]);
                    Wallet::where('user_id', $first_user->id)->update(['amount' => 14]);
                }
            }
            elseif ($first_user_pool == 2)
            {
                $pool2_users = User::where('created_at', '>', $first_user->created_at)
                                    ->where('pool', 2)
                                    ->count();
        
                if ($pool2_users >= 14)
                {
                    User::where('id', $first_user->id)->update(['pool' => 3]);
                    Wallet::where('user_id', $first_user->id)->update(['amount' => 28]);
                }
            }
        }


        // Other users except first user for pool one
        $pool1_users = User::where('id', '!=', $first_user->id)->get();
        $skip = 0;
        $pool = 2;
        $amount = 8;
        
        foreach ($pool1_users as $key => $pool1_user)
        {
            if ($key == 0) { $skip = $skip + 14; }
            else { $skip = $skip + 8; }

            $user_count = User::where('id', '!=', $first_user->id)->skip($skip)->take(20)->get();

            if ($first_user->id != $pool1_user->id && count($user_count) >= 8)
            {
                User::where('id', $pool1_user->id)->update(['pool' => $pool]);
                Wallet::where('user_id', $pool1_user->id)->update(['amount' => $amount]);
            }
        }

        // Other users except first user for pool two
        $pool2_users = User::where('id', '!=', $first_user->id)
                            ->where('pool', 2)
                            ->get();
        $skip = 0;
        $pool = 3;
        $amount = 16;
        
        foreach ($pool2_users as $key => $pool2_user)
        {
            if ($key == 0) { $skip = $skip + 14; }
            else { $skip = $skip + 8; }

            $user_count = User::where('id', '!=', $first_user->id)
                                ->where('pool', 2)
                                ->skip($skip)
                                ->take(20)
                                ->get();

            if ($first_user->id != $pool2_user->id && count($user_count) >= 8)
            {
                User::where('id', $pool2_user->id)->update(['pool' => $pool]);
                Wallet::where('user_id', $pool2_user->id)->update(['amount' => $amount]);
            }
        }
    }
}

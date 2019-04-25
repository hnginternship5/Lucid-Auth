<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Auth;
use App\User;

class SocialController extends Controller
{
    public function redirect($provider)
    {
    	return Socialite::driver($provider)->redirect();
    }

    public function Callback($provider)
    {
        $userSocial 	=   Socialite::driver($provider)->stateless()->user();
        $users       	=   User::where(['email' => $userSocial->getEmail()])->first();

        if($users){
            Auth::login($users);
            $login_user = array("name" => Auth::user()->name, "email" => Auth::user()->email, "pic" => Auth::user()->image);
             json_encode($login_user);
            return redirect()->to("http://localhost:3000/auth/".Auth::user()->provider."/".Auth::user()->provider_id)->send();
        }else{

            $user = User::create([
                'name'          => $userSocial->getName(),
                'email'         => $userSocial->getEmail(),
                'image'         => $userSocial->getAvatar(),
                'provider_id'   => $userSocial->getId(),
                'provider'      => $provider,
            ]);
            $login_user = array("name" => $user->name, "email" => $user->email, "pic" => $user->image);
          //  return redirect()->to('/auth?s=done')->send();
          return redirect()->to("http://localhost:3000/auth/".$user->provider."/".$user->provider_id)->send();
            //Redirect::to('/auth?s=done&n='.$user->name.'&e='.$user->email.'&p='.Auth::user()->image.'');
        }
    }


}

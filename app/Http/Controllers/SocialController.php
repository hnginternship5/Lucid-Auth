<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Socialite;
use Auth;
use App\User;
use App\Mail\ZikiMail;

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
            return redirect()->to("https://ziki.techteel.com/auth/".Auth::user()->provider."/".Auth::user()->provider_id)->send();
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
          return redirect()->to("https://ziki.techteel.com/auth/".$user->provider."/".$user->provider_id)->send();
            //Redirect::to('/auth?s=done&n='.$user->name.'&e='.$user->email.'&p='.Auth::user()->image.'');
        }
    }

    public function validateCheck($provider, $token)
    {
        //die($token);
        $user = User::where(['provider' => $provider, 'provider_id' => $token])->first();
        if($user){
            return response()->json($user);
        }
        else{
            return array("error" => true, "message" => "you are not authorize to be here.");
        }
    }

    public function email($provider, $address){
        $user = User::where(['email' => $address])->first();
        if ($user) {
            $secret = md5($user->provider_id);
            Mail::to($user->email)->send(new ZikiMail($user, $secret));
            User::where('email', $address)
                ->where('password', NULL)
                ->update(['password' => $secret]);
            $data = array("error" => false, "message" => "Magic link sent successfully, check your email.");
        }
        else{
            $data = array("error" => true, "message" => "Invalid email account, signup with a social account.");
        }
        return $data;
    }

}

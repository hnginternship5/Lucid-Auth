<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Socialite;
use Auth;
use App\User;
use App\Mail\ZikiMail;

class SocialController extends Controller
{
    public function __construct()
    {
        session_start();
        $this->type = Input::get('type');
    }
    public function redirect($provider)
    {
        $_SESSION['action_type'] = Input::get('type');
        $_SESSION['domain'] = Input::get('url');
    	return Socialite::driver($provider)->redirect();
    }

    public function Callback($provider)
    {
        $userSocial 	=   Socialite::driver($provider)->stateless()->user();
        $users       	=   User::where(['email' => $userSocial->getEmail()])->first();
        try {
            $host = Crypt::decryptString($_SESSION['domain']);
        } catch (DecryptException $e) {
            dd($e);
        }
        if($users){
            Auth::login($users);
            $login_user = array("name" => Auth::user()->name, "email" => Auth::user()->email, "pic" => Auth::user()->image);
             json_encode($login_user);
             if ($_SESSION['action_type'] == 'login') {
                return redirect()->to("{$host}/auth/".Auth::user()->provider."/".Auth::user()->provider_id)->send();
             }
             else{
                return redirect()->to("{$host}/setup/".Auth::user()->provider."/".Auth::user()->provider_id)->send();
             }
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
          return redirect()->to("{$host}/auth/".$user->provider."/".$user->provider_id)->send();
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

    public function email($provider){
        $address = Input::get('address');
        $sourcekey = Input::get('domain');
        $user = User::where(['email' => $address])->first();
        if (is_null($user)) {
            $data = array("error" => true, "message" => "Invalid email account, signup with a social account.");
        }
        else{
            $secret = $user->provider_id;
            Mail::to($user->email)->send(new ZikiMail($user, $secret, $sourcekey));
            User::where('email', $address)
                ->update(['password' => $user->provider_id]);
            $data = array("error" => false, "message" => "Magic link sent successfully, check your email.");
        }
        return $data;
    }

    public function updateEmail() {
        $address = Input::get('old_email');
        $new_email = Input::get('new_email');
        $update = User::where('email', $address)
                    ->update(['email' => $new_email]);
        if($update){
            $user = User::where(['email' => $new_email])->first();
            if($user){
                return response()->json($user);
            }
            else{
                return array("error" => true, "message" => "you are not authorize to be here.");
            }
        }
        else{
            $data = array("error" => true, "message" => "New Email Update Failed");
            return $data;
        }
    }

    public function magicLink (){
        $token = Input::get('token');
        $key = Input::get('sha');
        $user = User::where(['password' => $token])->first();
        try {
            $host = Crypt::decrypt($key);
        } catch (DecryptException $e) {
            dd($e);
        }
        if ($user) {
            User::where('email', $user->email)
                ->update(['provider' => "email"]);
            //  return redirect()->to('/auth?s=done')->send();
            return redirect()->to("{$host}/auth/{$user->provider}/{$user->provider_id}")->send();
        }
        else{
            return redirect()->to("{$host}");
        }
    }

    public function encrypter() {
        $host = Input::get('host');
        $encrypted = Crypt::encryptString($host);
        return $encrypted;
    }

}

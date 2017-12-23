<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Deep\LoginController;
use App\sessions;
use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class isAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->cookies->has('phpsesid'))
        {
            $sesid = decrypt(decrypt($request->cookie('phpsesid')));

            $checker = sessions::where('sescode','=',$sesid)->where('user_agent','=',$request->userAgent())->first();
            if($checker)
            {
                $user = User::find($checker->user_id);
                if($user)
                {
                    Auth::login($user,true);
                }
                else
                {
                    $t = new LoginController();
                    $t->logout($request);
                }
            }
            else
            {
                $t = new LoginController();
                $t->logout($request);
            }
        }
        else
        {
            $t = new LoginController();
            $t->logout($request);
        }

        return $next($request);
    }
}

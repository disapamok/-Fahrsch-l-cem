<?php
namespace Simcify\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Simcify\Middleware\School;
use Pecee\Http\Request;
use Simcify\Auth;

class Authenticate implements IMiddleware {

    /**
     * Redirect the user if they are unautenticated
     * 
     * @param   \Pecee\Http\Request $request
     * @return  \Pecee\Http]Request
     */
    public function handle(Request $request) {

        Auth::remember();
        
        if (Auth::check()) {
            $request->user = Auth::user();
            $school = School::setup();
            date_default_timezone_set($school->timezone);
            // Set the locale to the user's preference
            config('app.locale.default', $request->user->{config('auth.locale')});
        } else {
            $request->setRewriteUrl(url('Auth@get'));
        }
        return $request;

    }
}

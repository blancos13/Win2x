<?php namespace App\Http\Middleware;

use App\User;
use Illuminate\Http\Response;
use Closure;

class CheckReferral {
	
	public function handle($request, Closure $next) {
		if(!$request->hasCookie('ref') && $request->query('ref') ) {
			$user = User::where('unique_id', $request->query('ref'))->first();
			if(!is_null($user)) {
				$user->link_trans += 1;
				$user->save();
			}
			return redirect($request->url())->withCookie(cookie()->forever('ref', $request->query('ref')));
		}

		return $next($request);
	}
}
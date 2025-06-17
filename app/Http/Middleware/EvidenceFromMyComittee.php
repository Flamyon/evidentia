<?php

namespace App\Http\Middleware;

use App\Models\Evidence;
use Closure;
use Illuminate\Support\Facades\Auth;

class EvidenceFromMyCommittee
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
        

        $id = $request->route('id');
        if($id == null) // si se recibe por POST
        {
            $id = $request->_id;
        }
        $evidence = Evidence::find($id);

        if($evidence->comittee->id != Auth::user()->coordinator->comittee->id)
        {
            return redirect()->route('home',$instance);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware {

    public function handle($request, Closure $next)
    {
//        $response = $next($request);
//        $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS');
//        $response->header('Access-Control-Allow-Headers', "Origin, X-Requested-With, Content-Type, Access-Control-Allow-Origin"/*$request->header('Access-Control-Request-Headers')*/);
//        $response->header('Access-Control-Allow-Origin', '*');
//        return $response;
        header("Access-Control-Allow-Origin: http://picturesofjoy:3000");
        // ALLOW OPTIONS METHOD
        $headers = [
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token,  x-xsrf-token, Origin, Authorization'
        ];
        if ($request->getMethod() == "OPTIONS") {
            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
            return response('OK', 200, $headers);
        }
        $response = $next($request);
        foreach ($headers as $key => $value)
            $response->header($key, $value);
        return $response;
    }
}
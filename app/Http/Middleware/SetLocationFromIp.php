<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class SetLocationFromIp
{
    public function handle(Request $request, Closure $next)
    {
      	
        // Only run if a location is NOT already in the session
        if (!Session::has('GetCountry')) {
            
            // Thanks to the TrustProxies fix, this will now get the REAL user IP
            $userIp = $request->ip();

            // Use a sample IP for local testing, as 127.0.0.1 won't work
            if ($userIp == '127.0.0.1' || $userIp == '::1') {
                $userIp = '202.166.168.131'; // Example: A public IP in Pakistan for testing
            }

            try {
                // Call the free and simple IP geolocation API
                $response = Http::get("http://ip-api.com/json/{$userIp}");
                
                if ($response->successful() && $response->json()['status'] == 'success') {
                    $locationData = $response->json();
                    
                    Session::put([
                        'Getlat'     => $locationData['lat'],
                        'Getlng'     => $locationData['lon'],
                        'GetCity'    => $locationData['city'],
                        'GetState'   => $locationData['regionName'],
                        'GetCountry' => $locationData['country'],
                        'GetAddress' => $locationData['city'] . ', ' . $locationData['country'],
                    ]);
                }
            } catch (\Exception $e) {
                // If the API fails, do nothing.
            }
        }

        return $next($request);
    }
}
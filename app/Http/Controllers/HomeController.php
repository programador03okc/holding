<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $renovacion = 0;
        if (Auth::user()->fecha_renovacion == date('Y-m-d')) {
            $renovacion = 1;
        }
        return view('mgcp.home', get_defined_vars());
    }

    public function apiCRM()
    {
        $url = curl_init("okcomputer-417086857152612391.myfreshworks.com/crm/sales/api/cpq/products");
        $authorization = "Authorization: Token token=hq_8NTJMWQY8WktD89B1Rg";

        curl_setopt($url, CURLOPT_HTTPHEADER, array("Content-Type: application/json" , $authorization));
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($url);
        curl_close($url);
        $resultado = json_decode($result);
        return response()->json($resultado, 200);
    }
}

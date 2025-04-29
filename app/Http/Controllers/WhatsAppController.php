<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function send(Request $request)
    {
        $response = Http::post('http://103.185.44.204:3000/send-message', [
            'number' => $request->number, // format: 628xxxxxxx
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => $response->successful(),
            'response' => $response->json(),
        ]);
    }
}

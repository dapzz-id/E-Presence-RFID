<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceConnection;
use App\Models\Settings;

class ConnectionController extends Controller
{
    public function index()
    {
        $userConnections = Settings::where('user_id', Auth::id())->get();
        
        // Format connections for the view
        $connections = [];
        foreach ($userConnections as $connection) {
            $connections[$connection->service] = true;
        }
        
        return view('connections.index', compact('connections'));
    }
    
    public function connect(Request $request)
    {
        $request->validate([
            'service' => 'required|string',
            'apiKey' => 'required|string',
        ]);
        
        try {            
            // Check if connection already exists
            $existingConnection = Settings::where('user_id', Auth::id())
                ->where('service', $request->service)
                ->first();
                
            if ($existingConnection) {
                $existingConnection->api_key = encrypt($request->apiKey);
                $existingConnection->save();
            } else {
                Settings::create([
                    'user_id' => Auth::id(),
                    'service' => $request->service,
                    'api_key' => encrypt($request->apiKey),
                    'connected_at' => now(),
                ]);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect: ' . $e->getMessage()
            ]);
        }
    }
    
    public function disconnect(Request $request)
    {
        $request->validate([
            'service' => 'required|string',
        ]);
        
        try {
            // Delete the connection
            Settings::where('user_id', Auth::id())
                ->where('service', $request->service)
                ->delete();
                
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to disconnect: ' . $e->getMessage()
            ]);
        }
    }
}
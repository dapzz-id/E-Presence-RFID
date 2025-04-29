<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        $photos = Storage::disk('s3')->files('profile');
        
        $photos = array_map(function($path) {
            return basename($path);
        }, $photos);
        
        return view('Main.Components.foto-siswa', compact('photos'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'photo_files.*' => 'required|image|max:5120|mimes:jpeg,png,jpg',
        ]);
        
        $uploadedFiles = [];
        $errors = [];
        
        if ($request->hasFile('photo_files')) {
            foreach ($request->file('photo_files') as $file) {
                $filename = $file->getClientOriginalName();
                $filePath = 'profile/' . $filename;
                
                try {
                    // This will automatically replace the file if it already exists
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                    
                    $fileExists = Storage::disk('s3')->exists($filePath);
                    $uploadedFiles[] = [
                        'filename' => $filename,
                        'replaced' => $fileExists
                    ];
                } catch (\Exception $e) {
                    $errors[] = "Failed to upload {$filename}: {$e->getMessage()}";
                }
            }
        }
        
        if ($request->ajax()) {
            if (count($errors) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => implode(', ', $errors),
                    'errors' => $errors
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully.',
                'files' => $uploadedFiles
            ]);
        }
        
        if (count($errors) > 0) {
            return redirect()->back()->with('error', implode('<br>', $errors));
        }
        
        return redirect()->route('photos.index')->with('success', count($uploadedFiles) . ' file(s) uploaded successfully.');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
        ]);
        
        $filename = $request->input('filename');
        $path = 'profile/' . $filename;
        
        if (Storage::disk('s3')->exists($path)) {
            Storage::disk('s3')->delete($path);
            return response()->json(['success' => true]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'File not found'
        ]);
    }
}

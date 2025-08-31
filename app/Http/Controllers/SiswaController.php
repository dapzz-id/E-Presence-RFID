<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use App\Exports\SiswaTemplateExport;
use App\Models\WargaTels;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SiswaController extends Controller
{    
    /**
     * Download Excel template for student import
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        return Excel::download(new SiswaTemplateExport, 'template-siswa.xlsx');
    }

    /**
     * Import students from Excel file
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
            'photo_zip' => 'nullable|file|mimes:zip',
        ]);

        try {
            DB::beginTransaction();
            
            $photoMap = [];
            if ($request->hasFile('photo_zip')) {
                $zip = new \ZipArchive;
                $zipFile = $request->file('photo_zip');
                $zipPath = $zipFile->getRealPath();
                
                if ($zip->open($zipPath) === TRUE) {
                    $tempDir = storage_path('app/temp_photos_' . time());
                    if (!file_exists($tempDir)) {
                        mkdir($tempDir, 0755, true);
                    }
                    
                    $zip->extractTo($tempDir);
                    $zip->close();
                    
                    $files = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($tempDir),
                        \RecursiveIteratorIterator::LEAVES_ONLY
                    );
                    
                    foreach ($files as $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $fileName = $file->getFilename();
                            
                            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $newPath = 'profile/' . $fileName;
                                Storage::disk('s3')->put($newPath, file_get_contents($filePath));
                                
                                $photoMap[$fileName] = $fileName;
                            }
                        }
                    }
                    
                    $this->deleteDirectory($tempDir);
                }
            }
            
            $import = new SiswaImport($photoMap);
            Excel::import($import, $request->file('excel_file'));
            
            DB::commit();
            
            $message = "Import data siswa berhasil dilakukan.";
            
            return redirect()->route('siswa')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Excel import error: ' . $e->getMessage());
            return redirect()->route('siswa')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Helper function to recursively delete a directory
     */
    private function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    public function index(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $user->update(['last_seen' => Carbon::now()]);
        }

        $search = $request->query('search');
        $kelas = $request->query('kelas');
    
        $query = WargaTels::query();
    
        // Filter by search query
        if (!empty($search)) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('alamat', 'like', "%$search%");
        }
    
        // Filter by kelas
        if (!empty($kelas) && $kelas !== 'all') {
            $query->where('kelas', $kelas);
        }

        $wargaTels = $query->paginate(10, ['*'], 'data-siswa');
        return view('Main.Components.data-siswa', compact('wargaTels'));
    }
    
    public function create()
    {
        return view('Main.add-siswa');
    }
    
    public function store(Request $request)
    {
        $validationRules = [
            'nis' => 'required|unique:warga_tels,nis',
            'name' => 'required',
            'class' => 'required',
            'address' => 'required',
        ];
        
        if (!$request->existing_photo) {
            $validationRules['photo'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }
        
        $request->validate($validationRules, [
            'nis.required' => 'Kolom NIS tidak boleh kosong',
            'nis.unique' => 'NIS sudah terdaftar',
            'name.required' => 'Kolom Nama tidak boleh kosong',
            'class.required' => 'Kolom Kelas tidak boleh kosong',
            'address.required' => 'Kolom Alamat tidak boleh kosong',
            'photo.required' => 'Kolom Foto tidak boleh kosong',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'File harus berupa jpeg, png, jpg',
            'photo.max' => 'Ukuran file maksimal 2MiB',
        ]);

        $filename = null;
        
        if($request->file('photo')){
            $file = $request->file('photo');
            $filename = time() . '-' . $file->getClientOriginalName();
            $image = Image::make($file)
                ->fit(255, 340, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode(null, 100);

            Storage::disk('s3')->put("profile/{$filename}", (string) $image);
        }
        elseif($request->existing_photo) {
            $filename = $request->existing_photo;
        }
        else {
            return redirect()->back()->withErrors(['photo' => 'Foto harus dipilih atau diunggah'])->withInput();
        }

        WargaTels::create([
            'nis' => $request->nis,
            'name' => $request->name,
            'kelas' => $request->class,
            'alamat' => $request->address,
            'foto_profile' => $filename,
        ]);

        return redirect()->route('siswa')->with('success', 'Siswa berhasil ditambahkan');
    }
    
    public function edit($nis)
    {
        $siswa = WargaTels::where('nis', $nis)->first();
        
        if (!$siswa) {
            return redirect()->route('siswa')->with('error', 'Siswa tidak ditemukan');
        }
        
        return view('Main.edit-siswa', compact('siswa'));
    }
    
    public function update(Request $request)
    {
        $originalNis = $request->input('original_nis');
        $validationRules = [
            'nis' => 'required|unique:warga_tels,nis,' . $originalNis . ',nis',
            'name' => 'required',
            'class' => 'required',
            'address' => 'required',
        ];
        
        if (!$request->existing_photo && !$request->file('photo')) {
            $validationRules['photo'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        } elseif ($request->file('photo')) {
            $validationRules['photo'] = 'image|mimes:jpeg,png,jpg|max:2048';
        }
        
        $request->validate($validationRules, [
            'nis.required' => 'NIS tidak boleh kosong',
            'nis.unique' => 'NIS sudah terdaftar',
            'name.required' => 'Nama tidak boleh kosong',
            'class.required' => 'Kelas tidak boleh kosong',
            'address.required' => 'Alamat tidak boleh kosong',
            'photo.required' => 'Foto tidak boleh kosong',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'File harus berupa jpeg, png, jpg',
            'photo.max' => 'Ukuran file maksimal 2MiB',
        ]);
        
        $data = [
            'nis' => $request->input('nis'),
            'name' => $request->input('name'),
            'kelas' => $request->input('class'),
            'alamat' => $request->input('address'),
        ];
        
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '-' . $file->getClientOriginalName();
            
            $image = Image::make($file)
                ->fit(255, 340, function ($constraint) {
                    $constraint->upsize();
                })
                ->encode(null, 100);
                
            Storage::disk('s3')->put("profile/{$filename}", (string) $image);
            $data['foto_profile'] = $filename;
            
            $oldPhoto = WargaTels::where('nis', $originalNis)->value('foto_profile');
            if ($oldPhoto && $oldPhoto !== $filename && Storage::disk('s3')->exists("profile/{$oldPhoto}")) {
                Storage::disk('s3')->delete("profile/{$oldPhoto}");
            }
        } elseif ($request->input('existing_photo')) {
            $data['foto_profile'] = $request->input('existing_photo');
        }
        
        WargaTels::where('nis', $originalNis)->update($data);
        return redirect()->route('siswa')->with('success', 'Data siswa berhasil diperbarui');
    }
    
    public function destroy($nis)
    {
        $siswa = WargaTels::where('nis', $nis)->first();
        
        if (!$siswa) {
            return redirect()->route('siswa')->with('error', 'Siswa tidak ditemukan');
        }
        
        if ($siswa->foto_profile && Storage::disk('s3')->exists("profile/{$siswa->foto_profile}")) {
            Storage::disk('s3')->delete("profile/{$siswa->foto_profile}");
        }
        
        $siswa->delete();
        
        return redirect()->route('siswa')->with('success', 'Siswa berhasil dihapus');
    }
    
    public function getProfilePhotos()
    {
        $photos = [];
        $files = Storage::disk('s3')->files('profile');

        foreach ($files as $file) {
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                $fileName = basename($file);
                $fileSize = Storage::disk('s3')->size($file);

                if ($fileSize < 1024) {
                    $formattedSize = $fileSize . ' B';
                } elseif ($fileSize < 1024 * 1024) {
                    $formattedSize = round($fileSize / 1024, 2) . ' KB';
                } else {
                    $formattedSize = round($fileSize / (1024 * 1024), 2) . ' MB';
                }

                $photos[] = [
                    'name' => $fileName,
                    'url' => Storage::disk('s3')->temporaryUrl(
                        'profile/' . $fileName,
                        now()->addMinutes(5)
                    ),
                    'size' => $formattedSize
                ];
            }
        }

        return response()->json($photos);
    }

    // public function edit($nis)
    // {
    //     $siswa = DB::table('warga_tels')->where('nis', $nis)->first();
        
    //     if (!$siswa) {
    //         return redirect()->route('siswa')->with('error', 'Siswa tidak ditemukan');
    //     }
        
    //     return view('Main.edit-siswa', compact('siswa'));
    // }

    // public function update(Request $request)
    // {
    //     $validationRules = [
    //         'nis' => 'required',
    //         'name' => 'required',
    //         'class' => 'required',
    //         'address' => 'required',
    //     ];
        
    //     if (!$request->existing_photo) {
    //         $validationRules['photo'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
    //     }
        
    //     $request->validate($validationRules, [
    //         'nis.required' => 'Kolom NIS tidak boleh kosong',
    //         'name.required' => 'Kolom Nama tidak boleh kosong',
    //         'class.required' => 'Kolom Kelas tidak boleh kosong',
    //         'address.required' => 'Kolom Alamat tidak boleh kosong',
    //         'photo.required' => 'Kolom Foto tidak boleh kosong',
    //         'photo.image' => 'File harus berupa gambar',
    //         'photo.mimes' => 'File harus berupa jpeg, png, jpg',
    //         'photo.max' => 'Ukuran file maksimal 2MiB',
    //     ]);

    //     $filename = null;
        
    //     if($request->file('photo')){
    //         $file = $request->file('photo');
    //         $filename = time() . '-' . $file->getClientOriginalName();
    //         $image = Image::make($file)
    //             ->fit(255, 340, function ($constraint) {
    //                 $constraint->upsize();
    //             })
    //             ->encode(null, 100);

    //         Storage::disk('public')->put("profile/{$filename}", (string) $image);
    //     }
    //     elseif($request->existing_photo) {
    //         $filename = $request->existing_photo;
    //     }
    //     else {
    //         return redirect()->back()->withErrors(['photo' => 'Foto harus dipilih atau diunggah'])->withInput();
    //     }




    //     $request->validate([
    //         'nis' => 'required|exists:warga_tels,nis',
    //         'name' => 'required',
    //         'kelas' => 'required',
    //         'alamat' => 'required',
    //         'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    //         'existing_photo' => 'required|string',
    //     ],[
    //         'nis.required' => 'NIS tidak boleh kosong',
    //         'name.required' => 'Nama tidak boleh kosong',
    //         'kelas.required' => 'Kelas tidak boleh kosong',
    //         'alamat.required' => 'Alamat tidak boleh kosong',
    //         'foto.image' => 'File harus berupa gambar',
    //         'foto.mimes' => 'File harus berupa jpeg, png, jpg',
    //         'foto.max' => 'Ukuran file maksimal 2MB',
    //         'foto.required' => 'Foto tidak boleh kosong',
    //         'existing_photo.string' => 'Existing photo must be a string',
    //     ]);
        
    //     $originalNis = $request->input('original_nis');
    //     $data = [
    //         'nis' => $request->input('nis'),
    //         'name' => $request->input('name'),
    //         'kelas' => $request->input('kelas'),
    //         'alamat' => $request->input('alamat'),
    //     ];
        
    //     if ($request->hasFile('foto')) {
    //         $foto = $request->file('foto');
    //         $fotoName = time() . '.' . $foto->getClientOriginalExtension();
    //         $foto->storeAs('public/profile', $fotoName);
    //         $data['foto_profile'] = $fotoName;
    //     } elseif ($request->input('existing_photo')) {
    //         $data['foto_profile'] = $request->input('existing_photo');
    //     }
        
    //     DB::table('warga_tels')
    //         ->where('nis', $originalNis)
    //         ->update($data);
        
    //     return redirect()->route('siswa')->with('success', 'Data siswa berhasil diperbarui');
    // }

    // public function getProfilePhotos()
    // {
    //     $photos = [];
    //     $files = Storage::disk('public')->files('profile');
        
    //     foreach ($files as $file) {
    //         if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
    //             $fileName = basename($file);
    //             $fileSize = Storage::disk('public')->size($file);
                
    //             if ($fileSize < 1024) {
    //                 $formattedSize = $fileSize . ' B';
    //             } elseif ($fileSize < 1024 * 1024) {
    //                 $formattedSize = round($fileSize / 1024, 2) . ' KB';
    //             } else {
    //                 $formattedSize = round($fileSize / (1024 * 1024), 2) . ' MB';
    //             }
                
    //             $photos[] = [
    //                 'name' => $fileName,
    //                 'url' => env('APP_URL') . 'storage/profile/' . $fileName,
    //                 'size' => $formattedSize
    //             ];
    //         }
    //     }
        
    //     return response()->json($photos);
    // }
}
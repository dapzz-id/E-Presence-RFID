<table>
    <thead>
        <!-- Baris 1: Logo dan Nama Perusahaan -->
        <tr>
            <th colspan="2"></th> <!-- Kolom untuk logo -->
            <th colspan="8" style="font-size: 16px; font-weight: bold;">RAADEVELOPERZ</th>
        </tr>
        
        <!-- Baris 2: Alamat -->
        <tr>
            <th colspan="2"></th>
            <th colspan="8">Kami selalu menyediakan dan melayani jasa pembuatan aplikasi web, android, maupun desktop dengan berkualitas, aman dan berintegritas tinggi.</th>
        </tr>
        
        <!-- Baris 3: Informasi Kontak -->
        <tr>
            <th colspan="2"></th>
            <th colspan="8">Email: raadeveloperz@gmail.com | Website: raadeveloperz.web.id | Instagram: @raadeveloperz</th>
        </tr>
        
        <!-- Baris 4: Judul Laporan -->
        <tr>
            <th colspan="10" style="font-size: 14px; font-weight: bold;">{{ $title }}</th>
        </tr>
        
        <!-- Baris 5: Spasi -->
        <tr>
            <th colspan="11"></th>
        </tr>
        
        <!-- Baris 6: Header Tabel -->
        <tr>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">No</th>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">NIS</th>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">Nama</th>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">Kelas</th>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">Waktu Masuk</th>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">Status Masuk</th>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">Waktu Keluar</th>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">Status Keluar</th>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">Alasan Masuk Telat</th>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">Alasan Pulang</th>
            <th style="background-color: #CCCCCC; font-weight: bold; text-align: center;">Keterangan Lainnya</th>
        </tr>
    </thead>
    <tbody>
        @foreach($presences as $index => $presence)
        <tr>
            <!-- Your table cells remain the same -->
            <td>{{ $index + 1 }}</td>
            <td>{{ $presence->nis }}</td>
            <td>{{ $presence->warga_tels->name ?? '-' }}</td>
            <td>{{ $presence->warga_tels->kelas ?? '-' }}</td>
            <td>{{ $presence->time_masuk ?? '-' }}</td>
            <td>{{ $presence->status ?? '-' }}</td>
            <td>{{ $presence->time_keluar ?? '-' }}</td>
            <td>{{ $presence->status_keluar ?? '-' }}</td>
            <td>{{ $presence->alasan_datang_telat ?? $presence->alasan_datang ?? '-' }}</td>
            <td>{{ $presence->alasan_pulang_telat ?? $presence->alasan_pulang_duluan ?? '-' }}</td>
            <td>{{ $presence->izin_reason ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
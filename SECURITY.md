# 🔐 E-Presence Application Security Policy
#### Developed by: raadeveloperz <br> Founder & Startup Leader: Kadavi Raditya Alvino
<br>

## 1. Introduction <br>
<ul>
  <li>EN: This document outlines the security policies applied in the development and deployment of the E-Presence system application student attendance application based on RFID card tapping.</li>
  <li>ID: Dokumen ini menjelaskan kebijakan keamanan yang diterapkan dalam pengembangan dan implementasi sistem aplikasi E-Presence absensi siswa berbasis tap kartu RFID.</li>
</ul>

## 2. Platform Overview <br>
<ul>
  <li>EN: <b>Web</b> (Admin Panel), <b>Windows</b> (Attendance Logger), <b>Android</b> (Parent App).</li>
  <li>ID: <b>Web</b> (Panel Admin), <b>Windows</b> (Pencatatan Absensi), <b>Android</b> (Aplikasi Orang Tua).</li>
</ul>

## 3. User Authentication and Authorization <br>
<ul>
  <li>EN: 
    <ul>
      <li>All platforms implement role-based access control (RBAC).</li>
      <li>Admins log in using secure credentials via HTTPS.</li>
      <li>Android and Windows clients authenticate with secure API tokens.</li>
      <li>Sessions will expire automatically after a specified time limit (Web)</li>
      <li>Session will expire automatically when account is used on another device (Android)</li>
    </ul>
  </li>
  <li>ID:
    <ul>
      <li>Semua platform menerapkan kontrol akses berbasis peran (RBAC).</li>
      <li>Admin login menggunakan kredensial aman melalui HTTPS.</li>
      <li>Klien Android mengautentikasi menggunakan token API yang aman.</li>
      <li>Sesi akan kadaluarsa secara otomatis setelah batas yang telah ditentukan (Web).</li>
      <li>Sesi akan kadaluarsa secara otomatis ketika akun digunakan di perangkat lain (Android)</li>
    </ul>
  </li>
</ul>

## 4. Updates and Patch Management
<ul>
  <li>EN: Security patches are released regularly for all platforms.</li>
  <li>ID: Patch keamanan dirilis secara berkala untuk semua platform.</li>
</ul>

## 5. Data Privacy
<ul>
  <li>EN: 
    <ul>
      <li>User data is never shared with third parties.</li>
      <li>Students can only access their own attendance history.</li>
      <li>Admins cannot modify attendance data without logging the change.</li>
    </ul>
  </li>
  <li>ID:
    <ul>
      <li>Data pengguna tidak pernah dibagikan ke pihak ketiga.</li>
      <li>Siswa hanya dapat mengakses riwayat kehadiran mereka sendiri.</li>
      <li>Admin tidak dapat mengubah data kehadiran tanpa mencatat perubahan tersebut.</li>
    </ul>
  </li>
</ul>

<br>

## Supported Versions

| Programming Language | Platform      |
| ------- | -------------------------------------- |
| PHP Language   | Website Application             |
| Javascript Language | Interaction on the website |
| C# Language   | Windows Application              |
| Java Language   | Android Application            |

| Version PHP | Supported      |
| ------- | ------------------ |
| < 8.2   | :x:                |
| 8.2.x   | :white_check_mark: |
| 8.3.x   | :white_check_mark: |
| 8.4.x   | :x:    |

| Version Java | Supported      |
| ------- | ------------------ |
| < 11   | :x:     |
| 11  | :white_check_mark:            |
| > 11  | :white_check_mark:     |

## Reporting a Vulnerability

If there is an error, please just contact my Instagram which is <span onclick="window.location.href = 'https://instagram.com/x.dapzz'">@x.dapzz</span>

package kadaviradityaa.id.e_presence;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.Dialog;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.graphics.Color;
import android.graphics.Typeface;
import android.graphics.drawable.ColorDrawable;
import android.net.Uri;
import android.nfc.NfcAdapter;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Environment;
import android.provider.Settings;
import android.util.Log;
import android.view.View;
import android.view.WindowInsetsController;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.core.content.res.ResourcesCompat;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.bumptech.glide.Glide;
import com.github.mikephil.charting.charts.BarChart;
import com.github.mikephil.charting.components.XAxis;
import com.github.mikephil.charting.components.YAxis;
import com.github.mikephil.charting.data.BarData;
import com.github.mikephil.charting.data.BarDataSet;
import com.github.mikephil.charting.data.BarEntry;
import com.github.mikephil.charting.formatter.ValueFormatter;
import com.github.mikephil.charting.utils.ColorTemplate;
import com.google.android.material.bottomnavigation.BottomNavigationView;
import com.google.android.material.snackbar.Snackbar;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Objects;

import kadaviradityaa.id.e_presence.Library.Module;

@RequiresApi(api = Build.VERSION_CODES.TIRAMISU)
public class DashboardActivity extends AppCompatActivity {
    private static final String TAG = "DashboardActivity";

    // UI Components
    private ImageView profileImageLarge, profileImage;
    private TextView txtNama, txtTotalHadir, txtIzinSakit, btnLinkCard, txtTotalAlpa, btnAllowAccess, txtTimeNow, btnBuatSurat, txtProfileName, txtClass, txtNIS;
    private ImageView statusAbsensiBadge, statusAccessBadge, statusLinkCardBadge;
    private LinearLayout layoutWarningNFC;
    private final Handler handler = new Handler();
    private boolean isAbsensi;
    private SharedPreferences sharedPreferences;
    private ConstraintLayout btnAbout;

    // Permission Handling
    private static final int PERMISSION_REQUEST_CODE = 1001;
    private static final int PERMISSION_REQUEST_CODE_GROUP_1 = 1002;
    private static final int PERMISSION_REQUEST_CODE_GROUP_2 = 1003;
    private final ActivityResultLauncher<Intent> manageAllFilesPermissionLauncher = registerForActivityResult(
            new ActivityResultContracts.StartActivityForResult(),
            result -> checkPermissionStatus()
    );

    // Permission lists
    private final List<String> basePermissions = Arrays.asList(
            Manifest.permission.CAMERA,
            Manifest.permission.INTERNET,
            Manifest.permission.ACCESS_FINE_LOCATION,
            Manifest.permission.ACCESS_COARSE_LOCATION,
            Manifest.permission.NFC
    );

    private final List<String> storagePermissionsPreQ = Arrays.asList(
            Manifest.permission.READ_EXTERNAL_STORAGE,
            Manifest.permission.WRITE_EXTERNAL_STORAGE
    );

    private final List<String> storagePermissionsPostQ = Arrays.asList(
            Manifest.permission.READ_EXTERNAL_STORAGE
    );

    private final List<String> storagePermissionsTiramisu = Arrays.asList(
            Manifest.permission.READ_MEDIA_IMAGES,
            Manifest.permission.READ_MEDIA_VIDEO
    );

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_dashboard);

        setupWindowConfig();
        initializeViews();
        initBottomNavigation();
        LogicApps();
        checkPermissionStatus();
    }

    private void setupWindowConfig() {
        getWindow().setNavigationBarColor(Color.TRANSPARENT);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            getWindow().setNavigationBarContrastEnforced(true);
            getWindow().setNavigationBarDividerColor(Color.BLACK);
        }

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, 0);
            return insets;
        });

        Objects.requireNonNull(getWindow().getInsetsController()).setSystemBarsAppearance(
                WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS,
                WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS
        );
    }

    private void initializeViews() {
        profileImage = findViewById(R.id.profileImage);
        txtNama = findViewById(R.id.tvName);
        txtTotalHadir = findViewById(R.id.tvAttendanceCount);
        txtIzinSakit = findViewById(R.id.tvLeaveCount);
        statusAbsensiBadge = findViewById(R.id.btnTodayAttendance);
        statusAccessBadge = findViewById(R.id.btnGiveAccess);
        statusLinkCardBadge = findViewById(R.id.btnConnectCard);
        layoutWarningNFC = findViewById(R.id.textWarningNFC);
        btnLinkCard = findViewById(R.id.tvConnectCardActions);
        btnAllowAccess = findViewById(R.id.tvAllowAccess);
        txtTimeNow = findViewById(R.id.tvCurrentTime);
        btnBuatSurat = findViewById(R.id.tvCreateLetter);
        btnAbout = findViewById(R.id.layoutAbout);
        profileImageLarge = findViewById(R.id.profileImageLarge);
        txtProfileName = findViewById(R.id.tvProfileName);
        txtClass = findViewById(R.id.tvClass);
        txtNIS = findViewById(R.id.tvStudentId);
        txtTotalAlpa = findViewById(R.id.tvAlpaCount);

        isAbsensi = false;
        sharedPreferences = getSharedPreferences("LoginPrefs", Context.MODE_PRIVATE);

        findViewById(R.id.viewDashboard).setVisibility(LinearLayout.VISIBLE);
        findViewById(R.id.viewNotification).setVisibility(LinearLayout.GONE);
        findViewById(R.id.viewProfile).setVisibility(LinearLayout.GONE);
    }

    private void initBottomNavigation() {
        BottomNavigationView bottomNavigationView = findViewById(R.id.bottomNavigation);
        bottomNavigationView.setOnItemSelectedListener(item -> {
            int id = item.getItemId();
            if (id == R.id.btnHomeNav) {
                findViewById(R.id.viewDashboard).setVisibility(LinearLayout.VISIBLE);
                findViewById(R.id.viewNotification).setVisibility(LinearLayout.GONE);
                findViewById(R.id.viewProfile).setVisibility(LinearLayout.GONE);
            }

            if (id == R.id.btnAnnouncement) {
                findViewById(R.id.viewDashboard).setVisibility(LinearLayout.GONE);
                findViewById(R.id.viewNotification).setVisibility(LinearLayout.VISIBLE);
                findViewById(R.id.viewProfile).setVisibility(LinearLayout.GONE);
            }

            if (id == R.id.btnProfile) {
                findViewById(R.id.viewDashboard).setVisibility(LinearLayout.GONE);
                findViewById(R.id.viewNotification).setVisibility(LinearLayout.GONE);
                findViewById(R.id.viewProfile).setVisibility(LinearLayout.VISIBLE);
            }

            return true;
        });
    }

    @Override
    protected void onStart() {
        super.onStart();
        checkPermissionStatus();
    }

    @Override
    protected void onResume() {
        super.onResume();
        loadUserData();
    }

    private void checkPermissionStatus() {
        if (areAllRequiredPermissionsGranted()) {
            runOnUiThread(() -> {
                btnAllowAccess.setEnabled(false);
                btnAllowAccess.setVisibility(View.GONE);
                Glide.with(this)
                        .load(R.drawable.ix_success_filled)
                        .placeholder(R.drawable.ic_sharp_do_disturb_on)
                        .error(R.drawable.ic_sharp_do_disturb_on)
                        .into(statusAccessBadge);
            });
        } else {
            runOnUiThread(() -> {
                btnAllowAccess.setEnabled(true);
                btnAllowAccess.setVisibility(View.VISIBLE);
                Glide.with(this)
                        .load(R.drawable.ic_sharp_do_disturb_on)
                        .into(statusAccessBadge);
            });
        }
    }

    private boolean areAllRequiredPermissionsGranted() {
        // Check base permissions
        for (String permission : basePermissions) {
            if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
                Log.d(TAG, "Permission not granted: " + permission);
                return false;
            }
        }

        // Check storage permissions based on Android version
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            for (String permission : storagePermissionsTiramisu) {
                if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
                    Log.d(TAG, "Permission not granted (Tiramisu+): " + permission);
                    return false;
                }
            }
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            for (String permission : storagePermissionsPostQ) {
                if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
                    Log.d(TAG, "Permission not granted (Q+): " + permission);
                    return false;
                }
            }
        } else {
            for (String permission : storagePermissionsPreQ) {
                if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
                    Log.d(TAG, "Permission not granted (Pre-Q): " + permission);
                    return false;
                }
            }
        }

        return true;
    }

    private void requestAllPermissions() {
        // Group 1: Camera and Location permissions
        List<String> group1Permissions = new ArrayList<>();
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED) {
            group1Permissions.add(Manifest.permission.CAMERA);
        }
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            group1Permissions.add(Manifest.permission.ACCESS_FINE_LOCATION);
        }
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            group1Permissions.add(Manifest.permission.ACCESS_COARSE_LOCATION);
        }

        // Group 2: NFC and Storage permissions
        List<String> group2Permissions = new ArrayList<>();
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.NFC) != PackageManager.PERMISSION_GRANTED) {
            group2Permissions.add(Manifest.permission.NFC);
        }

        // Add storage permissions based on Android version
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            for (String permission : storagePermissionsTiramisu) {
                if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
                    group2Permissions.add(permission);
                }
            }
        } else if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            for (String permission : storagePermissionsPostQ) {
                if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
                    group2Permissions.add(permission);
                }
            }
        } else {
            for (String permission : storagePermissionsPreQ) {
                if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
                    group2Permissions.add(permission);
                }
            }
        }

        // Request permissions in logical groups
        if (!group1Permissions.isEmpty()) {
            ActivityCompat.requestPermissions(
                    this,
                    group1Permissions.toArray(new String[0]),
                    PERMISSION_REQUEST_CODE_GROUP_1
            );
        } else if (!group2Permissions.isEmpty()) {
            ActivityCompat.requestPermissions(
                    this,
                    group2Permissions.toArray(new String[0]),
                    PERMISSION_REQUEST_CODE_GROUP_2
            );
        } else {
            checkPermissionStatus();
        }
    }


    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        boolean allGranted = true;
        boolean shouldShowRationale = false;

        for (int i = 0; i < permissions.length; i++) {
            if (grantResults[i] != PackageManager.PERMISSION_GRANTED) {
                allGranted = false;
                if (!ActivityCompat.shouldShowRequestPermissionRationale(this, permissions[i])) {
                    showPermissionDeniedDialog(permissions[i]);
                    return;
                } else {
                    shouldShowRationale = true;
                }
            }
        }

        if (allGranted) {
            // Check if there are more permissions to request
            if (requestCode == PERMISSION_REQUEST_CODE_GROUP_1) {
                // Now request group 2 permissions
                requestAllPermissions();
            } else {
                checkPermissionStatus();
                Toast.makeText(this, "Semua izin telah diizinkan", Toast.LENGTH_SHORT).show();
            }
        } else if (shouldShowRationale) {
            showRationaleDialog();
        }
    }

    private void showRationaleDialog() {
        new AlertDialog.Builder(this)
                .setTitle("Izin Diperlukan")
                .setMessage("Aplikasi membutuhkan izin berikut untuk berfungsi dengan baik:\n\n" +
                        "- Kamera: Untuk memindai kartu NFC\n" +
                        "- Lokasi: Untuk verifikasi kehadiran\n" +
                        "- Penyimpanan: Untuk menyimpan dokumen\n" +
                        "- NFC: Untuk berkomunikasi dengan kartu siswa\n\n" +
                        "Mohon berikan izin yang diminta untuk pengalaman terbaik.")
                .setPositiveButton("Berikan Izin", (dialog, which) -> requestAllPermissions())
                .setNegativeButton("Nanti", (dialog, which) -> dialog.dismiss())
                .show();
    }

    private void showPermissionDeniedDialog(String permission) {
        new AlertDialog.Builder(this)
                .setTitle("Izin Ditolak")
                .setMessage("Anda telah memblokir izin " + getPermissionName(permission) + " secara permanen. " +
                        "Anda perlu memberikan izin ini melalui Pengaturan Aplikasi untuk menggunakan fitur terkait.")
                .setPositiveButton("Buka Pengaturan", (dialog, which) -> openAppSettings())
                .setNegativeButton("Tutup", null)
                .show();
    }

    private String getPermissionName(String permission) {
        switch (permission) {
            case Manifest.permission.CAMERA:
                return "Kamera";
            case Manifest.permission.READ_EXTERNAL_STORAGE:
            case Manifest.permission.READ_MEDIA_IMAGES:
                return "Akses Penyimpanan";
            case Manifest.permission.ACCESS_FINE_LOCATION:
                return "Lokasi Presisi";
            case Manifest.permission.NFC:
                return "NFC";
            default:
                return "yang diperlukan";
        }
    }

    private void openAppSettings() {
        try {
            Intent intent = new Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS);
            Uri uri = Uri.fromParts("package", getPackageName(), null);
            intent.setData(uri);
            startActivity(intent);
        } catch (Exception e) {
            Intent intent = new Intent(Settings.ACTION_APPLICATION_SETTINGS);
            startActivity(intent);
        }
    }

    private void loadUserData() {
        Module.init(this);
        Module.getObjectWithToken(this, Module.urlKoneksi + "api/getMyAccount/" + sharedPreferences.getString("uid", ""), sharedPreferences.getString("token", ""), response -> {
            try {
                if (response.getString("status").equals("success")) {
                    txtNama.setText(response.getString("name"));
                    txtProfileName.setText(response.getString("name"));
                    txtClass.setText(response.getString("kelas"));
                    txtNIS.setText(response.getString("nis"));
                    txtTotalHadir.setText(response.getString("total_hadir_bulan_ini"));
                    txtTotalAlpa.setText(response.getString("total_alpa_bulan_ini"));
                    txtIzinSakit.setText(response.getString("total_izin_bulan_ini"));
                    Glide.with(this)
                            .load(response.getString("profile"))
                            .placeholder(R.drawable.co_profile)
                            .error(R.drawable.co_profile)
                            .into(profileImage);

                    Glide.with(this)
                            .load(response.getString("profile"))
                            .placeholder(R.drawable.co_profile)
                            .error(R.drawable.co_profile)
                            .into(profileImageLarge);

                    Glide.with(this)
                            .load(response.getBoolean("rfid_status") ? R.drawable.ix_success_filled : R.drawable.ic_sharp_do_disturb_on)
                            .placeholder(R.drawable.ic_sharp_do_disturb_on)
                            .error(R.drawable.ic_sharp_do_disturb_on)
                            .into(statusLinkCardBadge);

                    Glide.with(this)
                            .load(response.getBoolean("absen_hari_ini_status") ? R.drawable.ix_success_filled : R.drawable.ic_sharp_do_disturb_on)
                            .placeholder(R.drawable.ic_sharp_do_disturb_on)
                            .error(R.drawable.ic_sharp_do_disturb_on)
                            .into(statusAbsensiBadge);

                    btnLinkCard.setVisibility(response.getBoolean("rfid_status") ? View.GONE : View.VISIBLE);
                    btnLinkCard.setEnabled(!response.getBoolean("rfid_status") && isNfcSupported(this));

                    isAbsensi = response.getBoolean("absen_hari_ini_status");
                    btnBuatSurat.setEnabled(!response.getBoolean("absen_hari_ini_status"));
                    btnBuatSurat.setVisibility(response.getBoolean("absen_hari_ini_status") ? View.GONE : View.VISIBLE);

                    JSONArray absensiArray = response.getJSONArray("absensi_per_bulan");
                    JSONArray absensiNonProdArray = response.getJSONArray("absensi_per_bulan_non_productive");

                    HashMap<Integer, Integer> absensiMap = new HashMap<>();
                    HashMap<Integer, Integer> absensiNonProdMap = new HashMap<>();
                    for (int i = 1; i <= 12; i++) {
                        absensiMap.put(i, 0);
                        absensiNonProdMap.put(i, 0);
                    }

                    for (int i = 0; i < absensiArray.length(); i++) {
                        JSONObject data = absensiArray.getJSONObject(i);
                        int bulan = data.getInt("bulan");
                        int total = data.getInt("persentase");
                        absensiMap.put(bulan, total);
                    }

                    for (int i = 0; i < absensiNonProdArray.length(); i++) {
                        JSONObject datas = absensiNonProdArray.getJSONObject(i);
                        int bulan = datas.getInt("bulan");
                        int total = datas.getInt("persentase");
                        absensiNonProdMap.put(bulan, total);
                    }

                    ArrayList<BarEntry> entries = new ArrayList<>();
                    ArrayList<BarEntry> entriesNonProd = new ArrayList<>();

                    for (int i = 1; i <= 12; i++) {
                        int total = absensiMap.get(i);
                        entries.add(new BarEntry(i, total));

                        int totalNonProd = absensiNonProdMap.get(i);
                        entriesNonProd.add(new BarEntry(i, totalNonProd));
                    }

                    if (!entries.isEmpty()) {
                        setupBarChart(entries);
                    }

                    if (!entriesNonProd.isEmpty()) {
                        setupBarChart2(entriesNonProd);
                    }
                }
            } catch (JSONException e) {
                throw new RuntimeException(e);
            }
        }, error -> Snackbar.make(findViewById(android.R.id.content), "Tidak ada tanggapan dari server. Silahkan coba lagi nanti...", Snackbar.LENGTH_INDEFINITE).setAction("OK", view -> finishAffinity()).show());
    }

    private void LogicApps() {
        boolean checkNFCSupport = isNfcSupported(this);
        layoutWarningNFC.setVisibility(checkNFCSupport ? View.GONE : View.VISIBLE);
        btnLinkCard.setEnabled(checkNFCSupport);
        btnLinkCard.setTextColor(checkNFCSupport ? Color.parseColor("#0000FF") : Color.parseColor("#AAAAAA"));

        updateTime();
        loadUserData();

        btnAllowAccess.setOnClickListener(v -> requestAllPermissions());
        btnLinkCard.setOnClickListener(v -> startActivity(new Intent(this, NFCActivity.class)));

        btnBuatSurat.setOnClickListener(v -> {
            Intent intent = new Intent(this, LeaveFormActivity.class);
            intent.putExtra("nis", txtNIS.getText());
            startActivity(intent);
        });

        btnAbout.setOnClickListener(v -> {
            Dialog dialog = new Dialog(this);
            dialog.setContentView(R.layout.dialog_info_dev);
            Objects.requireNonNull(dialog.getWindow()).setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

            TextView btnInstagram = dialog.findViewById(R.id.goToInstagram);
            TextView btnLinkedIn = dialog.findViewById(R.id.goToLinkedIn);
            TextView btnWebsite = dialog.findViewById(R.id.goToWeb);
            Button btnClose = dialog.findViewById(R.id.btnCancel);

            btnInstagram.setOnClickListener(v1 -> {
                Uri uri = Uri.parse("https://instagram.com/x.dapzz");
                Intent instaAppIntent = new Intent(Intent.ACTION_VIEW, uri);
                instaAppIntent.setPackage("com.instagram.android");

                Uri webUri = Uri.parse("https://instagram.com/x.dapzz");
                Intent webIntent = new Intent(Intent.ACTION_VIEW, webUri);

                try {
                    startActivity(instaAppIntent);
                    dialog.dismiss();
                } catch (ActivityNotFoundException e) {
                    startActivity(webIntent);
                    dialog.dismiss();
                }
            });

            btnWebsite.setOnClickListener(v1 -> {
                Uri webUri = Uri.parse("https://dapzz.my.id");
                Intent webIntent = new Intent(Intent.ACTION_VIEW, webUri);
                startActivity(webIntent);
                dialog.dismiss();
            });

            btnLinkedIn.setOnClickListener(v1 -> {
                Uri uriApp = Uri.parse("linkedin://in/ditzzyaa");
                Intent linkedInAppIntent = new Intent(Intent.ACTION_VIEW, uriApp);

                Uri uriWeb = Uri.parse("https://www.linkedin.com/in/ditzzyaa");
                Intent webIntent = new Intent(Intent.ACTION_VIEW, uriWeb);

                try {
                    startActivity(linkedInAppIntent);
                    dialog.dismiss();
                } catch (ActivityNotFoundException e) {
                    startActivity(webIntent);
                    dialog.dismiss();
                }
            });

            btnClose.setOnClickListener(view -> dialog.dismiss());

            dialog.setCancelable(false);
            dialog.show();
        });

        findViewById(R.id.layoutLogout).setOnClickListener(v -> {
            Dialog dialog = new Dialog(this);
            dialog.setContentView(R.layout.dialog_logout);
            Objects.requireNonNull(dialog.getWindow()).setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

            Button btnLogout = dialog.findViewById(R.id.btnLogout);
            Button btnClose = dialog.findViewById(R.id.btnCancel);

            btnLogout.setOnClickListener(v1 -> {
                SharedPreferences loginPrefs = getSharedPreferences("LoginPrefs", Context.MODE_PRIVATE);
                SharedPreferences.Editor editor = loginPrefs.edit();
                editor.clear();
                editor.apply();

                startActivity(new Intent(this, LoginActivity.class));
                finish();
            });

            btnClose.setOnClickListener(view -> dialog.dismiss());

            dialog.setCancelable(false);
            dialog.show();
        });
    }

    private void setupBarChart(ArrayList<BarEntry> entries) {
        BarChart barChart = findViewById(R.id.barChart);

        BarDataSet barDataSet = new BarDataSet(entries, "Presensi Produktif Per Bulan");
        barDataSet.setColors(ColorTemplate.MATERIAL_COLORS);
        barDataSet.setValueTextSize(12f);

        barDataSet.setValueFormatter(new ValueFormatter() {
            @SuppressLint("DefaultLocale")
            @Override
            public String getFormattedValue(float value) {
                return String.format("%.1f%%", value);
            }
        });

        BarData barData = new BarData(barDataSet);
        barChart.setData(barData);

        XAxis xAxis = barChart.getXAxis();
        xAxis.setPosition(XAxis.XAxisPosition.BOTTOM);
        xAxis.setGranularity(1f);
        xAxis.setLabelCount(12, true);
        xAxis.setAxisMinimum(1f);
        xAxis.setAxisMaximum(12f);

        xAxis.setValueFormatter(new ValueFormatter() {
            private final String[] months = {"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"};

            @Override
            public String getFormattedValue(float value) {
                int index = (int) value - 1;
                if (index >= 0 && index < months.length) {
                    return months[index];
                } else {
                    return "";
                }
            }
        });

        YAxis yAxis = barChart.getAxisLeft();
        yAxis.setAxisMinimum(0f);
        yAxis.setGranularity(20f);
        yAxis.setGranularityEnabled(true);
        yAxis.setAxisMaximum(100f);

        barChart.getAxisRight().setEnabled(false);
        barChart.setFitBars(true);
        barChart.invalidate();
    }

    private void setupBarChart2(ArrayList<BarEntry> entries) {
        BarChart barChart = findViewById(R.id.barNonProduktifChart);

        BarDataSet barDataSet = new BarDataSet(entries, "Presensi Non-Produktif Per Bulan");
        barDataSet.setColors(ColorTemplate.MATERIAL_COLORS);
        barDataSet.setValueTextSize(12f);

        barDataSet.setValueFormatter(new ValueFormatter() {
            @SuppressLint("DefaultLocale")
            @Override
            public String getFormattedValue(float value) {
                return String.format("%.1f%%", value);
            }
        });

        BarData barData = new BarData(barDataSet);
        barChart.setData(barData);

        XAxis xAxis = barChart.getXAxis();
        xAxis.setPosition(XAxis.XAxisPosition.BOTTOM);
        xAxis.setGranularity(1f);
        xAxis.setLabelCount(12, true);
        xAxis.setAxisMinimum(1f);
        xAxis.setAxisMaximum(12f);

        xAxis.setValueFormatter(new ValueFormatter() {
            private final String[] months = {"Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"};

            @Override
            public String getFormattedValue(float value) {
                int index = (int) value - 1;
                if (index >= 0 && index < months.length) {
                    return months[index];
                } else {
                    return "";
                }
            }
        });

        YAxis yAxis = barChart.getAxisLeft();
        yAxis.setAxisMinimum(0f);
        yAxis.setGranularity(20f);
        yAxis.setGranularityEnabled(true);
        yAxis.setAxisMaximum(100f);

        barChart.getAxisRight().setEnabled(false);
        barChart.setFitBars(true);
        barChart.invalidate();
    }

    private static boolean isNfcSupported(Context context) {
        NfcAdapter nfcAdapter = NfcAdapter.getDefaultAdapter(context);
        return nfcAdapter != null;
    }

    @Override
    protected void onDestroy() {
        handler.removeCallbacksAndMessages(null);
        super.onDestroy();
    }

    private void updateTime() {
        handler.post(new Runnable() {
            @Override
            public void run() {
                SimpleDateFormat sdf = new SimpleDateFormat("HH:mm", Locale.getDefault());
                String currentTime = sdf.format(Calendar.getInstance().getTime());

                txtTimeNow.setText(currentTime);

                int hour = Calendar.getInstance().get(Calendar.HOUR_OF_DAY);

                Typeface poppinsRegular = ResourcesCompat.getFont(DashboardActivity.this, R.font.poppins_regular);
                Typeface poppinsBold = ResourcesCompat.getFont(DashboardActivity.this, R.font.poppins_bold);
                Typeface poppinsSemiBold = ResourcesCompat.getFont(DashboardActivity.this, R.font.poppins_semibold);

                if (hour <= 6) {
                    if (isAbsensi) {
                        statusAbsensiBadge.setImageDrawable(ContextCompat.getDrawable(DashboardActivity.this, R.drawable.ix_success_filled));
                        txtTimeNow.setTextColor(Color.parseColor("#18BA66"));
                    } else {
                        txtTimeNow.setTextColor(Color.BLACK);
                        if (hour == 6) {
                            btnBuatSurat.setVisibility(View.VISIBLE);
                            btnBuatSurat.setEnabled(true);
                        }
                    }
                    txtTimeNow.setTypeface(poppinsRegular);
                } else if (hour <= 8) {
                    if (isAbsensi) {
                        statusAbsensiBadge.setImageDrawable(ContextCompat.getDrawable(DashboardActivity.this, R.drawable.ix_success_filled));
                        btnBuatSurat.setVisibility(View.GONE);
                        btnBuatSurat.setEnabled(false);
                        txtTimeNow.setTextColor(Color.parseColor("#18BA66"));
                        txtTimeNow.setTypeface(poppinsRegular);
                    } else {
                        btnBuatSurat.setEnabled(true);
                        btnBuatSurat.setVisibility(View.VISIBLE);
                        txtTimeNow.setTextColor(Color.parseColor("#CBCE2F"));
                        txtTimeNow.setTypeface(poppinsSemiBold);
                    }
                } else {
                    if (isAbsensi) {
                        statusAbsensiBadge.setImageDrawable(ContextCompat.getDrawable(DashboardActivity.this, R.drawable.ix_success_filled));
                        btnBuatSurat.setVisibility(View.GONE);
                        btnBuatSurat.setEnabled(false);
                        txtTimeNow.setTextColor(Color.parseColor("#18BA66"));
                        txtTimeNow.setTypeface(poppinsRegular);
                    } else {
                        btnBuatSurat.setEnabled(true);
                        btnBuatSurat.setVisibility(View.VISIBLE);
                        txtTimeNow.setTextColor(Color.parseColor("#FF0000"));
                        txtTimeNow.setTypeface(poppinsBold);
                    }
                }

                handler.postDelayed(this, 1000);
            }
        });
    }
}
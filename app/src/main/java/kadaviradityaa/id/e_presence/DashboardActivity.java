package kadaviradityaa.id.e_presence;

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
import android.view.View;
import android.view.WindowInsetsController;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import android.Manifest;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.res.ResourcesCompat;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.bumptech.glide.Glide;
import com.github.mikephil.charting.charts.BarChart;
import com.github.mikephil.charting.components.XAxis;
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
import java.util.Calendar;
import java.util.HashMap;
import java.util.Locale;
import java.util.Objects;

import de.hdodenhof.circleimageview.CircleImageView;
import kadaviradityaa.id.e_presence.Library.Module;

public class DashboardActivity extends AppCompatActivity {
    private CircleImageView profileImage;
    private ImageView profileImageLarge;
    private TextView txtNama, txtTotalHadir, txtIzinSakit, btnLinkCard, btnAllowAccess, txtTimeNow, btnBuatSurat ,txtProfileName, txtClass, txtNIS;
    private ImageView statusAbsensiBadge, statusAccessBadge, statusLinkCardBadge;
    private LinearLayout layoutWarningNFC;
    private final Handler handler = new Handler();
    private boolean isAbsensi;
    private SharedPreferences sharedPreferences;
    private ConstraintLayout btnAbout;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_dashboard);

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

        initialitaion();
        initBottomNavigation();
        LogicApps();
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

    private void initialitaion(){
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

        isAbsensi = false;
        sharedPreferences = getSharedPreferences("LoginPrefs", Context.MODE_PRIVATE);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
            Objects.requireNonNull(getWindow().getInsetsController()).setSystemBarsAppearance(
                    WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS,
                    WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS
            );
        } else {
            getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR);
        }

        profileImageLarge = findViewById(R.id.profileImageLarge);
        txtProfileName = findViewById(R.id.tvProfileName);
        txtClass = findViewById(R.id.tvClass);
        txtNIS = findViewById(R.id.tvStudentId);

        findViewById(R.id.viewDashboard).setVisibility(LinearLayout.VISIBLE);
        findViewById(R.id.viewNotification).setVisibility(LinearLayout.GONE);
        findViewById(R.id.viewProfile).setVisibility(LinearLayout.GONE);
    }

    @Override
    protected void onStart() {
        super.onStart();
        ArrayList<Object> permissions2 = new ArrayList<>();

        if (android.os.Build.VERSION.SDK_INT < android.os.Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED) {
                permissions2.add(Manifest.permission.READ_EXTERNAL_STORAGE);
            }
        } else {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_MEDIA_IMAGES) != PackageManager.PERMISSION_GRANTED) {
                permissions2.add(Manifest.permission.READ_MEDIA_IMAGES);
            }
        }

        if (android.os.Build.VERSION.SDK_INT < android.os.Build.VERSION_CODES.Q) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED) {
                permissions2.add(Manifest.permission.WRITE_EXTERNAL_STORAGE);
            }
        }

        if (ContextCompat.checkSelfPermission(this, Manifest.permission.INTERNET) != PackageManager.PERMISSION_GRANTED) {
            permissions2.add(Manifest.permission.INTERNET);
        }
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            permissions2.add(Manifest.permission.ACCESS_FINE_LOCATION);
        }
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            permissions2.add(Manifest.permission.ACCESS_COARSE_LOCATION);
        }
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.NFC) != PackageManager.PERMISSION_GRANTED) {
            permissions2.add(Manifest.permission.NFC);
        }

        if (permissions2.isEmpty()) {
            btnAllowAccess.setEnabled(false);
            btnAllowAccess.setVisibility(View.GONE);
            Glide.with(this)
                    .load(R.drawable.ix_success_filled)
                    .placeholder(R.drawable.ic_sharp_do_disturb_on)
                    .error(R.drawable.ic_sharp_do_disturb_on)
                    .into(statusAccessBadge);
        }
    }

    private void LogicApps(){
        boolean checkNFCSupport = isNfcSupported(this);
        layoutWarningNFC.setVisibility(checkNFCSupport ? View.GONE : View.VISIBLE);
        btnLinkCard.setEnabled(checkNFCSupport);
        btnLinkCard.setTextColor(checkNFCSupport ? Color.parseColor("#0000FF") : Color.parseColor("#AAAAAA"));

        updateTime();
        Module.init(this);
        Module.getObjectWithToken(this, Module.urlKoneksi + "api/getMyAccount/" + sharedPreferences.getString("uid", ""), sharedPreferences.getString("token", ""), response -> {
            try {
                if (response.getString("status").equals("success")){
                    txtNama.setText(response.getString("name"));
                    txtProfileName.setText(response.getString("name"));
                    txtClass.setText(response.getString("kelas"));
                    txtNIS.setText(response.getString("nis"));
                    txtTotalHadir.setText(response.getString("total_hadir_bulan_ini"));
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
                    btnBuatSurat.setEnabled(response.getBoolean("absen_hari_ini_status"));
                    btnBuatSurat.setVisibility(response.getBoolean("absen_hari_ini_status") ? View.GONE : View.VISIBLE);

                    JSONArray absensiArray = response.getJSONArray("absensi_per_bulan");

                    HashMap<Integer, Integer> absensiMap = new HashMap<>();
                    for (int i = 1; i <= 12; i++) {
                        absensiMap.put(i, 0);
                    }

                    for (int i = 0; i < absensiArray.length(); i++) {
                        JSONObject data = absensiArray.getJSONObject(i);
                        int bulan = data.getInt("bulan");
                        int total = data.getInt("total");
                        absensiMap.put(bulan, total);
                    }

                    ArrayList<BarEntry> entries = new ArrayList<>();

                    for (int i = 1; i <= 12; i++) {
                        int total = absensiMap.get(i);
                        entries.add(new BarEntry(i, total));
                    }

                    if (!entries.isEmpty()) {
                        setupBarChart(entries);
                    }
                }
            } catch (JSONException e) {
                throw new RuntimeException(e);
            }
        }, error -> Snackbar.make(findViewById(android.R.id.content), "Tidak ada tanggapan dari server. Silahkan coba lagi nanti...", Snackbar.LENGTH_INDEFINITE).setAction("OK", view -> finishAffinity()).show());

        btnAllowAccess.setOnClickListener(v -> requestAllPermissions());
        btnLinkCard.setOnClickListener(v -> startActivity(new Intent(this, NFCActivity.class)));

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

    private static final int PERMISSION_REQUEST_CODE = 100;

    private void requestAllPermissions() {
        ArrayList<Object> permissions = new ArrayList<>();

        if (android.os.Build.VERSION.SDK_INT < android.os.Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED) {
                permissions.add(Manifest.permission.READ_EXTERNAL_STORAGE);
            }
        } else {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_MEDIA_IMAGES) != PackageManager.PERMISSION_GRANTED) {
                permissions.add(Manifest.permission.READ_MEDIA_IMAGES);
            }
        }

        if (android.os.Build.VERSION.SDK_INT < android.os.Build.VERSION_CODES.Q) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED) {
                permissions.add(Manifest.permission.WRITE_EXTERNAL_STORAGE);
            }
        }

        if (ContextCompat.checkSelfPermission(this, Manifest.permission.INTERNET) != PackageManager.PERMISSION_GRANTED) {
            permissions.add(Manifest.permission.INTERNET);
        }
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            permissions.add(Manifest.permission.ACCESS_FINE_LOCATION);
        }
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            permissions.add(Manifest.permission.ACCESS_COARSE_LOCATION);
        }
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.NFC) != PackageManager.PERMISSION_GRANTED) {
            permissions.add(Manifest.permission.NFC);
        }

        if (!permissions.isEmpty()) {
            ActivityCompat.requestPermissions(this, (String[]) permissions.toArray(new Object[0]), PERMISSION_REQUEST_CODE);
        } else {
            btnAllowAccess.setEnabled(false);
            btnAllowAccess.setVisibility(View.GONE);
            Glide.with(this)
                    .load(R.drawable.ix_success_filled)
                    .placeholder(R.drawable.ic_sharp_do_disturb_on)
                    .error(R.drawable.ic_sharp_do_disturb_on)
                    .into(statusAccessBadge);
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        if (requestCode == PERMISSION_REQUEST_CODE) {
            boolean allGranted = true;

            for (int result : grantResults) {
                if (result != PackageManager.PERMISSION_GRANTED) {
                    allGranted = false;
                    break;
                }
            }

            if (allGranted) {
                btnAllowAccess.setEnabled(false);
                btnAllowAccess.setVisibility(View.GONE);
                Glide.with(this)
                        .load(R.drawable.ix_success_filled)
                        .placeholder(R.drawable.ic_sharp_do_disturb_on)
                        .error(R.drawable.ic_sharp_do_disturb_on)
                        .into(statusAccessBadge);
            } else {
                Toast.makeText(this, "Beberapa izin ditolak. Aplikasi mungkin tidak berfungsi dengan baik.", Toast.LENGTH_LONG).show();
            }
        }
    }

    private static boolean isNfcSupported(Context context) {
        NfcAdapter nfcAdapter = NfcAdapter.getDefaultAdapter(context);
        return nfcAdapter != null;
    }

    private void setupBarChart(ArrayList<BarEntry> entries) {
        BarChart barChart = findViewById(R.id.barChart);

        BarDataSet barDataSet = new BarDataSet(entries, "Absensi Per Bulan");
        barDataSet.setColors(ColorTemplate.MATERIAL_COLORS);
        barDataSet.setValueTextSize(12f);

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

        barChart.getAxisRight().setEnabled(false);
        barChart.getAxisLeft().setAxisMinimum(0f);
        barChart.setFitBars(true);
        barChart.invalidate();
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
                    btnBuatSurat.setVisibility(View.GONE);
                    if(isAbsensi){
                        statusAbsensiBadge.setImageDrawable(ContextCompat.getDrawable(DashboardActivity.this, R.drawable.ix_success_filled));
                        txtTimeNow.setTextColor(Color.parseColor("#18BA66"));
                    }else{
                        txtTimeNow.setTextColor(Color.BLACK);
                        if(hour == 6) btnBuatSurat.setVisibility(View.VISIBLE);
                    }
                    txtTimeNow.setTypeface(poppinsRegular);
                } else if (hour <= 8) {
                    if(isAbsensi){
                        statusAbsensiBadge.setImageDrawable(ContextCompat.getDrawable(DashboardActivity.this, R.drawable.ix_success_filled));
                        btnBuatSurat.setVisibility(View.GONE);
                        txtTimeNow.setTextColor(Color.parseColor("#18BA66"));
                        txtTimeNow.setTypeface(poppinsRegular);
                    }else{
                        btnBuatSurat.setVisibility(View.VISIBLE);
                        txtTimeNow.setTextColor(Color.parseColor("#CBCE2F"));
                        txtTimeNow.setTypeface(poppinsSemiBold);
                    }
                } else {
                    if(isAbsensi){
                        statusAbsensiBadge.setImageDrawable(ContextCompat.getDrawable(DashboardActivity.this, R.drawable.ix_success_filled));
                        btnBuatSurat.setVisibility(View.GONE);
                        txtTimeNow.setTextColor(Color.parseColor("#18BA66"));
                        txtTimeNow.setTypeface(poppinsRegular);
                    }else{
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
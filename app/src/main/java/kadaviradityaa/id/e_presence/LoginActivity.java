package kadaviradityaa.id.e_presence;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.animation.ObjectAnimator;
import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.nfc.NfcAdapter;
import android.nfc.Tag;
import android.nfc.tech.MifareClassic;
import android.nfc.tech.MifareUltralight;
import android.nfc.tech.NfcA;
import android.os.Build;
import android.os.Bundle;
import android.provider.Settings;
import android.text.InputFilter;
import android.text.TextUtils;
import android.util.Log;
import android.view.View;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;

import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;

import org.json.JSONException;
import org.json.JSONObject;

import java.math.BigInteger;
import java.util.Objects;

import kadaviradityaa.id.e_presence.Library.Module;
import kadaviradityaa.id.e_presence.Library.UsernameInputFilter;

public class LoginActivity extends AppCompatActivity {
    private Button btnSign;
    private TextInputLayout layoutNIS, layoutEmail;
    private TextInputEditText txtNIS, txtUsername, txtPass, txtMail;
    private TextView txtDesc, btnReSign, btnForgotPassword;
    private static boolean signStatus;
    private static final String TAG = "LoginActivity";
    private NfcAdapter nfcAdapter;
    private PendingIntent pendingIntent;
    private IntentFilter[] intentFiltersArray;
    private String[][] techListsArray;
    private final boolean isReading = true;

    @SuppressLint("SetTextI18n")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        getWindow().addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
        getWindow().clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);
        getWindow().setStatusBarColor(Color.TRANSPARENT);

        getWindow().setNavigationBarColor(Color.argb(220, 0, 0, 0));

        getWindow().getDecorView().setSystemUiVisibility(
                View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN |
                        View.SYSTEM_UI_FLAG_LAYOUT_STABLE |
                        View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR
        );

        initialitation();
        txtUsername.setFilters(new InputFilter[]{new UsernameInputFilter()});
        Module.init(this);

        signStatus = false;
        viewTheSign(false);
        signStatus = true;

        LogicApps();
    }

    private void initialitation(){
        btnSign = findViewById(R.id.btn_sign);
        btnReSign = findViewById(R.id.btn_resign);
        txtNIS = findViewById(R.id.nis);
        txtPass = findViewById(R.id.password);
        txtUsername = findViewById(R.id.username);
        txtDesc = findViewById(R.id.textView2);
        layoutNIS = findViewById(R.id.layoutNIS);
        layoutEmail = findViewById(R.id.layoutEmail);
        txtMail = findViewById(R.id.email);
        btnForgotPassword = findViewById(R.id.forgotPassword);

        nfcAdapter = NfcAdapter.getDefaultAdapter(this);
        if (nfcAdapter == null) {
            return;
        }

        int pendingIntentFlag;
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.S) {
            pendingIntentFlag = PendingIntent.FLAG_MUTABLE;
        } else {
            pendingIntentFlag = PendingIntent.FLAG_UPDATE_CURRENT;
        }

        pendingIntent = PendingIntent.getActivity(
                this, 0,
                new Intent(this, getClass()).addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP),
                pendingIntentFlag
        );

        IntentFilter tagDiscovered = new IntentFilter(NfcAdapter.ACTION_TAG_DISCOVERED);
        IntentFilter techDiscovered = new IntentFilter(NfcAdapter.ACTION_TECH_DISCOVERED);
        IntentFilter ndefDiscovered = new IntentFilter(NfcAdapter.ACTION_NDEF_DISCOVERED);

        try {
            ndefDiscovered.addDataType("*/*");
        } catch (IntentFilter.MalformedMimeTypeException e) {
            Log.e(TAG, "MalformedMimeTypeException", e);
        }

        intentFiltersArray = new IntentFilter[] { tagDiscovered, techDiscovered, ndefDiscovered };

        techListsArray = new String[][] {
                new String[] { MifareClassic.class.getName() },
                new String[] { MifareUltralight.class.getName() },
                new String[] { NfcA.class.getName() }
        };

        if (!nfcAdapter.isEnabled()) {
            showNfcSettingsDialog();
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        if (nfcAdapter != null) {
            if (!nfcAdapter.isEnabled()) {
                showNfcSettingsDialog();
            } else if (isReading) {
                nfcAdapter.enableForegroundDispatch(this, pendingIntent, intentFiltersArray, techListsArray);
                Log.d(TAG, "NFC foreground dispatch enabled");
            }
        }
    }

    @Override
    protected void onPause() {
        super.onPause();
        if (nfcAdapter != null) {
            nfcAdapter.disableForegroundDispatch(this);
            Log.d(TAG, "NFC foreground dispatch disabled");
        }
    }

    @Override
    protected void onNewIntent(@NonNull Intent intent) {
        super.onNewIntent(intent);
        if (isReading) {
            handleIntent(intent);
        }
    }

    @SuppressLint({"SetTextI18n", "DefaultLocale"})
    private void handleIntent(Intent intent) {
        String action = intent.getAction();
        if (NfcAdapter.ACTION_TAG_DISCOVERED.equals(action) ||
                NfcAdapter.ACTION_TECH_DISCOVERED.equals(action) ||
                NfcAdapter.ACTION_NDEF_DISCOVERED.equals(action)) {

            Tag tag = intent.getParcelableExtra(NfcAdapter.EXTRA_TAG);
            if (tag != null) {
                String[] techList = tag.getTechList();
                boolean isMifareCard = false;

                for (String tech : techList) {
                    if (tech.contains("MifareClassic") || tech.contains("MifareUltralight") || tech.contains("NfcA")) {
                        isMifareCard = true;
                        break;
                    }
                }

                if (isMifareCard && signStatus) {
                    byte[] uid = tag.getId();

                    byte[] reversed = new byte[uid.length];
                    for (int i = 0; i < uid.length; i++) {
                        reversed[i] = uid[uid.length - 1 - i];
                    }

                    BigInteger uidDecimal = new BigInteger(1, reversed);
                    String formattedUid = String.format("%010d", uidDecimal);

                    if (!formattedUid.isEmpty()) {
                        JSONObject postDatas = new JSONObject();
                        try {
                            postDatas.put("id", formattedUid);
                        } catch (JSONException e) {
                            throw new RuntimeException(e);
                        }

                        Module.postObject(this, Module.urlKoneksi + "api/sanctum/tap", postDatas,
                                response -> {
                                    String token;
                                    try {
                                        if(response.getString("status").equals("success")){
                                            token = response.getString("token");

                                            SharedPreferences sharedPreferences = this.getSharedPreferences("LoginPrefs", Context.MODE_PRIVATE);
                                            SharedPreferences.Editor editor = sharedPreferences.edit();
                                            editor.putString("token", token);
                                            editor.putString("uid", response.getString("uid"));
                                            editor.putBoolean("status", true);
                                            editor.apply();

                                            startActivity(new Intent(this, DashboardActivity.class));
                                            finish();
                                        } else if (response.getString("status").equals("failed")) {
                                            Toast.makeText(this, response.getString("message"), Toast.LENGTH_SHORT).show();
                                        }
                                    } catch (JSONException e) {
                                        throw new RuntimeException(e);
                                    }
                                },
                                error -> Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show()
                        );
                    }
                }else{
                    if(isMifareCard){
                        Toast.makeText(this, "Silahkan beralih ke Login terlebih dahulu...", Toast.LENGTH_SHORT).show();
                    }
                }
            }
        }
    }

    private void showNfcSettingsDialog() {
        new AlertDialog.Builder(this)
                .setTitle("NFC Tidak Aktif")
                .setMessage("NFC diperlukan untuk login dengan kartu. Aktifkan NFC sekarang?")
                .setPositiveButton("Pengaturan", (dialog, which) -> startActivity(new Intent(Settings.ACTION_NFC_SETTINGS)))
                .setNegativeButton("Batal", (dialog, which) -> finish())
                .setCancelable(false)
                .show();
    }

    @SuppressLint("SetTextI18n")
    private void viewTheSign(boolean condition){
        if(!condition){
            txtDesc.setText("Belum punya akun?");
            btnReSign.setText("Daftar");
            btnSign.setText("Masuk");
            fadeView(layoutNIS, false);
            fadeView(txtNIS, false);
            fadeView(layoutEmail, false);
            fadeView(txtMail, false);
            fadeView(btnForgotPassword, true);
        }else{
            txtDesc.setText("Sudah punya akun?");
            btnReSign.setText("Masuk");
            btnSign.setText("Daftar");
            fadeView(layoutNIS, true);
            fadeView(txtNIS, true);
            fadeView(layoutEmail, true);
            fadeView(txtMail, true);
            fadeView(btnForgotPassword, false);
        }
    }

    private void fadeView(View view, boolean show) {
        if (show) {
            view.setAlpha(0f);
            view.setVisibility(VISIBLE);
            ObjectAnimator.ofFloat(view, "alpha", 0f, 1f).setDuration(800).start();
        } else {
            ObjectAnimator animator = ObjectAnimator.ofFloat(view, "alpha", 1f, 0f);
            animator.setDuration(800);
            animator.addListener(new AnimatorListenerAdapter() {
                @Override
                public void onAnimationEnd(Animator animation) {
                    view.setVisibility(GONE);
                }
            });
            animator.start();
        }
    }

    private void LogicApps(){
        btnReSign.setOnClickListener(v -> {
            if (signStatus) {
                viewTheSign(true);
                signStatus = false;
            }else{
                viewTheSign(false);
                signStatus = true;
            }
        });

        btnForgotPassword.setOnClickListener(v -> startActivity(new Intent(this, ResetPasswordActivity.class)));

        btnSign.setOnClickListener(v -> {
            if(signStatus){
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.VANILLA_ICE_CREAM) {
                    if(!Objects.requireNonNull(txtUsername.getText()).isEmpty() && !Objects.requireNonNull(txtPass.getText()).isEmpty()){
                        JSONObject postData = new JSONObject();
                        try {
                            postData.put("username", txtUsername.getText());
                            postData.put("password", txtPass.getText());
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }

                        Module.postObject(this, Module.urlKoneksi + "api/sanctum/token", postData,
                            response -> {
                                String token;
                                try {
                                    if(response.getString("status").equals("success")){
                                        token = response.getString("token");

                                        SharedPreferences sharedPreferences = this.getSharedPreferences("LoginPrefs", Context.MODE_PRIVATE);
                                        SharedPreferences.Editor editor = sharedPreferences.edit();
                                        editor.putString("token", token);
                                        editor.putString("uid", response.getString("uid"));
                                        editor.putBoolean("status", true);
                                        editor.apply();

                                        startActivity(new Intent(this, DashboardActivity.class));
                                        finish();
                                    } else if (response.getString("status").equals("failed")) {
                                        Toast.makeText(this, response.getString("message"), Toast.LENGTH_SHORT).show();
                                    }
                                } catch (JSONException e) {
                                    throw new RuntimeException(e);
                                }
                            },
                            error -> Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show()
                        );
                    }else{
                        Toast.makeText(this, "Silahkan isi kolom Username dan Password!", Toast.LENGTH_SHORT).show();
                    }
                }else {
                    if (!TextUtils.isEmpty(txtUsername.getText()) && !TextUtils.isEmpty(txtPass.getText())) {
                        JSONObject postData = new JSONObject();
                        try {
                            postData.put("username", txtUsername.getText());
                            postData.put("password", txtPass.getText());
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }

                        Module.postObject(this, Module.urlKoneksi + "api/sanctum/token", postData,
                                response -> {
                                    String token;
                                    try {
                                        if(response.getString("status").equals("success")){
                                            token = response.getString("token");

                                            SharedPreferences sharedPreferences = this.getSharedPreferences("LoginPrefs", Context.MODE_PRIVATE);
                                            SharedPreferences.Editor editor = sharedPreferences.edit();
                                            editor.putString("token", token);
                                            editor.putString("uid", response.getString("uid"));
                                            editor.putBoolean("status", true);
                                            editor.apply();

                                            startActivity(new Intent(this, DashboardActivity.class));
                                            finish();
                                        } else if (response.getString("status").equals("failed")) {
                                            Toast.makeText(this, response.getString("message"), Toast.LENGTH_SHORT).show();
                                        }
                                    } catch (JSONException e) {
                                        throw new RuntimeException(e);
                                    }
                                },
                                error -> Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show()
                        );
                    }else{
                        Toast.makeText(this, "Silahkan isi kolom Username dan Password!", Toast.LENGTH_SHORT).show();
                    }
                }
            }else{
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.VANILLA_ICE_CREAM) {
                    if(!Objects.requireNonNull(txtUsername.getText()).isEmpty() && !Objects.requireNonNull(txtMail.getText()).isEmpty() && !Objects.requireNonNull(txtPass.getText()).isEmpty() && !Objects.requireNonNull(txtNIS.getText()).isEmpty()){
                        JSONObject postData = new JSONObject();
                        try {
                            postData.put("nis", txtNIS.getText().toString());
                            postData.put("email", txtMail.getText().toString());
                            postData.put("username", txtUsername.getText());
                            postData.put("password", txtPass.getText());
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }

                        Module.postObject(this, Module.urlKoneksi + "api/daftar-akun", postData,
                                response -> {
                                    try {
                                        if(response.getString("status").equals("success")){
                                            JSONObject dataObj = response.getJSONObject("data");
                                            JSONObject includeObj = response.getJSONObject("include");

                                            Dialog dialog = new Dialog(this);
                                            dialog.setContentView(R.layout.dialog_konfirmasi);
                                            Objects.requireNonNull(dialog.getWindow()).setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

                                            TextView textNama = dialog.findViewById(R.id.textNama);
                                            TextView textKelas = dialog.findViewById(R.id.textKelas);
                                            TextView textAlamat = dialog.findViewById(R.id.textAlamat);
                                            Button btnBatal = dialog.findViewById(R.id.btnBatal);
                                            Button btnLanjut = dialog.findViewById(R.id.btnLanjut);

                                            textNama.setText(dataObj.has("name") ? dataObj.getString("name") : "-");
                                            textKelas.setText(dataObj.has("kelas") ? dataObj.getString("kelas") : "-");
                                            textAlamat.setText(dataObj.has("alamat") ? dataObj.getString("alamat") : "-");

                                            btnBatal.setOnClickListener(view -> dialog.dismiss());

                                            btnLanjut.setOnClickListener(view -> {
                                                JSONObject sendObj = new JSONObject();
                                                try {
                                                    sendObj.put("nis", includeObj.getString("nis"));
                                                    sendObj.put("email", includeObj.getString("email"));
                                                    sendObj.put("username", includeObj.getString("username"));
                                                    sendObj.put("password", includeObj.getString("password"));
                                                } catch (JSONException e) {
                                                    throw new RuntimeException(e);
                                                }
                                                Module.postObject(this, Module.urlKoneksi + "api/register-account", sendObj,
                                                    response1 -> {
                                                        try {
                                                            if(response1.has("status") && response1.getString("status").equals("success")){
                                                                txtNIS.setText(null);
                                                                txtMail.setText(null);
                                                                txtPass.setText(null);
                                                                txtUsername.setText(null);

                                                                signStatus = false;
                                                                viewTheSign(false);
                                                                signStatus = true;

                                                                Toast.makeText(this, response1.getString("message"), Toast.LENGTH_SHORT).show();
                                                            }else if (response1.has("status") && response1.getString("status").equals("failed")){
                                                                Toast.makeText(this, response1.getString("message"), Toast.LENGTH_SHORT).show();
                                                            }
                                                        } catch (JSONException e) {
                                                            throw new RuntimeException(e);
                                                        }
                                                    },
                                                    error -> Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show()
                                                );
                                                dialog.dismiss();
                                            });

                                            dialog.setCancelable(false);
                                            dialog.show();
                                        } else if (response.getString("status").equals("failed")) {
                                            Toast.makeText(this, response.getString("message"), Toast.LENGTH_SHORT).show();
                                        }
                                    } catch (JSONException e) {
                                        throw new RuntimeException(e);
                                    }
                                },
                                error -> Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show()
                        );
                    }else{
                        Toast.makeText(this, "Silahkan isi kolom NIS, Username, Email dan Password!", Toast.LENGTH_SHORT).show();
                    }
                }else {
                    if (!TextUtils.isEmpty(txtUsername.getText()) && !TextUtils.isEmpty(txtPass.getText()) && !TextUtils.isEmpty(txtMail.getText()) && !TextUtils.isEmpty(txtNIS.getText())) {
                        JSONObject postData = new JSONObject();
                        try {
                            postData.put("nis", txtNIS.getText().toString());
                            postData.put("email", txtMail.getText().toString());
                            postData.put("username", txtUsername.getText());
                            postData.put("password", txtPass.getText());
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }

                        Module.postObject(this, Module.urlKoneksi + "api/daftar-akun", postData,
                                response -> {
                                    try {
                                        if(response.getString("status").equals("success")){
                                            JSONObject dataObj = response.getJSONObject("data");
                                            JSONObject includeObj = response.getJSONObject("include");

                                            Dialog dialog = new Dialog(this);
                                            dialog.setContentView(R.layout.dialog_konfirmasi);
                                            Objects.requireNonNull(dialog.getWindow()).setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

                                            TextView textNama = dialog.findViewById(R.id.textNama);
                                            TextView textKelas = dialog.findViewById(R.id.textKelas);
                                            TextView textAlamat = dialog.findViewById(R.id.textAlamat);
                                            Button btnBatal = dialog.findViewById(R.id.btnBatal);
                                            Button btnLanjut = dialog.findViewById(R.id.btnLanjut);

                                            textNama.setText(dataObj.has("name") ? dataObj.getString("name") : "-");
                                            textKelas.setText(dataObj.has("kelas") ? dataObj.getString("kelas") : "-");
                                            textAlamat.setText(dataObj.has("alamat") ? dataObj.getString("alamat") : "-");

                                            btnBatal.setOnClickListener(view -> dialog.dismiss());

                                            btnLanjut.setOnClickListener(view -> {
                                                JSONObject sendObj = new JSONObject();
                                                try {
                                                    sendObj.put("nis", includeObj.getString("nis"));
                                                    sendObj.put("username", includeObj.getString("username"));
                                                    sendObj.put("email", includeObj.getString("email"));
                                                    sendObj.put("password", includeObj.getString("password"));
                                                } catch (JSONException e) {
                                                    throw new RuntimeException(e);
                                                }
                                                Module.postObject(this, Module.urlKoneksi + "api/register-account", sendObj,
                                                        response1 -> {
                                                            try {
                                                                if(response1.has("status") && response1.getString("status").equals("success")){
                                                                    txtNIS.setText(null);
                                                                    txtPass.setText(null);
                                                                    txtUsername.setText(null);

                                                                    signStatus = false;
                                                                    viewTheSign(false);
                                                                    signStatus = true;

                                                                    Toast.makeText(this, response1.getString("message"), Toast.LENGTH_SHORT).show();
                                                                }else if (response1.has("status") && response1.getString("status").equals("failed")){
                                                                    Toast.makeText(this, response1.getString("message"), Toast.LENGTH_SHORT).show();
                                                                }
                                                            } catch (JSONException e) {
                                                                throw new RuntimeException(e);
                                                            }
                                                        },
                                                        error -> Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show()
                                                );
                                                dialog.dismiss();
                                            });

                                            dialog.setCancelable(false);
                                            dialog.show();
                                        } else if (response.getString("status").equals("failed")) {
                                            Toast.makeText(this, response.getString("message"), Toast.LENGTH_SHORT).show();
                                        }
                                    } catch (JSONException e) {
                                        throw new RuntimeException(e);
                                    }
                                },
                                error -> Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show()
                        );
                    }else{
                        Toast.makeText(this, "Silahkan isi kolom NIS, Username, Email dan Password!", Toast.LENGTH_SHORT).show();
                    }
                }
            }
        });
    }
}
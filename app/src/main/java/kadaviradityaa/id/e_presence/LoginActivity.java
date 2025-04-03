package kadaviradityaa.id.e_presence;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.animation.ObjectAnimator;
import android.app.Dialog;
import android.app.PendingIntent;
import android.content.ClipboardManager;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.nfc.NfcAdapter;
import android.nfc.Tag;
import android.os.Build;
import android.os.Bundle;
import android.provider.Settings;
import android.text.InputFilter;
import android.text.TextUtils;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.bumptech.glide.Glide;
import com.google.android.material.snackbar.Snackbar;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.Objects;

import kadaviradityaa.id.e_presence.Library.Module;
import kadaviradityaa.id.e_presence.Library.UsernameInputFilter;

public class LoginActivity extends AppCompatActivity {
    private Button btnSign;
    private TextInputLayout layoutNIS, layoutEmail;
    private TextInputEditText txtNIS, txtUsername, txtPass, txtMail;
    private TextView txtDesc, btnReSign, btnForgotPassword;
    private static boolean signStatus;
    private NfcAdapter nfcAdapter;
    private String uidCard;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_login);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        initialitation();
        txtUsername.setFilters(new InputFilter[]{new UsernameInputFilter()});
        Module.init(this);

        signStatus = false;
        viewTheSign(signStatus);
        signStatus = true;

        LogicApps();
//        detectTapCardLogin();
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

        if (!nfcAdapter.isEnabled()) {
            showNfcSettingsDialog();
        }
    }
    private void showNfcSettingsDialog() {
        new AlertDialog.Builder(this)
                .setTitle("Nyalakan NFC")
                .setMessage("NFC Anda tidak aktif. Silakan aktifkan di pengaturan.")
                .setPositiveButton("Buka Pengaturan", (dialog, which) -> {
                    Intent intent = new Intent(Settings.ACTION_NFC_SETTINGS);
                    startActivity(intent);
                })
                .setNegativeButton("Batal", null)
                .setCancelable(false)
                .show();
    }

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

//    @Override
//    protected void onResume() {
//        super.onResume();
//        IntentFilter nfcIntent = new IntentFilter(NfcAdapter.ACTION_TAG_DISCOVERED);
//        nfcAdapter.enableForegroundDispatch(this, PendingIntent.getActivity(this, 0, new Intent(this, getClass()), 0), new IntentFilter[]{nfcIntent}, null);
//    }
//
//    @Override
//    protected void onPause() {
//        super.onPause();
//        nfcAdapter.disableForegroundDispatch(this);
//    }
//
//    private String bytesToHex(byte[] bytes) {
//        StringBuilder stringBuilder = new StringBuilder();
//        for (byte b : bytes) {
//            stringBuilder.append(String.format("%02X", b));
//        }
//        return stringBuilder.toString();
//    }
//
//    private void detectTapCardLogin(){
//        if (NfcAdapter.ACTION_TAG_DISCOVERED.equals(getIntent().getAction())) {
//            Tag tag = getIntent().getParcelableExtra(NfcAdapter.EXTRA_TAG);
//            if (tag != null) {
//                byte[] uid = tag.getId();
//                uidCard = bytesToHex(uid);
//                if(!uidCard.isEmpty()) {
//                    Toast.makeText(this, "Tunggu sebentar, login Anda sedang diproses...", Toast.LENGTH_SHORT).show();
//                    JSONObject postDatas = new JSONObject();
//                    try {
//                        postDatas.put("id", uidCard);
//                    } catch (JSONException e) {
//                        e.printStackTrace();
//                    }
//
//                    Module.postObject(this, Module.urlKoneksi + "api/sanctum/tap", postDatas,
//                            response -> {
//                                String token = null;
//                                try {
//                                    if(response.getString("status").equals("success")){
//                                        token = response.getString("token");
//
//                                        SharedPreferences sharedPreferences = this.getSharedPreferences("LoginPrefs", Context.MODE_PRIVATE);
//                                        SharedPreferences.Editor editor = sharedPreferences.edit();
//                                        editor.putString("token", token);
//                                        editor.putString("uid", response.getString("uid"));
//                                        editor.putBoolean("status", true);
//                                        editor.apply();
//
//                                        startActivity(new Intent(this, DashboardActivity.class));
//                                        finish();
//                                    } else if (response.getString("status").equals("failed")) {
//                                        Toast.makeText(this, response.getString("message"), Toast.LENGTH_SHORT).show();
//                                    }
//                                } catch (JSONException e) {
//                                    throw new RuntimeException(e);
//                                }
//                            },
//                            error -> {
//                                Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show();
//                            }
//                    );
//                }
//            }
//        }
//    }

    private void LogicApps(){
        btnReSign.setOnClickListener(v -> {
            if (signStatus) {
                viewTheSign(signStatus);
                signStatus = false;
            }else{
                viewTheSign(signStatus);
                signStatus = true;
            }
        });

        btnForgotPassword.setOnClickListener(v -> {
            startActivity(new Intent(this, ResetPasswordActivity.class));
        });

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
                                String token = null;
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
                            error -> {
                                Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show();
                            }
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
                                    String token = null;
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
                                error -> {
                                    Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show();
                                }
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
                                                                viewTheSign(signStatus);
                                                                signStatus = true;

                                                                Toast.makeText(this, response1.getString("message"), Toast.LENGTH_SHORT).show();
                                                            }else if (response1.has("status") && response1.getString("status").equals("failed")){
                                                                Toast.makeText(this, response1.getString("message"), Toast.LENGTH_SHORT).show();
                                                            }
                                                        } catch (JSONException e) {
                                                            throw new RuntimeException(e);
                                                        }
                                                    },
                                                    error -> {
                                                        Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show();
                                                    }
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
                                error -> {
                                    Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show();
                                }
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
                                                                    viewTheSign(signStatus);
                                                                    signStatus = true;

                                                                    Toast.makeText(this, response1.getString("message"), Toast.LENGTH_SHORT).show();
                                                                }else if (response1.has("status") && response1.getString("status").equals("failed")){
                                                                    Toast.makeText(this, response1.getString("message"), Toast.LENGTH_SHORT).show();
                                                                }
                                                            } catch (JSONException e) {
                                                                throw new RuntimeException(e);
                                                            }
                                                        },
                                                        error -> {
                                                            Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show();
                                                        }
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
                                error -> {
                                    Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show();
                                }
                        );
                    }else{
                        Toast.makeText(this, "Silahkan isi kolom NIS, Username, Email dan Password!", Toast.LENGTH_SHORT).show();
                    }
                }
            }
        });
    }
}
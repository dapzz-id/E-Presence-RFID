package kadaviradityaa.id.e_presence;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.google.android.material.snackbar.Snackbar;

import org.json.JSONException;

import kadaviradityaa.id.e_presence.Library.Module;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_main);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        Module.init(this);

        new Handler(Looper.getMainLooper()).postDelayed(() -> {
            SharedPreferences login = getSharedPreferences("LoginPrefs", Context.MODE_PRIVATE);
            boolean isLoggedIn = login.getBoolean("status", false);

            if(isLoggedIn){
                Module.getObjectWithToken(this, Module.urlKoneksi + "api/profile", login.getString("token", ""),
                    response -> {
                        try {
                            if(response.has("status") && response.getString("status").equals("success")){
                                startActivity(new Intent(this, DashboardActivity.class));
                                finish();
                            } else if(response.has("status") && response.getString("status").equals("failed")){
                                Snackbar.make(findViewById(android.R.id.content), "Akun Anda sedang digunakan di perangkat lain, silahkan login kembali...", Snackbar.LENGTH_INDEFINITE).setAction("OK", view -> {
                                    SharedPreferences loginPrefs = getSharedPreferences("LoginPrefs", Context.MODE_PRIVATE);
                                    SharedPreferences.Editor editor = loginPrefs.edit();
                                    editor.clear();
                                    editor.apply();

                                    startActivity(new Intent(this, LoginActivity.class));
                                    finish();
                                }).show();
                            }
                        } catch (JSONException e) {
                            throw new RuntimeException(e);
                        }
                    },
                    error -> Snackbar.make(findViewById(android.R.id.content), "Tidak ada tanggapan dari server. Silahkan coba lagi nanti...", Snackbar.LENGTH_INDEFINITE).setAction("OK", view -> finishAffinity()).show()
                );
            }else{
                startActivity(new Intent(this, LoginActivity.class));
                finish();
            }
        }, 3000);
    }
}
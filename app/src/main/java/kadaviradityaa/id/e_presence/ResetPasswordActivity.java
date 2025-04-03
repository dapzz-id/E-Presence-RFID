package kadaviradityaa.id.e_presence;

import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.util.Log;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputEditText;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;
import java.util.Objects;

import kadaviradityaa.id.e_presence.Library.Module;

public class ResetPasswordActivity extends AppCompatActivity {
    private MaterialButton btnBack, btnSendLink;
    private TextInputEditText txtEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_reset_password);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        initalization();
        LogicProgram();
    }

    private void initalization(){
        btnBack = findViewById(R.id.btn_back);
        btnSendLink = findViewById(R.id.btn_send_link);
        txtEmail = findViewById(R.id.email);
    }

    private void LogicProgram(){
        btnBack.setOnClickListener(v -> finish());

        btnSendLink.setOnClickListener(v -> {
            if (!Objects.requireNonNull(txtEmail.getText()).toString().isEmpty()) {
                StringRequest stringRequest = new StringRequest(Request.Method.POST, Module.urlKoneksi + "api/forgot-password",
                        response -> {
                            try {
                                JSONObject jsonResponse = new JSONObject(response);
                                if (jsonResponse.getString("status").equals("success")) {
                                    txtEmail.setText(null);
                                    Toast.makeText(this, jsonResponse.getString("message"), Toast.LENGTH_SHORT).show();
                                    new Handler(Looper.getMainLooper()).postDelayed(this::finish, 3000);
                                } else {
                                    Toast.makeText(this, jsonResponse.getString("message"), Toast.LENGTH_SHORT).show();
                                }
                            } catch (JSONException e) {
                                e.printStackTrace();
                            }
                        },
                        error -> {
                            Log.e("API_ERROR", "Error: " + error.toString());
                            if (error.networkResponse != null) {
                                Log.e("API_ERROR", "Status Code: " + error.networkResponse.statusCode);
                                Log.e("API_ERROR", "Response Data: " + new String(error.networkResponse.data));
                            }
                            Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show();
                        }
                ) {
                    @Override
                    protected Map<String, String> getParams() {
                        Map<String, String> params = new HashMap<>();
                        params.put("email", txtEmail.getText().toString());
                        return params;
                    }

                    @Override
                    public Map<String, String> getHeaders() {
                        Map<String, String> headers = new HashMap<>();
                        headers.put("Accept", "application/json");
                        headers.put("Content-Type", "application/x-www-form-urlencoded");
                        return headers;
                    }
                };

                // **Tambahkan request ke Volley RequestQueue**
                RequestQueue requestQueue = Volley.newRequestQueue(this);
                requestQueue.add(stringRequest);

            } else {
                Toast.makeText(this, "Silahkan isi kolom Email!", Toast.LENGTH_SHORT).show();
            }
        });
    }
}
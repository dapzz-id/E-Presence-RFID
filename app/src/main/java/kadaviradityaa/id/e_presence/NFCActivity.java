package kadaviradityaa.id.e_presence;

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
import android.os.Handler;
import android.os.Looper;
import android.provider.Settings;
import android.util.Log;
import android.view.View;
import android.view.WindowInsetsController;
import android.widget.Button;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.bumptech.glide.Glide;

import org.json.JSONException;
import org.json.JSONObject;

import java.math.BigInteger;
import java.util.Objects;

import kadaviradityaa.id.e_presence.Library.Module;

public class NFCActivity extends AppCompatActivity {
    private static final String TAG = "NFCActivity";
    private NfcAdapter nfcAdapter;
    private PendingIntent pendingIntent;
    private IntentFilter[] intentFiltersArray;
    private String[][] techListsArray;
    private AppCompatButton btnContinue;
    private String formattedUid;
    private final boolean isReading = true;
    private SharedPreferences sharedPreferences;

    @SuppressLint("SetTextI18n")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_nfcactivity);

        getWindow().setNavigationBarColor(Color.TRANSPARENT);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            getWindow().setNavigationBarContrastEnforced(true);
            getWindow().setNavigationBarDividerColor(Color.BLACK);
        }

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
            Objects.requireNonNull(getWindow().getInsetsController()).setSystemBarsAppearance(
                    WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS,
                    WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS
            );
        } else {
            getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR);
        }

        AppCompatButton btnCancel = findViewById(R.id.btnCancel);
        btnContinue = findViewById(R.id.btnContinue);

        Module.init(this);
        sharedPreferences = getSharedPreferences("LoginPrefs", Context.MODE_PRIVATE);

        btnCancel.setOnClickListener(v -> finish());
        btnContinue.setOnClickListener(v -> {
            Dialog dialog = new Dialog(this);
            dialog.setContentView(R.layout.dialog_connect_card);
            Objects.requireNonNull(dialog.getWindow()).setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

            Button btnConfirm = dialog.findViewById(R.id.btnConfirm);
            Button btnClose = dialog.findViewById(R.id.btnCancel);

            btnConfirm.setOnClickListener(v1 -> {
                JSONObject sendObj = new JSONObject();
                try {
                    sendObj.put("id_card", formattedUid);
                } catch (JSONException e) {
                    throw new RuntimeException(e);
                }
                Module.patchObjectWithToken(this, Module.urlKoneksi + "api/linkedCard/" + sharedPreferences.getString("uid", ""), sharedPreferences.getString("token", ""), sendObj,
                        response1 -> {
                            try {
                                if(response1.has("status") && response1.getString("status").equals("success")){
                                    Toast.makeText(this, response1.getString("message"), Toast.LENGTH_SHORT).show();
                                    new Handler(Looper.getMainLooper()).postDelayed(this::finish, 1500);
                                }else if (response1.has("status") && response1.getString("status").equals("failed")){
                                    Toast.makeText(this, response1.getString("message"), Toast.LENGTH_SHORT).show();
                                }
                            } catch (JSONException e) {
                                throw new RuntimeException(e);
                            }
                        },
                        error -> Toast.makeText(this, "Silahkan coba lagi nanti", Toast.LENGTH_SHORT).show()
                );
            });

            btnClose.setOnClickListener(view -> dialog.dismiss());

            dialog.setCancelable(false);
            dialog.show();
        });

        nfcAdapter = NfcAdapter.getDefaultAdapter(this);

        if (nfcAdapter == null) {
            Toast.makeText(this, "Perangkat ini tidak mendukung NFC", Toast.LENGTH_LONG).show();
            finish();
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

                if (isMifareCard) {
                    byte[] uid = tag.getId();

                    byte[] reversed = new byte[uid.length];
                    for (int i = 0; i < uid.length; i++) {
                        reversed[i] = uid[uid.length - 1 - i];
                    }

                    BigInteger uidDecimal = new BigInteger(1, reversed);
                    formattedUid = String.format("%010d", uidDecimal);

                    runOnUiThread(() -> {
                        Log.d(TAG, "Updating UID: " + formattedUid);

                        if (!formattedUid.isEmpty()) {
                            Glide.with(this)
                                    .load(R.drawable.ix_success_prf_filled)
                                    .placeholder(R.drawable.ic_nfc)
                                    .error(R.drawable.ic_sharp_prf_do_disturb_on)
                                    .into((android.widget.ImageView) findViewById(R.id.nfcLogo));
                            btnContinue.setEnabled(true);
                            btnContinue.setBackgroundResource(R.drawable.green_button_background);
                        }else {
                            Glide.with(this)
                                    .load(R.drawable.ic_sharp_prf_do_disturb_on)
                                    .placeholder(R.drawable.ic_nfc)
                                    .error(R.drawable.ic_sharp_prf_do_disturb_on)
                                    .into((android.widget.ImageView) findViewById(R.id.nfcLogo));
                            btnContinue.setEnabled(false);
                            btnContinue.setBackgroundResource(R.drawable.gray_button_background);
                        }
                    });
                }
            }
        }
    }

    private void showNfcSettingsDialog() {
        new AlertDialog.Builder(this)
                .setTitle("NFC Tidak Aktif")
                .setMessage("NFC diperlukan untuk menghubungkan kartu. Aktifkan NFC sekarang?")
                .setPositiveButton("Pengaturan", (dialog, which) -> startActivity(new Intent(Settings.ACTION_NFC_SETTINGS)))
                .setNegativeButton("Batal", (dialog, which) -> finish())
                .setCancelable(false)
                .show();
    }
}
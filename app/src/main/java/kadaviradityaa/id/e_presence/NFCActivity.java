package kadaviradityaa.id.e_presence;

import android.annotation.SuppressLint;
import android.app.PendingIntent;
import android.content.Intent;
import android.content.IntentFilter;
import android.nfc.NfcAdapter;
import android.nfc.Tag;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import android.nfc.tech.MifareClassic;
import android.nfc.tech.MifareUltralight;
import android.nfc.tech.NfcA;
import android.os.Build;
import android.os.Bundle;
import android.provider.Settings;
import android.util.Log;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;

public class NFCActivity extends AppCompatActivity {
    private static final String TAG = "NFCActivity";
    private NfcAdapter nfcAdapter;
    private PendingIntent pendingIntent;
    private IntentFilter[] intentFiltersArray;
    private String[][] techListsArray;
    private AppCompatButton btnContinue;
    private boolean isReading = false;

    @SuppressLint("SetTextI18n")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_nfcactivity);

        // Initialize buttons
        AppCompatButton btnCancel = findViewById(R.id.btnCancel);
        btnContinue = findViewById(R.id.btnContinue);

        // Set button click listeners
        btnCancel.setOnClickListener(v -> finish());
        btnContinue.setOnClickListener(v -> {
            if (!isReading) {
                startNfcReading();
                btnContinue.setText("Berhenti");
                isReading = true;
            } else {
                stopNfcReading();
                btnContinue.setText("Lanjutkan");
                isReading = false;
            }
        });

        // Initialize NFC adapter
        nfcAdapter = NfcAdapter.getDefaultAdapter(this);

        // Check if NFC is available on this device
        if (nfcAdapter == null) {
            Toast.makeText(this, "Perangkat ini tidak mendukung NFC", Toast.LENGTH_LONG).show();
            finish();
            return;
        }

        // Create a PendingIntent that will be used to read NFC tags
        // Use FLAG_MUTABLE or FLAG_IMMUTABLE based on Android version
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

        // Setup intent filters for NFC
        IntentFilter ndef = new IntentFilter(NfcAdapter.ACTION_TAG_DISCOVERED);
        try {
            ndef.addDataType("*/*");
        } catch (IntentFilter.MalformedMimeTypeException e) {
            Log.e(TAG, "MalformedMimeTypeException", e);
        }

        intentFiltersArray = new IntentFilter[] { ndef };

        // Setup tech lists for MIFARE cards
        techListsArray = new String[][] {
                new String[] { MifareClassic.class.getName() },
                new String[] { MifareUltralight.class.getName() },
                new String[] { NfcA.class.getName() }
        };

        // Check if NFC is enabled
        if (!nfcAdapter.isEnabled()) {
            showNfcSettingsDialog();
        }

        // Process any NFC intent that launched this activity
        handleIntent(getIntent());
    }

    @Override
    protected void onResume() {
        super.onResume();
        if (nfcAdapter != null && nfcAdapter.isEnabled() && isReading) {
            nfcAdapter.enableForegroundDispatch(this, pendingIntent, intentFiltersArray, techListsArray);
        }
    }

    @Override
    protected void onPause() {
        super.onPause();
        if (nfcAdapter != null) {
            nfcAdapter.disableForegroundDispatch(this);
        }
    }

    @Override
    protected void onNewIntent(@NonNull Intent intent) {
        super.onNewIntent(intent);
        handleIntent(intent);
    }

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
                    String uidHex = bytesToHex(uid);

                    // Display UID in Toast
                    Toast.makeText(this, "UID Kartu: " + bytesToHex(uid), Toast.LENGTH_LONG).show();

                    // Log the UID
                    Log.d(TAG, "Detected MIFARE card with UID: " + uidHex);

                    // Here you can add code to send the UID to your server or save it locally
                }
            }
        }
    }

    private String bytesToHex(byte[] bytes) {
        StringBuilder hexString = new StringBuilder();
        for (byte b : bytes) {
            hexString.append(String.format("%02X", b));  // Konversi setiap byte ke format Hex
        }
        return hexString.toString();
    }

    private void showNfcSettingsDialog() {
        new AlertDialog.Builder(this)
                .setTitle("NFC Tidak Aktif")
                .setMessage("NFC diperlukan untuk menghubungkan kartu. Aktifkan NFC sekarang?")
                .setPositiveButton("Pengaturan", (dialog, which) -> {
                    // Open NFC settings
                    startActivity(new Intent(Settings.ACTION_NFC_SETTINGS));
                })
                .setNegativeButton("Batal", (dialog, which) -> finish())
                .setCancelable(false)
                .show();
    }

    private void startNfcReading() {
        if (nfcAdapter != null && nfcAdapter.isEnabled()) {
            nfcAdapter.enableForegroundDispatch(this, pendingIntent, intentFiltersArray, techListsArray);
            Toast.makeText(this, "Siap membaca kartu NFC", Toast.LENGTH_SHORT).show();
        } else {
            showNfcSettingsDialog();
        }
    }

    private void stopNfcReading() {
        if (nfcAdapter != null) {
            nfcAdapter.disableForegroundDispatch(this);
            Toast.makeText(this, "Pembacaan kartu dihentikan", Toast.LENGTH_SHORT).show();
        }
    }
}
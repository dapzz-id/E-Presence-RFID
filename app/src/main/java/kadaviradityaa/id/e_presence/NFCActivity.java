package kadaviradityaa.id.e_presence;

import android.app.PendingIntent;
import android.content.ActivityNotFoundException;
import android.content.ClipboardManager;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.Uri;
import android.nfc.NfcAdapter;
import android.nfc.Tag;
import android.os.Bundle;
import android.provider.Settings;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.AppCompatButton;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.bumptech.glide.Glide;
import com.google.android.material.snackbar.Snackbar;

public class NFCActivity extends AppCompatActivity {
    private AppCompatButton btnCancel, btnComplete;
    private ImageView statusScan;
    private TextView txt_statusScan;
    private NfcAdapter nfcAdapter;
    private String uidCard;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_nfcactivity);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        initialization();
        LogicProgram();
    }

    @Override
    protected void onResume() {
        super.onResume();
        IntentFilter nfcIntent = new IntentFilter(NfcAdapter.ACTION_TAG_DISCOVERED);
        nfcAdapter.enableForegroundDispatch(this, PendingIntent.getActivity(this, 0, new Intent(this, getClass()), 0), new IntentFilter[]{nfcIntent}, null);
    }

    @Override
    protected void onPause() {
        super.onPause();
        nfcAdapter.disableForegroundDispatch(this);
    }

    private String bytesToHex(byte[] bytes) {
        StringBuilder stringBuilder = new StringBuilder();
        for (byte b : bytes) {
            stringBuilder.append(String.format("%02X", b));
        }
        return stringBuilder.toString();
    }

    private void initialization() {
        btnCancel = findViewById(R.id.btnCancel);
        btnComplete = findViewById(R.id.btnContinue);
        statusScan = findViewById(R.id.nfcLogo);
        txt_statusScan = findViewById(R.id.tvInstruction);
        nfcAdapter = NfcAdapter.getDefaultAdapter(this);

        if (nfcAdapter == null) {
            Snackbar.make(findViewById(android.R.id.content), "Perangkat Anda tidak mendukung fitur NFC", Snackbar.LENGTH_INDEFINITE).setAction("OK", view -> {
                finish();
            }).show();
        } else {
            if (!nfcAdapter.isEnabled()) {
                Snackbar.make(findViewById(android.R.id.content), "NFC Anda tidak aktif, silahkan aktifkan terlebih dahulu...", Snackbar.LENGTH_INDEFINITE).setAction("Aktifkan", view -> {
                    startActivity(new Intent(Settings.ACTION_NFC_SETTINGS));
                }).show();
            }
        }
    }

    private void LogicProgram(){
        btnCancel.setOnClickListener(v -> finish());
        if (NfcAdapter.ACTION_TAG_DISCOVERED.equals(getIntent().getAction())) {
            // NFC tag ditemukan, baca UID
            Tag tag = getIntent().getParcelableExtra(NfcAdapter.EXTRA_TAG);
            if (tag != null) {
                byte[] uid = tag.getId();
                uidCard = bytesToHex(uid);
                if(!uidCard.isEmpty()) {
                    txt_statusScan.setText("Kartu berhasil dibaca, silahkan klik Lanjutkan!");
                    Glide.with(this)
                            .load(R.drawable.ix_success_prf_filled)
                            .placeholder(R.drawable.ic_nfc)
                            .error(R.drawable.ic_sharp_prf_do_disturb_on)
                            .into(statusScan);
                }else{
                    txt_statusScan.setText("Tempelkan kartu pelajar Anda");
                    Glide.with(this)
                            .load(R.drawable.ic_nfc)
                            .placeholder(R.drawable.ic_nfc)
                            .error(R.drawable.ic_sharp_prf_do_disturb_on)
                            .into(statusScan);
                }
                ClipboardManager clipboard = (ClipboardManager) getSystemService(Context.CLIPBOARD_SERVICE);
                if (clipboard != null) {
                    android.content.ClipData clip = android.content.ClipData.newPlainText("UID Kartu: ", uidCard);
                    clipboard.setPrimaryClip(clip);

                    Snackbar.make(findViewById(android.R.id.content), "UID Kartu disalin ke clipboard!", Snackbar.LENGTH_SHORT).show();
                }
            }
        }
    }
}
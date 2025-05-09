package kadaviradityaa.id.e_presence;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.content.ClipData;
import android.content.ClipboardManager;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Color;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.provider.MediaStore;
import android.provider.OpenableColumns;
import android.util.Log;
import android.view.View;
import android.view.WindowInsetsController;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.HttpHeaderParser;
import com.android.volley.toolbox.Volley;
import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.chip.Chip;
import com.google.android.material.chip.ChipGroup;
import com.google.android.material.textfield.TextInputEditText;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.nio.charset.StandardCharsets;
import java.text.SimpleDateFormat;
import java.time.LocalDate;
import java.time.ZoneId;
import java.time.temporal.ChronoUnit;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.Objects;

import kadaviradityaa.id.e_presence.Library.Module;

public class LeaveFormActivity extends AppCompatActivity {

    private TextInputEditText startDateEditText, endDateEditText, reasonEditText;
    private RecyclerView attachmentRecyclerView;
    private View emptyAttachmentView;
    private TextView durationSummary;
    private String leaveType = "Sakit";
    private final List<Uri> attachments = new ArrayList<>();
    private AttachmentAdapter attachmentAdapter;
    private RequestQueue requestQueue;
    private MaterialButton btnSubmit;

    private String nisku;

    private final ActivityResultLauncher<Intent> attachmentLauncher = registerForActivityResult(
            new ActivityResultContracts.StartActivityForResult(),
            result -> {
                if (result.getResultCode() == RESULT_OK && result.getData() != null) {
                    handleAttachmentResult(result.getData());
                }
            });

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);
        setContentView(R.layout.activity_leave_form);

        // Initialize Volley RequestQueue
        requestQueue = Volley.newRequestQueue(this);

        // Window configuration
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

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
            Objects.requireNonNull(getWindow().getInsetsController()).setSystemBarsAppearance(
                    WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS,
                    WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS
            );
        } else {
            getWindow().getDecorView().setSystemUiVisibility(View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR);
        }

        nisku = getIntent().getStringExtra("nis");

        // Initialize views
        MaterialToolbar toolbar = findViewById(R.id.toolbar);
        ChipGroup leaveTypeChipGroup = findViewById(R.id.leaveTypeChipGroup);
        startDateEditText = findViewById(R.id.startDate);
        endDateEditText = findViewById(R.id.endDate);
        reasonEditText = findViewById(R.id.reason);
        btnSubmit = findViewById(R.id.btnSubmit);
        MaterialButton btnAddAttachment = findViewById(R.id.btnAddAttachment);
        attachmentRecyclerView = findViewById(R.id.attachmentRecyclerView);
        emptyAttachmentView = findViewById(R.id.emptyAttachmentView);
        durationSummary = findViewById(R.id.durationSummary);

        // Setup toolbar
        toolbar.setNavigationOnClickListener(v -> finish());

        // Setup leave type chips
        leaveTypeChipGroup.setOnCheckedChangeListener((group, checkedId) -> {
            Chip chip = group.findViewById(checkedId);
            if (chip != null) {
                leaveType = chip.getText().toString();
            }
        });

        // Setup date pickers
        setupDatePickers();

        // Setup attachments
        setupAttachmentRecyclerView();

        // Setup submit button
        btnSubmit.setOnClickListener(v -> submitLeaveRequest());

        // Setup add attachment button
        btnAddAttachment.setOnClickListener(v -> showAttachmentSourceDialog());
    }

    private void setupDatePickers() {
        final Calendar calendar = Calendar.getInstance();

        // Start date picker
        startDateEditText.setOnClickListener(v -> new DatePickerDialog(
                LeaveFormActivity.this,
                (view, year, month, dayOfMonth) -> {
                    calendar.set(year, month, dayOfMonth);
                    SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
                    startDateEditText.setText(sdf.format(calendar.getTime()));

                    // Reset end date if it's before the new start date
                    if (!endDateEditText.getText().toString().isEmpty()) {
                        try {
                            Date startDate = sdf.parse(startDateEditText.getText().toString());
                            Date endDate = sdf.parse(endDateEditText.getText().toString());
                            if (endDate.before(startDate)) {
                                endDateEditText.setText("");
                            }
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                    }
                    calculateDuration();
                },
                calendar.get(Calendar.YEAR),
                calendar.get(Calendar.MONTH),
                calendar.get(Calendar.DAY_OF_MONTH)
        ).show());

        // End date picker with min date validation
        endDateEditText.setOnClickListener(v -> {
            if (startDateEditText.getText().toString().isEmpty()) {
                Toast.makeText(this, "Harap pilih tanggal mulai terlebih dahulu", Toast.LENGTH_SHORT).show();
                return;
            }

            try {
                SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
                Date startDate = sdf.parse(startDateEditText.getText().toString());

                DatePickerDialog dialog = new DatePickerDialog(
                        LeaveFormActivity.this,
                        (view, year, month, dayOfMonth) -> {
                            calendar.set(year, month, dayOfMonth);
                            endDateEditText.setText(sdf.format(calendar.getTime()));
                            calculateDuration();
                        },
                        calendar.get(Calendar.YEAR),
                        calendar.get(Calendar.MONTH),
                        calendar.get(Calendar.DAY_OF_MONTH)
                );

                // Set min date to start date
                dialog.getDatePicker().setMinDate(startDate.getTime());
                dialog.show();
            } catch (Exception e) {
                e.printStackTrace();
            }
        });
    }

    @SuppressLint("SetTextI18n")
    private void calculateDuration() {
        try {
            SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
            Date start = sdf.parse(startDateEditText.getText().toString());
            Date end = sdf.parse(endDateEditText.getText().toString());

            // Using Java 8+ time API for more accurate calculation
            LocalDate startDate = start.toInstant().atZone(ZoneId.systemDefault()).toLocalDate();
            LocalDate endDate = end.toInstant().atZone(ZoneId.systemDefault()).toLocalDate();

            long days = ChronoUnit.DAYS.between(startDate, endDate) + 1; // +1 to include both start and end dates

            durationSummary.setText(String.format(Locale.getDefault(), "Durasi: %d hari", days));
        } catch (Exception e) {
            durationSummary.setText("Durasi: 0 hari");
        }
    }

    @SuppressLint("NotifyDataSetChanged")
    private void setupAttachmentRecyclerView() {
        attachmentAdapter = new AttachmentAdapter(attachments, uri -> {
            // Handle attachment removal
            attachments.remove(uri);
            updateAttachmentVisibility();
            attachmentAdapter.notifyDataSetChanged();
        });

        attachmentRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        attachmentRecyclerView.setAdapter(attachmentAdapter);
        updateAttachmentVisibility();
    }

    private void updateAttachmentVisibility() {
        if (attachments.isEmpty()) {
            attachmentRecyclerView.setVisibility(View.GONE);
            emptyAttachmentView.setVisibility(View.VISIBLE);
        } else {
            attachmentRecyclerView.setVisibility(View.VISIBLE);
            emptyAttachmentView.setVisibility(View.GONE);
        }
    }

    private void showAttachmentSourceDialog() {
        String[] options = {"Galeri", "File", "Kamera", "Video"};

        new AlertDialog.Builder(this)
                .setTitle("Pilih Sumber Lampiran")
                .setItems(options, (dialog, which) -> {
                    switch (which) {
                        case 0: openGallery(); break;
                        case 1: openFilePicker(); break;
                        case 2: openCamera(); break;
                        case 3: openVideoPicker(); break;
                    }
                })
                .show();
    }

    @SuppressLint("IntentReset")
    private void openGallery() {
        Intent intent = new Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
        intent.setType("image/*");
        intent.putExtra(Intent.EXTRA_ALLOW_MULTIPLE, false);
        attachmentLauncher.launch(intent);
    }

    private void openFilePicker() {
        Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
        intent.setType("*/*");
        intent.putExtra(Intent.EXTRA_ALLOW_MULTIPLE, false);
        intent.addCategory(Intent.CATEGORY_OPENABLE);
        attachmentLauncher.launch(intent);
    }

    @SuppressLint("QueryPermissionsNeeded")
    private void openCamera() {
        Intent intent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        if (intent.resolveActivity(getPackageManager()) != null) {
            attachmentLauncher.launch(intent);
        } else {
            Toast.makeText(this, "Tidak ada aplikasi kamera yang tersedia", Toast.LENGTH_SHORT).show();
        }
    }

    private void openVideoPicker() {
        Intent intent = new Intent(Intent.ACTION_PICK, MediaStore.Video.Media.EXTERNAL_CONTENT_URI);
        intent.setType("video/*");
        intent.putExtra(Intent.EXTRA_ALLOW_MULTIPLE, false);
        attachmentLauncher.launch(intent);
    }

    @SuppressLint("NotifyDataSetChanged")
    private void handleAttachmentResult(Intent data) {
        if (data.getData() != null) {
            Uri uri = data.getData();
            if (uri != null) {
                // Clear previous attachments and add new one
                attachments.clear();
                attachments.add(uri);
                updateAttachmentVisibility();
                attachmentAdapter.notifyDataSetChanged();
            }
        } else if (data.getExtras() != null && data.getExtras().get("data") != null) {
            // Handle camera result
            Toast.makeText(this, "Foto dari kamera perlu disimpan terlebih dahulu", Toast.LENGTH_LONG).show();
        }
    }

    private void submitLeaveRequest() {
        String startDate = startDateEditText.getText().toString().trim();
        String endDate = endDateEditText.getText().toString().trim();
        String reason = reasonEditText.getText().toString().trim();

        // Validate all fields
        if (startDate.isEmpty()) {
            Toast.makeText(this, "Harap pilih tanggal mulai", Toast.LENGTH_SHORT).show();
            return;
        }

        if (endDate.isEmpty()) {
            Toast.makeText(this, "Harap pilih tanggal akhir", Toast.LENGTH_SHORT).show();
            return;
        }

        if (reason.isEmpty()) {
            Toast.makeText(this, "Harap masukkan alasan izin/sakit", Toast.LENGTH_SHORT).show();
            return;
        }

        if (leaveType.isEmpty()) {
            Toast.makeText(this, "Harap pilih jenis izin", Toast.LENGTH_SHORT).show();
            return;
        }

        if (attachments.isEmpty()) {
            Toast.makeText(this, "Harap tambahkan lampiran terlebih dahulu", Toast.LENGTH_SHORT).show();
            return;
        }

        calculateDuration();
        showSubmissionConfirmation(startDate, endDate, reason);
    }

    private void showSubmissionConfirmation(String startDate, String endDate, String reason) {
        AlertDialog dialog = new AlertDialog.Builder(this)
                .setTitle("Konfirmasi Pengajuan")
                .setMessage("Apakah Anda yakin ingin mengajukan izin ini?")
                .setPositiveButton("Ya", (d, which) -> {
                    uploadLeaveRequest(startDate, endDate, reason);
                    btnSubmit.setEnabled(false);
                })
                .setNegativeButton("Tidak", null)
                .show();

        dialog.getButton(AlertDialog.BUTTON_POSITIVE).setTextColor(Color.WHITE);
        dialog.getButton(AlertDialog.BUTTON_NEGATIVE).setTextColor(Color.WHITE);
    }

    private void uploadLeaveRequest(String startDate, String endDate, String reason) {
        try {
            if (attachments.isEmpty()) {
                Toast.makeText(this, "Tidak ada lampiran yang dipilih", Toast.LENGTH_SHORT).show();
                btnSubmit.setEnabled(true);
                return;
            }

            Uri fileUri = attachments.get(0);
            String fileName = getFileName(fileUri);
            String mimeType = getContentResolver().getType(fileUri);

            // Validasi tipe file
            if (mimeType == null ||
                    (!mimeType.startsWith("image/") &&
                            !mimeType.equals("application/pdf") &&
                            !mimeType.startsWith("video/"))) {
                Toast.makeText(this, "Format file tidak didukung", Toast.LENGTH_SHORT).show();
                btnSubmit.setEnabled(true);
                return;
            }

            InputStream inputStream = getContentResolver().openInputStream(fileUri);
            byte[] fileData = getBytes(inputStream);

            String url = Module.urlKoneksi + "api/send-leave-document";
            VolleyMultipartRequest multipartRequest = new VolleyMultipartRequest(
                    Request.Method.POST,
                    url,
                    response -> {
                        try {
                            String responseString = new String(response.data, StandardCharsets.UTF_8);
                            JSONObject jsonResponse = new JSONObject(responseString);

                            if (jsonResponse.getString("status").equals("success")) {
                                Toast.makeText(LeaveFormActivity.this,
                                        "Permohonan berhasil dikirim",
                                        Toast.LENGTH_SHORT).show();
                                clearForm();
                                finish();
                            } else {
                                String errorMsg = jsonResponse.optString("message", "Gagal mengirim permohonan");
                                Toast.makeText(LeaveFormActivity.this,
                                        errorMsg,
                                        Toast.LENGTH_SHORT).show();
                                btnSubmit.setEnabled(true);
                            }
                        } catch (JSONException e) {
                            Log.e("UploadError", "JSON parsing error", e);
                            Toast.makeText(LeaveFormActivity.this,
                                    "Error parsing response",
                                    Toast.LENGTH_SHORT).show();
                            btnSubmit.setEnabled(true);
                        }
                    },
                    error -> {
                        String errorMessage = "Terjadi kesalahan jaringan";
                        if (error.networkResponse != null && error.networkResponse.data != null) {
                            try {
                                errorMessage = new String(error.networkResponse.data, StandardCharsets.UTF_8);
                            } catch (Exception e) {
                                Log.e("UploadError", "Error parsing error response", e);
                            }
                        }

                        Toast.makeText(LeaveFormActivity.this,
                                errorMessage,
                                Toast.LENGTH_SHORT).show();
                        btnSubmit.setEnabled(true);
                    }
            ) {
                @Override
                protected Map<String, String> getParams() {
                    Map<String, String> params = new HashMap<>();
                    params.put("nis", nisku.trim());
                    params.put("type", leaveType.toLowerCase());
                    params.put("start_date", startDate);
                    params.put("end_date", endDate);
                    params.put("reason", reason);
                    return params;
                }

                @Override
                protected Map<String, DataPart> getByteData() {
                    Map<String, DataPart> params = new HashMap<>();
                    params.put("document", new DataPart(fileName, fileData, mimeType));
                    return params;
                }

                private String getBoundary() {
                    return "Boundary-" + System.currentTimeMillis();
                }
            };

            requestQueue.add(multipartRequest);

        } catch (IOException e) {
            Log.e("UploadError", "File error", e);
            Toast.makeText(this, "Error membaca file", Toast.LENGTH_SHORT).show();
        } catch (Exception e) {
            Log.e("UploadError", "Unexpected error", e);
            Toast.makeText(this, "Terjadi kesalahan", Toast.LENGTH_SHORT).show();
        }
    }

    private byte[] getBytes(InputStream inputStream) throws IOException {
        ByteArrayOutputStream byteBuffer = new ByteArrayOutputStream();
        byte[] buffer = new byte[1024];
        int len;
        while ((len = inputStream.read(buffer)) != -1) {
            byteBuffer.write(buffer, 0, len);
        }
        return byteBuffer.toByteArray();
    }

    @SuppressLint("Range")
    private String getFileName(Uri uri) {
        String result = null;
        if (uri.getScheme().equals("content")) {
            try (Cursor cursor = getContentResolver().query(uri, null, null, null, null)) {
                if (cursor != null && cursor.moveToFirst()) {
                    result = cursor.getString(cursor.getColumnIndex(OpenableColumns.DISPLAY_NAME));
                }
            }
        }
        if (result == null) {
            result = uri.getPath();
            int cut = result.lastIndexOf('/');
            if (cut != -1) {
                result = result.substring(cut + 1);
            }
        }
        return result;
    }

    @SuppressLint({"SetTextI18n", "NotifyDataSetChanged"})
    private void clearForm() {
        startDateEditText.setText("");
        endDateEditText.setText("");
        reasonEditText.setText("");
        durationSummary.setText("Durasi: 0 hari");
        leaveType = "Sakit";
        attachments.clear();
        updateAttachmentVisibility();
        attachmentAdapter.notifyDataSetChanged();

        // Clear chip selection
        ChipGroup leaveTypeChipGroup = findViewById(R.id.leaveTypeChipGroup);
        leaveTypeChipGroup.clearCheck();
    }

    private static class VolleyMultipartRequest extends Request<NetworkResponse> {
        private static final String LINE_FEED = "\r\n";
        private final Response.Listener<NetworkResponse> mListener;
        private final Response.ErrorListener mErrorListener;
        private final Map<String, DataPart> mByteData;
        private final Map<String, String> mParams;
        private final String mBoundary;

        public VolleyMultipartRequest(int method, String url,
                                      Response.Listener<NetworkResponse> listener,
                                      Response.ErrorListener errorListener) {
            super(method, url, errorListener);
            mListener = listener;
            mErrorListener = errorListener;
            mByteData = new HashMap<>();
            mParams = new HashMap<>();
            mBoundary = "Boundary-" + System.currentTimeMillis();
        }

        @Override
        protected Map<String, String> getParams() {
            return mParams;
        }

        protected Map<String, DataPart> getByteData() {
            return mByteData;
        }

        @Override
        public String getBodyContentType() {
            return "multipart/form-data;boundary=" + mBoundary;
        }

        @Override
        public byte[] getBody() {
            ByteArrayOutputStream bos = new ByteArrayOutputStream();
            DataOutputStream dos = new DataOutputStream(bos);

            try {
                // Add text parameters
                Map<String, String> params = getParams();
                if (params != null && !params.isEmpty()) {
                    for (Map.Entry<String, String> entry : params.entrySet()) {
                        buildTextPart(dos, entry.getKey(), entry.getValue());
                    }
                }

                // Add file data
                Map<String, DataPart> data = getByteData();
                if (data != null && !data.isEmpty()) {
                    for (Map.Entry<String, DataPart> entry : data.entrySet()) {
                        buildDataPart(dos, entry.getValue(), entry.getKey());
                    }
                }

                // End of multipart
                dos.writeBytes("--" + mBoundary + "--" + LINE_FEED);
                return bos.toByteArray();
            } catch (IOException e) {
                e.printStackTrace();
            }
            return null;
        }

        private void buildTextPart(DataOutputStream dataOutputStream, String parameterName, String parameterValue) throws IOException {
            dataOutputStream.writeBytes("--" + mBoundary + LINE_FEED);
            dataOutputStream.writeBytes("Content-Disposition: form-data; name=\"" + parameterName + "\"" + LINE_FEED);
            dataOutputStream.writeBytes("Content-Type: text/plain; charset=UTF-8" + LINE_FEED);
            dataOutputStream.writeBytes(LINE_FEED);
            dataOutputStream.writeBytes(parameterValue + LINE_FEED);
        }

        private void buildDataPart(DataOutputStream dataOutputStream, DataPart dataFile, String inputName) throws IOException {
            dataOutputStream.writeBytes("--" + mBoundary + LINE_FEED);
            dataOutputStream.writeBytes("Content-Disposition: form-data; name=\"" + inputName + "\"; filename=\"" + dataFile.getFileName() + "\"" + LINE_FEED);
            dataOutputStream.writeBytes("Content-Type: " + dataFile.getType() + LINE_FEED);
            dataOutputStream.writeBytes("Content-Transfer-Encoding: binary" + LINE_FEED);
            dataOutputStream.writeBytes(LINE_FEED);

            dataOutputStream.write(dataFile.getContent());
            dataOutputStream.writeBytes(LINE_FEED);
        }

        @Override
        protected Response<NetworkResponse> parseNetworkResponse(NetworkResponse response) {
            return Response.success(response, HttpHeaderParser.parseCacheHeaders(response));
        }

        @Override
        protected void deliverResponse(NetworkResponse response) {
            mListener.onResponse(response);
        }

        @Override
        public void deliverError(VolleyError error) {
            mErrorListener.onErrorResponse(error);
        }
    }

    private static class DataPart {
        private final String fileName;
        private final byte[] content;
        private final String type;

        public DataPart(String fileName, byte[] content, String type) {
            this.fileName = fileName;
            this.content = content;
            this.type = type;
        }

        public String getFileName() {
            return fileName;
        }

        public byte[] getContent() {
            return content;
        }

        public String getType() {
            return type;
        }
    }
}
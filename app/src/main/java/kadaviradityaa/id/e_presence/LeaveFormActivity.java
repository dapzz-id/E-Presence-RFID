package kadaviradityaa.id.e_presence;

import android.app.DatePickerDialog;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;
import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.google.android.material.appbar.MaterialToolbar;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.chip.Chip;
import com.google.android.material.chip.ChipGroup;
import com.google.android.material.textfield.TextInputEditText;

import java.text.SimpleDateFormat;
import java.time.LocalDate;
import java.time.ZoneId;
import java.time.temporal.ChronoUnit;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.Locale;

public class LeaveFormActivity extends AppCompatActivity {

    private TextInputEditText startDateEditText, endDateEditText, reasonEditText;
    private RecyclerView attachmentRecyclerView;
    private View emptyAttachmentView;
    private TextView durationSummary;
    private String leaveType = "Sakit";
    private List<Uri> attachments = new ArrayList<>();
    private AttachmentAdapter attachmentAdapter;

    // Activity result launcher for attachments
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
        setContentView(R.layout.activity_leave_form);

        // Initialize views
        MaterialToolbar toolbar = findViewById(R.id.toolbar);
        ChipGroup leaveTypeChipGroup = findViewById(R.id.leaveTypeChipGroup);
        startDateEditText = findViewById(R.id.startDate);
        endDateEditText = findViewById(R.id.endDate);
        reasonEditText = findViewById(R.id.reason);
        MaterialButton btnSubmit = findViewById(R.id.btnSubmit);
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

        // Setup date pickers with improved validation
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
        startDateEditText.setOnClickListener(v -> {
            new DatePickerDialog(
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
            ).show();
        });

        // End date picker with min date validation
        endDateEditText.setOnClickListener(v -> {
            if (startDateEditText.getText().toString().isEmpty()) {
                Toast.makeText(this, "Please select start date first", Toast.LENGTH_SHORT).show();
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
        String[] options = {"Galeri", "File", "Kamera"};

        new AlertDialog.Builder(this)
                .setTitle("Pilih Sumber Lampiran")
                .setItems(options, (dialog, which) -> {
                    switch (which) {
                        case 0: openGallery(); break;
                        case 1: openFilePicker(); break;
                        case 2: openCamera(); break;
                    }
                })
                .show();
    }

    private void openGallery() {
        Intent intent = new Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
        intent.putExtra(Intent.EXTRA_ALLOW_MULTIPLE, true);
        intent.setType("image/*");
        attachmentLauncher.launch(intent);
    }

    private void openFilePicker() {
        Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
        intent.setType("*/*");
        intent.putExtra(Intent.EXTRA_ALLOW_MULTIPLE, true);
        attachmentLauncher.launch(intent);
    }

    private void openCamera() {
        Intent intent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        if (intent.resolveActivity(getPackageManager()) != null) {
            attachmentLauncher.launch(intent);
        }
    }

    private void handleAttachmentResult(Intent data) {
        if (data.getClipData() != null) {
            // Multiple files selected
            for (int i = 0; i < data.getClipData().getItemCount(); i++) {
                attachments.add(data.getClipData().getItemAt(i).getUri());
            }
        } else if (data.getData() != null) {
            // Single file selected
            attachments.add(data.getData());
        }

        updateAttachmentVisibility();
        attachmentAdapter.notifyDataSetChanged();
    }

    private void submitLeaveRequest() {
        String startDate = startDateEditText.getText().toString().trim();
        String endDate = endDateEditText.getText().toString().trim();
        String reason = reasonEditText.getText().toString().trim();

        // Validate inputs
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

        // Calculate duration again to be sure
        calculateDuration();

        // TODO: Implement API call to Laravel backend
        // You'll need to create a method to upload the leave request with attachments
        // For now, just show a success message
        Toast.makeText(this, "Permohonan berhasil dikirim", Toast.LENGTH_SHORT).show();
    }
}
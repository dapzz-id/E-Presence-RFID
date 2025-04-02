package kadaviradityaa.id.e_presence;

import android.content.ClipData;
import android.content.ClipboardManager;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;
import androidx.appcompat.app.AppCompatActivity;

public class DebugActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_debug);

        TextView tvErrorMessage = findViewById(R.id.tv_error_message);
        TextView tvStackTrace = findViewById(R.id.tv_stack_trace);
        Button btnCopy = findViewById(R.id.btn_copy);

        String errorMessage = getIntent().getStringExtra("error_message");
        String stackTrace = getIntent().getStringExtra("stack_trace");

        tvErrorMessage.setText(errorMessage);
        tvStackTrace.setText(stackTrace);

        btnCopy.setOnClickListener(v -> {
            ClipboardManager clipboard = (ClipboardManager) getSystemService(Context.CLIPBOARD_SERVICE);
            ClipData clip = ClipData.newPlainText("Error Details", errorMessage + "\n\n" + stackTrace);
            clipboard.setPrimaryClip(clip);
        });
    }

    public static void startDebugActivity(Context context, Throwable throwable) {
        Intent intent = new Intent(context, DebugActivity.class);
        intent.putExtra("error_message", throwable.getMessage());
        intent.putExtra("stack_trace", getStackTraceString(throwable));
        context.startActivity(intent);
    }

    private static String getStackTraceString(Throwable throwable) {
        StringBuilder result = new StringBuilder();
        result.append("Device Info:\n");
        result.append("Model: ").append(Build.MODEL).append("\n");
        result.append("Brand: ").append(Build.BRAND).append("\n");
        result.append("SDK: ").append(Build.VERSION.SDK_INT).append("\n\n");
        result.append("Stack Trace:\n");

        for (StackTraceElement element : throwable.getStackTrace()) {
            result.append(element.toString()).append("\n");
        }
        return result.toString();
    }
}
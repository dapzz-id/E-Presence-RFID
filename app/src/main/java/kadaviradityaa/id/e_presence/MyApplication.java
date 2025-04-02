package kadaviradityaa.id.e_presence;

import android.app.Application;
import android.content.Intent;

public class MyApplication extends Application
{
    @Override
    public void onCreate() {
        super.onCreate();

        Thread.setDefaultUncaughtExceptionHandler((thread, throwable) -> {
            Intent intent = new Intent(getApplicationContext(), DebugActivity.class);
            intent.putExtra("error_message", throwable.getMessage());
            intent.putExtra("stack_trace", getStackTraceString(throwable));
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            startActivity(intent);

            android.os.Process.killProcess(android.os.Process.myPid());
            System.exit(1);
        });
    }

    private static String getStackTraceString(Throwable throwable) {
        StringBuilder result = new StringBuilder();
        result.append("Stack Trace:\n");

        for (StackTraceElement element : throwable.getStackTrace()) {
            result.append(element.toString()).append("\n");
        }
        return result.toString();
    }
}

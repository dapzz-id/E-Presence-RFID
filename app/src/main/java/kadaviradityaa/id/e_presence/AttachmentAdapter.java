package kadaviradityaa.id.e_presence;

import kadaviradityaa.id.e_presence.R;

import android.annotation.SuppressLint;
import android.content.Context;
import android.database.Cursor;
import android.net.Uri;
import android.provider.OpenableColumns;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.google.android.material.button.MaterialButton;

import java.util.List;

public class AttachmentAdapter extends RecyclerView.Adapter<AttachmentAdapter.ViewHolder> {
    private List<Uri> attachments;
    private OnRemoveClickListener onRemoveClickListener;

    public interface OnRemoveClickListener {
        void onRemoveClick(Uri uri);
    }

    public AttachmentAdapter(List<Uri> attachments, OnRemoveClickListener listener) {
        this.attachments = attachments;
        this.onRemoveClickListener = listener;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_attachment, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        Uri uri = attachments.get(position);

        // Set file name
        String fileName = getFileName(holder.itemView.getContext(), uri);
        holder.attachmentName.setText(fileName);

        // Set appropriate icon based on file type
        setFileIcon(holder.attachmentIcon, fileName);

        holder.removeButton.setOnClickListener(v -> {
            onRemoveClickListener.onRemoveClick(uri);
        });
    }

    @Override
    public int getItemCount() {
        return attachments.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        ImageView attachmentIcon;
        TextView attachmentName;
        MaterialButton removeButton; // Changed from ImageButton to MaterialButton

        public ViewHolder(@NonNull View itemView) {
            super(itemView);
            attachmentIcon = itemView.findViewById(R.id.attachmentIcon);
            attachmentName = itemView.findViewById(R.id.attachmentName);
            removeButton = itemView.findViewById(R.id.removeButton); // This matches your XML
        }
    }

    @SuppressLint("Range")
    private String getFileName(Context context, Uri uri) {
        String result = null;
        if (uri.getScheme().equals("content")) {
            try (Cursor cursor = context.getContentResolver().query(uri, null, null, null, null)) {
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

    private void setFileIcon(ImageView imageView, String fileName) {
        if (fileName == null) return;

        String extension = fileName.substring(fileName.lastIndexOf(".") + 1).toLowerCase();
        int iconRes;
        iconRes = R.drawable.ic_file;

        imageView.setImageResource(iconRes);
    }
}
package kadaviradityaa.id.e_presence;

import kadaviradityaa.id.e_presence.R;
import android.net.Uri;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import java.util.List;

public class AttachmentAdapter extends RecyclerView.Adapter<AttachmentAdapter.ViewHolder> {

    private final List<Uri> attachments;
    private final OnAttachmentClickListener listener;

    public interface OnAttachmentClickListener {
        void onAttachmentClick(Uri uri);
    }

    public AttachmentAdapter(List<Uri> attachments, OnAttachmentClickListener listener) {
        this.attachments = attachments;
        this.listener = listener;
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
        holder.bind(uri);
    }

    @Override
    public int getItemCount() {
        return attachments.size();
    }

    class ViewHolder extends RecyclerView.ViewHolder {
        private final ImageView attachmentIcon;
        private final TextView attachmentName;
        private final ImageButton removeButton;

        public ViewHolder(@NonNull View itemView) {
            super(itemView);
            attachmentIcon = itemView.findViewById(R.id.attachmentIcon);
            attachmentName = itemView.findViewById(R.id.attachmentName);
            removeButton = itemView.findViewById(R.id.removeButton);
        }

        public void bind(Uri uri) {
            attachmentName.setText(uri.getLastPathSegment());

            removeButton.setOnClickListener(v -> {
                if (listener != null) {
                    listener.onAttachmentClick(uri);
                }
            });
        }
    }
}
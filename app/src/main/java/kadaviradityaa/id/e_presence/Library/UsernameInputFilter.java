package kadaviradityaa.id.e_presence.Library;

import android.text.InputFilter;
import android.text.Spanned;

public class UsernameInputFilter implements InputFilter {
    @Override
    public CharSequence filter(CharSequence source, int start, int end, Spanned dest, int dstart, int dend) {
        // Hanya mengizinkan huruf (a-z, A-Z) dan angka (0-9)
        String regex = "^[a-zA-Z0-9]+$";

        for (int i = start; i < end; i++) {
            char character = source.charAt(i);
            if (!String.valueOf(character).matches(regex)) {
                return "";
            }
        }
        return null;
    }
}

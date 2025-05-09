package kadaviradityaa.id.e_presence.Library;

import android.content.Context;

import com.android.volley.AuthFailureError;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.toolbox.JsonArrayRequest;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;

public class Module {

    public static String urlKoneksi = "http://urlmu:8000/";
    private static RequestQueue requestQueue;

    public static void init(Context context) {
        if (requestQueue == null) {
            requestQueue = Volley.newRequestQueue(context.getApplicationContext());
        }
    }

    // GET Object
    public static void getObject(Context context, String url, Response.Listener<JSONObject> listener, Response.ErrorListener errorListener) {
        JsonObjectRequest request = new JsonObjectRequest(Request.Method.GET, url, null, listener, errorListener);
        requestQueue.add(request);
    }

    public static void getObjectWithToken(Context context, String url, String token, Response.Listener<JSONObject> listener, Response.ErrorListener errorListener) {
        JsonObjectRequest request = new JsonObjectRequest(Request.Method.GET, url, null, listener, errorListener){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                Map<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + token);
                return headers;
            }
        };
        requestQueue.add(request);
    }

    // GET Array
    public static void getArray(Context context, String url, Response.Listener<JSONArray> listener, Response.ErrorListener errorListener) {
        JsonArrayRequest request = new JsonArrayRequest(Request.Method.GET, url, null, listener, errorListener);
        requestQueue.add(request);
    }

    // POST Object
    public static void postObject(Context context, String url, JSONObject data, Response.Listener<JSONObject> listener, Response.ErrorListener errorListener) {
        JsonObjectRequest request = new JsonObjectRequest(Request.Method.POST, url, data, listener, errorListener);
        requestQueue.add(request);
    }

    // POST Array
    public static void postArray(Context context, String url, JSONArray data, Response.Listener<JSONArray> listener, Response.ErrorListener errorListener) {
        JsonArrayRequest request = new JsonArrayRequest(Request.Method.POST, url, data, listener, errorListener);
        requestQueue.add(request);
    }

    // PUT Object
    public static void patchObjectWithToken(Context context, String url, String token, JSONObject data, Response.Listener<JSONObject> listener, Response.ErrorListener errorListener) {
        JsonObjectRequest request = new JsonObjectRequest(Request.Method.PATCH, url, data, listener, errorListener){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                Map<String, String> headers = new HashMap<>();
                headers.put("Authorization", "Bearer " + token);
                return headers;
            }
        };
        requestQueue.add(request);
    }

    // PUT Array
    public static void putArray(Context context, String url, JSONArray data, Response.Listener<JSONArray> listener, Response.ErrorListener errorListener) {
        JsonArrayRequest request = new JsonArrayRequest(Request.Method.PUT, url, data, listener, errorListener);
        requestQueue.add(request);
    }

    // DELETE Object/Array
    public static void deleteRequest(Context context, String url, Response.Listener<String> listener, Response.ErrorListener errorListener) {
        StringRequest request = new StringRequest(Request.Method.DELETE, url, listener, errorListener);
        requestQueue.add(request);
    }
}


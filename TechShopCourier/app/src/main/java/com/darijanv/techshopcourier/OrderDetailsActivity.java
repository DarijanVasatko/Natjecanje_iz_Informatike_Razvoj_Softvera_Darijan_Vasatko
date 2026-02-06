package com.darijanv.techshopcourier;

import android.graphics.Bitmap;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ProgressBar;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;

import com.google.android.material.chip.Chip;

import java.io.File;
import java.io.FileOutputStream;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class OrderDetailsActivity extends AppCompatActivity {

    public static final String EXTRA_ORDER_ID = "order_id";

    private ApiService api;
    private int orderId;

    private TextView tvHeader, tvMeta, tvAddress, tvItems;
    private Chip chipStatus;
    private Button btnDelivered, btnNotDelivered;
    private ProgressBar progress;

    private ScrollView scrollView;
    private View cardSignature;
    private SignatureView signatureView;
    private Button btnClearSignature, btnConfirmDelivered;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_details);

        ToolbarUtil.setup(this, "Courier", true);

        orderId = getIntent().getIntExtra(EXTRA_ORDER_ID, -1);
        if (orderId == -1) {
            finish();
            return;
        }

        api = ApiClient.getClient().create(ApiService.class);

        scrollView = findViewById(R.id.scrollView);

        tvHeader = findViewById(R.id.tvHeader);
        tvMeta = findViewById(R.id.tvMeta);
        tvAddress = findViewById(R.id.tvAddress);
        tvItems = findViewById(R.id.tvItems);
        chipStatus = findViewById(R.id.chipStatus);

        btnDelivered = findViewById(R.id.btnDelivered);
        btnNotDelivered = findViewById(R.id.btnNotDelivered);
        progress = findViewById(R.id.progress);

        cardSignature = findViewById(R.id.cardSignature);
        signatureView = findViewById(R.id.signatureView);
        btnClearSignature = findViewById(R.id.btnClearSignature);
        btnConfirmDelivered = findViewById(R.id.btnConfirmDelivered);


        btnDelivered.setBackgroundColor(ContextCompat.getColor(this, R.color.action_delivered_bg));
        btnDelivered.setTextColor(ContextCompat.getColor(this, R.color.action_delivered_text));
        btnNotDelivered.setBackgroundColor(ContextCompat.getColor(this, R.color.action_failed_bg));
        btnNotDelivered.setTextColor(ContextCompat.getColor(this, R.color.action_failed_text));


        btnDelivered.setOnClickListener(v -> {
            android.content.Intent i = new android.content.Intent(this, ConfirmDeliveryActivity.class);
            i.putExtra(ConfirmDeliveryActivity.EXTRA_ORDER_ID, orderId);
            i.putExtra(ConfirmDeliveryActivity.EXTRA_ORDER_TITLE, "Order #" + orderId);
            startActivityForResult(i, 2001);
        });



        btnClearSignature.setOnClickListener(v -> signatureView.clear());


        btnConfirmDelivered.setOnClickListener(v -> {
            if (!signatureView.hasSigned()) {
                Toast.makeText(this, "Please sign first.", Toast.LENGTH_SHORT).show();
                return;
            }


            saveSignatureToCache();

            updateStatusDelivered(orderId);
        });


        btnNotDelivered.setOnClickListener(v -> updateStatusNotDelivered(orderId));

        loadDetails(orderId);
    }

    private void setLoading(boolean loading) {
        progress.setVisibility(loading ? View.VISIBLE : View.GONE);
        btnDelivered.setEnabled(!loading);
        btnNotDelivered.setEnabled(!loading);
        btnConfirmDelivered.setEnabled(!loading);
        btnClearSignature.setEnabled(!loading);
    }

    private void loadDetails(int id) {
        setLoading(true);

        api.getOrderDetails(id).enqueue(new Callback<OrderDetailsResponse>() {
            @Override
            public void onResponse(@NonNull Call<OrderDetailsResponse> call,
                                   @NonNull Response<OrderDetailsResponse> response) {
                setLoading(false);

                if (!response.isSuccessful() || response.body() == null || response.body().data == null) {
                    Toast.makeText(OrderDetailsActivity.this, "API error: " + response.code(), Toast.LENGTH_SHORT).show();
                    return;
                }

                OrderDetails d = response.body().data;

                tvHeader.setText("Order #" + d.id);
                tvMeta.setText(d.total + " € • " + d.created_at);
                tvAddress.setText("Address: " + (d.address == null ? "-" : d.address));

                String status = d.status == null ? "Unknown" : d.status;
                chipStatus.setText(status);
                styleStatusChip(status);

                StringBuilder sb = new StringBuilder();
                if (d.items != null) {
                    for (OrderItem it : d.items) {
                        sb.append("• ")
                                .append(it.name == null ? "(unknown)" : it.name)
                                .append("  x").append(it.qty)
                                .append("  (").append(it.price).append(" €)")
                                .append("\n");
                    }
                }
                tvItems.setText(sb.length() == 0 ? "No items." : sb.toString());
            }

            @Override
            public void onFailure(@NonNull Call<OrderDetailsResponse> call, @NonNull Throwable t) {
                setLoading(false);
                Toast.makeText(OrderDetailsActivity.this, "Request failed: " + t.getMessage(), Toast.LENGTH_LONG).show();
            }
        });
    }

    private void styleStatusChip(String status) {
        String s = status.toLowerCase();

        if (s.contains("neusp") || s.contains("nije")) {
            chipStatus.setChipBackgroundColorResource(R.color.status_failed_bg);
            chipStatus.setTextColor(ContextCompat.getColor(this, R.color.status_failed_text));
        } else if (s.contains("obradi") || s.contains("na dostavi") || s.contains("pla")) {
            chipStatus.setChipBackgroundColorResource(R.color.status_active_bg);
            chipStatus.setTextColor(ContextCompat.getColor(this, R.color.status_active_text));
        } else {
            chipStatus.setChipBackgroundColorResource(R.color.status_default_bg);
            chipStatus.setTextColor(ContextCompat.getColor(this, R.color.status_default_text));
        }
    }

    private void updateStatusDelivered(int id) {
        setLoading(true);

        api.markDelivered(id).enqueue(new Callback<SimpleResponse>() {
            @Override
            public void onResponse(@NonNull Call<SimpleResponse> call,
                                   @NonNull Response<SimpleResponse> response) {
                setLoading(false);

                if (!response.isSuccessful() || response.body() == null) {
                    Toast.makeText(OrderDetailsActivity.this, "API error: " + response.code(), Toast.LENGTH_SHORT).show();
                    return;
                }

                Toast.makeText(OrderDetailsActivity.this, response.body().message, Toast.LENGTH_SHORT).show();
                setResult(RESULT_OK);
                finish();
            }

            @Override
            public void onFailure(@NonNull Call<SimpleResponse> call, @NonNull Throwable t) {
                setLoading(false);
                Toast.makeText(OrderDetailsActivity.this, "Request failed: " + t.getMessage(), Toast.LENGTH_LONG).show();
            }
        });
    }

    private void updateStatusNotDelivered(int id) {
        setLoading(true);

        api.markNotDelivered(id).enqueue(new Callback<SimpleResponse>() {
            @Override
            public void onResponse(@NonNull Call<SimpleResponse> call,
                                   @NonNull Response<SimpleResponse> response) {
                setLoading(false);

                if (!response.isSuccessful() || response.body() == null) {
                    Toast.makeText(OrderDetailsActivity.this, "API error: " + response.code(), Toast.LENGTH_SHORT).show();
                    return;
                }

                Toast.makeText(OrderDetailsActivity.this, response.body().message, Toast.LENGTH_SHORT).show();
                setResult(RESULT_OK);
                finish();
            }

            @Override
            public void onFailure(@NonNull Call<SimpleResponse> call, @NonNull Throwable t) {
                setLoading(false);
                Toast.makeText(OrderDetailsActivity.this, "Request failed: " + t.getMessage(), Toast.LENGTH_LONG).show();
            }
        });
    }

    private File saveSignatureToCache() {
        try {
            Bitmap bmp = signatureView.getSignatureBitmapCopy();
            if (bmp == null) return null;

            File file = new File(getCacheDir(), "signature_order_" + orderId + ".png");
            try (FileOutputStream out = new FileOutputStream(file)) {
                bmp.compress(Bitmap.CompressFormat.PNG, 100, out);
            }
            return file;
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }
    @Override
    protected void onActivityResult(int requestCode, int resultCode, android.content.Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == 2001 && resultCode == RESULT_OK) {
            setResult(RESULT_OK);
            finish();
        }
    }

}

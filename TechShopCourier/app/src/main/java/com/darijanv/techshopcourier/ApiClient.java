package com.darijanv.techshopcourier;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class ApiClient {

    private static Retrofit retrofit = null;

    public static Retrofit getClient() {
        if (retrofit == null) {
            retrofit = new Retrofit.Builder()

                    // Example local:
                    // .baseUrl("http://10.0.2.2:8000/")
                    // Example production:
                    // .baseUrl("https://your-domain.com/")
                    //.baseUrl("http://192.168.50.219:8000/")
                    .baseUrl("http://192.168.50.219:8000/")

                    .addConverterFactory(GsonConverterFactory.create())
                    .build();
        }
        return retrofit;
    }
}



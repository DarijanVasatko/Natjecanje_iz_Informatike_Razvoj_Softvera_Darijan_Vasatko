package com.darijanv.techshopcourier;


public class OrderResponse {
    public boolean has_new_order;
    public Order order;

    public static class Order {
        public int id;
        public double total;
        public String created_at;
        public String address;
        public String status;
    }
}

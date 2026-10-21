# Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o{ ORDERS : makes
    FILMS ||--o{ SHOWTIMES : scheduled_as
    FILMS ||--o{ ORDER_DETAILS : ticket_for
    SHOWTIMES ||--o{ ORDER_DETAILS : booked_for
    ORDERS ||--|{ ORDER_DETAILS : contains

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role
        datetime created_at
    }
    FILMS {
        bigint id PK
        string name
        enum category
        integer price
        text description
        string image
    }
    SHOWTIMES {
        bigint id PK
        bigint film_id FK
        datetime starts_at
        string hall
    }
    ORDERS {
        bigint id PK
        bigint user_id FK
        string customer_name
        string seat_number
        integer total_price
        string status
    }
    ORDER_DETAILS {
        bigint id PK
        bigint order_id FK
        bigint film_id FK
        bigint showtime_id FK
        integer quantity
        integer price
    }
```

## Relasi dan aturan

- Satu film memiliki banyak jadwal tayang dan banyak baris detail tiket.
- Satu pesanan memiliki satu atau lebih detail tiket.
- Detail tiket menunjuk film dan, untuk pesanan baru, jadwal tayang tertentu.
- `order_details.price` menyimpan harga satu tiket saat transaksi, sehingga laporan historis tidak berubah ketika harga film diubah.
- `orders.user_id` dapat kosong untuk pesanan lama yang dibuat sebelum akun penonton terhubung; jika akun dihapus, pesanan tetap disimpan dan referensi akun dibuat kosong.
- Peran akun disimpan pada `users.role`: `admin` atau `user`. Pendaftaran publik selalu membuat peran `user`.

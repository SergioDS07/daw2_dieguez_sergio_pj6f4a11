# EuroCulture Order Manager

PJ6 – Back-End amb tecnologia PHP i gestors de continguts
CFGS Desenvolupament d'Aplicacions Web · Escola del Clot · Curs 2026-27

An e-commerce order management application for a European chain of cultural
shops (museum and theatre tickets, books and art merchandise). Sales reps can
log in, register new orders and look up orders already placed.

## 1. Features

1. **Start screen** – application logo, name and access buttons.
2. **Description & documentation** – explains the app and every operation.
3. **Authentication** – user/password login validated on the server.
4. **Operations menu** – choose between creating or viewing orders.
5. **Create a new order** – order code, customer data and up to 4 products;
   the server calculates the price with 21% VAT and stores the order.
6. **View all orders** – table with every stored order.
7. **View one order** – search an order by its code.

Client-side (HTML/CSS/JavaScript) only collects input and displays results.
All storage and calculations happen server-side (PHP), using the JSON files
in `orders/` and `users/`.

## 2. Project structure

```
orderManagementApp/
├── application/
│   ├── client/              → HTML, CSS and JavaScript (user interface)
│   │   ├── css/style.css
│   │   ├── js/               (login.js, menu.js, newOrder.js, viewOrder.js)
│   │   ├── index.html
│   │   ├── documentation.html
│   │   ├── login.html
│   │   ├── menu.html
│   │   ├── newOrder.html
│   │   ├── viewOrders.html
│   │   └── viewOrder.html
│   └── server/               → PHP (business logic, no HTML/CSS)
│       ├── functions.php
│       ├── login.php
│       ├── createOrder.php
│       ├── getAllOrders.php
│       └── getOrder.php
├── orders/
│   └── orders.json          → created automatically with the first order
└── users/
    └── users.json           → predefined, must not be modified
```

## 3. Requirements

- Apache with PHP (e.g. XAMPP)
- A modern browser (Firefox or Chrome)
- HTTPS enabled on the local server

## 4. Setup and run

1. Copy the `orderManagementApp` folder into Apache's document root
   (e.g. `C:\xampp\htdocs\`).
2. Start Apache (and configure/enable SSL for HTTPS).
3. Make sure the `orders/` folder is writable by Apache, since
   `orders.json` is generated automatically when the first order is created.
4. Open in the browser:
   ```
   https://localhost/orderManagementApp/application/client/index.html
   ```

## 5. Login

- **User:** `admin`
- **Password:** the one matching the bcrypt hash stored in
  `users/users.json` (set by the teacher / configured for testing).

## 6. Technologies

| Layer       | Technology           |
|-------------|-----------------------|
| Client      | HTML5, CSS3, JavaScript |
| Server      | PHP                    |
| Storage     | JSON files             |
| Web server  | Apache (HTTPS)          |
| Versioning  | Git & GitHub            |

## 7. HTTP methods and responses

- `GET` is used for read operations (view all orders, view one order).
- `POST` is used for write operations (login, create order).
- The server responds with `200 OK` on success and `404 Not Found` when
  `orders.json` cannot be opened or a requested order does not exist.

## 8. Author

Sergio — DAW2, Escola del Clot

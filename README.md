# 🛠️ MVC Linkshim (Educational Project)

This is an educational project built from scratch in **pure PHP**, following the **MVC architecture pattern**. The main goal of this project is to help students (including myself!) understand how MVC works internally without relying on any external frameworks like Laravel or Symfony.

> ⚠️ This is **not** intended for production. It focuses on **learning** and **understanding** the internals of web applications and MVC structure.

---

## 📚 What is Implemented?

### ✅ Core Features

- Custom `Router` with route matching and dispatch
- Controller-View-Model separation
- Basic `Request` and `Response` abstractions
- Session management with custom `SessionHandler`
- Basic authentication using **JWT**
- File upload and **resumable file download** (with `Range` support)
- Error and exception handling
- PSR-compliant structure (`PSR-4` autoloading, `PSR-1`, `PSR-12` coding standards)
- Static asset serving via a custom development server
- Database migration scripts (MySQL)

### ⚙️ Technical Details

- PHP 8+
- Composer-based autoloading
- PDO for database access
- Minimal custom ORM layer
- Modular structure for scalability

---

## 🎯 Educational Goals

This project aims to teach and demonstrate:

- How MVC works behind the scenes
- How to write clean, PSR-compliant PHP code
- Understanding request lifecycle: from route to response
- JWT-based stateless authentication
- Basics of security (XSS, CSRF, input sanitization)
- Good practices for file handling and sessions

## 🏁 Getting Started

1. Clone the repo
   ```bash
   git clone https://github.com/dgithuba/mvc-linkshim.git
   cd mvc-linkshim
   ```
2. copy [.env.example](/.env.example) to `.env` and fill required data.
3. if you don't hava mysql, you can use docker to set it up with `docker compose up -d`.
4. Install dependencies
    ```bash
   composer install 
   npm i
   ```
5. for serve your application and see a result in browser run 
   - on windows:
       ```bash
       .\serve.cmd
       ```
   - on others:
       ```bash
       php -S localhost:8000 -t public router.php
       ```
   then visit http://localhost:8000/ in browser.

6. in this project, we use tailwind in view files. so If you make changes to view styles, run:
    ```bash
   npm run build 
   ```

## ✅ TODOs
- [ ] Improve and optimize code for better clarity and understanding
- [ ] Build a better Router class with more features
- [ ] Enhance error reporting and debugging experience
- [ ] more usefully artisan commands.
- [ ] more model/controller samples.

## 📄 License
This project is licensed under the MIT License.

# Smart Syndicates — Security Guard Management System

Smart Syndicates is a robust web-based system designed to streamline the management of security guards, deployment sites, attendance tracking, and salary calculations.

This application is ideal for security companies that require a centralized system to handle their workforce, daily attendance, and automated payroll processes efficiently.

---

## 🚀 Features

- **Employee (Guard) Management**
  - Add/edit guard details
  - Assign guards to multiple sites
  - Track BR Allowance, Basic Salary, Rank, etc.

- **Site Management**
  - Add & manage security sites
  - Define shift rates, contact persons, and other site-specific details

- **Attendance Management**
  - Excel-style daily attendance input
  - Track both **Day** and **Night** hours
  - Auto-calculate normal and overtime hours

- **Salary & Payroll**
  - Generate salary breakdown per employee per month
  - Automatically calculate:
    - Basic + BR Allowance
    - OT Earnings
    - EPF (8%) + ETF (15%)
    - Advances
    - Net Salary
  - Generate professional PDF pay slips

- **Salary Advances**
  - Record & deduct salary advances in monthly salary

- **User Management**
  - Manage system users securely
  - Role-based access (Coming Soon)

---

## 📊 Technologies Used

- **Laravel 10** – PHP framework (backend)
- **TailwindCSS** – Styling and utility-first CSS
- **Vite** – Modern asset bundler
- **MySQL** – Database
- **Dompdf** – PDF payslip generation
- **Alpine.js** – Lightweight interactivity

---

## 📂 Folder Structure Highlights

- `app/Models` – Eloquent models like `Employee`, `Site`, `Attendance`
- `resources/views` – Blade UI for guards, sites, salaries, etc.
- `routes/web.php` – All route definitions
- `public/images/logo.png` – Brand logo (used in header)

---

## 📸 Screenshots

> _(Optional: include 2–3 screenshots of dashboard, attendance, and salary breakdown here if available.)_

---

## 📦 Installation (Local)

```bash
git clone https://github.com/yourusername/guard-management-system.git
cd guard-management-system
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run dev
php artisan serve

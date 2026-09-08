# Panabo City Library System Setup Guide

This guide shows how a client can download and run the system using Git.

## Requirements

- Git installed on the computer
- PHP installed
- Composer installed
- Node.js and npm installed
- MySQL or MariaDB installed    

## 1. Create a Folder

Create a folder anywhere you want, for example:

```text
C:\Projects
```

You can also create a specific folder for the system:

```text
C:\Projects\panabo-library-system
```

## 2. Open Git Bash or Terminal

Open Git Bash, PowerShell, or Terminal inside the folder.

Example:

```bash
cd C:\Projects
```

## 3. Clone the Repository from GitHub

If the project is already pushed to GitHub, clone it using:

```bash
git clone https://github.com/your-username/your-repository-name.git
```

Then go into the project folder:

```bash
cd your-repository-name
```

## 4. Install PHP Dependencies

Run Composer install:

```bash
composer install
```

## 5. Install Frontend Dependencies

Run npm install:

```bash
npm install
```

## 6. Create the Environment File

If `.env` does not exist, copy the example file:

```bash
copy .env.example .env
```

If you are on Mac or Linux:

```bash
cp .env.example .env
```

## 7. Generate the App Key

Run:

```bash
php artisan key:generate
```

## 8. Configure the Database

Open the `.env` file and update these values:

```env

```

Make sure the database already exists in MySQL.

## 9. Run the Migrations

Run:

```bash
php artisan migrate
```

## 10. Seed the Database

If you want the sample data, run:

```bash
php artisan db:seed
```

## 11. Create the Storage Link

Run:

```bash
php artisan storage:link
```

## 12. Compile Frontend Assets

Run:

```bash
npm run build
```

If you are developing and want live rebuild:

```bash
npm run dev
```

## 13. Start the System

Run the Laravel server:

```bash
php artisan serve
```

Then open:

```text
http://127.0.0.1:8000
```

## Example Full Flow

Here is a simple example of the full setup process:

1. Create a folder like `C:\Projects`.
2. Open Git Bash or Terminal inside that folder.
3. Clone the GitHub repository.
4. Enter the project folder.
5. Run `composer install`.
6. Run `npm install`.
7. Copy `.env.example` to `.env`.
8. Update database name, username, and password in `.env`.
9. Run `php artisan key:generate`.
10. Run `php artisan migrate`.
11. Run `php artisan db:seed`.
12. Run `php artisan storage:link`.
13. Run `npm run build`.
14. Run `php artisan serve`.
15. Open the site in the browser.

## Notes

- If the repository is private, the client must have access to the GitHub repo.
- If the database name in `.env` is wrong, the system will not run correctly.
- If they change code later, they can pull updates with:

```bash
git pull
```

## Common Login Accounts

If you seeded the database, you may already have sample accounts like:

- Admin
- Staff
- Student
- Researcher

Use the seeded credentials from your database seeder or project documentation.

## Role Access Overview

The system already supports these built-in roles and permissions:

### Librarian / System Administrator

Can:

- Manage books
- Manage users
- Manage attendance
- Review reports
- Manage WiFi vouchers
- Approve research proposals

### Staff

Can:

- Assist library operations
- Manage book transactions
- Generate vouchers
- Process attendance

### Students

Can:

- Scan barcode attendance
- Search books
- Borrow books
- Return books
- Receive notifications

### Researchers

Can:

- Submit research proposals
- Upload requirements
- Track proposal status
- Receive updates

Notes:

- Admin and staff accounts are typically created by seeding or by an existing administrator.
- Student and researcher accounts can register through the public form, then be verified if required by the workflow.

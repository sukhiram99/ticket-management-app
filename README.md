🎫 Ticket Management System (API-Based)
📌 Overview

The Ticket Management System is a RESTful API built with Laravel that allows users to create and manage support tickets efficiently.
It supports user authentication, role-based access (User/Admin), ticket lifecycle management, and ticket replies, making it suitable for customer support or internal helpdesk systems.

The application uses Laravel Sanctum for secure API authentication and follows clean architecture principles with Form Requests, Controllers, and Models.

🚀 Features
User Features

Register and log in using API tokens

Create new support tickets

View their own tickets

Update ticket details

Close tickets

Add replies to tickets

Admin Features

View all tickets in the system

Change ticket status (open, in_progress, closed)

Reply to user tickets

🧱 System Architecture

The application is structured using:

Models: User, Ticket, Reply

Controllers: Handle business logic

Form Requests: Centralized validation & authorization

Middleware: Role-based access control

Sanctum: API authentication

RESTful APIs: JSON responses only

🔐 Authentication

Authentication is handled using Laravel Sanctum.

Users receive a Bearer Token upon login or registration

Protected routes require a valid token

Admin-only routes are protected via middleware

🔄 Application Flow / Process
1️⃣ User Registration & Login

User registers with name, email, and password

User logs in and receives an API token

Token is used to access protected endpoints

2️⃣ Ticket Creation

Authenticated users can create a support ticket

Each ticket includes:

Title

Description

Status (open by default)

Ticket is linked to the user who created it

3️⃣ Ticket Management

Users can:

View their own tickets

Update ticket details

Close tickets when resolved

Admins can:

View all tickets

Change ticket status (open, in_progress, closed)

4️⃣ Ticket Replies

Both users and admins can reply to tickets

Replies are stored with:

Ticket reference

User reference

Message content

Enables back-and-forth communication

5️⃣ Validation & Error Handling

All validations are handled using Form Request classes

Invalid requests return structured JSON error responses

Authorization failures return proper HTTP status codes

📂 Database Models
User

Has many tickets

Has many replies

Role-based access (user, admin)

Ticket

Belongs to a user

Has many replies

Has a status lifecycle

Reply

Belongs to a ticket

Belongs to a user

📦 API Response Format

All API responses are returned in JSON format.

Example:

{
"success": true,
"data": {
"id": 1,
"title": "Login Issue",
"status": "open"
}
}

🛠️ Technologies Used

Laravel

Laravel Sanctum

MySQL / PostgreSQL

REST API

JSON

✅ Best Practices Implemented

RESTful API design

Token-based authentication

Role-based authorization

Form Request validation

Clean and maintainable code structure

Separation of concerns

📈 Possible Enhancements

API Resources for consistent responses

Swagger / OpenAPI documentation

Ticket priority levels

File attachments for tickets

Email notifications

🧑‍💻 Author

Developed as a Mini Ticketing System to demonstrate backend API design, authentication, and role-based access using Laravel.

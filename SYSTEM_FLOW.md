# Library Management System — System Flow

## 1. System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Web Browser (Client)                  │
├─────────────────────────────────────────────────────────┤
│  Admin Dashboard  │  Student Portal  │  Auth Pages      │
└──────────┬──────────┴─────────┬──────────┘
           │                    │
           ▼                    ▼
┌─────────────────────────────────────────────────────────┐
│               Laravel Application (Backend)             │
├─────────────────────────────────────────────────────────┤
│  Routes (web.php)  →  Controllers  →  Models  →  DB    │
│  Middleware (auth:admin / auth:student)                 │
│  Notifications (Database Channel)                       │
│  Queue (Database Driver)                                │
└─────────────────────────────────────────────────────────┘
           │
           ▼
┌─────────────────────────────────────────────────────────┐
│                    MySQL Database                        │
├─────────────────────────────────────────────────────────┤
│  users  │  books  │  requests  │  categories  │  logs   │
│  config │  notifications │ sessions │ cache             │
└─────────────────────────────────────────────────────────┘
```

## 2. User Roles

| Role | Guard | Capabilities |
|------|-------|-------------|
| **ADMIN** | `admin` | Full CRUD on books, categories, users; approve/deny requests; manage fines; view logs; manage system config |
| **USER** (Student) | `student` | Browse books, search/filter, request books, view request history, receive notifications |

Both roles share the same `users` table, differentiated by the `role` column (`ADMIN` or `USER`).

---

## 3. Authentication Flow

### 3.1 Admin Registration & Verification

```
[Register Form] → POST /admin/register
    ↓
Validate: email (unique), password (min 8, uppercase, lowercase, number, special char)
    ↓
Create User (role = ADMIN, email_verified = false)
    ↓
Generate verification token (bin2hex(random_bytes(32)))
    ↓
Send WelcomeMail with verification link
    ↓
Admin clicks verify link → GET /admin/verify?email=&token=
    ↓
Validate token, set email_verified = true, clear token
    ↓
Redirect to admin.login with success message
```

### 3.2 Admin Login

```
[Login Form] → POST /admin/login (throttled: 5 attempts/hour)
    ↓
Validate credentials via Auth::guard('admin')
    ↓
Check: role === 'ADMIN'  AND  email_verified === true
    ↓
If pass: Create session, log activity, redirect to admin.dashboard
If fail: Redirect back with error
```

### 3.3 Student Registration & Login

```
[Register Form] → POST /student/register
    ↓
Validate: email (@gmail.com only, unique), password (min 8, confirmed)
    ↓
Create User (role = USER, email_verified = true)
    ↓
Redirect to student.login

[Login Form] → POST /student/login (throttled)
    ↓
Validate credentials via Auth::guard('student')
    ↓
Check: role === 'USER'
    ↓
Redirect to student.dashboard
```

### 3.4 Password Reset Flow

```
[Forgot Form] → POST /admin/forgot
    ↓
Validate email exists
    ↓
Generate reset_token (bin2hex 32 bytes), set reset_expires (24 hours)
    ↓
Send ResetPasswordMail with reset link
    ↓
[Reset Form] → GET /admin/reset?email=&token= (validate token + expiry)
    ↓
[Submit] → POST /admin/reset → update password, clear token/expiry
    ↓
Redirect to admin.login
```

---

## 4. Book Management Flow (Admin)

### 4.1 Add Book

```
[Manage Books Tab] → Fill form (ISBN, Title, Author, Stock, Category, Image)
    ↓
POST /admin/books (StoreBookRequest validation)
    ↓
Validate: ISBN unique, title, author, stock >= 1, category exists
    ↓
Upload image to storage/app/public/book_covers/
    ↓
Create Book record (status = 'AVAILABLE')
    ↓
Log activity: "Admin added book: {title}"
    ↓
Refresh dashboard with success message
```

### 4.2 Update Book

```
[Edit Modal] → Fill form (Title, Author, Category, Stock)
    ↓
PUT /admin/books (with hidden book_id)
    ↓
Validate fields, find book by ID, update record
    ↓
Log activity
    ↓
Refresh
```

### 4.3 Delete Book

```
[Remove button] → Confirm dialog
    ↓
DELETE /admin/books/{book}
    ↓
Check: status !== 'BORROWED' (cannot delete borrowed books)
    ↓
Delete image from storage if exists
    ↓
Delete book record
    ↓
Log activity
```

### 4.4 View Books

- **Books List tab**: Grid view of all books with cover images, title, author, stock, status
- **Manage Books tab**: Table view with ISBN, title, author, category, stock, status, edit/remove actions
- The book list excludes books with PENDING requests to avoid clutter during active requests

---

## 5. Book Request Flow (Student → Admin)

### 5.1 Student Requests a Book

```
[Student Dashboard] → Browse books → Click book card
    ↓
Fill form: Full Name, Student ID Number
    ↓
POST /student/request
    ↓
Check: No existing PENDING or APPROVED request for same book+user
    ↓
Create BookRequest (status = 'PENDING', req_date = now())
    ↓
Load book relationship
    ↓
NOTIFY all ADMIN users → NewBookRequest notification (database)
    ↓
Student sees: "Request Sent!"
    ↓
JS polling (10s) fetches unread notifications for the student
```

### 5.2 Admin Approves a Request

```
[Admin Dashboard → Requested Books tab]
    ↓
View pending requests: Student name, Book title, Requested date
    ↓
Click "Approve" → POST /admin/requests/{id}/approve
    ↓
Update BookRequest: status = 'APPROVED', action_date = now(), return_date = now() + 3 days
    ↓
Update Book: status = 'BORROWED'
    ↓
Load book + user relationships
    ↓
NOTIFY the requesting student → RequestApproved notification (database)
    ↓
Admin sees: "Request approved!"
    ↓
Student's polling picks up the new notification with return-by date
```

### 5.3 Admin Denies a Request

```
Click "Deny" → POST /admin/requests/{id}/deny
    ↓
Update BookRequest: status = 'DENIED', action_date = now()
    ↓
NOTIFY the requesting student → RequestDenied notification (database)
    ↓
Admin sees: "Request denied."
```

### 5.4 Admin Marks Book as Returned

```
[Admin Dashboard → Issued Books tab]
    ↓
View borrowed books with student, issue date, return due date
    ↓
Click "Mark Returned" → POST /admin/requests/{id}/return
    ↓
Update BookRequest: status = 'RETURNED', action_date = now()
    ↓
Update Book: status = 'AVAILABLE'
    ↓
NOTIFY the requesting student → BookReturned notification (database)
```

### 5.5 Request Status Lifecycle

```
    ┌──────────┐
    │ PENDING  │
    └────┬─────┘
         │
    ┌────┴─────┐
    │          │
    ▼          ▼
┌────────┐ ┌────────┐
│APPROVED│ │ DENIED │
└───┬────┘ └────────┘
    │
    ▼
┌─────────┐
│ RETURNED│
└─────────┘
```

---

## 6. Notification System Flow

### 6.1 Architecture

```
[Event Trigger] → Controller dispatches Notification
    ↓
Notification class creates array data
    ↓
Laravel stores in 'notifications' table (database channel)
    ↓
Dashboard JS polls /admin/notifications or /student/notifications every 10s
    ↓
Notification badge updates with unread count
    ↓
User clicks bell → dropdown shows notifications
    ↓
User clicks notification → POST /notifications/{id}/read
    ↓
Notification marked as read, badge decrements
```

### 6.2 Notification Types

| Type | Trigger | Sent To | Data |
|------|---------|---------|------|
| `new_request` | Student requests a book | All ADMIN users | Student name, book title, request ID |
| `request_approved` | Admin approves request | Requesting student | Book title, return-by date |
| `request_denied` | Admin denies request | Requesting student | Book title |
| `book_returned` | Admin marks book returned | Requesting student | Book title |

### 6.3 JS Polling Mechanism

```
setInterval(fetchNotifs, 10000)  ← runs every 10 seconds
    ↓
GET /{guard}/notifications (AJAX)
    ↓
Response: { unread_count, notifications: [{ id, data, created_at }] }
    ↓
Update badge count in UI
    ↓
If dropdown is open, render notification list with icons
    ↓
Click notification → POST /{guard}/notifications/{id}/read
    ↓
Mark all read → POST /{guard}/notifications/read-all
```

---

## 7. Fine Management Flow

### 7.1 Fine Accumulation

```
Admin sets fine rate via config (default: PHP 10/day)
    ↓
Config stored in 'config' table (key: 'fine_rate')
    ↓
When a book is issued, return_date = approval_date + 3 days
    ↓
Each day past return_date accrues fine: days_late × fine_rate
    ↓
Displayed in Fines tab with days late, fine amount, payment status
```

### 7.2 Fine Payment

```
[Admin Dashboard → Fines tab]
    ↓
View outstanding fines: Student, Book, Due date, Days late, Fine amount
    ↓
Student pays → Admin clicks "Mark Paid"
    ↓
POST /admin/requests/{id}/pay-fine
    ↓
Update BookRequest: fine_paid = true
    ↓
Log activity
```

---

## 8. Category Management

```
Admin clicks "Add Category" → POST /admin/categories
    ↓
Validate: name (required, unique)
    ↓
Create Category record
    ↓
Categories appear in:
    - Add Book form dropdown
    - Student dashboard filter
    - Books by Category chart (admin dashboard)
```

---

## 9. System Logs

```
Every significant admin action creates a Log entry:
    - Login/logout
    - Add/edit/delete book
    - Approve/deny/return request
    - Fine payment
    - Profile update
    - Password change
    - User deletion

Logs displayed in System Logs tab (most recent 50 entries)
    ↓
Time | Description
```

---

## 10. Dashboard Statistics & Charts

### Admin Dashboard Stats Cards

| Card | Source |
|------|--------|
| Members | `COUNT(users) WHERE role = 'USER'` |
| Issued Books | `COUNT(requests) WHERE status = 'APPROVED'` |
| Available Books | `COUNT(books) WHERE status = 'AVAILABLE'` |
| Total Fines | `SUM(fine) WHERE fine > 0` |
| Pending Requests | `COUNT(requests) WHERE status = 'PENDING'` |

### Admin Dashboard Charts

| Chart | Type | Data |
|-------|------|------|
| Books by Status | Doughnut | AVAILABLE vs BORROWED counts |
| Book Requests (6 months) | Line | Monthly request counts |
| Fines by Month (6 months) | Bar | Monthly fine sums |
| Books by Category | Bar | Book count per category |

---

## 11. Database Schema Relationships

```
users
  ├── id (PK)
  ├── role (ADMIN | USER)
  └── has many → requests (user_id)

books
  ├── id (PK, ISBN)
  ├── category → categories.name
  ├── status (AVAILABLE | BORROWED)
  └── has many → requests (book_id)

requests (table name: 'requests')
  ├── id (PK)
  ├── book_id (FK → books.id)
  ├── user_id (FK → users.id)
  ├── status (PENDING | APPROVED | DENIED | RETURNED)
  ├── fine, fine_paid
  └── belongs to → users, books

notifications (polymorphic)
  ├── id (UUID PK)
  ├── type (notification class name)
  ├── notifiable_id (polymorphic)
  ├── notifiable_type (polymorphic)
  ├── data (JSON)
  └── read_at (nullable)
```

---

## 12. Route Structure

```
/                           → Redirect to admin/student dashboard
/admin/*                   → Admin routes (auth:admin middleware)
  /login                   → Admin login
  /register                → Admin registration
  /forgot                  → Password reset
  /reset                   → Reset password
  /dashboard               → Admin dashboard (stats, charts, CRUD)
  /books/*                 → Book CRUD
  /categories/*            → Category CRUD
  /requests/*              → Approve/deny/return/pay
  /notifications           → Fetch unread (JSON)
  /notifications/{id}/read → Mark one read
  /notifications/read-all  → Mark all read

/student/*                 → Student routes (auth:student middleware)
  /login                   → Student login
  /register                → Student registration
  /dashboard               → Student portal (browse, search, request)
  /request                 → Submit book request
  /notifications           → Fetch unread (JSON)
  /notifications/{id}/read → Mark one read
  /notifications/read-all  → Mark all read
```

---

## 13. Email Flow

```
Admin Registration
    └── WelcomeMail → Verify email link → Enable account

Admin Forgot Password
    └── ResetPasswordMail → Reset token link → Change password

Mailer: Gmail SMTP (smtp.gmail.com:587, TLS)
Mailable classes implement ShouldQueue (queueable)
```

---

## 14. Security Measures

- **Password policy**: Min 8 chars, uppercase, lowercase, number, special character (admin only)
- **Login throttling**: 5 attempts per hour per IP
- **Auth guards**: Separate guards for admin and student sessions
- **Email verification**: Required for admin accounts
- **CSRF protection**: All POST/PUT/DELETE forms include @csrf
- **Session management**: Database-driven sessions
- **Duplicate request prevention**: Students cannot request same book twice while PENDING/APPROVED
- **Borrowed book protection**: Cannot delete a book when status = 'BORROWED'
- **Self-deletion prevention**: Admin cannot delete their own account

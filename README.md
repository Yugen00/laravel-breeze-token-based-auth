## Laravel Breeze Token-Based Auth (Sanctum)

This project adapts the default Laravel Breeze authentication system to use token-based authentication via Laravel Sanctum. The original Breeze setup relies on session-based auth, which causes CSRF token mismatch issues when consumed by frontends like React or mobile apps. This repo resolves that problem by transforming all auth flows into API-friendly, stateless token-based routes.

---

### Key Enhancements Made

1. **Sanctum Integration**

   - Breeze now uses Sanctum for issuing and managing API tokens.
   - Login returns a token instead of setting a session cookie.
   - Auth-protected routes use `auth:sanctum`.

2. **Session to Token-Based Flow**

   - CSRF token mismatch issues are avoided entirely.
   - Authentication now works seamlessly with frontend apps (React, mobile, etc.).

3. **Email Verification Refactor**

   - Verification links are generated and sent from the backend.
   - Frontend captures and confirms the verification by forwarding the link parts (ID, hash) to a custom API route.
   - No redirects from the backend — responses are sent in JSON.

4. **Password Reset Flow**

   - Password reset emails contain a token and email that point to the frontend.
   - The frontend captures these and sends them to the backend to reset the password.
   - Flow is fully decoupled from session or views.

5. **Custom API Responses**

   - All responses (register, login, verify, reset, etc.) now return JSON instead of redirects.
   - Implemented `UserResource` to control what user data is returned globally.

6. **Grouped Middleware for Clean Routing**
   - Routes are grouped logically by `guest` and `auth:sanctum` middleware.

---

### Auth Endpoints Refactored

- `POST /register`
- `POST /login`
- `POST /logout`
- `POST /forgot-password`
- `POST /reset-password`
- `POST /email/verification-notification`
- `GET /verify-email/{id}/{hash}`

All routes respond with JSON and are compatible with token-authenticated clients.

---

## Getting Started

### Requirements

- PHP >= 8.2
- Laravel 12.x
- Composer
- Node.js >= 18.x
- NPM >= 9.x
- MySQL or supported DB

---

### Installation

```bash
# Clone the repo
git clone https://github.com/yugen00/laravel-breeze-token-based-auth.git
cd laravel-breeze-token-based-auth

# Install dependencies
composer install
npm install && npm run build

# Set up environment
cp .env.example .env
php artisan key:generate

# Update .env with your DB credentials
# Then run:
php artisan migrate
```

---

### API Usage

All requests must include proper headers:

```bash
Accept: application/json
Content-Type: application/json
```

This ensures Laravel returns JSON instead of redirects or views (especially for validation errors).

---

### Environment Variables

Make sure your `.env` includes:
Ports based on your system instance

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000
```

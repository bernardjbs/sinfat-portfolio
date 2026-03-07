## Module 3 — Authentication
> 🟢 Sonnet

### Goal
Admin login/logout working. `/admin` routes protected. Terminal-styled login page.

### Tasks
- [ ] Create `AdminAuthController`
- [ ] Create auth routes
- [ ] Create `auth` middleware guard (already in Laravel)
- [ ] Build `Login.vue` component — terminal aesthetic
- [ ] Test login/logout flow

### Technical Detail

**Routes (`routes/web.php`):**
```php
Route::get('/login', [AdminAuthController::class, 'show'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login']);
Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth');
```

**Routes (`routes/api.php`):**
```php
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    // blog routes — Module 5
    // ai routes — Module 6
});
```

**AdminAuthController:**
```php
public function show() {
    return view('app'); // SPA entry point
}

public function login(Request $request) {
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return response()->json(['redirect' => '/admin']);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
}

public function logout(Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['redirect' => '/']);
}
```

**Login.vue — terminal aesthetic:**
```
Dark background (#0d1117)
Monospace font (Geist Mono)
Simple form: email + password + submit
Subtle green accent on focus/active states
No logo, no hero — just the form, centred
Error state: dim red text below field
```

### Acceptance Criteria
- [ ] `GET /login` serves the SPA
- [ ] `POST /login` with valid credentials returns 200 + redirect
- [ ] `POST /login` with invalid credentials returns 401 + error message
- [ ] Visiting `/admin` when unauthenticated redirects to `/login`
- [ ] Login page matches terminal aesthetic
- [ ] Logout clears session

### Dependencies
Module 2 (admin user seeded)

---


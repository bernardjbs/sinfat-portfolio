## Module 2 — Database Schema
> 🔴 Opus — review schema carefully before running migrations

### Goal
All database tables designed, migrated, and verified.

### Tasks
- [ ] Design and create all migrations
- [ ] Create all Eloquent models with relationships
- [ ] Seed admin user
- [ ] Verify schema in TablePlus

### Schema

**users**
```
id                  bigint PK
name                varchar(255)
email               varchar(255) unique
password            varchar(255)
remember_token      varchar(100) nullable
created_at          timestamp
updated_at          timestamp
```
Note: Single admin user only. No registration route.

**blog_posts**
```
id                  bigint PK
title               varchar(255)
slug                varchar(255) unique
excerpt             text nullable
content             longtext          ← markdown stored as-is
status              enum('draft','published')  default: draft
published_at        timestamp nullable
ai_generated        boolean default: false    ← was this AI drafted?
ai_model            varchar(100) nullable      ← which model drafted it
created_at          timestamp
updated_at          timestamp

Indexes:
- slug (unique)
- status + published_at (for public listing query)
```

**guest_usage**
```
id                  bigint PK
identifier          varchar(255)      ← IP address or session fingerprint
type                enum('ip','session')
count               tinyint default: 0
last_used_at        timestamp
expires_at          timestamp         ← reset after 24 hours
created_at          timestamp
updated_at          timestamp

Indexes:
- identifier + type (unique composite)
- expires_at (for cleanup)
```
Note: Redis handles runtime rate limiting. This table is for persistence and analytics only.

**ai_sessions**
```
id                  bigint PK
identifier          varchar(255)      ← guest identifier or 'admin'
type                enum('guest','admin')
topic               text
model               varchar(100)
tokens_used         int nullable
status              enum('pending','streaming','completed','failed')
created_at          timestamp
updated_at          timestamp
```
Note: Logs every AI generation attempt. Useful for monitoring cost and usage patterns.

### Models

```
User          → hasMany(BlogPost) [future]
BlogPost      → no relations for now
GuestUsage    → standalone
AiSession     → standalone
```

### Seeders

```php
// AdminSeeder — creates the single admin user
User::create([
    'name'     => 'Bernard',
    'email'    => env('ADMIN_EMAIL'),
    'password' => Hash::make(env('ADMIN_PASSWORD')),
]);
```

Run: `php artisan db:seed --class=AdminSeeder`

Add to `.env`:
```
ADMIN_EMAIL=your@email.com
ADMIN_PASSWORD=your-secure-password
```

### Acceptance Criteria
- [ ] All migrations run without errors: `php artisan migrate`
- [ ] All tables visible in TablePlus with correct columns
- [ ] Admin seeder creates user: `php artisan db:seed --class=AdminSeeder`
- [ ] Admin user can log in (Module 3)

### Dependencies
Module 1 (Redis configured in `.env`)

---


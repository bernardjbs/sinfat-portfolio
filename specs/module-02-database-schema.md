## Module 2 — Database Schema
> 🟡 Sonnet — review schema carefully before running migrations

### Goal
All database tables designed, migrated, and verified.

### Tasks
- [x] Design and create all migrations
- [x] Create all Eloquent models with relationships
- [x] Seed admin user
- [x] Verify schema in TablePlus

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
category            varchar(100) nullable       ← e.g. 'laravel', 'ai', 'devops'
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
- [x] All migrations run without errors: `php artisan migrate`
- [x] All tables visible in TablePlus with correct columns
- [x] Admin seeder creates user: `php artisan db:seed --class=AdminSeeder`
- [x] Admin user can log in (Module 3)

### Dependencies
Module 1 (Redis configured in `.env`)

---


# Deploying Job Portal to InfinityFree (free PHP + MySQL)

InfinityFree gives you free PHP hosting with MySQL, phpMyAdmin, FTP, and **persistent
storage** — so uploaded resumes in `uploads/resumes/` stay put. That makes it a great fit
for this plain-PHP + MySQL app.

## How the config works (read this first)

`includes/connect.php` now loads `includes/config.php`, which **auto-detects the environment**:

- Running on **localhost (WAMP)** → uses `root` / no password / `job_portal` automatically.
- Running on your **live InfinityFree domain** → uses the live credentials.

So you never edit code when switching between local and live. You only set the **live**
credentials once (Step 4).

---

## Step 1 — Create an InfinityFree account & website
1. Go to **https://infinityfree.com** and sign up (free).
2. Create a new website. You get a free subdomain like `yoursite.infinityfreeapp.com`
   (you can attach a custom domain later).
3. Wait a few minutes for the account to activate, then open its **Control Panel**.

## Step 2 — Create the MySQL database
1. In the Control Panel, open **MySQL Databases**.
2. Create a database — type `job_portal`. InfinityFree adds a prefix, so the real name
   becomes something like `if0_36xxxxxx_job_portal`.
3. Write down these **4 values** shown on the page (you'll need them in Step 4):

   | Value | Looks like |
   |-------|-----------|
   | MySQL **Hostname** | `sql200.infinityfree.com` |
   | MySQL **Username** | `if0_36xxxxxx` |
   | MySQL **Password** | (your account password unless changed) |
   | **Database Name** | `if0_36xxxxxx_job_portal` |

## Step 3 — Import your database
1. On the MySQL Databases page, click **Admin** (opens phpMyAdmin) next to your database.
2. Select your database in the left sidebar → **Import** tab.
3. Choose the file `DB/job_portal.sql` from your project → **Go**.
   - The dump has no `CREATE DATABASE`/`USE` lines, so it imports cleanly into the
     prefixed database. ✅

## Step 4 — Set your LIVE database credentials

Pick **one** of these:

**Option A — Recommended (keeps secrets out of Git): `config.local.php`**
After uploading files (Step 5), use the **File Manager** to create a new file at
`htdocs/includes/config.local.php` with your values from Step 2:
```php
<?php
$DB_HOST = 'sql200.infinityfree.com';
$DB_USER = 'if0_36xxxxxx';
$DB_PASS = 'your_db_password';
$DB_NAME = 'if0_36xxxxxx_job_portal';
```
This file is gitignored, so your credentials never get committed. (There's a template at
`includes/config.local.php.example` you can copy.)

**Option B — Quick: edit `includes/config.php`**
Open `includes/config.php`, and under the `LIVE HOST (InfinityFree)` block fill in the four
`$DB_*` values. Simpler, but the credentials end up in your Git history.

## Step 5 — Upload the files
InfinityFree's web root is the **`htdocs`** folder. Upload the **contents** of your
`job-portal` folder into `htdocs` using either:
- the Control Panel's **Online File Manager**, or
- **FTP** (FileZilla) — host `ftpupload.net`, username/password from the **FTP Accounts** page.

Upload: `index.php`, `jobs.php`, `apply.php`, `login.php`, `register.php`, `profile.php`,
`applications.php`, `edit-application.php`, `job-details.php`, `logout.php`, and the
`admin/`, `assets/`, `includes/`, `uploads/` folders, plus `.htaccess`.

You do **not** need to upload: `DB/`, `.git/`, `DEPLOY.md`, or `config.local.php.example`.

Make sure the `uploads/` and `uploads/resumes/` folders exist on the server (create them in
File Manager if missing) so resume uploads work.

## Step 6 — Test it
1. Open `https://yoursite.infinityfreeapp.com`.
2. Register a user → log in → browse jobs → apply to one (uploads a resume) → check the
   `admin/` panel.
3. If you see **"Database connection failed"**, re-check the 4 values in Step 4 — most often
   the **username** and **database name** need the `if0_...` prefix, and the **hostname** is
   `sqlXXX.infinityfree.com`, not `localhost`.

---

## Gotchas on the free tier
- **No remote MySQL:** you can't connect to the InfinityFree database from your PC — only
  from their servers and their phpMyAdmin. That's why the import in Step 3 is done through
  their phpMyAdmin, not from your machine.
- **SSL:** free HTTPS can take ~15 minutes to activate after the site goes live. If
  `https://` errors at first, try `http://` for a bit.
- **Uploads persist** (unlike cloud containers), so submitted resumes stay in
  `uploads/resumes/`. ✅
- **PHP version:** set it under the Control Panel if needed. This app runs on PHP 7.4–8.x.
- **Daily limits:** free accounts have hit/CPU limits — fine for testing and sharing, not for
  heavy production traffic.
- Your **local WAMP keeps working unchanged** — `config.php` auto-uses the WAMP settings on
  localhost.

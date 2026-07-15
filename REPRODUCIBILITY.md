# Reproducibility Guide — EduQBot

This document explains how to set up and run EduQBot locally to reproduce the system described in the paper *"EduQBot: A Real-Time AI Question Generator for Automated Educational Assessment."*

## 1. Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP (procedural, `mysqli`) |
| Database | MySQL / MariaDB |
| AI Model | Google Gemini 2.0 Flash API |
| Email | PHPMailer (vendored in `/phpmailer`) |
| Speech Input | Browser Web Speech API (client-side, no server dependency) |

No package manager (Composer/npm) is used — all dependencies are vendored directly in the repo.

## 2. Prerequisites

- A local server stack with PHP 7.4+ and MySQL/MariaDB — **XAMPP** is assumed by the code (default `localhost` / `root` / no password)
- A **Google Gemini API key** — get one at https://aistudio.google.com/
- A modern browser with microphone permission (for the speech-to-text input feature)

## 3. Setup Steps

1. **Clone the repo** into your server's web root (e.g. `C:\xampp\htdocs\EduQBot` for XAMPP on Windows).
2. **Create the database:**
   ```sql
   CREATE DATABASE chatbot;
   ```
3. **Create required tables** by running, in order:
   ```
   database/create_history_table.sql      -- legacy 'history' table
   database/update_question_history.sql   -- current 'question_history' table
   ```
   or use the provided `run_sql.bat` (Windows/XAMPP path assumed — edit the path if your MySQL binary lives elsewhere).

   > **Note:** the `userinfo` table (columns: `id`, `firstname`, `lastname`, `email`, `password`) is referenced by `signup.php`, `login.php`, and as a foreign key in both history tables, but its `CREATE TABLE` statement is **not included** in the `database/` folder. You'll need to create it manually before the SQL scripts above will run (they have a `FOREIGN KEY ... REFERENCES userinfo(id)` dependency).

4. **Add your Gemini API key.** In `pages/chat_api.php`:
   - Line 18 currently has a hardcoded key — replace it with your own.
   - Line ~30 (`$url = 'YOUR_API_KEY_HERE'`) is a placeholder string, **not a real endpoint** — replace it with the actual Gemini endpoint, e.g.:
     ```
     https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=YOUR_KEY
     ```
5. **Start Apache + MySQL** (via XAMPP Control Panel or equivalent).
6. **Open** `http://localhost/EduQBot/index.php` in your browser.

## 4. Database Schema Reference

**`userinfo`** (not included as a `.sql` file — create manually)
| Column | Type |
|---|---|
| id | INT, AUTO_INCREMENT, PRIMARY KEY |
| firstname | VARCHAR |
| lastname | VARCHAR |
| email | VARCHAR |
| password | VARCHAR (stored in plaintext — see Known Limitations) |

**`question_history`** (current, from `update_question_history.sql`)
| Column | Type |
|---|---|
| id | INT, AUTO_INCREMENT, PRIMARY KEY |
| user_id | INT, FK → userinfo(id) |
| input_text | TEXT |
| generated_questions | TEXT |
| created_at | TIMESTAMP |

**`history`** (legacy, from `create_history_table.sql` — superseded by `question_history`)

## 5. Known Reproducibility Limitations

- **DB credentials are duplicated, not centralized.** `config/database.php` exists but is empty; each PHP file (`index.php`, `login.php`, `signup.php`, etc.) independently hardcodes `localhost` / `root` / `""` / `chatbot`. Anyone deploying outside default XAMPP settings must edit every file individually.
- **Gemini API key is hardcoded and was committed to the public repo** (`pages/chat_api.php`, line 18). This should be rotated and moved to an environment variable or a `.gitignore`'d config file before further public use.
- **Passwords are stored in plaintext** (`signup.php` explicitly skips hashing, per its own comment). Not a reproducibility blocker, but a security limitation worth noting if this repo is shared publicly or used beyond a research prototype.
- **No automated evaluation scripts included.** The evaluation described in the paper (50 curriculum inputs across 4 subjects, 3 certified raters, ICC(3,k) scoring) was conducted manually / offline and is not reproducible directly from this repo — only the live system that generated the question-answer pairs is included here.

## 6. Manual Reproduction of Paper Results

The repo lets you reproduce the **system behavior** (real-time Q&A generation, speech-to-text input, session history) but not the **evaluation numbers** automatically. To approximate the paper's evaluation:

1. Log in and submit the same curriculum texts described in Section 3.C of the paper (Mathematics, Science, Urdu, English).
2. Record response time, generated Q&A pairs, and cognitive-level tags for each input.
3. Have raters score contextual relevance / MCA alignment using the same 5-point Likert scale described in the paper.

---
*Maintained alongside the manuscript submission to Scientific Reports (Nature Portfolio). For questions, contact the corresponding author.*

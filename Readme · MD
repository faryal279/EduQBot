# EduQBot: A Real-Time AI Question Generator for Automated Educational Assessment

This repository contains the source code accompanying the manuscript **"EduQBot: A Real-Time AI Question Generator for Automated Educational Assessment,"** submitted to *Scientific Reports* (Nature Portfolio).

EduQBot is a web-based, real-time AI chatbot that generates contextually relevant question-answer pairs from teacher-provided curriculum text, aligned with the **Minimal Competency Assessment (MCA)** framework used in Pakistani high schools. Unlike systems that require local model training or produce static, pre-generated content, EduQBot generates grammatically correct, context-specific Q&A pairs dynamically through Google's Gemini 2.0 Flash API.

## Authors

Faryal Idrees¹, Brekhna Brekhna¹, Abdulrahman Bin Rabiah², Waqar Khan³ (Corresponding Author)

¹ Department of Computer Science, Shaheed Benazir Bhutto Women University, Peshawar, Pakistan
² Department of Information Systems, College of Computer and Information Sciences, King Saud University, Riyadh, Saudi Arabia
³ School of Big Data, Fuzhou University of International Studies and Trade, Fuzhou, China

## Key Features

- **Real-time Q&A generation** from any curriculum paragraph, with no local model training or dataset collection required
- **Speech-to-text input** for accessibility, via the browser's native Web Speech API
- **Automatic cognitive-level tagging** of generated questions, mapped to the MCA framework (Finding Information, Interpreting & Integrating, Evaluating & Reflecting)
- **Duplicate filtering** using TF-IDF and cosine similarity to ensure question diversity across sessions
- **Session history** — all generated Q&A pairs are stored per user and retrievable later for review and reuse
- **Editable output** — teachers can update, add descriptions to, or set answer keys for generated questions before classroom use

## System Architecture

The system follows a three-tier architecture:

- **Frontend:** HTML, CSS, JavaScript — handles text/voice input and displays results in a chat-like interface
- **Backend:** PHP — validates requests, constructs prompts, and manages session/history logic
- **AI Layer:** Google Gemini 2.0 Flash API — performs the actual question-answer generation
- **Database:** MySQL — stores user accounts and question history

See Figures 1, 2, and 7 in the manuscript for the full methodology flow and system architecture diagrams.

## Repository Structure

```
EduQBot/
├── index.php / index.html      # Landing page
├── pages/
│   ├── chatbot.html / .css      # Main chat interface
│   ├── chat_api.php             # Gemini API integration (question generation)
│   ├── login.php / signup.php   # Authentication
│   ├── history.php / .html      # Session history view
│   ├── save_history.php         # Persists generated Q&A to the database
│   └── send_contact.php         # Contact form (via PHPMailer)
├── config/
│   ├── database.php              # (empty — DB credentials are currently set per-file, see Reproducibility Guide)
│   └── gemini_config.example.php # Template for API key configuration
├── database/
│   ├── create_history_table.sql       # Legacy history table
│   └── update_question_history.sql    # Current question_history table
├── phpmailer/                    # Vendored PHPMailer library
├── icons/                        # UI assets
└── styles.css                    # Global styles
```

## Tech Stack

| Component | Technology |
|---|---|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP (procedural, `mysqli`) |
| Database | MySQL / MariaDB |
| AI Model | Google Gemini 2.0 Flash API |
| Email | PHPMailer |
| Speech Input | Web Speech API (browser-native) |

## Getting Started

Full setup instructions, database schema, and known limitations are documented in **[REPRODUCIBILITY.md](./REPRODUCIBILITY.md)**. In short:

1. Deploy this repo on a PHP + MySQL stack (e.g. XAMPP).
2. Create the `chatbot` database and required tables (see `database/`).
3. Copy `config/gemini_config.example.php` to `config/gemini_config.php` and add your own Gemini API key.
4. Start Apache + MySQL and open `index.php` in your browser.

## Evaluation Summary

EduQBot was evaluated on 50 curriculum inputs spanning 4 subjects (Mathematics, Science, Urdu, English), producing 150 question-answer pairs assessed by 3 certified teacher-raters (inter-rater reliability ICC(3,k) = 0.82). Average response time was 2.8 seconds, with 98% grammatical accuracy, 4.6/5 contextual relevance, and 4.7/5 user satisfaction, evaluated against a GPT-4o baseline. Full methodology and results are reported in Sections 3 and 4 of the manuscript.

## Citation

If you use this code or refer to this work, please cite the manuscript once published (citation details to be updated upon publication in *Scientific Reports*).

## License

This code is provided for academic and research reproducibility purposes alongside the associated manuscript. Contact the corresponding author regarding reuse beyond academic evaluation.

## Contact

For questions about this repository or the associated paper, contact the corresponding author: Waqar Khan (wangkang@fzfu.edu.cn).

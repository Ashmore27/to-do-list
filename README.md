# TaskFlow: To-Do List Web Application with End-to-End DevOps CI/CD Pipeline

A complete, production-ready To-Do List web application built with **PHP, MySQL, HTML5, CSS3, and JavaScript**, surrounded by a comprehensive **DevOps CI/CD lifecycle** utilizing **Git, GitHub, Jenkins, Docker, and Vercel/PaaS hosting**.

---

## 1. Project Overview & Objective

The primary objective of this project is to demonstrate an industry-standard, end-to-end DevOps automation pipeline for a traditional relational web application:

$$\text{Code} \longrightarrow \text{Version Control} \longrightarrow \text{Continuous Integration (CI)} \longrightarrow \text{Automated Testing} \longrightarrow \text{Artifact Packaging} \longrightarrow \text{Continuous Deployment (CD)} \longrightarrow \text{Monitoring}$$

### Core Features of Application:
* **User Authentication**: Secure user registration, password hashing (`bcrypt`), and session-based login.
* **Task Management (CRUD)**: Create tasks, assign due dates, update descriptions, mark as pending/completed, and delete tasks.
* **User Isolation**: Secure multi-tenant session isolation ensuring users only access their personal task database.
* **Export Capability**: Generate clean downloadable text/PDF summaries of pending and completed tasks.
* **DevOps Ready**: Integrated health check endpoint (`/healthcheck.php`), portable environment variable configuration, automated test suites, and declarative Jenkinsfile.

---

## 2. Technology Stack

### Application Layer
| Layer | Technology | Details |
|---|---|---|
| **Backend** | PHP 7.4+ / 8.2 | Object-Oriented PDO database abstraction, Session Auth, REST-style handlers |
| **Database** | MySQL 8.0 / MariaDB | Relational schema (`users`, `tasks`) with foreign keys and cascade deletions |
| **Frontend** | HTML5, CSS3, JavaScript | Modern responsive UI, Glassmorphism, Bootstrap 5, FontAwesome, Google Fonts |
| **Dependencies** | Composer | Dependency management (`mpdf/mpdf`) |

### DevOps Toolchain
| DevOps Stage | Tool / Technology | Purpose |
|---|---|---|
| **Version Control** | Git | Distributed version tracking, branching (`main`), commit history |
| **Code Repository** | GitHub | Remote source hosting, collaboration, and webhook trigger origin |
| **CI/CD Automation** | Jenkins | Declarative pipeline orchestrating build, test, packaging, and deployment |
| **Linting & Validation** | PHP CLI (`php -l`) | Static code analysis and syntax checking on every commit |
| **Automated Testing** | Custom Test Runner | Automated file integrity, DB schema validation, and security assertion suite |
| **Containerization** | Docker & Docker Compose | Portable application and database containers (`php:8.2-apache`, `mysql:8.0`) |
| **Deployment** | Vercel / Web Server / PaaS | Automated deployment targeting Apache / Docker / Cloud PaaS / Vercel |
| **Monitoring & Ops** | Jenkins & Health Check API | Build status badges, execution logs, and live JSON endpoint (`/healthcheck.php`) |

---

## 3. DevOps Lifecycle Architecture

```
+-----------------------------------------------------------------------------------+
|                                  DEV-OPS LIFECYCLE                                |
+-----------------------------------------------------------------------------------+

 [ PLAN ]  --> User Stories & Task Flow Requirements
     |
     v
 [ CODE ]  --> PHP, HTML, CSS, JS Developed in IDE / Local XAMPP
     |
     v
 [ SCM ]   --> Git Commit & Push to GitHub (main branch)
     |
     v (GitHub Webhook / SCM Polling)
+-----------------------------------------------------------------------------------+
|                              JENKINS CI/CD PIPELINE                               |
+-----------------------------------------------------------------------------------+
|  1. Checkout SCM          : Clones latest code commit from GitHub                 |
|  2. Prepare Dependencies  : Verifies PHP runtime and Composer packages            |
|  3. Code Validation       : Runs 'php -l' syntax linting across all source files  |
|  4. Automated Testing     : Executes 'tests/test_runner.php' (Fail = Red Build)   |
|  5. Build & Package       : Bundles deployable release into 'build/todo-app.zip'  |
|  6. Deployment            : Automated push to Web Server / Container / Vercel     |
|  7. Post-Deploy Verify    : Probes 'healthcheck.php' to verify HTTP 200 uptime    |
+-----------------------------------------------------------------------------------+
     |
     v
 [ MONITOR ] --> Jenkins Stage View, Console Logs, and Health Check Endpoint
```

### DevOps Lifecycle Stage Mapping

| Lifecycle Stage | Implementation in this Project | Tool Used |
|---|---|---|
| **Plan** | Requirement specification: To-Do CRUD, auth isolation, and CI/CD automation | GitHub Projects / Jira |
| **Code** | Modular PHP application code with PDO MySQL and responsive Bootstrap UI | VS Code / IDE |
| **Build** | Synthesizing release artifact, excluding dev dependencies (`build/todo-list-app.zip`) | Jenkins / Powershell / Bash |
| **Test** | Syntax analysis (`php -l`), schema assertions, and security rule tests | Custom Test Suite (`tests/test_runner.php`) |
| **Release** | Versioned artifact packaging and archival in Jenkins Workspace | Jenkins Artifact Archiver |
| **Deploy** | Automated deployment to Apache Web Root, Docker Host, or Cloud Platform | Jenkins Pipeline Script / Vercel CLI |
| **Operate** | Apache/PHP runtime process management, session management, and MySQL server | Apache / Docker Engine |
| **Monitor** | Jenkins Build Status, Stage View, Console Output, and `/healthcheck.php` API | Jenkins + JSON Health Check API |

---

## 4. Project Directory Structure

```
to-do-list/
├── .env.example          # Environment variables template (placeholders only)
├── .gitignore            # Git exclusion rules (ignores secrets, vendor, IDE, OS)
├── Dockerfile            # Container definition for PHP 8.2 Apache runtime
├── docker-compose.yml    # Multi-container orchestration (PHP Web App + MySQL DB)
├── Jenkinsfile           # Declarative Jenkins CI/CD pipeline definition
├── README.md             # Complete project and DevOps documentation
├── composer.json         # PHP project manifest and test runner script definitions
├── config.php            # Environment-aware database connection & auth helper
├── dashboard.php         # Core task management dashboard (CRUD interface)
├── database.sql          # Relational SQL schema (users and tasks tables)
├── export_pdf.php        # Task report generator and text/PDF downloader
├── healthcheck.php       # JSON health check endpoint for monitoring & CI/CD
├── index.php             # Application entry point (routing to login/dashboard)
├── install.php           # Database auto-installer and migration script
├── login.php             # User authentication and login view
├── logout.php            # Session destruction and logout handler
├── register.php          # User registration and password hashing view
├── vercel.json           # Serverless deployment configuration for Vercel
├── assets/
│   └── css/
│       └── style.css     # Custom styles, CSS variables, and glassmorphism UI
└── tests/
    └── test_runner.php   # Automated unit and integration test suite
```

---

## 5. Local Setup & Execution Guide

### Option A: Running with XAMPP (Standard Local Setup)

1. **Clone or Copy Files**:
   Copy the `to-do-list` directory to your web server document root:
   ```bash
   C:\xampp\htdocs\to-do-list\
   ```
2. **Start Services**:
   Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
3. **Database Initialization**:
   * Open your browser and navigate to:
     ```
     http://localhost/to-do-list/install.php
     ```
   * Alternatively, import `database.sql` directly into phpMyAdmin (`http://localhost/phpmyadmin`).
4. **Access the Application**:
   Navigate to `http://localhost/to-do-list/` in your browser.
5. **Run the Automated Tests**:
   ```bash
   php tests/test_runner.php
   ```

---

### Option B: Running with Docker & Docker Compose (Zero Configuration)

If Docker Desktop is installed, start both the web server and the MySQL database with one command:
```bash
docker-compose up --build -d
```
* **Application URL**: `http://localhost:8080/`
* **Health Check**: `http://localhost:8080/healthcheck.php`
* **MySQL Database**: Exposed internally to web container on port `3306`, externally on port `3307`.
* To stop the environment:
  ```bash
  docker-compose down
  ```

---

## 6. Testing Strategy

The project features a fast, portable, and zero-external-dependency automated test runner located at `tests/test_runner.php`.

### What the Test Suite Verifies:
1. **File Integrity Suite**: Ensures all critical files (`config.php`, `index.php`, `login.php`, `register.php`, `dashboard.php`, `export_pdf.php`, `logout.php`, `database.sql`, `.env.example`, `assets/css/style.css`, `healthcheck.php`) exist and are non-empty.
2. **PHP Syntax Validation Suite**: Runs `php -l` on every PHP source file to detect syntax errors before runtime.
3. **Database Schema Integrity**: Verifies `database.sql` defines the `users` and `tasks` tables with proper keys.
4. **Security & Auth Assertions**: Ensures passwords are encrypted with `password_hash()`, `config.php` supports environment variables, and `dashboard.php` enforces session authentication.

### How to Run Tests Locally:
```bash
php tests/test_runner.php
```
Or via Composer:
```bash
composer test
```

### Exit Codes for CI/CD Integration:
* **Exit Code `0`**: All tests passed. Jenkins marks the **Test** stage as GREEN.
* **Exit Code `1`**: One or more tests failed. Jenkins immediately halts the pipeline and marks the build as FAILED (RED).

---

## 7. Jenkins CI/CD Pipeline Breakdown

The `Jenkinsfile` in this repository uses **Declarative Pipeline syntax** with seven clear, robust stages:

```
[ Checkout SCM ]
       │
       ▼
[ Prepare Dependencies ]
       │
       ▼
[ Code Validation (php -l) ]
       │
       ▼
[ Automated Testing ] ──(Failure: Exit 1)──► [ Build FAILS (RED) ]
       │
       ▼
[ Build & Package Artifact ]
       │
       ▼
[ Deployment ]
       │
       ▼
[ Post-Deploy Health Check ]
       │
       ▼
[ Post-Build Notifications ]
```

### Stage Details:
1. **Checkout SCM**: Uses `checkout scm` to pull the latest commit from the GitHub repository branch (`main`).
2. **Prepare Dependencies**: Validates the PHP CLI runtime and Composer availability on the build agent.
3. **Code Validation**: Iterates through all PHP files and performs static linting (`php -l`). If any developer made a syntax error, the build halts immediately before running tests.
4. **Automated Testing**: Executes `php tests/test_runner.php`. Evaluates the exit code. If non-zero, Jenkins throws an error, marking the build as failed.
5. **Build & Package Artifact**: Collects tested release files, excludes development/test files, and packages a production-ready archive (`build/todo-list-app.zip`), registering it in Jenkins Artifacts.
6. **Deployment**: Deploys the application:
   * To local web server (`C:/xampp/htdocs/to-do-list` or `/var/www/html/to-do-list`).
   * To Vercel (if `VERCEL_TOKEN` credential is provided).
   * Or to a Docker/cloud container runtime.
7. **Post-Deployment Verification**: Invokes `healthcheck.php` to verify the web service responds with HTTP status 200 and JSON status `"UP"`.

---

## 8. GitHub Integration (Webhook vs Polling)

To trigger the Jenkins pipeline automatically whenever code is pushed to GitHub, two approaches are supported:

### Approach A: GitHub Webhook (Recommended for Cloud/Public Jenkins)
1. In your GitHub repository, navigate to **Settings** $\rightarrow$ **Webhooks** $\rightarrow$ **Add webhook**.
2. **Payload URL**: `http://<your-jenkins-ip-or-domain>:8080/github-webhook/`
3. **Content type**: `application/json`
4. **Which events**: Select **Just the push event**.
5. Save the webhook. Whenever a developer runs `git push origin main`, GitHub sends an HTTP POST payload to Jenkins, initiating the pipeline within seconds.

> **Note for Localhost / College Lab Demonstrations**: If your Jenkins server is running on `localhost:8080`, GitHub cannot reach your local machine directly across the public internet. Use either:
> * **Tool (ngrok)**: Expose your local Jenkins port: `ngrok http 8080`. Copy the generated HTTPS forwarding URL (e.g., `https://xyz.ngrok-free.app/github-webhook/`) into the GitHub webhook settings.
> * **Approach B (Poll SCM)**: In your Jenkins job configuration, under **Build Triggers**, check **Poll SCM** and enter `H/2 * * * *` (checks GitHub every 2 minutes for changes).

---

## 9. Vercel Deployment Architecture Analysis

### Architectural Analysis: Can Vercel Host Traditional PHP?

| Criterion | Vercel Serverless Architecture | Traditional PHP Architecture (This Project) | Compatibility Assessment |
|---|---|---|---|
| **Runtime Model** | Ephemeral, stateless Node/Edge microVMs | Stateful, long-running Apache / PHP-FPM process | ⚠️ Partial (Requires `vercel-php` builder) |
| **Session State** | In-memory/filesystem resets on every request | Native `$_SESSION` stored on local server filesystem | ❌ File sessions lost between requests on serverless |
| **Database** | Expects serverless DB / HTTP APIs | Direct persistent TCP connection to MySQL database | ❌ Localhost MySQL cannot be reached from Vercel |
| **Static Assets** | Global CDN edge network | Served via Apache DocumentRoot | ✅ Fully compatible |

### Realistic College DevOps Conclusion:
1. **The Vercel Configuration (`vercel.json`)**: We provide a functional `vercel.json` configured with the community runtime `vercel-php@0.7.3`. If deployed to Vercel, it routes requests to serverless PHP functions.
2. **External Requirement**: To make Vercel work with full database capabilities, the application's database credentials (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`) must point to an accessible cloud MySQL provider (such as Clever Cloud, Aiven, or Supabase free tiers) rather than `localhost`.
3. **Recommended Production Hosting for PHP**: For traditional PHP + MySQL web applications, containerized hosting (**Docker on Render**, **Railway**, **Fly.io**, or an **AWS EC2 / Apache Virtual Host**) represents the realistic, industry-standard deployment target.

---

## 10. Environment Variables & Secrets Management

Hardcoded database credentials have been replaced with dynamic environment variables in `config.php`:

```php
define('DB_HOST', getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost');
define('DB_PORT', getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306');
define('DB_USER', getenv('DB_USER') !== false ? getenv('DB_USER') : 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_NAME', getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'todo_app');
```

### Template File: `.env.example`
A sample `.env.example` file is included in the repository. Real passwords must **NEVER** be committed to Git.

### Configuring Secrets in Jenkins:
1. Go to **Jenkins Dashboard** $\rightarrow$ **Manage Jenkins** $\rightarrow$ **Credentials** $\rightarrow$ **System** $\rightarrow$ **Global credentials**.
2. Click **Add Credentials**:
   * Kind: **Secret text**
   * ID: `DB_PASS` (or `VERCEL_TOKEN`)
   * Secret: `your_actual_secure_password`
3. In `Jenkinsfile`, reference credentials securely without exposing them in logs:
   ```groovy
   withCredentials([string(credentialsId: 'DB_PASS', variable: 'DB_PASSWORD')]) {
       // use $DB_PASSWORD securely inside deployment step
   }
   ```

---

## 11. Monitoring & Observability

During the **Operate & Monitor** stage, continuous feedback is gathered using:

1. **Jenkins Dashboard & Build History**: Real-time status indicators (Blue/Green for Success, Red for Failure, Yellow for Unstable).
2. **Jenkins Stage View / Blue Ocean**: Visual progress metrics showing execution time for each individual pipeline stage (Checkout, Lint, Test, Build, Deploy).
3. **Console Output Logs**: Full standard output/error stream for auditing, debugging, and trace analysis.
4. **Application Health Check API (`healthcheck.php`)**:
   Returns structured JSON uptime metrics:
   ```json
   {
       "status": "UP",
       "service": "To-Do List Web Application",
       "timestamp": "2026-09-28T00:15:00+05:30",
       "php_version": "8.2.12",
       "checks": {
           "web_server": "OK",
           "php_runtime": "OK",
           "database": "OK"
       }
   }
   ```

---

## 12. Complete Step-by-Step Live Demonstration Guide

Follow this script to demonstrate the complete DevOps lifecycle to your project guide or examiner:

### Step 1: Show the Working Application
* Open `http://localhost/to-do-list/` in the browser.
* Log in, create a task (e.g. "Prepare DevOps Viva Presentation"), mark it completed, and click **Export Tasks**.
* Explain: *"The application is running locally on Apache and MySQL."*

### Step 2: Introduce a Code Change
* Open `dashboard.php` in your editor.
* Make a visible UI change (e.g., change the brand name from `TaskFlow` to `TaskFlow Pro v2.0` on line 52).

### Step 3: Commit and Push to GitHub
* Run Git commands in the terminal:
  ```bash
  git add dashboard.php
  git commit -m "feat: upgrade branding to TaskFlow Pro v2.0"
  git push origin main
  ```

### Step 4: Show Jenkins Automatically Triggering
* Open Jenkins Dashboard: `http://localhost:8080/job/todo-list-pipeline/`.
* Show that a new build (e.g., `#2`) has automatically started via Webhook / SCM Polling.

### Step 5: Walk Through Pipeline Stages
* Point out each stage transitioning to green:
  1. `Checkout SCM` $\rightarrow$ Pulled commit from GitHub.
  2. `Prepare Dependencies` $\rightarrow$ Verified PHP runtime.
  3. `Code Validation` $\rightarrow$ Linted syntax with `php -l`.
  4. `Automated Testing` $\rightarrow$ Ran 27 automated tests; all passed.
  5. `Build & Package` $\rightarrow$ Created `build/todo-list-app.zip`.
  6. `Deployment` $\rightarrow$ Transferred release files to the web server root.
  7. `Post-Deployment Verification` $\rightarrow$ Hit `healthcheck.php`, confirming status `UP`.

### Step 6: Verify the Live Deployment
* Refresh `http://localhost/to-do-list/`.
* Show that the application immediately reflects the new brand name `TaskFlow Pro v2.0`.
* Show `http://localhost/to-do-list/healthcheck.php` returning JSON status `UP`.

### Step 7: Demonstrate Pipeline Failure (Crucial DevOps Demonstration!)
* Open `login.php`.
* Deliberately introduce a syntax error (e.g., remove a semicolon `;` on line 10).
* Commit and push:
  ```bash
  git add login.php
  git commit -m "test: simulate syntax failure"
  git push origin main
  ```
* Show Jenkins immediately triggering Build `#3`.
* Watch Jenkins halt at **Stage 3: Code Validation** with status **FAILED (RED)**!
* Open **Console Output** in Jenkins and show the error:
  `PHP Parse error: syntax error, unexpected ... in login.php`.
* Explain to the examiner: *"Because of Continuous Integration, broken code is caught automatically before it can ever be deployed to production!"*

### Step 8: Fix and Restore
* Re-add the semicolon in `login.php`.
* Commit and push:
  ```bash
  git add login.php
  git commit -m "fix: restore valid syntax"
  git push origin main
  ```
* Show Jenkins triggering Build `#4`, passing all stages, and turning GREEN.

---

## 13. Frequently Asked College Viva Questions & Answers

### Q1: What is DevOps and how does this project demonstrate it?
> **Answer**: DevOps is a cultural and operational methodology that bridges software development (Dev) and IT operations (Ops) through automated workflows. This project demonstrates the entire lifecycle: code changes in Git are automatically detected by GitHub, triggering a Jenkins CI/CD pipeline that validates syntax, runs unit/schema tests, packages a build artifact, deploys to a web server, and monitors uptime through health check endpoints.

### Q2: What is the difference between Continuous Integration (CI) and Continuous Deployment (CD)?
> **Answer**:
> * **Continuous Integration (CI)**: Automates merging code changes from multiple developers, compiling/linting the code (`php -l`), and running automated tests (`tests/test_runner.php`) on every commit to catch bugs early.
> * **Continuous Deployment (CD)**: Automatically takes the successfully tested and built release artifact and deploys it into the production/target environment without human intervention.

### Q3: Why did you use Declarative Pipeline syntax instead of Scripted Pipeline in Jenkins?
> **Answer**: Declarative pipeline (`pipeline { ... }`) provides a structured, opinionated, and cleaner syntax with built-in blocks for `stages`, `steps`, `environment`, and `post` actions. It is easier to read, maintain, and audit, and offers native restart-from-stage capabilities compared to Scripted pipeline (`node { ... }`).

### Q4: Why is Vercel not the ideal native host for a traditional PHP + MySQL application?
> **Answer**: Vercel is built for stateless, serverless architectures (JAMstack, Node.js). Traditional PHP applications like this To-Do list depend on:
> 1. File-based persistent PHP sessions (`$_SESSION`), which are lost between ephemeral serverless invocations.
> 2. Direct TCP connection pooling to a MySQL database, whereas Vercel has no native MySQL database service.
> While Vercel can run PHP via community serverless builders (`vercel-php`), traditional hosting using Apache/Docker/PaaS is the authentic architectural fit.

### Q5: How are credentials and sensitive keys handled securely in this pipeline?
> **Answer**: No hardcoded credentials exist in source code. `config.php` dynamically loads values from environment variables (`getenv('DB_PASS')`) with local fallbacks. Real credentials are kept in `.env` (which is excluded by `.gitignore`) and securely stored within the Jenkins Credentials Store using `withCredentials()`.

### Q6: What is a Webhook and how does it differ from Polling?
> **Answer**:
> * **Webhook (Push Model)**: GitHub sends an instantaneous HTTP POST request to Jenkins the exact millisecond a push occurs. It uses zero unnecessary resources and triggers builds instantly.
> * **Polling (Pull Model)**: Jenkins repeatedly checks GitHub at regular intervals (e.g. every 5 minutes) asking if new commits exist. It is less responsive and wastes CPU cycles, but works when Jenkins is behind a firewall/NAT.

### Q7: What purpose does `healthcheck.php` serve in the DevOps lifecycle?
> **Answer**: It represents the **Monitor / Operate** stage. By returning a standardized JSON response verifying web server uptime and database connectivity, it allows post-deployment automated verification and external monitoring services (like UptimeRobot) to alert administrators if the service goes down.#   t o - d o - l i s t  
 
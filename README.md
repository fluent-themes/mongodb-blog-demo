# SimpleBlog - MongoDB Edition

A minimal blog application demonstrating MongoDB integration with PHP. Built for WordPress developers learning modern PHP stacks.

## Features

- List all posts with pagination (5 per page)
- View single post
- Create new post (title, content, category)
- Edit existing posts
- Delete posts (with confirmation)
- Search posts by title
- Category sidebar with post counts (MongoDB aggregation)
- Date range analytics (aggregation pipeline)
- Query optimization with indexes (title, category, createdAt)

---

## Part 1: Installing Prerequisites (The "WordPress" Layer)

Before installing this project, you need PHP, Composer, and MongoDB. Think of these as "WordPress core" - they must be installed first.

### 1.1 Install PHP 8.1+

**Windows:**
1. Go to https://windows.php.net/download/
2. Download **VS16 x64 Thread Safe** ZIP (e.g., php-8.1.x-Win32-vs16-x64.zip)
3. Extract the ZIP to `C:\php`
4. Add PHP to your system PATH:
   - Press `Win + R`, type `sysdm.cpl`, press Enter
   - Click **Advanced** tab → **Environment Variables**
   - Under **System variables**, find **Path**, click **Edit**
   - Click **New**, type `C:\php`, click **OK** on all windows
5. In the `C:\php` folder, copy `php.ini-development` and rename copy to `php.ini`
6. Open Command Prompt and verify: `php -v`

**Mac:**
```bash
# Install Homebrew first (if not installed)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP
brew install php

# Verify
php -v
```

**Linux (Ubuntu/Debian):**
```bash
sudo apt update
sudo apt install php8.1 php8.1-cli php8.1-common php8.1-curl php8.1-dev php-pear
php -v
```

### 1.2 Install Composer

**Windows:**
1. Go to https://getcomposer.org/download/
2. Download and run **Composer-Setup.exe**
3. Follow the installer (it finds PHP automatically)
4. Open a **new** Command Prompt and verify: `composer -V`

**Mac:**
```bash
brew install composer
composer -V
```

**Linux:**
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
composer -V
```

### 1.3 Install MongoDB

**Windows:**
1. Go to https://www.mongodb.com/try/download/community
2. Select **Windows**, download the MSI installer
3. Run installer, choose **Complete** setup
4. Check **Install MongoDB as a Service** 
5. MongoDB Compass (GUI) installs automatically
6. Open MongoDB Compass, click **Connect** (connects to `localhost:27017`)

**Mac:**
```bash
brew tap mongodb/brew
brew install mongodb-community
brew services start mongodb-community

# Verify (opens MongoDB shell)
mongosh
```

**Linux (Ubuntu):**
```bash
# Import GPG key
curl -fsSL https://pgp.mongodb.com/server-7.0.asc | sudo gpg -o /usr/share/keyrings/mongodb-server-7.0.gpg --dearmor

# Add repository
echo "deb [ signed-by=/usr/share/keyrings/mongodb-server-7.0.gpg ] https://repo.mongodb.org/apt/ubuntu jammy/mongodb-org/7.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-7.0.list

# Install and start
sudo apt update
sudo apt install mongodb-org
sudo systemctl start mongod
sudo systemctl enable mongod
```

### 1.4 Install MongoDB PHP Extension

**Windows:**
1. Go to https://pecl.php.net/package/mongodb
2. Download the DLL matching your PHP version (e.g., `php_mongodb-1.15.0-8.1-ts-vs16-x64.zip`)
3. Extract `php_mongodb.dll` to `C:\php\ext`
4. Open `C:\php\php.ini`, add this line: `extension=mongodb`
5. Verify: `php -m | findstr mongodb`

**Mac:**
```bash
pecl install mongodb
# Add to php.ini: extension=mongodb
php -m | grep mongodb
```

**Linux:**
```bash
sudo pecl install mongodb
# Add to php.ini: extension=mongodb
php -m | grep mongodb
```

### Verify All Prerequisites

Run these commands - all should work:
```bash
php -v                    # Should show PHP 8.1.x+
composer -V               # Should show Composer 2.x
php -m | grep mongodb     # Should show "mongodb"
mongosh --eval "db.version()"   # Should show MongoDB version
```

---

## Part 2: Installing SimpleBlog (The "Plugin" Layer)

### Step 1: Download the Project

**Option A - Using Git:**
```bash
cd ~/projects              # or C:\projects on Windows
git clone https://github.com/YOUR_USERNAME/simpleblog-mongodb.git
cd simpleblog-mongodb
```

**Option B - Download ZIP:**
1. Go to the GitHub repo page
2. Click green **Code** button → **Download ZIP**
3. Extract to your projects folder
4. Open Terminal/Command Prompt, navigate to the extracted folder

### Step 2: Install Dependencies

```bash
composer install
```
Wait for it to download packages (creates a `/vendor` folder).

### Step 3: Configure Environment

```bash
# Windows
copy .env.example .env

# Mac/Linux
cp .env.example .env
```

Open `.env` in a text editor. Default values work for local development:
```
MONGODB_URI=mongodb://localhost:27017
MONGODB_DB=simpleblog
MONGODB_COLLECTION=posts
```

### Step 4: Run the Application

```bash
php -S localhost:8000 -t public
```

Open your browser and go to: **http://localhost:8000**

You should see the SimpleBlog homepage! Try creating a post.

To stop the server: Press `Ctrl + C` in Terminal.

---

## Project Structure

```
simpleblog-mongodb/
├── public/
│   ├── index.php        # Homepage (list + pagination + search)
│   ├── view.php         # Single post view
│   ├── create.php       # New post form
│   ├── edit.php         # Edit post form
│   ├── delete.php       # Delete handler
│   ├── style.css        # Styles
│   └── .htaccess        # Apache rewrite rules
├── src/
│   └── Database.php     # MongoDB connection & CRUD class
├── .env.example         # Environment template
├── .gitignore
├── composer.json
└── README.md
```

---

## Deployment to Shared Hosting

Most shared hosts don't have MongoDB installed locally. Use **MongoDB Atlas** (free tier):

1. Go to https://www.mongodb.com/atlas and create a free account
2. Create a free cluster
3. Create a database user (save the password!)
4. Get your connection string (looks like): `mongodb+srv://user:pass@cluster0.xxxxx.mongodb.net/simpleblog`

**Deploy steps:**
1. Upload all project files via FTP to `/public_html/simpleblog/`
2. Edit `.env` with your Atlas connection string
3. Set your domain to point to the `/public` folder (or access via `/simpleblog/public/`)
4. Visit `http://yourdomain.com/simpleblog/public/` in browser

---

## Lessons for MySQL users

MongoDB differs from MySQL in several key ways that WordPress developers should understand:

- **Documents vs Rows**: Each post is a flexible JSON-like document, not a fixed table row. You can add fields without altering a schema.
- **Collections vs Tables**: Posts live in a "collection" rather than a table. No JOINs needed.
- **Aggregation vs GROUP BY**: The `countByCategory()` method uses MongoDB's aggregation pipeline instead of SQL GROUP BY.
- **Date Range Analytics**: The `getPostsByDateRange()` method demonstrates time-based aggregation.
- **Indexing Strategy**: The `ensureIndexes()` method creates indexes on `title` (text search), `category`, and `createdAt` for query optimization.
- **ObjectId vs Auto-increment**: MongoDB uses unique ObjectId strings instead of auto-incrementing integers.

The MongoDB PHP library provides a fluent, object-oriented interface that feels more modern than raw SQL queries.

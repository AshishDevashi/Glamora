# Glamora Jewelry Rental - Setup Instructions

Follow these steps to set up and run the Glamora Jewelry Rental application using XAMPP.

## 1. XAMPP Installation

If you haven't already installed XAMPP:
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP following the installation instructions for your operating system
3. Launch XAMPP Control Panel

## 2. Configure Project Directory

You have two options:

### Option A: Move the project to XAMPP's htdocs folder
```bash
# Copy your project to XAMPP's htdocs directory
cp -r /Users/developer/Documents/Glamora /Applications/XAMPP/xamppfiles/htdocs/glamora
```

### Option B: Create a symbolic link (preferred way)
```bash
# Create a symbolic link in XAMPP's htdocs directory pointing to your project
ln -s /Users/developer/Documents/Glamora /Applications/XAMPP/xamppfiles/htdocs/glamora
```

## 3. Database Setup

1. Start Apache and MySQL from XAMPP Control Panel
2. Open your browser and navigate to: http://localhost/phpmyadmin/
3. Create a new database:
   - Click "New" in the left sidebar
   - Enter "jewelry_rental" as the database name
   - Select "utf8mb4_unicode_ci" as the collation
   - Click "Create"
4. Import the database schema:
   - Select the newly created "jewelry_rental" database
   - Click the "Import" tab
   - Click "Choose File" and select the `database_schema.sql` file from your project
   - Click "Go" to import the schema

## 4. Configure Database Connection

Edit the `/php/config/database.php` file to match your MySQL settings:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'jewelry_rental');
define('DB_USER', 'root');     // XAMPP default user
define('DB_PASS', '');         // XAMPP default password (empty)
```

## 5. Set Up Virtual Host (Optional but Recommended)

1. Edit your Apache configuration file:
   - Open `/Applications/XAMPP/xamppfiles/etc/httpd.conf`
   - Make sure the following line is uncommented (remove # if present):
     ```
     Include /Applications/XAMPP/etc/extra/httpd-vhosts.conf
     ```

2. Edit the virtual hosts file:
   - Open `/Applications/XAMPP/xamppfiles/etc/extra/httpd-vhosts.conf`
   - Add the following configuration:
     ```
     <VirtualHost *:80>
         ServerName glamora.local
         DocumentRoot "/Applications/XAMPP/xamppfiles/htdocs/glamora"
         <Directory "/Applications/XAMPP/xamppfiles/htdocs/glamora">
             Options Indexes FollowSymLinks
             AllowOverride All
             Require all granted
         </Directory>
     </VirtualHost>
     ```

3. Edit your hosts file:
   - Open Terminal and run: `sudo nano /etc/hosts`
   - Add this line: `127.0.0.1 glamora.local`
   - Save and exit (Ctrl+O, Enter, Ctrl+X)

4. Restart Apache from XAMPP Control Panel

## 6. Run the Application

1. Make sure both Apache and MySQL are running in XAMPP Control Panel
2. Open your browser and navigate to:
   - If using virtual host: http://glamora.local/
   - If direct access: http://localhost/glamora/

## 7. Troubleshooting

### File Permissions
Make sure your files have proper permissions:

```bash
chmod -R 755 /Applications/XAMPP/xamppfiles/htdocs/glamora
chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/glamora/php/logs
chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/glamora/php/uploads
```

### .htaccess Configuration
Create an .htaccess file in the root directory of your application with the following content:

```
RewriteEngine On
RewriteBase /

# If the request is for a real directory or file, don't rewrite
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f

# API routes
RewriteRule ^api/(.*)$ php/api/index.php [QSA,L]

# Frontend routes
RewriteRule ^(.*)$ php/public/index.php [QSA,L]
```

### Apache Configuration
Make sure mod_rewrite is enabled in your Apache configuration.

## 8. Directory Structure
Your final directory structure should look like this:

```
/xampp/htdocs/glamora/
  ├── database_schema.sql
  ├── .htaccess
  ├── php/
  │   ├── api/
  │   ├── classes/
  │   ├── config/
  │   ├── logs/
  │   ├── public/
  │   ├── uploads/
  │   └── views/
  └── public/
      ├── css/
      ├── js/
      └── images/
```

## 9. Login Credentials
After setting up the database, you can log in with:
- Email: admin@glamora.com
- Password: admin123 
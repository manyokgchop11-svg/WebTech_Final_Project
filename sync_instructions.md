# Sync Instructions for School Server

## Method 1: Download and Upload
1. Go to: https://github.com/manyokgchop11-svg/WebTech_Final_Project
2. Click "Code" → "Download ZIP"
3. Extract the ZIP file
4. Upload all files to your school server directory
5. Make sure to overwrite existing files

## Method 2: Git Clone (if Git is available on school server)
```bash
git clone https://github.com/manyokgchop11-svg/WebTech_Final_Project.git
```

## Method 3: Manual File Transfer
Use FTP/SFTP client to upload files from your local project to school server

## Important Files to Check:
- index.php (main entry point)
- config/database.php (database configuration)
- All files in admin/, customer/, api/ folders
- assets/ folder with CSS, JS, images

## Database Setup on School Server:
1. Import database/setup.sql to your school database
2. Update config/database.php with school server credentials
3. Run setup_tables.php to initialize data

## Verification:
- Check that index.php loads correctly
- Test login functionality
- Verify database connection
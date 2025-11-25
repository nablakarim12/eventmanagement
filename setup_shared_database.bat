@echo off
echo ============================================
echo  EventSphere Shared Database Setup
echo ============================================
echo.
echo This will help you set up a shared database between
echo your 'eventmanagement' project and your friend's 'signupgo' project.
echo.

:MENU
echo Choose your preferred database hosting option:
echo.
echo 1. Supabase (Recommended - Free PostgreSQL)
echo 2. Railway (Alternative - Free PostgreSQL)
echo 3. PlanetScale (MySQL option)
echo 4. Just export migrations for manual setup
echo 5. Exit
echo.
set /p choice=Enter your choice (1-5): 

if "%choice%"=="1" goto SUPABASE
if "%choice%"=="2" goto RAILWAY  
if "%choice%"=="3" goto PLANETSCALE
if "%choice%"=="4" goto EXPORT
if "%choice%"=="5" goto END

echo Invalid choice. Please try again.
goto MENU

:SUPABASE
echo.
echo ============================================
echo  Supabase Setup Instructions
echo ============================================
echo.
echo 1. Go to https://supabase.com and create account
echo 2. Create new project called 'EventSphere'
echo 3. Wait for project to initialize (2-3 minutes)
echo 4. Go to Settings ^> Database
echo 5. Copy the connection details
echo.
echo Your connection will look like:
echo Host: db.xxxxxxxxxxxxx.supabase.co
echo Database: postgres
echo Port: 5432
echo User: postgres
echo Password: [your-password]
echo.
echo 6. Update your .env file with these credentials
echo 7. Share the same credentials with your friend
echo 8. Run 'php artisan migrate' to create tables
echo 9. Your friend copies your migrations and runs migrate too
echo.
echo Press any key to see next steps...
pause > nul
goto NEXT_STEPS

:RAILWAY
echo.
echo ============================================
echo  Railway Setup Instructions  
echo ============================================
echo.
echo 1. Go to https://railway.app and create account
echo 2. Create new project
echo 3. Add PostgreSQL service
echo 4. Copy the database URL or individual credentials
echo.
echo Connection format:
echo DATABASE_URL=postgresql://postgres:password@host:port/database
echo.
echo 5. Update both projects' .env files
echo 6. Run migrations to create tables
echo.
echo Press any key to see next steps...
pause > nul
goto NEXT_STEPS

:PLANETSCALE
echo.
echo ============================================
echo  PlanetScale Setup Instructions
echo ============================================
echo.
echo 1. Go to https://planetscale.com and create account
echo 2. Create new database called 'eventsphere'
echo 3. Create main branch
echo 4. Get connection string
echo.
echo Note: You'll need to modify your migrations for MySQL
echo (PostgreSQL-specific features will need adjustment)
echo.
echo 5. Update DB_CONNECTION=mysql in .env files
echo 6. Run migrations
echo.
echo Press any key to see next steps...
pause > nul
goto NEXT_STEPS

:EXPORT
echo.
echo ============================================
echo  Exporting Migrations for Manual Setup
echo ============================================
echo.
echo Creating migration export package...

set EXPORT_DIR=SharedDatabase_Export
if exist %EXPORT_DIR% rmdir /s /q %EXPORT_DIR%
mkdir %EXPORT_DIR%
mkdir %EXPORT_DIR%\migrations
mkdir %EXPORT_DIR%\models

echo Copying migration files...
copy "database\migrations\*.php" "%EXPORT_DIR%\migrations\"

echo Copying model files...
copy "app\Models\*.php" "%EXPORT_DIR%\models\"

echo Copying configuration...
copy ".env.example" "%EXPORT_DIR%\"
copy "SHARED_DATABASE_SETUP.md" "%EXPORT_DIR%\"
copy "TWO_PROJECT_WORKFLOW.md" "%EXPORT_DIR%\"

echo.
echo Export completed! 
echo Package created in: %EXPORT_DIR%\
echo.
echo Share this folder with your friend.
echo They should:
echo 1. Copy migrations to their database/migrations/ folder
echo 2. Copy models to their app/Models/ folder  
echo 3. Configure same database in their .env
echo 4. Run php artisan migrate
echo.
pause
goto END

:NEXT_STEPS
echo.
echo ============================================
echo  Next Steps for Both Projects
echo ============================================
echo.
echo FOR YOU (eventmanagement project):
echo 1. Update your .env with shared database credentials
echo 2. Test connection: php artisan migrate:status
echo 3. Run your existing migrations: php artisan migrate
echo 4. Verify tables created successfully
echo.
echo FOR YOUR FRIEND (signupgo project):
echo 1. Copy your migration files to their project
echo 2. Update their .env with SAME database credentials
echo 3. Copy your model files to their project
echo 4. Run: php artisan migrate (will see existing tables)
echo 5. Start building their user features
echo.
echo ONGOING COLLABORATION:
echo - When you create new migrations, share the files
echo - When friend creates new migrations, they share with you
echo - Both run 'php artisan migrate' after getting new files
echo - Use Git to version control migration files
echo.
echo ============================================
echo  Migration Files Your Friend Needs:
echo ============================================
dir /b database\migrations\*.php
echo.
echo ============================================
echo  Model Files Your Friend Needs:
echo ============================================  
dir /b app\Models\*.php
echo.
pause

:END
echo.
echo Setup complete! Check the documentation files for detailed instructions.
echo.
pause
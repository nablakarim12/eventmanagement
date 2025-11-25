@echo off
echo ============================================
echo  EventSphere Existing Database Sharing
echo ============================================
echo.
echo You already have a Supabase project with your database.
echo This will help you share it with your friend's 'signupgo' project.
echo.

:MENU
echo Choose your sharing approach:
echo.
echo 1. Share existing Supabase credentials (Quick & Easy)
echo 2. Create database export package (Manual setup)
echo 3. Show current database info
echo 4. Test current database connection
echo 5. Exit
echo.
set /p choice=Enter your choice (1-5): 

if "%choice%"=="1" goto SHARE_CREDENTIALS
if "%choice%"=="2" goto EXPORT_PACKAGE
if "%choice%"=="3" goto SHOW_INFO
if "%choice%"=="4" goto TEST_CONNECTION
if "%choice%"=="5" goto END

echo Invalid choice. Please try again.
goto MENU

:SHARE_CREDENTIALS
echo.
echo ============================================
echo  Sharing Your Existing Supabase Database
echo ============================================
echo.
echo STEP 1: Get your current database credentials
echo ----------------------------------------
echo 1. Go to https://supabase.com/dashboard
echo 2. Select your existing project
echo 3. Go to Settings ^> Database
echo 4. Copy the connection details
echo.
echo STEP 2: What to share with your friend
echo ------------------------------------
echo Share these exact values from your .env file:
echo - DB_HOST
echo - DB_PORT
echo - DB_DATABASE  
echo - DB_USERNAME
echo - DB_PASSWORD
echo.
echo STEP 3: Files your friend needs
echo -----------------------------
echo Your friend needs to copy these to their 'signupgo' project:
echo.
echo Migration files to copy:
dir /b database\migrations\*.php 2>nul
echo.
echo Model files to copy:
dir /b app\Models\*.php 2>nul
echo.
echo STEP 4: Your friend's setup process
echo ---------------------------------
echo 1. Copy all migration files to signupgo/database/migrations/
echo 2. Copy all model files to signupgo/app/Models/
echo 3. Update their .env with your SAME database credentials
echo 4. Run: php artisan migrate:status (should show all as 'Ran')
echo 5. Test: php artisan tinker ^> App\Models\Event::count()
echo.
echo STEP 5: Verify both projects work
echo -------------------------------
echo Both of you run: php artisan tinker
echo Then: App\Models\Event::all()
echo You should see the SAME events in both projects!
echo.
goto NEXT_STEPS

:EXPORT_PACKAGE
echo.
echo Creating comprehensive sharing package...
echo.

set EXPORT_DIR=SignupGo_Database_Package
if exist %EXPORT_DIR% rmdir /s /q %EXPORT_DIR%
mkdir %EXPORT_DIR%
mkdir %EXPORT_DIR%\migrations
mkdir %EXPORT_DIR%\models
mkdir %EXPORT_DIR%\docs

echo Copying migration files...
copy "database\migrations\*.php" "%EXPORT_DIR%\migrations\" >nul 2>&1

echo Copying model files...
copy "app\Models\*.php" "%EXPORT_DIR%\models\" >nul 2>&1

echo Creating setup instructions...
copy "EXISTING_SUPABASE_SETUP.md" "%EXPORT_DIR%\docs\" >nul 2>&1
copy ".env.example" "%EXPORT_DIR%\" >nul 2>&1

echo Creating quick setup guide...
echo # SignupGo Database Setup > %EXPORT_DIR%\QUICK_SETUP.md
echo. >> %EXPORT_DIR%\QUICK_SETUP.md
echo ## What to do: >> %EXPORT_DIR%\QUICK_SETUP.md
echo 1. Copy migration files from migrations/ to your signupgo/database/migrations/ >> %EXPORT_DIR%\QUICK_SETUP.md
echo 2. Copy model files from models/ to your signupgo/app/Models/ >> %EXPORT_DIR%\QUICK_SETUP.md
echo 3. Ask your friend for their Supabase database credentials >> %EXPORT_DIR%\QUICK_SETUP.md
echo 4. Update your signupgo/.env with those credentials >> %EXPORT_DIR%\QUICK_SETUP.md
echo 5. Run: php artisan migrate:status >> %EXPORT_DIR%\QUICK_SETUP.md
echo 6. Test: php artisan tinker ^> App\Models\Event::count() >> %EXPORT_DIR%\QUICK_SETUP.md
echo. >> %EXPORT_DIR%\QUICK_SETUP.md
echo ## Ongoing collaboration: >> %EXPORT_DIR%\QUICK_SETUP.md
echo - When your friend makes new migrations, copy them to your project >> %EXPORT_DIR%\QUICK_SETUP.md
echo - When you make new migrations, share them with your friend >> %EXPORT_DIR%\QUICK_SETUP.md
echo - Both of you run migrations after getting new files >> %EXPORT_DIR%\QUICK_SETUP.md

echo.
echo ============================================
echo Package created: %EXPORT_DIR%\
echo ============================================
echo.
echo Send this folder to your friend along with your database credentials.
echo.
goto MENU

:SHOW_INFO
echo.
echo ============================================
echo  Current Database Information
echo ============================================
echo.
echo Checking your current .env configuration...
echo.
findstr "DB_" .env 2>nul
echo.
echo Migration files you have:
dir /b database\migrations\*.php 2>nul
echo.
echo Model files you have:
dir /b app\Models\*.php 2>nul
echo.
goto MENU

:TEST_CONNECTION
echo.
echo ============================================
echo  Testing Database Connection
echo ============================================
echo.
echo Testing current database connection...
php artisan migrate:status
echo.
echo If you see a list of migrations above, your database is working!
echo.
goto MENU

:NEXT_STEPS
echo.
echo ============================================
echo  Important Security Notes
echo ============================================
echo.
echo ⚠️  BEFORE SHARING DATABASE ACCESS:
echo.
echo 1. BACKUP YOUR DATABASE
echo    - Go to Supabase dashboard
echo    - Database ^> Backups
echo    - Create manual backup
echo.
echo 2. SHARE CREDENTIALS SECURELY  
echo    - Don't send via email/text
echo    - Use secure method (Signal, encrypted file, etc.)
echo.
echo 3. MONITOR USAGE
echo    - Two projects = double database usage
echo    - Check Supabase usage dashboard regularly
echo.
echo 4. COORDINATE SCHEMA CHANGES
echo    - Discuss before creating new migrations
echo    - Test schema changes carefully
echo.
echo ============================================
echo  Benefits of This Approach
echo ============================================
echo.
echo ✅ No new Supabase project needed
echo ✅ All your existing data preserved
echo ✅ Real-time sharing between projects  
echo ✅ Both projects see same events/organizers
echo ✅ Immediate access to production-like data
echo.
pause

:END
echo.
echo Setup guidance complete!
echo.
pause
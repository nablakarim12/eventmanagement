@echo off
echo ============================================
echo  EventSphere Database Sharing Package
echo ============================================
echo.

set SHARE_DIR=EventSphere_Database_Share
set TIMESTAMP=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%

echo Creating sharing package...
if exist %SHARE_DIR% rmdir /s /q %SHARE_DIR%
mkdir %SHARE_DIR%
mkdir %SHARE_DIR%\migrations
mkdir %SHARE_DIR%\models
mkdir %SHARE_DIR%\seeders
mkdir %SHARE_DIR%\config
mkdir %SHARE_DIR%\docs

echo.
echo Copying migration files...
copy "database\migrations\2014_10_12_000000_create_users_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2014_10_12_100000_create_password_reset_tokens_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2019_08_19_000000_create_failed_jobs_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2019_12_14_000001_create_personal_access_tokens_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2025_10_03_151812_create_admins_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2025_10_03_151857_create_event_organizers_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2025_10_03_151858_create_event_organizer_documents_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2025_10_07_145406_create_event_categories_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2025_10_07_145951_create_event_types_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2025_10_07_153123_create_event_organizer_password_resets_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2025_10_13_153257_create_organizer_documents_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2025_10_13_164619_create_jobs_table.php" "%SHARE_DIR%\migrations\"
copy "database\migrations\2025_10_27_161100_create_events_table.php" "%SHARE_DIR%\migrations\"

echo.
echo Copying model files...
copy "app\Models\User.php" "%SHARE_DIR%\models\"
copy "app\Models\Admin.php" "%SHARE_DIR%\models\"
copy "app\Models\Event.php" "%SHARE_DIR%\models\"
copy "app\Models\EventCategory.php" "%SHARE_DIR%\models\"
copy "app\Models\EventOrganizer.php" "%SHARE_DIR%\models\"
copy "app\Models\EventOrganizerDocument.php" "%SHARE_DIR%\models\"
copy "app\Models\EventType.php" "%SHARE_DIR%\models\"
copy "app\Models\OrganizerDocument.php" "%SHARE_DIR%\models\"

echo.
echo Copying configuration files...
copy ".env.example" "%SHARE_DIR%\"
copy "config\auth.php" "%SHARE_DIR%\config\"

echo.
echo Copying seeder files...
if exist "database\seeders\*.php" copy "database\seeders\*.php" "%SHARE_DIR%\seeders\"

echo.
echo Copying documentation...
copy "DATABASE_SCHEMA.md" "%SHARE_DIR%\docs\"
copy "SETUP_GUIDE.md" "%SHARE_DIR%\docs\"
copy "SHARING_CHECKLIST.md" "%SHARE_DIR%\docs\"

echo.
echo Creating instructions file...
echo # EventSphere Database Package > %SHARE_DIR%\README.md
echo. >> %SHARE_DIR%\README.md
echo ## Installation Instructions >> %SHARE_DIR%\README.md
echo. >> %SHARE_DIR%\README.md
echo 1. Copy migration files from `migrations/` to your `database/migrations/` folder >> %SHARE_DIR%\README.md
echo 2. Copy model files from `models/` to your `app/Models/` folder >> %SHARE_DIR%\README.md
echo 3. Copy `.env.example` to your project root and rename to `.env` >> %SHARE_DIR%\README.md
echo 4. Copy `config/auth.php` to your `config/` folder >> %SHARE_DIR%\README.md
echo 5. Configure your database settings in `.env` >> %SHARE_DIR%\README.md
echo 6. Run: `php artisan key:generate` >> %SHARE_DIR%\README.md
echo 7. Run: `php artisan migrate` >> %SHARE_DIR%\README.md
echo 8. Optional: Copy and run seeders for sample data >> %SHARE_DIR%\README.md
echo. >> %SHARE_DIR%\README.md
echo ## Documentation >> %SHARE_DIR%\README.md
echo. >> %SHARE_DIR%\README.md
echo - See `docs/DATABASE_SCHEMA.md` for database structure >> %SHARE_DIR%\README.md
echo - See `docs/SETUP_GUIDE.md` for detailed setup instructions >> %SHARE_DIR%\README.md
echo - See `docs/SHARING_CHECKLIST.md` for collaboration workflow >> %SHARE_DIR%\README.md

echo.
echo ============================================
echo  Package created successfully!
echo ============================================
echo.
echo Package location: %SHARE_DIR%\
echo.
echo Share this folder with your teammate. It contains:
echo - All migration files
echo - All model files  
echo - Configuration files
echo - Setup documentation
echo - Installation instructions
echo.
echo Your teammate should follow the README.md instructions
echo to set up the database on their system.
echo.
pause
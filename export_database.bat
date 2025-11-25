@echo off
echo EventSphere Database Export Script
echo ====================================

echo.
echo Generating Laravel schema dump...
php artisan schema:dump --prune

echo.
echo Export completed!
echo.
echo Files to share with your teammate:
echo 1. All files in database\migrations\
echo 2. All files in app\Models\
echo 3. database\seeders\ (optional - for sample data)
echo 4. DATABASE_SCHEMA.md documentation
echo 5. .env.example (database configuration example)
echo.
echo Your teammate should:
echo 1. Copy the migration files to their database\migrations\ folder
echo 2. Copy the model files to their app\Models\ folder
echo 3. Configure their .env database settings
echo 4. Run: php artisan migrate
echo 5. Run: php artisan db:seed (if using seeders)
echo.
pause
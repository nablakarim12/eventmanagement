# EventSphere Database Package 
 
## Installation Instructions 
 
1. Copy migration files from `migrations/` to your `database/migrations/` folder 
2. Copy model files from `models/` to your `app/Models/` folder 
3. Copy `.env.example` to your project root and rename to `.env` 
4. Copy `config/auth.php` to your `config/` folder 
5. Configure your database settings in `.env` 
6. Run: `php artisan key:generate` 
7. Run: `php artisan migrate` 
8. Optional: Copy and run seeders for sample data 
 
## Documentation 
 
- See `docs/DATABASE_SCHEMA.md` for database structure 
- See `docs/SETUP_GUIDE.md` for detailed setup instructions 
- See `docs/SHARING_CHECKLIST.md` for collaboration workflow 

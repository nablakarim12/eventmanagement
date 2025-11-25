#!/bin/bash

# EventSphere Database Export Script
# Run this to export current database schema and data for sharing

# Export database schema only (structure)
php artisan schema:dump --prune

# Alternative: Export with sample data
# pg_dump --host=localhost --username=your_username --dbname=eventmanagement --file=eventsphere_schema.sql --schema-only

# Create a complete backup with data
# pg_dump --host=localhost --username=your_username --dbname=eventmanagement --file=eventsphere_full.sql

echo "Database export completed!"
echo ""
echo "Files to share with your teammate:"
echo "1. All files in database/migrations/"
echo "2. All files in app/Models/"
echo "3. database/seeders/ (optional - for sample data)"
echo "4. This DATABASE_SCHEMA.md documentation"
echo ""
echo "Your teammate should:"
echo "1. Copy the migration files to their database/migrations/ folder"
echo "2. Copy the model files to their app/Models/ folder"
echo "3. Run: php artisan migrate"
echo "4. Run: php artisan db:seed (if using seeders)"
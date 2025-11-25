# Shared Database Setup Guide

## Option A: Supabase (Recommended - Free Tier)

### 1. Create Supabase Account
- Go to https://supabase.com
- Sign up for free account
- Create new project: "EventSphere"

### 2. Get Database Credentials
After project creation, go to Settings > Database:
```
Host: db.[your-project-ref].supabase.co
Database: postgres
Port: 5432
User: postgres
Password: [your-password]
```

### 3. Configure Both Projects

#### In your `eventmanagement/.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=db.[your-project-ref].supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[your-supabase-password]
```

#### In your friend's `signupgo/.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=db.[your-project-ref].supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[your-supabase-password]
```

## Option B: Railway (Alternative)

### 1. Create Railway Account
- Go to https://railway.app
- Sign up and create new project
- Add PostgreSQL service

### 2. Get Connection URL
Railway provides a connection string like:
```
postgresql://postgres:password@host:port/database
```

### 3. Configure Both Projects
```env
DATABASE_URL=postgresql://postgres:password@host:port/database
# Or separate variables:
DB_CONNECTION=pgsql
DB_HOST=host
DB_PORT=port
DB_DATABASE=database
DB_USERNAME=postgres
DB_PASSWORD=password
```

## Option C: PlanetScale (MySQL Alternative)

If you prefer MySQL:
- Go to https://planetscale.com
- Create free database
- Get connection credentials
- Both projects use same credentials

## Security Considerations

1. **Use Environment Variables**: Never commit database credentials to Git
2. **Limit Access**: Only give credentials to trusted developers
3. **Backup Regularly**: Cloud providers handle this, but verify
4. **Monitor Usage**: Stay within free tier limits

## Cost Comparison

- **Supabase**: Free tier - 500MB storage, 2GB transfer
- **Railway**: Free tier - $5/month credit
- **PlanetScale**: Free tier - 1 database, 1GB storage
- **AWS RDS**: Paid only - ~$15-25/month for small instance

## Recommended: Supabase
- Best free tier limits
- Great PostgreSQL support
- Built-in admin interface
- Real-time features (if needed later)
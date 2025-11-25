# QR Check-In System - Automated Installation Script
# For SignupGo Project
# Run this script from the SignupGo root directory

Write-Host "╔═══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  QR Code Check-In System - Installation Script               ║" -ForegroundColor Cyan
Write-Host "║  For SignupGo User Portal                                    ║" -ForegroundColor Cyan
Write-Host "╚═══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Configuration
$packagePath = "C:\laragon\www\eventmanagement\QR_CheckIn_Package_For_SignupGo"
$signupGoPath = Get-Location

Write-Host "📦 Package Location: $packagePath" -ForegroundColor Yellow
Write-Host "🎯 Installing to: $signupGoPath" -ForegroundColor Yellow
Write-Host ""

# Verify package exists
if (!(Test-Path $packagePath)) {
    Write-Host "❌ ERROR: Package not found at $packagePath" -ForegroundColor Red
    Write-Host "Please ensure the package folder exists." -ForegroundColor Red
    exit 1
}

# Verify we're in SignupGo directory
if (!(Test-Path ".\artisan")) {
    Write-Host "❌ ERROR: This doesn't appear to be a Laravel project directory." -ForegroundColor Red
    Write-Host "Please run this script from your SignupGo root directory." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Pre-flight checks passed!" -ForegroundColor Green
Write-Host ""

# Ask for confirmation
$confirm = Read-Host "Ready to install? This will copy files to your SignupGo project. (Y/N)"
if ($confirm -ne "Y" -and $confirm -ne "y") {
    Write-Host "Installation cancelled." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "🚀 Starting installation..." -ForegroundColor Cyan
Write-Host ""

# Step 1: Copy Controller
Write-Host "📄 [1/4] Copying QrCheckInController..." -ForegroundColor Yellow
try {
    Copy-Item "$packagePath\app\Http\Controllers\QrCheckInController.php" -Destination "app\Http\Controllers\" -Force
    Write-Host "  ✅ Controller copied successfully" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Failed to copy controller: $_" -ForegroundColor Red
    exit 1
}

# Step 2: Copy QR Views
Write-Host "📄 [2/4] Copying QR check-in views..." -ForegroundColor Yellow
try {
    New-Item -ItemType Directory -Path "resources\views\qr" -Force | Out-Null
    Copy-Item "$packagePath\resources\views\qr\*" -Destination "resources\views\qr\" -Recurse -Force
    Write-Host "  ✅ QR views copied successfully (4 files)" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Failed to copy QR views: $_" -ForegroundColor Red
    exit 1
}

# Step 3: Copy Dashboard Views
Write-Host "📄 [3/4] Copying dashboard registration views..." -ForegroundColor Yellow
try {
    New-Item -ItemType Directory -Path "resources\views\dashboard\registrations" -Force | Out-Null
    Copy-Item "$packagePath\resources\views\dashboard\registrations\*" -Destination "resources\views\dashboard\registrations\" -Recurse -Force
    Write-Host "  ✅ Dashboard views copied successfully (2 files)" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Failed to copy dashboard views: $_" -ForegroundColor Red
    exit 1
}

# Step 4: Clear Caches
Write-Host "🧹 [4/4] Clearing Laravel caches..." -ForegroundColor Yellow
try {
    php artisan route:clear | Out-Null
    php artisan view:clear | Out-Null
    php artisan config:clear | Out-Null
    php artisan cache:clear | Out-Null
    Write-Host "  ✅ Caches cleared successfully" -ForegroundColor Green
} catch {
    Write-Host "  ⚠️  Warning: Some caches may not have been cleared" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "╔═══════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║  ✅ Installation Complete!                                    ║" -ForegroundColor Green
Write-Host "╚═══════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""

# Display next steps
Write-Host "📋 NEXT STEPS:" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Add routes to routes/web.php" -ForegroundColor Yellow
Write-Host "   Open: routes/web.php" -ForegroundColor Gray
Write-Host "   Copy routes from: $packagePath\routes_to_add.txt" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Update EventRegistration model" -ForegroundColor Yellow
Write-Host "   Open: app/Models/EventRegistration.php" -ForegroundColor Gray
Write-Host "   Add to `$fillable: 'qr_code', 'qr_image_path', 'checked_in_at'" -ForegroundColor Gray
Write-Host ""
Write-Host "3. Create storage symlink (if not already done)" -ForegroundColor Yellow
Write-Host "   Run: php artisan storage:link" -ForegroundColor Gray
Write-Host ""
Write-Host "4. Test the installation" -ForegroundColor Yellow
Write-Host "   Visit: http://signupgo.test/dashboard/registrations" -ForegroundColor Gray
Write-Host ""

# Display test URLs
Write-Host "🧪 TEST URLS (Smart City Event):" -ForegroundColor Cyan
Write-Host ""
Write-Host "Mantul (Both):       http://signupgo.test/check-in/REG-BYFVRQK0AR0D" -ForegroundColor Gray
Write-Host "Ahmad Maslan (Jury): http://signupgo.test/check-in/REG-JCWRB6SIJUJY" -ForegroundColor Gray
Write-Host "Qixi Chang (Jury):   http://signupgo.test/check-in/REG-XEHQFYOOQLI1" -ForegroundColor Gray
Write-Host ""

# Summary
Write-Host "📁 FILES INSTALLED:" -ForegroundColor Cyan
Write-Host "  ✓ app/Http/Controllers/QrCheckInController.php" -ForegroundColor Green
Write-Host "  ✓ resources/views/qr/*.blade.php (4 files)" -ForegroundColor Green
Write-Host "  ✓ resources/views/dashboard/registrations/*.blade.php (2 files)" -ForegroundColor Green
Write-Host ""

Write-Host "📖 For detailed instructions, see:" -ForegroundColor Cyan
Write-Host "  • $packagePath\INSTALLATION_GUIDE.md" -ForegroundColor Gray
Write-Host "  • $packagePath\STEP_BY_STEP_GUIDE.md" -ForegroundColor Gray
Write-Host ""

Write-Host "✨ Happy event managing! 🎉" -ForegroundColor Cyan
Write-Host ""

# Ask if user wants to open routes file
$openRoutes = Read-Host "Would you like to open routes/web.php now to add routes? (Y/N)"
if ($openRoutes -eq "Y" -or $openRoutes -eq "y") {
    notepad "routes\web.php"
    notepad "$packagePath\routes_to_add.txt"
    Write-Host "✅ Opened routes files in Notepad" -ForegroundColor Green
}

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

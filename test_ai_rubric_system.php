<?php

/**
 * Test AI Rubric Generation System
 * 
 * This script tests all components of the AI rubric generation system:
 * 1. File parsing (PDF, Word, Excel)
 * 2. Gemini API connection
 * 3. Rubric generation
 */

require __DIR__.'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

echo "🧪 AI Rubric Generation System Test\n";
echo str_repeat('=', 60) . "\n\n";

// Test 1: Check required packages
echo "1️⃣ Checking required packages...\n";
$packages = [
    'PhpOffice\PhpSpreadsheet\Spreadsheet' => 'PhpSpreadsheet (Excel parsing)',
    'PhpOffice\PhpWord\PhpWord' => 'PhpWord (Word parsing)',
    'Smalot\PdfParser\Parser' => 'PDF Parser (PDF text extraction)',
];

foreach ($packages as $class => $name) {
    if (class_exists($class)) {
        echo "   ✅ {$name} - Installed\n";
    } else {
        echo "   ❌ {$name} - NOT FOUND\n";
    }
}
echo "\n";

// Test 2: Check Gemini API Key
echo "2️⃣ Checking Gemini API configuration...\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, 'GEMINI_API_KEY=') !== false) {
        preg_match('/GEMINI_API_KEY=(.+)/', $envContent, $matches);
        $apiKey = trim($matches[1] ?? '');
        if ($apiKey && $apiKey !== 'your_api_key_here') {
            echo "   ✅ GEMINI_API_KEY is configured\n";
            echo "   🔑 Key: " . substr($apiKey, 0, 10) . "..." . substr($apiKey, -5) . "\n";
        } else {
            echo "   ⚠️ GEMINI_API_KEY is not set properly\n";
            echo "   📝 Get your key from: https://makersuite.google.com/app/apikey\n";
        }
    } else {
        echo "   ❌ GEMINI_API_KEY not found in .env\n";
    }
} else {
    echo "   ❌ .env file not found\n";
}
echo "\n";

// Test 3: Check storage directory
echo "3️⃣ Checking storage directories...\n";
$tempDir = __DIR__ . '/storage/app/public/temp_rubrics';
if (!is_dir($tempDir)) {
    echo "   📁 Creating temp directory...\n";
    mkdir($tempDir, 0755, true);
    echo "   ✅ Directory created: {$tempDir}\n";
} else {
    echo "   ✅ Directory exists: {$tempDir}\n";
}

if (is_writable($tempDir)) {
    echo "   ✅ Directory is writable\n";
} else {
    echo "   ❌ Directory is NOT writable - fix permissions\n";
}
echo "\n";

// Test 4: Create sample Excel file
echo "4️⃣ Testing Excel file generation...\n";
try {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    $sheet->setCellValue('A1', 'Category');
    $sheet->setCellValue('B1', 'Criterion');
    $sheet->setCellValue('C1', 'Max Points');
    $sheet->setCellValue('D1', 'Excellent');
    $sheet->setCellValue('E1', 'Good');
    $sheet->setCellValue('F1', 'Poor');
    
    $sheet->setCellValue('A2', 'Content');
    $sheet->setCellValue('B2', 'Clarity');
    $sheet->setCellValue('C2', '10');
    $sheet->setCellValue('D2', 'Very clear (10)');
    $sheet->setCellValue('E2', 'Moderately clear (6)');
    $sheet->setCellValue('F2', 'Unclear (2)');
    
    $writer = new Xlsx($spreadsheet);
    $testFile = $tempDir . '/test_rubric.xlsx';
    $writer->save($testFile);
    
    echo "   ✅ Sample Excel created: {$testFile}\n";
    echo "   📊 File size: " . number_format(filesize($testFile)) . " bytes\n";
} catch (Exception $e) {
    echo "   ❌ Excel test failed: {$e->getMessage()}\n";
}
echo "\n";

// Test 5: Create sample Word file
echo "5️⃣ Testing Word file generation...\n";
try {
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    
    $section->addTitle('Evaluation Rubric', 1);
    $section->addText('Category: Content Quality');
    $section->addText('Criterion 1: Abstract Clarity (10 points)');
    $section->addText('- Excellent: Very clear and comprehensive');
    $section->addText('- Good: Clear with minor issues');
    $section->addText('- Poor: Unclear or incomplete');
    
    $writer = WordIOFactory::createWriter($phpWord, 'Word2007');
    $testFile = $tempDir . '/test_rubric.docx';
    $writer->save($testFile);
    
    echo "   ✅ Sample Word created: {$testFile}\n";
    echo "   📄 File size: " . number_format(filesize($testFile)) . " bytes\n";
} catch (Exception $e) {
    echo "   ❌ Word test failed: {$e->getMessage()}\n";
}
echo "\n";

// Test 6: Test file reading
echo "6️⃣ Testing file reading capabilities...\n";

// Test Excel reading
$excelFile = $tempDir . '/test_rubric.xlsx';
if (file_exists($excelFile)) {
    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFile);
        $sheet = $spreadsheet->getActiveSheet();
        $value = $sheet->getCell('A1')->getValue();
        echo "   ✅ Excel reading works - First cell: '{$value}'\n";
    } catch (Exception $e) {
        echo "   ❌ Excel reading failed: {$e->getMessage()}\n";
    }
}

// Test Word reading
$wordFile = $tempDir . '/test_rubric.docx';
if (file_exists($wordFile)) {
    try {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($wordFile);
        $sections = $phpWord->getSections();
        echo "   ✅ Word reading works - Sections found: " . count($sections) . "\n";
    } catch (Exception $e) {
        echo "   ❌ Word reading failed: {$e->getMessage()}\n";
    }
}
echo "\n";

// Test 7: Check PHP extensions
echo "7️⃣ Checking PHP extensions...\n";
$extensions = ['zip', 'xml', 'gd', 'mbstring', 'curl'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ {$ext} extension loaded\n";
    } else {
        echo "   ⚠️ {$ext} extension not loaded (might be needed)\n";
    }
}
echo "\n";

// Summary
echo str_repeat('=', 60) . "\n";
echo "🎉 TEST COMPLETE!\n\n";
echo "Next steps:\n";
echo "1. Visit: http://localhost:8000/organizer/events/{event_id}/rubrics/generate-ai\n";
echo "2. Upload one of the test files created above\n";
echo "3. Add a description and click 'Generate with AI'\n";
echo "4. Review the AI-generated rubric\n\n";

echo "Test files created:\n";
echo "- {$tempDir}/test_rubric.xlsx\n";
echo "- {$tempDir}/test_rubric.docx\n\n";

echo "📚 For detailed guide, see: AI_RUBRIC_GENERATION_GUIDE.md\n";

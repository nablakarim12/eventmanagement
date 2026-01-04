<?php

namespace App\Http\Controllers;

use App\Models\GeneratedCertificate;
use App\Models\SimpleCertificateTemplate;

class CertificateViewController extends Controller
{
    public function view(GeneratedCertificate $certificate)
    {
        // Try to load SimpleCertificateTemplate first
        $template = SimpleCertificateTemplate::find($certificate->template_id);

        // If no SimpleCertificateTemplate, fall back to old system
        if (!$template) {
            $template = $certificate->template;
            return view('certificates.html-certificate', compact('certificate', 'template'));
        }

        // Check if using custom uploaded template or AI generation
        if ($template->use_custom_template && $template->custom_template_url) {
            // Custom template with Cloudinary overlay
            return view('certificates.custom-template', compact('certificate', 'template'));
        } elseif ($template->use_ai_generation && $template->template_file_url) {
            // AI-generated certificate
            return view('certificates.ai-generated', compact('certificate', 'template'));
        }

        // Use built-in template design
        $viewName = 'certificates.classic-landscape'; // Default
        
        switch($template->template_design) {
            case 'modern':
                $viewName = 'certificates.modern-landscape';
                break;
            case 'elegant':
                $viewName = 'certificates.elegant-landscape';
                break;
            case 'minimal':
                $viewName = 'certificates.minimal-landscape';
                break;
            default:
                $viewName = 'certificates.classic-landscape';
        }

        return view($viewName, compact('certificate', 'template'));
    }
}

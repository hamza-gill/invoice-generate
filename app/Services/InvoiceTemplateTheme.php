<?php

namespace App\Services;

use App\Models\InvoiceTemplate;

class InvoiceTemplateTheme
{
    /**
     * UI theme tokens for guest invoice pages (accept, public, etc.).
     *
     * @return array<string, string>
     */
    public function forTemplate(?InvoiceTemplate $template): array
    {
        $defaults = $this->defaults();

        if (!$template) {
            return $defaults;
        }

        $config = is_array($template->config) ? $template->config : [];
        $primary = $config['primary_color'] ?? $defaults['primary'];
        $secondary = $config['secondary_color'] ?? $defaults['secondary'];

        $headerText = match ($template->slug) {
            'tech-startup' => $secondary,
            'bold-corporate' => $secondary,
            'construction' => '#1f2937',
            default => '#ffffff',
        };

        $headerBg = match ($template->slug) {
            'creative-agency' => "linear-gradient(135deg, {$primary} 0%, {$secondary} 100%)",
            'modern-minimal' => "linear-gradient(135deg, {$primary} 0%, {$secondary} 100%)",
            default => $primary,
        };

        $primaryRgb = $this->hexToRgb($primary);
        $secondaryRgb = $this->hexToRgb($secondary);

        return [
            'name' => $template->name,
            'slug' => $template->slug,
            'primary' => $primary,
            'secondary' => $secondary,
            'header_bg' => $headerBg,
            'header_text' => $headerText,
            'header_text_muted' => $this->mutedHeaderText($headerText),
            'accent' => $secondary,
            'page_bg' => "linear-gradient(135deg, rgba({$primaryRgb}, 0.08) 0%, rgba({$secondaryRgb}, 0.06) 50%, rgba({$primaryRgb}, 0.04) 100%)",
            'page_grid' => $this->pageGridCss($primaryRgb, $secondaryRgb),
            'card_shadow' => "0 18px 60px -20px rgba({$primaryRgb}, 0.35)",
            'card_border' => "rgba({$primaryRgb}, 0.18)",
            'badge_bg' => "rgba({$secondaryRgb}, 0.15)",
            'badge_text' => $secondary,
            'badge_ring' => "rgba({$secondaryRgb}, 0.35)",
            'button_text' => $this->buttonTextColor($primary),
            'icon' => $secondary,
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function defaults(): array
    {
        return [
            'name' => 'Default',
            'slug' => 'default',
            'primary' => '#4f46e5',
            'secondary' => '#7c3aed',
            'header_bg' => 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 45%, #a855f7 100%)',
            'header_text' => '#ffffff',
            'header_text_muted' => 'rgba(255,255,255,0.82)',
            'accent' => '#7c3aed',
            'page_bg' => 'linear-gradient(135deg, rgba(79,70,229,0.08) 0%, rgba(124,58,237,0.08) 45%, rgba(168,85,247,0.08) 100%)',
            'page_grid' => $this->pageGridCss('79,70,229', '124,58,237'),
            'card_shadow' => '0 18px 60px -20px rgba(79,70,229,0.35)',
            'card_border' => 'rgba(255,255,255,0.6)',
            'badge_bg' => 'rgba(16,185,129,0.15)',
            'badge_text' => '#6ee7b7',
            'badge_ring' => 'rgba(52,211,153,0.35)',
            'button_text' => '#ffffff',
            'icon' => '#4f46e5',
        ];
    }

    protected function pageGridCss(string $primaryRgb, string $secondaryRgb): string
    {
        return implode(', ', [
            "radial-gradient(circle at 20% 20%, rgba({$primaryRgb},0.12), transparent 45%)",
            "radial-gradient(circle at 80% 10%, rgba({$secondaryRgb},0.10), transparent 40%)",
            "radial-gradient(circle at 30% 90%, rgba({$secondaryRgb},0.10), transparent 50%)",
            "linear-gradient(to right, rgba({$primaryRgb},0.06) 1px, transparent 1px)",
            "linear-gradient(to bottom, rgba({$primaryRgb},0.06) 1px, transparent 1px)",
        ]);
    }

    protected function mutedHeaderText(string $headerText): string
    {
        if (str_starts_with($headerText, 'rgba') || str_starts_with($headerText, 'rgb')) {
            return $headerText;
        }

        if ($headerText === '#ffffff') {
            return 'rgba(255,255,255,0.82)';
        }

        $rgb = $this->hexToRgb($headerText);

        return "rgba({$rgb},0.85)";
    }

    protected function buttonTextColor(string $primary): string
    {
        return $this->isLightColor($primary) ? '#1f2937' : '#ffffff';
    }

    protected function isLightColor(string $hex): bool
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.62;
    }

    protected function hexToRgb(string $hex): string
    {
        $hex = ltrim(trim($hex), '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return '79,70,229';
        }

        return implode(',', [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ]);
    }
}

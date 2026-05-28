@php
    $theme = $templateTheme ?? [];
@endphp
<style>
    :root {
        --theme-primary: {{ $theme['primary'] ?? '#4f46e5' }};
        --theme-secondary: {{ $theme['secondary'] ?? '#7c3aed' }};
        --theme-header: {{ $theme['header_bg'] ?? 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 45%, #a855f7 100%)' }};
        --theme-header-text: {{ $theme['header_text'] ?? '#ffffff' }};
        --theme-header-text-muted: {{ $theme['header_text_muted'] ?? 'rgba(255,255,255,0.82)' }};
        --theme-accent: {{ $theme['accent'] ?? '#7c3aed' }};
        --theme-icon: {{ $theme['icon'] ?? '#4f46e5' }};
        --theme-page-bg: {{ $theme['page_bg'] ?? 'linear-gradient(135deg, rgba(79,70,229,0.08) 0%, rgba(124,58,237,0.08) 100%)' }};
        --theme-page-grid: {!! $theme['page_grid'] ?? 'radial-gradient(circle at 20% 20%, rgba(79,70,229,0.12), transparent 45%)' !!};
        --theme-card-shadow: {{ $theme['card_shadow'] ?? '0 18px 60px -20px rgba(79,70,229,0.35)' }};
        --theme-card-border: {{ $theme['card_border'] ?? 'rgba(255,255,255,0.6)' }};
        --theme-badge-bg: {{ $theme['badge_bg'] ?? 'rgba(16,185,129,0.15)' }};
        --theme-badge-text: {{ $theme['badge_text'] ?? '#6ee7b7' }};
        --theme-badge-ring: {{ $theme['badge_ring'] ?? 'rgba(52,211,153,0.35)' }};
        --theme-button-text: {{ $theme['button_text'] ?? '#ffffff' }};
        --bloom-header: var(--theme-header);
    }
    body.template-themed {
        background: var(--theme-page-bg);
        background-image: var(--theme-page-grid);
        background-size: auto, auto, auto, 44px 44px, 44px 44px;
    }
    .theme-header-text { color: var(--theme-header-text); }
    .theme-header-text-muted { color: var(--theme-header-text-muted); }
    .theme-accent-text { color: var(--theme-accent); }
    .theme-icon { color: var(--theme-icon); }
</style>

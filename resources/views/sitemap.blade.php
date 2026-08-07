<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($urls as $u)
    <url>
        <loc>{{ url($u['loc']) }}</loc>
        <changefreq>{{ $u['freq'] ?? 'monthly' }}</changefreq>
        <priority>{{ $u['priority'] ?? 0.8 }}</priority>
    </url>
@endforeach
</urlset>

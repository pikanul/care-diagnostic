{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $url)
    <url>
        <loc>{{ $url }}</loc>
        <changefreq>weekly</changefreq>
        <priority>{{ str_ends_with($url, '/en') || str_ends_with($url, '/bn') ? '1.0' : '0.7' }}</priority>
    </url>
@endforeach
</urlset>

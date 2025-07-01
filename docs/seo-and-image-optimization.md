# SEO and Image Optimization Features

This document describes the SEO and image optimization features implemented in the boilerplate.

## 🗺️ Sitemap Generation

### Automatic Sitemap Generation

The boilerplate includes automatic XML sitemap generation for better search engine indexing.

#### Features:
- **Automatic Generation**: Sitemaps are generated automatically when pages are published
- **Multiple Formats**: Supports both XML and TXT formats
- **Smart URL Inclusion**: Only includes published pages that are not marked as no-index
- **Priority and Frequency**: Configurable priority and change frequency for different page types

#### Usage:

**Manual Generation:**
```bash
# Generate XML sitemap
php artisan sitemap:generate

# Generate TXT sitemap
php artisan sitemap:generate --format=txt
```

**Composer Script:**
```bash
composer sitemap
```

**Access Sitemaps:**
- XML: `https://yoursite.com/sitemap.xml`
- TXT: `https://yoursite.com/sitemap.txt`

#### Configuration:

In the admin panel under **Settings > SEO**:
- **Enable Sitemap**: Toggle automatic sitemap generation
- **Update Frequency**: Choose between daily, weekly, or monthly updates

## 🔍 Enhanced Meta Tags Management

### Extended SEO Fields

Pages now support comprehensive SEO metadata:

#### Basic Meta Tags:
- **Meta Title**: Page title for search engines (max 60 characters)
- **Meta Description**: Page description (max 160 characters)
- **Meta Keywords**: Keywords for search engines
- **Canonical URL**: Preferred URL for the page

#### Social Media Tags:
- **Open Graph Title**: Title for Facebook/LinkedIn sharing
- **Open Graph Description**: Description for social sharing
- **Open Graph Image**: Image for social media previews
- **Twitter Title**: Title for Twitter cards
- **Twitter Description**: Description for Twitter cards
- **Twitter Image**: Image for Twitter cards
- **Twitter Card Type**: Type of Twitter card (summary, summary_large_image, etc.)

#### Robots Meta Tags:
- **No Index**: Prevent search engine indexing
- **No Follow**: Prevent following links
- **No Archive**: Prevent archiving
- **No Snippet**: Prevent showing snippets in search results

#### Structured Data:
- **JSON-LD**: Add structured data for rich snippets

### Global SEO Settings

Configure global SEO settings in **Settings > SEO**:

- **Google Analytics ID**: Tracking ID for Google Analytics
- **Google Tag Manager ID**: Container ID for Google Tag Manager
- **Default Meta Title**: Fallback title for pages without specific titles
- **Default Meta Description**: Fallback description for pages
- **Default Open Graph Image**: Default image for social sharing
- **Twitter Username**: Twitter handle for social cards

## 🖼️ Image Optimization

### Automatic Image Optimization

Images are automatically optimized when uploaded to improve performance and reduce file sizes.

#### Features:
- **Automatic Processing**: Images are optimized immediately after upload
- **Multiple Formats**: Supports JPEG, PNG, GIF, and WebP
- **Quality Optimization**: Maintains visual quality while reducing file size
- **Background Processing**: Uses queues for non-blocking optimization
- **Error Handling**: Comprehensive error logging and retry mechanisms

#### Supported Optimizers:
- **JPEGoptim**: Optimizes JPEG images
- **Pngquant**: Optimizes PNG images
- **Optipng**: Additional PNG optimization
- **Gifsicle**: Optimizes GIF images
- **Cwebp**: Converts to WebP format
- **Avifenc**: Converts to AVIF format (modern browsers)

#### Usage:

**Manual Optimization:**
```bash
# Optimize all images
php artisan images:optimize

# Force re-optimization of all images
php artisan images:optimize --force

# Limit number of images to process
php artisan images:optimize --limit=100
```

**Composer Script:**
```bash
composer optimize-images
```

#### Configuration:

The image optimization is configured in `config/image-optimizer.php`:

```php
'optimizers' => [
    Spatie\ImageOptimizer\Optimizers\Jpegoptim::class => [
        '-m85', // 85% quality
        '--force',
        '--strip-all', // Remove metadata
        '--all-progressive',
    ],
    // ... other optimizers
],
```

#### Queue Configuration:

Image optimization uses the `image-optimization` queue. Make sure your queue worker is running:

```bash
php artisan queue:work --queue=image-optimization
```

## 🚀 Performance Benefits

### Sitemap Benefits:
- **Faster Indexing**: Search engines can discover pages more efficiently
- **Better Crawling**: Helps search engines understand your site structure
- **SEO Monitoring**: Track which pages are indexed

### Meta Tags Benefits:
- **Better CTR**: Optimized titles and descriptions improve click-through rates
- **Social Sharing**: Rich previews when shared on social media
- **Search Visibility**: Better control over how pages appear in search results

### Image Optimization Benefits:
- **Faster Loading**: Reduced file sizes improve page load times
- **Better SEO**: Faster pages rank better in search engines
- **User Experience**: Faster image loading improves user satisfaction
- **Bandwidth Savings**: Reduced server bandwidth usage

## 🔧 Installation and Setup

### Prerequisites:

1. **Image Optimization Tools**: Install system-level image optimization tools:

**Ubuntu/Debian:**
```bash
sudo apt-get install jpegoptim optipng pngquant gifsicle webp
```

**macOS:**
```bash
brew install jpegoptim optipng pngquant gifsicle webp
```

**Windows:**
Download and install the tools manually, then update the binary paths in `config/image-optimizer.php`.

2. **Queue Worker**: Ensure your queue worker is running:
```bash
php artisan queue:work --queue=image-optimization
```

### Configuration:

1. **Run Migrations:**
```bash
php artisan migrate
```

2. **Configure SEO Settings:**
   - Go to **Admin > Settings > SEO**
   - Configure Google Analytics, Tag Manager, and other settings

3. **Test Sitemap Generation:**
```bash
php artisan sitemap:generate
```

4. **Test Image Optimization:**
```bash
php artisan images:optimize --limit=5
```

## 📊 Monitoring and Maintenance

### Sitemap Monitoring:
- Check sitemap accessibility at `/sitemap.xml`
- Monitor sitemap generation in logs
- Review sitemap content for accuracy

### Image Optimization Monitoring:
- Check queue logs for optimization jobs
- Monitor file size reductions
- Review error logs for failed optimizations

### SEO Performance:
- Use Google Search Console to monitor indexing
- Track Core Web Vitals improvements
- Monitor search rankings and CTR

## 🐛 Troubleshooting

### Sitemap Issues:
- **Empty Sitemap**: Ensure pages are published and not marked as no-index
- **Missing Pages**: Check page status and no_index settings
- **Generation Errors**: Check logs for permission or file system issues

### Image Optimization Issues:
- **Failed Optimizations**: Check if optimization tools are installed
- **Queue Errors**: Ensure queue worker is running
- **Large Files**: Check file size limits in media library config

### SEO Issues:
- **Meta Tags Not Showing**: Clear cache and check page settings
- **Analytics Not Working**: Verify Google Analytics ID format
- **Social Cards Not Working**: Check Open Graph image URLs

## 📝 Best Practices

### Sitemap Best Practices:
- Keep sitemaps under 50MB and 50,000 URLs
- Update sitemaps when content changes
- Submit sitemaps to Google Search Console

### Meta Tags Best Practices:
- Keep titles under 60 characters
- Keep descriptions under 160 characters
- Use unique titles and descriptions for each page
- Include target keywords naturally

### Image Optimization Best Practices:
- Use appropriate image formats (WebP for photos, PNG for graphics)
- Optimize images before upload when possible
- Monitor optimization quality vs. file size
- Use responsive images for different screen sizes 
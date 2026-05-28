# Corestarter Theme

A clean, modern, and fully customizable WordPress starter theme built for speed, SEO, and developer flexibility. Lightweight by design with zero external dependencies — no jQuery, no page builders, no bloat. Just clean, standards-compliant WordPress code.

## Requirements

| Dependency | Version |
|---|---|
| WordPress | 6.0 or higher |
| PHP | 8.0 or higher |
| WooCommerce *(optional)* | 8.0 or higher |

---

## Features

### Performance & SEO

- **Zero jQuery dependency** — vanilla JavaScript throughout, smaller page weight
- **Deferred scripts** — all front-end JS loaded with `strategy: 'defer'` (non-blocking)
- **Critical CSS preloaded** — `<link rel="preload">` on the main stylesheet for faster paint
- **Google Fonts optimization** — API v2 with `display=swap`, preconnect + DNS-prefetch hints, font preloads
- **Logo LCP optimization** — `fetchpriority="high"` and `loading="eager"` on the logo image
- **Lazy images** — `loading="lazy"` and `decoding="async"` on all non-critical images
- **Dynamic CSS inlined** — theme options CSS injected via `wp_add_inline_style()`, zero extra HTTP requests
- **Conditional asset loading** — WooCommerce styles, shop scripts, and Google Maps API only loaded on relevant pages
- **Schema.org structured data** — WebSite + SearchAction on homepage, Article on posts, BreadcrumbList on all singular views
- **Print stylesheet** included for clean printed output

### Design & Layout

- **Fully responsive** — 5-breakpoint system (1440px, 1200px, 992px, 768px, 480px) with fluid typography
- **1440px desktop baseline** — full-width layout starts at 1440px; gracefully adapts down to 320px
- **Flexible layouts** — right sidebar, left sidebar, or no sidebar — configurable from Theme Options
- **CSS custom properties** — single source of truth for all colors, spacing, and typography
- **Sticky header** with scroll-aware shadow (toggle in options)
- **Back-to-top button** with smooth scroll and animated appearance (toggle in options)
- **Post cards** with hover lift effect on archive pages
- **`clamp()`-based base font sizing** for buttery smooth scaling between breakpoints
- **Responsive type scale system** — PHP-generated CSS media queries interpolate between desktop, tablet, and mobile heading/body scales configured in Theme Options

### Block Editor (Gutenberg)

- **`theme.json`** — declares the full design token set (color palette, font sizes, spacing scale, layout sizes, shadow presets) so all options are available natively in the block editor's color/typography pickers
- **`editor-style.css`** — block editor WYSIWYG matches the front end (same CSS variables, typography, blockquote/code/table styles, and button colors)
- **Wide and full alignment** — `align-wide` and `align-full` fully supported
- **Responsive embeds** — YouTube, Vimeo, etc. scale correctly out of the box

### Theme Options Panel (`wp-admin → Corestarter`)

| Tab | Options |
|---|---|
| **General** | Logo upload (SVG, WEBP, AVIF, PNG, JPG), hide site title / tagline |
| **Header** | Sticky header toggle, header background color |
| **Layout** | Container type (fixed/full), container width (960–1920px), sidebar position (right/left/none), products per page *(WooCommerce)* |
| **Typography** | Font family (Google Fonts or system), size, weight — per element (body, h1–h6, links, span) |
| **Typography → Responsive Scaling** | Tablet and mobile heading + body scale sliders |
| **Colors** | Primary color, secondary color, header background, footer background |
| **Social Icons** | Per-icon image upload + URL + new-tab toggle; placement: header / footer / both |
| **Footer** | Custom footer HTML text, footer background, back-to-top button toggle |
| **Tracking & Integrations** | GA4 Measurement ID, GTM Container ID, Google Maps API key, custom head scripts, custom body scripts |
| **Shortcodes** | Built-in shortcode reference with usage examples |
| **Custom Code** | Custom CSS (injected inline, no `<style>` tags needed), custom JS (injected before `</body>`) |

All options are sanitized server-side and cached in a single `get_option()` call per request. Every option value can be overridden by a child theme or plugin via the `corestarter_option` filter:

```php
// Example: Force the primary color to red from a child theme or plugin.
add_filter( 'corestarter_option', function( $value, $key ) {
    return 'primary_color' === $key ? '#dc2626' : $value;
}, 10, 2 );
```

### WooCommerce Integration

- Full WooCommerce theme support declared (thumbnail sizes, product grid configuration)
- **HPOS compatible** — High-Performance Order Storage (custom order tables) declared compatible
- **Cart/checkout blocks compatible**
- Product gallery zoom, lightbox, and slider enabled
- Custom header cart icon with AJAX mini-cart dropdown
- **`aria-live="polite"`** on cart count badge — screen readers announce count changes
- Custom My Account header icon
- Styled product grid, cart, checkout, and My Account pages
- Custom empty cart template
- Responsive product grid (3 → 2 → 1 columns)
- Left sidebar forced on shop/category archive pages with mobile filter drawer
- WooCommerce pages (Cart, Checkout, My Account) automatically removed from the Primary nav menu (replaced by header icons)
- Products per page configurable from Theme Options → Layout
- Custom breadcrumb markup with Schema.org `BreadcrumbList`

### Accessibility

- Skip-to-content link (keyboard accessible, visually hidden until focused)
- `aria-label` on all navigation regions, search toggle, and hamburger button
- `aria-expanded` / `aria-controls` wired to all interactive toggles
- `aria-live="polite"` + `aria-atomic="true"` on WooCommerce cart count
- `:focus-within` dropdown support (keyboard navigable dropdown menus)
- Screen-reader-only text on icon-only buttons
- `role="banner"`, `role="navigation"`, `role="contentinfo"`, `role="complementary"` landmarks

### Developer Features

- **`inc/` override system** — child theme can override any `inc/*.php` file by placing a same-named file in its own `inc/` directory
- **`shortcodes/` auto-loader** — drop `.php` files into `corestarter/shortcodes/` (parent) or `corestarter-child/shortcodes/` (child theme) and they load automatically
- **`corestarter_get_option()` filter** — any option value filterable without touching the database
- **`theme.json`** — design tokens available in block editor and as CSS custom properties
- **`editor-style.css`** — WYSIWYG block editor matches front end
- **Custom image sizes** — `corestarter-card` (780×439), `corestarter-wide` (1200×675), `corestarter-medium` (780×500), `corestarter-square` (300×300)
- **Translation-ready** — full `corestarter` text domain, `esc_html__()` / `esc_attr__()` throughout, `.pot` file included in `languages/`
- **WordPress Coding Standards** compliant
- **PHP 8.0–8.4 compatible** — no deprecated functions or type juggling issues
- **`ABSPATH` guard** on every template and inc file — direct file access blocked

---

## Built-in Shortcodes

| Shortcode | Description |
|---|---|
| `[corestarter_year]` | Outputs the current 4-digit year (great for copyright lines) |
| `[corestarter_button url="#" text="Click Here" style="primary" target="_self"]` | Renders a styled button. Styles: `primary` or `outline` |
| `[corestarter_map address="New York, NY" zoom="14" height="400px"]` | Embeds a Google Map by address. Requires API key in Theme Options |
| `[corestarter_map lat="14.5995" lng="120.9842" zoom="16"]` | Embeds a Google Map by coordinates |

**Adding custom shortcodes** — drop `.php` files into `corestarter-child/shortcodes/`. They are auto-loaded on every page. See `corestarter/shortcodes/example-shortcode.php` for a documented template.

---

## WordPress Features Supported

- Custom menus — Primary and Footer
- Custom background
- Featured images (post thumbnails)
- `title-tag` management
- HTML5 markup (search forms, comments, galleries, captions, style, script)
- Responsive embeds
- Wide and full block alignment
- Editor styles (`editor-style.css`)
- `theme.json` v3 (design tokens, fluid typography, layout sizes)
- Threaded comments
- Automatic feed links

---

## Widget Areas

| Area | Shown in |
|---|---|
| Sidebar | All post/page templates with sidebar enabled |
| Shop Sidebar | WooCommerce shop and category archive pages |
| Footer Widget Area 1 | Footer (left column) |
| Footer Widget Area 2 | Footer (center column) |
| Footer Widget Area 3 | Footer (right column) |

---

## Page Templates

| Template | Description |
|---|---|
| *(Default)* | Standard layout with header, footer, and sidebar |
| **Blank Canvas** | No header, footer, or sidebar — ideal for landing pages |
| **Full Width** | Header and footer, no sidebar, full content width |

---

## Installation

1. Download or clone this repository
2. Upload the `corestarter` folder to `wp-content/themes/`
3. Upload the `corestarter-child` folder to `wp-content/themes/`
4. Activate **Corestarter Child** from **Appearance → Themes**
5. Configure everything under **Appearance → Corestarter**

> **Tip:** Always activate the child theme, not the parent. The child theme is where all your customizations go, keeping them safe during parent theme updates.

---

## Theme File Structure

```
corestarter/
│
├── assets/
│   ├── css/
│   │   ├── main.css                 Primary front-end stylesheet
│   │   ├── admin-options.css        Theme options panel styles
│   │   ├── woocommerce-header.css   Cart & account icon styles (site-wide)
│   │   └── woocommerce.css          Shop/cart/checkout styles (WC pages only)
│   └── js/
│       ├── main.js                  Back-to-top, search dropdown, scroll effects
│       ├── navigation.js            Mobile hamburger menu toggle
│       ├── shop-filters.js          Mobile filter drawer (shop archives only)
│       └── admin-options.js         Color picker & media uploader (admin only)
│
├── inc/                             Modular PHP — all child-theme overridable
│   ├── enqueue.php                  Styles, scripts, Google Fonts, resource hints
│   ├── shortcodes.php               Shortcode loader + built-in shortcodes
│   ├── template-functions.php       Hooks: body classes, dynamic CSS, schema, analytics
│   ├── template-tags.php            Display helpers: meta, pagination, thumbnails
│   ├── theme-options-defaults.php   Option defaults, corestarter_get_option(), font lists
│   ├── theme-options.php            Admin options panel (Settings API)
│   └── woocommerce.php              Full WooCommerce integration layer
│
├── languages/
│   └── corestarter.pot              Translation template (gettext .pot file)
│
├── shortcodes/
│   └── example-shortcode.php        Example/template for custom shortcodes
│
├── template-parts/
│   ├── content/
│   │   ├── content.php              Archive post card
│   │   ├── content-single.php       Single post body
│   │   ├── content-page.php         Static page body
│   │   ├── content-search.php       Search result card
│   │   └── content-none.php         Empty state (no posts found)
│   └── social-icons.php             Social icons partial
│
├── templates/
│   ├── template-blank.php           Blank Canvas page template
│   └── template-full-width.php      Full Width page template
│
├── woocommerce/
│   └── cart/
│       └── cart-empty.php           Custom empty cart message
│
├── 404.php                          404 error page
├── archive.php                      Category / tag / date archives
├── comments.php                     Comment list and reply form
├── editor-style.css                 Block editor WYSIWYG styles
├── footer.php                       Site footer with widgets, nav, social, copyright
├── functions.php                    Bootstrap: constants, setup, widget areas, includes
├── header.php                       Site header with branding, nav, search, cart icons
├── index.php                        Blog / home fallback template
├── page.php                         Static page template
├── search.php                       Search results template
├── searchform.php                   Custom search form markup
├── sidebar-shop.php                 WooCommerce shop sidebar
├── sidebar.php                      Main sidebar
├── single.php                       Single post template
├── style.css                        Theme header (metadata only — no styles)
├── theme.json                       Design tokens, color palette, font sizes, layout
└── woocommerce.php                  WooCommerce page container wrapper

corestarter-child/
├── functions.php                    Child theme functions (enqueue parent styles)
├── style.css                        Child theme header + custom styles
└── screenshot.png
```

---

## Customization Guide

### Override any `inc/` file

Create a file at the same relative path in your child theme:

```
corestarter-child/inc/enqueue.php    → overrides corestarter/inc/enqueue.php
corestarter-child/inc/template-tags.php → overrides corestarter/inc/template-tags.php
```

### Add custom shortcodes

Create a file in your child theme's `shortcodes/` directory:

```
corestarter-child/shortcodes/my-shortcode.php
```

It is loaded automatically. See `corestarter/shortcodes/example-shortcode.php` for a full template.

### Override any theme option programmatically

```php
// In corestarter-child/functions.php
add_filter( 'corestarter_option', function( $value, $key ) {
    if ( 'container_width' === $key ) {
        return 1280;
    }
    return $value;
}, 10, 2 );
```

### Override a WooCommerce template

Copy the file from `corestarter/woocommerce/` into the same path in your child theme and modify it. WordPress/WooCommerce template override rules apply.

---

## Changelog

### 1.1.0
- **Layout** — desktop container baseline updated from 1200px to 1440px; responsive breakpoints now span 1440px → 1200px → 992px → 768px → 480px
- **theme.json** — full design token set added: color palette, fluid font sizes, spacing scale, layout sizes (contentSize 1440px, wideSize 1600px), shadow presets
- **editor-style.css** — block editor WYSIWYG now mirrors the front-end typography, colors, and block styles
- **Custom image sizes** — `corestarter-card`, `corestarter-wide`, `corestarter-medium`, `corestarter-square` registered
- **Schema.org** — expanded to WebSite + SearchAction (homepage), Article (posts), BreadcrumbList (all singular views)
- **`corestarter_get_option()` filter** — every option value now filterable by child themes and plugins
- **Products per page** — now configurable from Theme Options → Layout (was hard-coded to 12)
- **WooCommerce HPOS** — High-Performance Order Storage and cart/checkout blocks declared compatible
- **Accessibility** — `aria-live="polite"` + `aria-atomic="true"` on cart count badge
- **Performance** — `fetchpriority="high"` + `loading="eager"` on logo image; DNS-prefetch hints added alongside preconnect for Google Fonts
- **Shortcodes** — auto-loader now loads from both parent and child theme `shortcodes/` directories; `example-shortcode.php` template added
- **Translations** — `languages/` directory and `corestarter.pot` file added
- **Security** — `ABSPATH` guard added to all template files (index, single, page, archive, 404, search, comments, sidebar, woocommerce, template-blank)
- **Bug fix** — `esc_attr( get_search_query() )` in searchform.php (was unescaped)
- **Bug fix** — `esc_html( get_the_title() )` on WooCommerce product titles in shop loop
- **Bug fix** — `$content_width` now declared with proper `global` keyword inside `corestarter_setup()`
- **Compatibility** — `Tested up to: 6.8`

### 1.0.0
- Initial release

---

## License

Licensed under [GNU General Public License v2.0](http://www.gnu.org/licenses/gpl-2.0.html) or later.

---

## Author

**Daryl Ric Lanaban**
- GitHub: [@darylric](https://github.com/darylric)
- Theme URI: [github.com/darylric/corestarter](https://github.com/darylric/corestarter)

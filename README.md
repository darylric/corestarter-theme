# Corestarter Theme

A clean, modern, and fully customizable WordPress starter theme built for speed, SEO, and developer flexibility. Lightweight by design with zero dependencies — just clean, standards-compliant code.

## Features

### Performance & SEO
- **Lightweight & fast** — no jQuery dependency, minimal CSS, zero bloat
- **SEO-optimized markup** — semantic HTML5 with proper heading hierarchy, structured data-ready
- **Responsive images** — lazy loading and async decoding on all images
- **Print stylesheet** included for clean printed output
- **CSS custom properties** — single source of truth for theming, no redundant overrides

### Design & Layout
- **Fully responsive** — four breakpoints (1200px, 992px, 768px, 480px) with fluid typography scaling
- **Flexible layouts** — right sidebar, left sidebar, or no sidebar per page
- **Sticky header** option with scroll-aware shadow
- **Back-to-top button** with smooth scroll
- **Post cards** with hover effects on archive pages
- **Clean typography** — system font stack with `clamp()`-based fluid sizing

### Customization
- **Built-in Theme Options panel** — no plugin required
  - Custom logo upload
  - Primary and secondary color pickers
  - Header and footer background colors
  - Container width control
  - Sticky header toggle
  - Sidebar position (left, right, none)
  - Footer copyright text
  - Google Maps API key
  - Social media links with icon uploads (header, footer, or both)
- **CSS custom properties** — easily override colors, spacing, and typography from child theme or options panel
- **Custom page templates** — Blank (no header/footer) and Full Width

### WooCommerce Ready
- Full WooCommerce compatibility declared with theme support
- Product gallery zoom, lightbox, and slider enabled
- Custom header cart with AJAX mini-cart dropdown
- Styled product grid, cart, checkout, and My Account pages
- Custom empty cart template
- Responsive product grid (3 → 2 → 1 columns)
- Themed buttons, sale badges, star ratings, and breadcrumbs
- Variable product support out of the box

### Developer Friendly
- **Child theme included** — `corestarter-child` ready to go
- **Override system** — child theme can override any `inc/` file by placing a same-named file in its own `inc/` directory
- **Shortcodes** — `[cs_button]` and `[cs_map]` included
- **Translation ready** — full `corestarter` text domain with `esc_html__()` / `esc_attr__()` throughout
- **WordPress coding standards** compliant
- **Accessibility** — skip link, ARIA labels, screen reader text, keyboard navigation, `:focus-within` dropdown support

### WordPress Features Supported
- Custom logo
- Custom menus (Primary and Footer)
- Custom header and custom background
- Featured images (post thumbnails)
- Title tag management
- HTML5 markup for search forms, comments, galleries, captions
- Responsive embeds and wide/full alignment (Gutenberg ready)
- Editor styles support
- Threaded comments
- Three footer widget areas

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- WooCommerce 8.0+ (optional, for shop features)

## Installation

1. Download or clone this repository
2. Upload the `corestarter` folder to `wp-content/themes/`
3. Upload the `corestarter-child` folder to `wp-content/themes/`
4. Activate **Corestarter Child** from Appearance → Themes
5. Configure theme options under **Appearance → Theme Options**

## Theme Structure

```
corestarter/
├── assets/
│   ├── css/
│   │   ├── admin-options.css    # Theme options panel styles
│   │   ├── main.css             # Primary theme stylesheet
│   │   └── woocommerce.css      # WooCommerce-specific styles
│   └── js/
│       ├── admin-options.js     # Theme options panel scripts
│       ├── main.js              # Front-end scripts
│       └── navigation.js        # Mobile menu toggle
├── inc/
│   ├── enqueue.php              # Script and style enqueuing
│   ├── shortcodes.php           # Theme shortcodes
│   ├── template-functions.php   # Template helper functions
│   ├── template-tags.php        # Template tags
│   ├── theme-options-defaults.php # Default option values
│   ├── theme-options.php        # Admin options panel
│   └── woocommerce.php          # WooCommerce integration
├── template-parts/
│   ├── content/                 # Content templates
│   └── social-icons.php         # Social icons partial
├── templates/
│   ├── template-blank.php       # Blank page template
│   └── template-full-width.php  # Full width page template
├── woocommerce/
│   └── cart/
│       └── cart-empty.php       # Custom empty cart template
├── functions.php
├── header.php
├── footer.php
├── index.php
├── single.php
├── page.php
├── archive.php
├── search.php
├── searchform.php
├── 404.php
├── sidebar.php
├── comments.php
├── woocommerce.php
└── style.css

corestarter-child/
├── functions.php
├── screenshot.png
└── style.css
```

## License

Licensed under [GNU General Public License v2.0](http://www.gnu.org/licenses/gpl-2.0.html) or later.

## Author

**Daryl Ric Lanaban**
- GitHub: [@darylric](https://github.com/darylric)

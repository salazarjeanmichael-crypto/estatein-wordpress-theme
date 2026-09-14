# Estatein — Custom WordPress Theme

A hand-built WordPress theme converting the [Estatein real estate Figma template](https://www.figma.com/community/file/1314076616839640516) into a working site. No page builder, no starter theme, no CSS framework — plain PHP, CSS and vanilla JavaScript.

**Live demo:** _add your URL here_
**Admin:** `/wp-admin` — credentials supplied separately

---

## What is built

| Page | Template | Notes |
|---|---|---|
| Home | `front-page.php` | Hero, feature strip, featured properties, testimonials, FAQs, CTA |
| About Us | `page-about.php` | Journey, values, achievements, 6-step process, team, clients |
| Properties | `page-properties.php` | Search + filters, paginated grid, enquiry form |
| Property detail | `single-property.php` | Facts, description, sticky price panel, related listings |
| Services | `page-services.php` | Three service blocks, 12 services |
| Contact | `page-contact.php` | Contact routes, form, filterable office locations |
| Property archive | `archive-property.php` | Shared with `taxonomy-property_*.php` |
| Blog / search / 404 | `single.php`, `archive.php`, `search.php`, `404.php` | Complete fallbacks |

All six design breakpoints from the Figma file (Desktop 1920, Laptop 1440, Mobile 390) are honoured.

---

## Requirements

- WordPress 6.0+ (tested on 7.1)
- PHP 7.4+ (tested on 7.4 and 8.2)
- MySQL 5.7+ / MariaDB 10.3+

No plugins are required. The theme works on a bare WordPress install.

---

## Installation

1. Copy the `estatein` folder into `wp-content/themes/`.
2. **Appearance → Themes → Activate** "Estatein".
3. **Settings → Permalinks** → choose **Post name** and save (registers the `/property/` routes).
4. Optional — load the demo content shown in the design:

   ```bash
   php wp-content/themes/estatein/tools/seed-demo-content.php
   ```

   Creates 6 properties, 4 testimonials, 4 FAQs and 4 team members with images. Safe to re-run: items are matched by slug and updated, never duplicated.

5. Create pages named **About Us**, **Properties**, **Services**, **Contact Us** and assign the matching template under *Page Attributes*. Set **Settings → Reading → Front page** to your Home page.

---

## Content management

Everything in the design that a client would reasonably want to change is editable from the dashboard:

| Content | Where |
|---|---|
| Property listings | **Properties** — price, bedrooms, bathrooms, style, area, address |
| Property categories | **Properties → Property Types / Locations** |
| Client quotes | **Testimonials** — rating, name, location |
| Question block | **FAQs** |
| Staff | **Team** — job title, Twitter, email |
| Navigation | **Appearance → Menus** (1 primary + 5 footer locations) |
| Logo | **Appearance → Customise → Site Identity** |

Custom fields are plain WordPress meta boxes, so there is no plugin dependency. If ACF is installed the theme picks up a **Site Settings** options page for the announcement banner, contact details and social links — see `inc/acf-fields.php`. Without ACF those fall back to sensible defaults.

---

## Structure

```
estatein/
├── assets/
│   ├── css/main.css          design tokens + all component styles
│   ├── js/main.js            nav, carousels, FAQ toggles, filters
│   └── img/                  logo, photos, 39 icons exported from Figma
├── inc/
│   ├── helpers.php           template helpers, ACF-safe field reader
│   ├── cpt.php               property, testimonial, faq, team post types
│   ├── meta.php              custom fields + admin columns
│   ├── query.php             property search/filter query builder
│   ├── forms.php             contact + newsletter handlers
│   ├── form-fields.php       declarative field rendering
│   ├── seo.php               meta tags, Open Graph, JSON-LD
│   └── acf-fields.php        optional ACF integration
├── template-parts/
│   ├── components/           property card, testimonial, FAQ, forms, CTA…
│   └── home/                 one file per homepage section
├── tools/seed-demo-content.php
└── *.php                     page templates
```

---

## Design tokens

Colour, type and spacing come straight from the Figma variables and are declared once in `assets/css/main.css`:

```css
--grey-08: #141414;   /* page background */
--grey-10: #1A1A1A;   /* header, footer, cards */
--grey-15: #262626;   /* borders */
--grey-60: #999999;   /* body copy */
--purple-60: #703BF7; /* primary action */
```

Layout reproduces the Figma frames exactly — 1596px content with 162px gutters at 1920, 1280/80 at 1440, 358/16 at 390 — using one clamp:

```css
--gutter: clamp(1rem, 6.1vw - 0.5rem, 5rem);
```

---

## Notes

- Runs on PHP 7.4, so no `match`, nullsafe operators or named arguments.
- Text domain `estatein` is applied throughout; the theme is translation-ready.
- All output is escaped; all input is sanitised and nonce-checked.
- See `DEVELOPMENT-NOTES.md` for the reasoning behind the main decisions.

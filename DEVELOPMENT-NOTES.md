# Development Notes — Estatein WordPress Theme

A short account of how the Figma template became a working WordPress theme, and why the main decisions went the way they did.

---

## 1. Getting the design out of Figma accurately

Rather than eyeballing measurements from a screenshot, I duplicated the community file into my own Figma account and read it programmatically. That gave me the published design variables directly:

```
Grey/08 #141414   Grey/10 #1A1A1A   Grey/15 #262626   Grey/60 #999999
Purple/60 #703BF7  Purple/75 #A685FA  White #FFFFFF
```

Those became CSS custom properties with the same names, so any value in the stylesheet can be traced back to a Figma variable.

Two things this surfaced that a screenshot would not have:

- **The file ships Desktop, Laptop and Mobile frames** (1920 / 1440 / 390), not just desktop. So the breakpoints are the designer's, not my guesses.
- **Content widths are 1596 / 1280 / 358** with gutters of 162 / 80 / 16. Instead of three media queries I derived one expression that interpolates between the two smaller frames and lets the max-width reproduce the desktop gutter:

  ```css
  --content: 1596px;
  --gutter:  clamp(1rem, 6.1vw - 0.5rem, 5rem);
  .container { max-width: calc(var(--content) + var(--gutter) * 2); padding-inline: var(--gutter); }
  ```

  At 1920 the content lands at exactly 1596px with 162px gutters; at 1440 it is 1280/80; at 390 it is 358/16. All three match the design without a single breakpoint.

**Icons and photography were exported from the file, not redrawn.** All 39 SVGs are the designer's actual vectors. Hand-drawing approximations is where "looks almost right" usually creeps in.

---

## 2. Why a fully custom theme with no framework

The brief asked for a custom theme, so the starting point was an empty directory rather than a starter theme.

I also skipped Tailwind and Bootstrap deliberately. The design is a closed system — one accent colour, one type scale, six greys. A utility framework would have shipped far more CSS than the site uses, and a component framework would have fought the design. The whole stylesheet is **46KB unminified** and covers every page.

No build step, either. There is no `npm install` between the repository and a working site: upload, activate, done. On a small brochure site the tooling is usually a bigger maintenance burden than the problem it solves.

---

## 3. Content modelling

The design implies a content model, so I built it:

| Post type | Why not just pages |
|---|---|
| `property` | Needs querying, filtering, pagination and its own detail page |
| `testimonial` | Repeated on Home and About, ordered by the client |
| `faq` | Same block reused across pages |
| `team` | About page staff grid |

Plus two taxonomies — `property_type` and `property_location` — which power both the filter bar and the `/location/california/` style archives.

**On ACF:** the brief allows "ACF *or similar*". I used plain `add_meta_box()` for property and team fields instead, because it means the theme has zero plugin dependencies — a bare WordPress install renders it correctly. But every field read goes through one helper:

```php
function estatein_field( $selector, $fallback = '', $post_id = false ) {
    if ( ! function_exists( 'get_field' ) ) return $fallback;
    ...
}
```

So if ACF *is* installed, it takes over transparently, and `inc/acf-fields.php` adds a **Site Settings** options page for the banner, contact details and social links. Plugin present or absent, nothing fatals and nothing renders empty. That felt like the right trade: no forced dependency, but ready for one.

---

## 4. Search and filtering

The properties page filter bar submits with `GET`, not AJAX. A filtered view therefore has its own URL, can be bookmarked and shared, and the browser back button behaves correctly. `inc/query.php` turns the query string into a `WP_Query` — taxonomy terms into a `tax_query`, price brackets and bedroom counts into a numeric `meta_query`.

Verified working: `?beds=4` → 2 results, `?type=apartment` → 2, `?price=1000000-` → 1, `?q=cabin` → 1, `?beds=9` → the empty state.

---

## 5. Progressive enhancement

The three homepage carousels are plain flex rows in the markup. JavaScript upgrades them into sliders; without it the cards simply wrap into a grid and everything stays reachable. The same applies to the FAQ "Read More" toggles and the office-location tabs.

The carousel also manages focus: cards scrolled out of view get `tabindex="-1"` so keyboard users cannot tab into invisible content — a detail most slider implementations miss.

---

## 6. Forms

Both forms (property enquiry, general contact) post to one handler in `inc/forms.php`, which runs on `template_redirect` before any output so success can redirect (Post/Redirect/Get) — a refresh cannot resubmit.

Every submission goes through: nonce check → honeypot → sanitise → validate → escape on output. On failure the page re-renders with values intact and each bad field carrying `aria-invalid` plus an `aria-describedby` pointing at its message.

One deliberate fix: the redirect target travels in a hidden field rather than relying on the `Referer` header, which privacy tools and proxies strip. It is passed through `wp_validate_redirect()`, so it can only ever point back at this site.

The field sets differ between the two forms but the look, validation display and accessibility contract must not. So fields are declared as data and rendered by one function (`estatein_form_field()`), which keeps that contract in a single place instead of duplicating ~150 lines of markup.

---

## 7. Performance

- **Images.** The Figma exports were 1–3MB PNGs. Downscaled and re-encoded as JPEG they total **444KB for all ten** — the hero alone went from 3.2MB to 189KB. The LCP image carries `fetchpriority="high"`; everything below the fold is lazy-loaded.
- **Removed what the theme does not use.** The emoji detection script, `wp-block-library` CSS and `global-styles` are dequeued — this is a classic theme with no blocks.
- **Cache busting via `filemtime()`**, so a deploy invalidates CSS/JS without a manual version bump.
- JavaScript is deferred, fonts preconnect to `fonts.gstatic.com`.

Result: **~60KB of HTML, 46KB CSS, 7.6KB JS**, pages rendering in under 0.45s locally.

---

## 8. SEO and accessibility

`inc/seo.php` outputs meta description, canonical, Open Graph and Twitter tags, plus JSON-LD — `RealEstateListing` with price on a property, `RealEstateAgent` elsewhere. It checks for Yoast, Rank Math and AIOSEO first and steps aside rather than emitting competing tags.

On accessibility: skip link, one `h1` per page, landmark regions, visible focus rings, labels on every control, `prefers-reduced-motion` honoured, and decorative images marked `aria-hidden`. I audited the rendered HTML across all six page types:

```
0 images without alt   0 unlabelled inputs   0 duplicate IDs   1 h1 per page
```

Horizontal overflow was checked at 390px with the `overflow-x: hidden` guard lifted, so real overflow could not hide behind it — `scrollWidth` equals the viewport on every page.

---

## 9. Tools used

| Tool | Use |
|---|---|
| Figma MCP | Reading variables, layout and exporting icons/images from the source file |
| XAMPP (PHP 7.4, MariaDB) | Local development |
| Headless Chrome | Screenshot comparison against the design, responsive and overflow testing |
| PHP GD | Batch image downscaling and re-encoding |
| Claude Code | Pair programming — scaffolding, then reviewed and corrected by hand |

No WordPress plugins were used. No CSS or JS libraries are loaded.

---

## 10. What I would do next

Honest list of what is not there:

- **The Contact page gallery** ("Explore Estatein's World") is omitted. It is six lifestyle photographs of an office and staff — real client photography, not something to fake with stock. The rest of that page is complete.
- **"Visit Website" on the client cards** points at the contact page; real client URLs would be a custom field.
- Property image **galleries** (the design shows one photo per listing; multiple would need a gallery field).
- **Saved/shortlisted properties**, which the About page copy references, would need user accounts.
- A **caching layer and WebP delivery** would be the next performance step on a real host.

Given more time the first thing I would add is proper editorial control over the homepage section order — currently the sections are fixed in `front-page.php`, which is fine for a launch but not for a client who wants to reorder them.

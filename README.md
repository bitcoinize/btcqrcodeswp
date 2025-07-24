# Bitcoin QRCodes for WordPress

Secure, responsive QR-code generator for Bitcoin projects. This plugin renders **Onchain, Lightning, Liquid and Silent Payment** QR codes with a centered logo and human‑readable labels. It uses **[Qrious](https://github.com/neocotic/qrious)** (client-side) to build the QR, crops extra quiet zones to keep all codes the same visual size, converts them to `<img>` tags, and lays them out responsively (3 columns on desktop, 1 column on mobile).

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Directory Structure](#directory-structure)
- [Installation](#installation)
- [How It Works (Technical Overview)](#how-it-works-technical-overview)
- [Usage](#usage)
  - [Shortcode Attributes](#shortcode-attributes)
  - [Basic Examples](#basic-examples)
  - [Grouping QRs in a Grid](#grouping-qrs-in-a-grid)
- [Styling & Responsiveness](#styling--responsiveness)
- [Logos](#logos)
- [Security Notes](#security-notes)
- [Performance Tips](#performance-tips)
- [Troubleshooting](#troubleshooting)
- [Changelog](#changelog)
- [License](#license)
- [Credits](#credits)

---

## Features

- **4 QR types**: `onchain`, `lightning`, `liquid`, `silent`.
- **Centered logo** overlay for each type.
- **Automatic cropping** of the QR to eliminate padding differences between payloads.
- **Exact pixel generation** (50–200px), then CSS-driven responsiveness.
- **Responsive layout**:
  - Desktop (≥700px): three QRs per row, 20px horizontal padding.
  - Mobile (<700px): one QR per row, 25px horizontal padding.
- **Accessible**: `alt` and `aria-label` include the full address.
- **Safe sanitization** of shortcode attributes via WordPress core functions.

---

## Requirements

- WordPress 5.0+
- PHP 7.2+
- (Optional) GD library enabled if you ever switch to server-side generation. Current build is fully client-side.

---

## Directory Structure

Place everything inside `wp-content/plugins/btcqrcodes/`:

```
btcqrcodes/
├── btcqrcodes.php                # main plugin file
├── css/
│   └── btcqrcodes.css            # responsive styles
├── js/
│   ├── qrious.min.js             # Qrious library (optional local copy)
│   └── btcqrcodes-img.js         # our QR builder & cropper
└── img/
    ├── onchain.jpg
    ├── lightning.jpg
    ├── liquid.jpg
    └── silent.jpg
```

> **Tip:** If you don’t want to host `qrious.min.js`, the plugin falls back to the official CDN automatically.

---

## Installation

1. **Upload** the `btcqrcodes` folder to `wp-content/plugins/`.
2. **Activate** *Bitcoin QRCodes for Wordpress* in **Plugins → Installed Plugins**.
3. Ensure your logos are in `img/` and named exactly:
   - `onchain.jpg`
   - `lightning.jpg`
   - `liquid.jpg`
   - `silent.jpg`
4. (Optional) Edit `css/btcqrcodes.css` for custom colors or spacing.

---

## How It Works (Technical Overview)

1. The **shortcode** outputs an empty `<img>` with data attributes (`address`, `logo`, `size`).
2. **Qrious** generates a QR code on an off-screen `<canvas>` with `padding: 0` to minimize quiet zones.
3. The script scans pixel data to find the bounding box of the actual modules (black squares). It then **crops** the QR and rescales it to the requested `size`, ensuring *all* QRs appear visually identical.
4. **Logo overlay**: a second draw places the respective logo (≈ 1/3 of QR size) at the center.
5. The canvas is exported as a BASE64 PNG and injected into the `<img>` tag so themes cannot shrink it unexpectedly via canvas rules.
6. **CSS** makes the `<img>` fill 100% of its wrapper width, enabling responsiveness. The wrapper switches between 33.33% (desktop) and 100% (mobile) via media queries at 700px.

---

## Usage

### Shortcode Attributes

| Attribute | Required | Values                                           | Default   | Description                                                                 |
|-----------|----------|---------------------------------------------------|-----------|-----------------------------------------------------------------------------|
| `address` | Yes      | Any string                                        | —         | The full Bitcoin/Lightning/Liquid/Silent address or invoice                 |
| `type`    | No       | `onchain` \| `lightning` \| `liquid` \| `silent` | `onchain` | Chooses the logo and the label text                                         |
| `size`    | No       | Integer 50–200                                    | `200`     | Pixel size for QR generation (bigger = sharper when scaled)                 |

> **Note:** `size` is the internal generation resolution, not the final CSS width. The image is displayed at 100% of the wrapper width.

### Basic Examples

```html
[secure_qrcode address="bc1qpg...krv67cru6" type="onchain" size="200"]
[secure_qrcode address="bitcoinekasi@geyser.fund" type="lightning" size="200"]
[secure_qrcode address="VJLAebcWwBy...KWbo8Zas" type="liquid" size="200"]
```

### Grouping QRs in a Grid

Wrap your shortcodes in a container to center and clear floats / flex-wrap:

```html
<div class="btcqr-grid">
  [secure_qrcode address="..." type="onchain" size="200"]
  [secure_qrcode address="..." type="lightning" size="200"]
  [secure_qrcode address="..." type="liquid" size="200"]
</div>
```

This container (`.btcqr-grid`) is limited to **700px** width on desktop. It uses `flex-wrap` so your blocks wrap cleanly.

---

## Styling & Responsiveness

- **Desktop (≥700px)**:
  - `.btcqr-wrap { width:33.333%; padding:0 20px 25px; }`
- **Mobile (<700px)**:
  - `.btcqr-wrap { width:100%; padding:0 25px 25px; }`
- QR image (`.btcqr-img`) uses `width:100%; height:auto;`.

You can change breakpoints or padding values in `css/btcqrcodes.css`.

---

## Logos

Put your logo files in `img/` and ensure their filenames match the `$types` array in `btcqrcodes.php`. Recommended: square PNG/JPG with transparent or white background.

To change icons, overwrite the images or update the array:

```php
private static $types = [
  'onchain'   => 'my-onchain.png',
  'lightning' => 'bolt.svg',
  'liquid'    => 'liquid.png',
  'silent'    => 'silent.png',
];
```

---

## Security Notes

- **Sanitization**: We use `sanitize_text_field()` + `sanitize_key()` to process shortcode attributes.
- Do **not** trust raw user input—always sanitize and validate.
- **Logos** are loaded from your own plugin folder to avoid external injections.
- If you add AJAX/REST endpoints later, protect them with nonces and capability checks.
- Keep WordPress core, themes, and plugins updated.

---

## Performance Tips

- Generate QR at `size=200` for best downscale quality; the PNG is still small.
- If you have many QRs on one page, consider lazy-loading or pagination.
- Combine/minify CSS & JS if needed.

---

## Troubleshooting

**QR still looks small on mobile**  
- Ensure you’re not overriding `.btcqr-img { width:100%; }` in your theme.  
- Increase the `size` attribute (e.g., `size="200"`) to generate a higher-res PNG.

**Logos not appearing**  
- Check the `img/` folder and filenames.  
- Confirm correct file permissions or paths.

**All QRs have different visual sizes**  
- Ensure you’re using the included `btcqrcodes-img.js` script, which crops and rescales the QR.

**Shortcode text is displayed instead of the QR**  
- Plugin might not be active, or the block is stripping shortcodes. Use a Shortcode block or classic editor.

---

## Changelog

- **1.5.7**: Responsive refactor. Desktop container 700px, 3-up grid; Mobile 100% width. Cleaned inline styles.
- **1.5.6**: Initial responsive CSS, removed fixed pixel widths on `<img>`.
- **1.5.3–1.5.5**: Type labels added, ordering fixed, cleaned shortcode output.
- **1.5.0–1.5.2**: Qrious crop fix, switched to `<img>` export, improved sanitization.
- **1.0.0**: First working version.

---

## License

This plugin is released under the **GNU General Public License v3.0 (GPL‑3.0)**.

You are free to copy, modify, distribute, and convey this software under the terms of the GPL‑3.0. A copy of the full license text is available at <https://www.gnu.org/licenses/gpl-3.0.html> and should be included with any redistributions.

**Key points (summary, not a substitute for the full license):**
- You must provide source code (or an offer to provide it) when distributing binaries or modified versions.
- Derivative works must also be licensed under GPL‑3.0 (copyleft).
- No additional restrictions beyond those of GPL‑3.0 may be imposed.

If you include third‑party assets (e.g., logos) with different licenses, ensure they are compatible with GPL‑3.0 or distribute them separately under their own terms.

---

## Credits

Author: **Fernando Motolese**  
Made with ₿ for **Bitcoinize.com** and **BitcoinConfederation.org**

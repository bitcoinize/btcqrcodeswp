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
- **Respons**

# All In One Link Shortener

---

## 📊 Repo Stats
![Stars](https://img.shields.io/github/stars/shitalparmar443/all-in-one-link-shortener?style=flat)
![Forks](https://img.shields.io/github/forks/shitalparmar443/all-in-one-link-shortener?style=flat)
![Issues](https://img.shields.io/github/issues/shitalparmar443/all-in-one-link-shortener?style=flat)

---

**Contributors:** [shitalparmar443](https://profiles.wordpress.org/shitalparmar443/)
**Donate link:** [PayPal](https://www.paypal.me/shitalparmar443/)
**Tags:** shortlink, permalink, seo, url-shortener, links
**Requires at least:** 6.1
**Tested up to:** 6.8
**Stable tag:** 1.0
**Requires PHP:** 7.4
**License:** GPLv2 or later
**License URI:** [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

Create shortlinks for WordPress posts/pages with **Bitly, TinyURL, Rebrandly**, or **native permalinks**.

---

## 📚 Description

**All In One Link Shortener** allows easy generation of shortlinks for your WordPress content.
Choose from multiple providers: **Bitly, TinyURL, Rebrandly**, or use **WordPress permalinks**.

### 🔑 Compliance with WordPress.org Guidelines

* **External Services:** Only the permalink URL of a post/page is sent to third-party APIs (Bitly, TinyURL, Rebrandly). No personal data, titles, or analytics are transmitted.
* **Privacy:** The plugin does **not** track users, collect analytics, or include hidden data collection. Shortlinks are generated only when publishing or updating a post.

### ✨ Features

* Automatic shortlink generation on publish/update.
* Supports providers: Bitly, TinyURL, Rebrandly, or WordPress permalinks.
* Admin column with copy-to-clipboard button.
* Regenerate shortlinks via post actions or bulk actions.
* WP-CLI support for developers.
* Shortcode `[aiols_shortlink id="123"]`.
* Secure API token setup in plugin settings.

---

## 🔓 How to Generate API Tokens

### Bitly (Generic Access Token)

1. Sign in at [Bitly](https://bitly.com).
2. Navigate to **Settings → API / Developer**.
3. Generate a **Generic Access Token**.
4. Copy and paste into **All In One Link Shortener**.

### TinyURL (API Key)

1. Sign up/sign in at [TinyURL](https://tinyurl.com/app/dev).
2. Open **Developer / API Settings**.
3. Create an **API Key**.
4. Paste into plugin settings.

### Rebrandly (API Key)

1. Sign in at [Rebrandly Dashboard](https://app.rebrandly.com/).
2. Go to **Developer → API Keys**.
3. Generate a new API key.
4. Paste it into plugin settings.

### Add Token to Plugin

1. Go to **WordPress Admin → All In One Link Shortener**.
2. Select your provider and paste the token.
3. Save changes and publish a post to verify shortlink generation.

---

## 🛡️ Security Best Practices

1. Treat tokens as secrets — do not commit to Git or expose publicly.
2. Store tokens only in plugin settings (WordPress Options).
3. Limit token permissions if supported.
4. Rotate/revoke tokens if compromised.
5. Include links to each provider's privacy and terms pages.

---

## ⚙️ Installation

1. Upload the plugin to `/wp-content/plugins/all-in-one-link-shortener`.
2. Activate it from the **Plugins** menu.
3. Open **All In One Link Shortener** settings.
4. Choose default provider and enter API token if needed.
5. Publish/update a post to generate a shortlink.

---

## ❓ Frequently Asked Questions

**Which providers are supported?**

* WordPress Permalinks (no external API)
* Bitly → [API Docs](https://dev.bitly.com/)
* TinyURL → [API Docs](https://tinyurl.com/app/dev)
* Rebrandly → [API Docs](https://developers.rebrandly.com/)

**Do I need an API token?**
Yes, for Bitly, TinyURL, and Rebrandly. WordPress permalinks don’t require one.

**What data is sent?**
Only the permalink URL. No personal data is shared.

**Where are shortlinks stored?**
Shortlinks are stored as post meta (`_aiols_shortlink`).

**Can I use this plugin without third-party services?**
Yes, select **Permalink** as the provider.

---

## 🖼️ Screenshots

1. Copy shortlink button in the post editor.
2. Shortlink column in post list.
3. Settings page with provider + token options.
4. Shortcode display in frontend.

---

## 🌐 External Services

1. **Bitly**

   * API URL: [https://dev.bitly.com/api-reference/](https://dev.bitly.com/api-reference/)
   * Purpose: Generate shortlinks using Bitly.
   * Data Sent: Original URL + API token.
   * Terms: [Bitly TOS](https://bitly.com/pages/terms-of-service)
   * Privacy: [Bitly Privacy](https://bitly.com/pages/privacy)

2. **TinyURL**

   * API URL: [https://tinyurl.com/app/dev](https://tinyurl.com/app/dev)
   * Purpose: Generate shortlinks using TinyURL.
   * Data Sent: Original URL + API key.
   * Terms: [TinyURL TOS](https://tinyurl.com/app/terms)
   * Privacy: [TinyURL Privacy](https://tinyurl.com/app/privacy-policy)

3. **Rebrandly**

   * API URL: [https://developers.rebrandly.com/reference/createlink](https://developers.rebrandly.com/reference/createlink)
   * Purpose: Generate shortlinks using Rebrandly.
   * Data Sent: Original URL + API key.
   * Docs: [Rebrandly Docs](https://developers.rebrandly.com/docs/get-started)
   * Terms: [Rebrandly TOS](https://www.rebrandly.com/terms)
   * Privacy: [Rebrandly Privacy](https://www.rebrandly.com/privacy)

---

## 📌 Changelog

### 1.0

* Initial release
* Multi-provider support (Bitly, TinyURL, Rebrandly)
* Default WordPress Permalink provider
* Bulk regenerate + WP-CLI support
* Shortcode `[aiols_shortlink id="123"]`
* Proper enqueue of JS and CSS using `wp_enqueue_script()` and `wp_enqueue_style()`

---

## 🔔 Upgrade Notice

### 1.0

First release. Token-based providers supported. Only permalinks are sent to third-party APIs.

---

## 🌱 Open Source

* GitHub: [All In One Link Shortener](https://github.com/shitalparmar443/all-in-one-link-shortener)
* Contributions via pull requests are welcome.

---

## 💬 Support

* Use the [WordPress.org support forum](https://wordpress.org/support/plugin/all-in-one-link-shortener/) or open an issue on GitHub.

---

### Shortcode Example

```php
[aiols_shortlink id="123"]
```

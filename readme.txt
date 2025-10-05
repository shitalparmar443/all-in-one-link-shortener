=== All In One Link Shortener ===
Contributors: shitalparmar443
Donate link: https://www.paypal.me/shitalparmar443
Tags: shortlink, permalink, seo, url-shortener, links
Requires at least: 6.1
Tested up to: 6.8
Stable tag: 1.0.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Plugin URI: https://wordpress.org/plugins/all-in-one-link-shortener/
Author URI: https://profiles.wordpress.org/shitalparmar443/

Create shortlinks for WordPress posts/pages using Bitly, TinyURL, Rebrandly, or native WordPress permalinks.

== Description ==
All In One Link Shortener allows you to easily create short links for posts, pages, or any URL on your WordPress site. It supports multiple link shortening services including Bitly, TinyURL, Rebrandly, is.gd and cutt.ly. You can generate short links using a shortcode and quickly copy them in the admin area.

This plugin is lightweight, fully compatible with WordPress, and provides an intuitive interface in the admin dashboard.

== Features ==
- Automatic shortlink generation on publish/update.  
- Supports providers: Bitly, TinyURL, Rebrandly, cutt.ly, is.gd or native WordPress permalinks.  
- Admin column with copy-to-clipboard button.  
- Regenerate shortlinks via post actions or bulk actions.  
- WP-CLI support for developers.  
- Shortcode `[aiols_shortlink id="123"]`.  
- API token setup in plugin settings.  

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/all-in-one-link-shortener`.  
2. Activate it from the **Plugins** menu in WordPress.  
3. Go to **Settings → All In One Link Shortener**.  
4. Choose your default provider and enter your API token if required.  
5. Publish or update a post to automatically generate a shortlink.  

== How to Generate API Tokens ==

= Bitly (Generic Access Token) =
1. Sign in at [Bitly](https://bitly.com/).  
2. Open Settings → API / Developer section.  
3. Click "Generic Access Token" → Generate/Create.  
4. Re-enter password if prompted.  
5. Copy and store the token securely.  
6. Paste the token in plugin settings → Save Changes.  

= TinyURL (API Key) =
1. Sign in at [TinyURL](https://tinyurl.com/app/dev).  
2. Open Developer / API or API Settings.  
3. Click "Create API Key" and follow prompts.  
4. Copy the generated key and store securely.  
5. Paste into plugin settings → Save Changes.  

= Rebrandly (API Key) =
1. Sign in at [Rebrandly](https://app.rebrandly.com/).  
2. Open Dashboard → Developer / API section.  
3. Click "Create New API Key" and follow prompts.  
4. Copy and store the key securely.  
5. Paste into plugin settings → Save Changes.  

= Cutt.ly (API Key) =
1. Sign in at [Cutt.ly](https://cutt.ly/).
2. In the left sidebar, go to API → [API Key](https://cutt.ly/edit).
3. Your API Key will be visible on the right side under your account information.
4. If you don’t have one, click Generate API Key to create a new key.
5. Copy the generated key and store it securely.
6. Paste it into your plugin settings → Save Changes.

= is.gd (No API Key Required) =
1. Visit [is.gd](https://is.gd/developers.php)
2. You don’t need to create an account or API key — is.gd’s shortening service is open for public use.
3. To shorten a link using the API, simply use the following URL format: `https://is.gd/create.php?format=json&url=YOUR_LONG_URL`.
4. Replace YOUR_LONG_URL with the actual link you want to shorten.
5. You can test this directly in your browser or use it programmatically via your plugin.
6. For full API documentation, visit [is.gd API Reference](https://is.gd/developers.php).

== Security & Best Practices ==
- Treat tokens as secrets; do not commit to public repositories.  
- Store only in plugin settings; never print in frontend HTML.  
- Limit token permissions if the provider allows.  
- Rotate (revoke/regenerate) tokens periodically or if compromised.  

== Frequently Asked Questions ==

= How do I create a short link? =
Use the plugin settings page or the shortcode `[aiols_shortlink id="123"]`.

= Does it support third-party services? =
Yes, Bitly, TinyURL, and Rebrandly are supported. API keys are required. WordPress permalinks do not require keys.

= What data is sent? =
Only the **post/page URL** is sent. No personal data or post titles are transmitted.

= Where are shortlinks stored? =
Shortlinks are stored as post meta (`_aiols_shortlink`).

= Can I use this plugin without third-party services? =
Yes. Select **Permalink** as the provider. No external API requests are made.

== Screenshots ==
1. Copy shortlink button in the editor.  
2. Shortlink column in post list.  
3. Settings page with provider + API token options.  

== External Services ==
1. **Bitly**  
   - API URL: https://dev.bitly.com/api-reference/  
   - Purpose: Generate short links using Bitly.  
   - Data Sent: Original URL + API token.  
   - Terms of Service: https://bitly.com/pages/terms-of-service  
   - Privacy Policy: https://bitly.com/pages/privacy  

2. **TinyURL**  
   - API URL: https://tinyurl.com/app/dev  
   - Purpose: Generate short links using TinyURL.  
   - Data Sent: Original URL + API key.  
   - Terms of Service: https://tinyurl.com/app/terms  
   - Privacy Policy: https://tinyurl.com/app/privacy-policy  

3. **Rebrandly**  
   - API URL: https://developers.rebrandly.com/reference/createlink  
   - Purpose: Generate short links using Rebrandly.  
   - Data Sent: Original URL + API key.  
   - Documentation: https://developers.rebrandly.com/docs/get-started  
   - Terms of Service: https://www.rebrandly.com/terms  
   - Privacy Policy: https://cutt.ly/privacy

4. **cutt.ly**  
   - API URL: https://cutt.ly/api-documentation/regular-api  
   - Purpose: Generate short links using cutt.ly.  
   - Data Sent: Original URL + API key.  
   - Documentation: https://cutt.ly/api-documentation/regular-api
   - Terms of Service: https://cutt.ly/terms 
   - Privacy Policy: https://cutt.ly/privacy
   - Contact us : https://cutt.ly/contact

5. **is.gd API Information (No API Key Required)**
	- API URL: [https://is.gd/create.php?format=json&url=YOUR_LONG_URL](https://is.gd/create.php?format=json&url=YOUR_LONG_URL)  
	- Purpose: Generate short links without requiring an API key.  
	- Data Sent: Original long URL (no authentication required).  
	- Documentation: [https://is.gd/developers.php](https://is.gd/developers.php)  
	- Terms of Service: [https://is.gd/terms.php](https://is.gd/terms.php)  
	- Privacy Policy: [https://is.gd/privacy.php](https://is.gd/privacy.php)  
	- Contact Us: [https://is.gd/contact.php](https://is.gd/contact.php)

== Open Source ==
- GitHub repository: https://github.com/shitalparmar443/all-in-one-link-shortener  
- Contributions via pull requests are welcome.

== Changelog ==

= 1.0.2 =
* is.gd shortlink provider added.

= 1.0.1 =
* cutt.ly shortlink provider added.

= 1.0 =
* Initial release  
* Multi-provider shortlink support (Bitly, TinyURL, Rebrandly)  
* Default Permalink provider  
* Bulk regenerate, WP-CLI support  
* Shortcode `[aiols_shortlink id="123"]`  
* Proper enqueue of JS and CSS using `wp_enqueue_script()` and `wp_enqueue_style()`  

== Upgrade Notice ==
= 1.0 =
First release of All In One Link Shortener. Token-based providers supported. Only permalinks are sent to third-party APIs.
=== All In One Link Shortener ===
Plugin URI: https://wordpress.org/plugins/all-in-one-link-shortener
Contributors: shitalparmar443
Author URI: https://profiles.wordpress.org/shitalparmar443/
Donate link: https://www.paypal.me/shitalparmar443/
Tags: shortlink, permalink, seo, url-shortener, links
Requires at least: 6.1
Tested up to: 6.8
Stable tag: 1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create shortlinks for WordPress posts/pages with Bitly, TinyURL, Rebrandly or permalinks.

== Description ==

**All In One Link Shortener** makes it easy to generate shortlinks for your WordPress content.  
You can choose from multiple providers such as **Bitly, TinyURL, Rebrandly**, or simply use your **own WordPress permalink**.

### 🔑 Compliance with WordPress.org Guidelines
- **Guideline 6 – External Services**: This plugin connects to third-party APIs (Bitly, TinyURL, Rebrandly) to generate shortlinks. Only the permalink of your post/page is sent. Nothing else (title, user data, analytics) is transmitted. Each provider requires an API key/token that you generate yourself.  
- **Guidelines 7 & 9 – No Tracking Without Consent**: This plugin does **not** track users, does not send analytics, and does not include hidden data collection. Shortlinks are generated only when you publish or update a post.  

### ✨ Features
- Automatic shortlink generation on publish/update.  
- Providers: Bitly, TinyURL, Rebrandly, or native WordPress permalinks.  
- Admin column with copy-to-clipboard button.  
- Regenerate shortlinks via post actions or bulk actions.  
- WP-CLI support for developers.  
- Shortcode `[aiols_shortlink id="123"]`.  
- API token setup in plugin settings.  

== How to generate API tokens ==

= Bitly (Generic Access Token) =
1. Sign in to your Bitly account at bitly.com.
2. Open Settings (left sidebar) and select the API / Developer section.
3. Find "Generic Access Token" (or OAuth / Access Tokens) and click Generate / Create.
4. You may be asked to re-enter your Bitly password — confirm to proceed.
5. Copy the token shown (store it securely; treat as a secret).
6. Paste the token into the plugin settings (Settings → All In One Link Shortener) and save.
7. If needed, revoke/regenerate the token from the same Bitly settings page.

= TinyURL (API Key) =
1. Sign up or sign in at TinyURL (open the TinyURL developer/ dashboard page).
2. Go to the Developer / API or "API Settings" section in your TinyURL account.
3. Click "Create API Key" (or "Create token") and follow any prompts (name the key if asked).
4. Copy the generated API key and store it securely (do not publish it).
5. Paste the key into the plugin settings (Settings → All In One Link Shortener) and save.
6. Use TinyURL's dashboard to revoke/regenerate the key if required.

= Rebrandly (API Key) =
1. Sign up or sign in at app.rebrandly.com (Rebrandly dashboard).
2. Open your account Dashboard → Developer / API section.
3. Click "Create new API key" (or New API Key) and follow prompts.
4. Copy the generated API key and store it securely (do not commit it to VCS).
5. Paste the API key into the plugin settings (Settings → All In One Link Shortener) and save.
6. Optionally, use OAuth instead of API key if you need delegated access; revoke/regenerate keys from the same dashboard.

= How to add the token to this plugin =
1. In WordPress admin go to Settings → All In One Link Shortener.
2. Select your provider from the "Default Provider" dropdown.
3. Paste the provider token/API key into the "API Token" (or "API Key") field.
4. Click "Save Changes".
5. Verify: publish/update a post (or use a "Test" button if present) and confirm a shortlink is generated.

= Security & best practices =
1. Treat tokens as secrets — never commit them to Git or expose in public code.  
2. Store tokens only in plugin settings (WordPress Options) and never print them in frontend HTML.  
3. Limit token scope if the provider supports scopes/permissions.  
4. Rotate (revoke/regenerate) tokens periodically or immediately if compromised.  
5. Document in your plugin readme where tokens are stored and include links to each provider's privacy/terms pages.

== Installation ==

1. Upload the plugin to `/wp-content/plugins/all-in-one-link-shortener`.  
2. Activate it from the **Plugins** menu in WordPress.  
3. Go to **Settings → All In One Link Shortener**.  
4. Choose your default provider and enter your API token if required.  
5. Publish or update a post to automatically generate a shortlink.  

== Open source ==
1. Open source fully code: https://github.com/shitalparmar443/all-in-one-link-shortener/blob/main/README.md 
2. You can pull request anytime if you add new features or find any bugs — pull requests are welcome.

== Frequently Asked Questions ==

= Which providers are supported? =
- WordPress Permalink (no external request).  
- Bitly: https://dev.bitly.com/  
- TinyURL: https://tinyurl.com/app/dev  
- Rebrandly: https://developers.rebrandly.com/  

= Do I need an API token? =
Yes. Bitly, TinyURL, and Rebrandly require tokens. WordPress permalinks do not.

= What data is sent? =
Only the **permalink URL** of your post/page. No personal data, titles, or analytics are transmitted.

= Where are shortlinks stored? =
Each shortlink is stored as post meta (`_aiols_shortlink`).

= How do I generate an API token? =
- **Bitly**: https://dev.bitly.com/docs/getting-started/authentication/  
- **TinyURL**: https://tinyurl.com/app/dev  
- **Rebrandly**: https://developers.rebrandly.com/docs/get-started  

= Can I use this plugin without third-party services? =
Yes. Choose **Permalink** as the provider to use your WordPress permalink only. No external API requests are made.

== Screenshots ==
1. Copy shortlink button in the editor.  
2. Shortlink column in post list.  
3. Settings page with provider + token options.  

== Changelog ==

= 1.0 =
* Initial release  
* Multi-provider shortlink support (Bitly, TinyURL, Rebrandly)  
* Default Permalink provider (no external API calls)  
* Bulk regenerate, WP-CLI support  
* Shortcode `[aiols_shortlink]`  

== Upgrade Notice ==

= 1.0 =
First release. Token-based providers supported. Only permalinks are sent to third-party APIs.

== Notes ==

- This plugin is open-source. Contributions are welcome via the GitHub repository.

- If you find issues or want to contribute new features, please open a pull request on GitHub: https://github.com/shitalparmar443/all-in-one-link-shortener

== Support == 

- For support, please use the plugin's support forum on WordPress.org or open an issue on GitHub.
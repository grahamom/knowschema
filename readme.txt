=== KnowSchema ===
Contributors: grahamom
Tags: schema, json-ld, seo, entities, wikidata
Requires at least: 6.2
Tested up to: 6.7
Stable tag: 2.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress Schema Manager with AI Automation and Entity Publishing. Generate precise JSON-LD graphs and manage entities.

== Description ==

KnowSchema outputs comprehensive, correct JSON-LD schema across a site using a graph model. It is designed for solo creators, agencies, and teams who need precise control over their entity graph and schema output without the bloat of "all-in-one" SEO plugins.

**Core Features (Free)**

*   **Schema Graph:** Generates a single, interconnected JSON-LD graph per page.
*   **Entity Management:** Create reusable Organization and Person entities via a dedicated Custom Post Type.
*   **Template System:** Assign schema templates per post. Supports:
    *   Article
    *   WebPage
    *   Product (with WooCommerce support)
    *   Review
    *   Event
    *   Recipe
    *   SoftwareApplication
    *   VideoObject
    *   FAQPage
*   **Rich Results Readiness:** Live validation in the editor showing missing required and recommended fields (Green/Amber/Red status).
*   **Site Audit:** Overview of schema coverage and readiness status across your site.
*   **Wikidata Integration:** Link entities to Wikidata (sameAs) and export "Edit Plans" for manual updates.
*   **Import/Export:** Move settings and entities between sites.

**Pro Features (Architecture Ready)**
*   AI Automation hooks.
*   Wikidata Publishing hooks.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/knowschema` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to **Settings -> KnowSchema** to configure your primary entity.
4. Create your first Entity under **KnowSchema -> Schema Entities**.

== Frequently Asked Questions ==

= Does this plugin support WooCommerce? =
Yes. If you select the "Product" template on a WooCommerce product page, it will automatically try to map data from the product (SKU, price, availability).

= How do I check if my schema is valid? =
Use the "Preview Schema" button in the post editor to see the generated JSON-LD. Copy/paste that into the [Google Rich Results Test](https://search.google.com/test/rich-results).

= Does it conflict with other SEO plugins? =
KnowSchema is designed to work alongside them, but you should disable the schema output features of other plugins to avoid duplicate graphs.

== Screenshots ==

1. Entity Management
2. Schema Templates and Readiness Check

== Changelog ==

= 2.0.0 =
* Initial release of v2 architecture.
* Added Entity CPT.
* Added Templates: Article, Product, Event, Review, Recipe, SoftwareApplication, VideoObject.
* Added Site Audit.
* Added Import/Export.
# KnowSchema

**WordPress Schema Manager with AI Automation and Entity Publishing.**

KnowSchema outputs comprehensive, correct JSON-LD schema across a site using a graph model. It is designed for solo creators, agencies, and teams who need precise control over their entity graph and schema output.

## Features

### Core (Free)

*   **Schema Graph:** Generates a single, interconnected JSON-LD graph per page (`@graph`).
*   **Entity Management:** Create reusable Organization and Person entities via a dedicated Custom Post Type (`ks_entity`).
*   **Template System:** Assign schema templates (Article, Product, Review, Event, FAQPage, etc.) per post with specific fields.
*   **Rich Results Readiness:** Live validation in the post editor showing missing required and recommended fields (Green/Amber/Red status).
*   **Site Audit:** Overview of schema coverage and readiness status across your site.
*   **Wikidata Integration (Manual):** Link entities to Wikidata (`sameAs`) and export structured "Edit Plans" for manual updates.
*   **Import/Export:** JSON-based import/export of settings, entities, and schema data.

### Pro (Architecture Ready)

The plugin includes interfaces and hooks for Pro extensions:
*   **AI Automation:** Interfaces defined for AI providers to draft schema, FAQs, and Entity profiles.
*   **Wikidata Publishing:** Interfaces defined for OAuth connection and API publishing.

## Installation

1.  **Download/Clone:**
    *   Clone this repository into `wp-content/plugins/knowschema`.
    *   Run `composer install` to verify dependencies (though the release zip should include them).
2.  **Activate:**
    *   Go to **Plugins** in WordPress admin.
    *   Activate **KnowSchema**.
3.  **Initial Setup:**
    *   Navigate to **Settings -> KnowSchema**.
    *   Enter your Organization Name (optional fallback).
    *   **Crucial:** Create a new Entity under **KnowSchema -> Schema Entities** (e.g., your Company or yourself).
    *   Return to **Settings -> KnowSchema** and select this new entity as the **Primary Entity**.

## Configuration & Verification

To ensure the plugin is working correctly and producing valid Schema, follow these steps:

### 1. Verify Global Schema (WebSite & Organization)
1.  Visit your site's **Homepage**.
2.  View Source and search for `application/ld+json`.
3.  **Check:** You should see a `WebSite` node and an `Organization` (or `Person`) node representing your Primary Entity.
4.  **Validate:** Copy the script content and paste it into the [Rich Results Test](https://search.google.com/test/rich-results) or [Schema.org Validator](https://validator.schema.org/).

### 2. Verify Per-Post Schema
1.  Create or Edit a **Post**.
2.  Scroll down to the **KnowSchema Settings** metabox.
3.  Select a **Schema Template** (e.g., `Article`, `Product`, `Review`).
4.  Fill in the specific fields that appear (e.g., Rating, Price, ISBN).
5.  **Check Readiness:** Ensure the "Rich Results Readiness" badge turns Green. If Red or Amber, fill in the missing fields listed.
6.  **Preview:** Click **Preview Schema** to generate the JSON-LD in real-time without saving.
7.  **Publish/Update** the post.
8.  **Validate:** URL test the post in the Rich Results Test tool.

### 3. Verify Entity Linking (Graph)
1.  Check the `publisher` or `author` field in your Post's schema.
2.  **Expectation:** It should reference the `@id` of your Primary Entity (e.g., `https://yoursite.com/#organization`), ensuring the graph is connected.

### 4. Verify Wikidata "Edit Plan"
1.  Go to **KnowSchema -> Schema Entities**.
2.  Edit your Primary Entity.
3.  Ensure you have selected "Organization" or "Person" and filled in the URL.
4.  Click **Export Edit Plan** in the sidebar.
5.  **Expectation:** A text area should appear with structured text formatted for Wikidata entry.

## Developer Notes: Pro Features & APIs

This repository contains the **Free** version of KnowSchema. The "Pro" features (AI and automated Wikidata publishing) are implemented via interfaces.

### Connecting an AI Provider
To test or implement the AI functionality, you must register a provider class that implements `KnowSchema\AI\AI_Provider_Interface`.

**Hook:** `knowschema_ai_provider`

```php
add_filter( 'knowschema_ai_provider', function( $provider ) {
    return new My_Custom_AI_Provider(); // Must implement AI_Provider_Interface
});
```

**Verification:**
1.  Implement a dummy provider.
2.  Go to a Post Editor.
3.  The "Draft Schema with AI" button in the metabox should now be **enabled** (previously disabled).

### Connecting Wikidata API
To test the Wikidata API logic, you would implement `KnowSchema\Wikidata\Wikidata_Client_Interface`. The current Free version uses a local "Edit Plan" generator instead of a live API client.

## Development

### Requirements

*   PHP 8.0+
*   WordPress 6.2+
*   Composer

### Running Tests

Unit tests use PHPUnit and Mockery.

```bash
composer install
vendor/bin/phpunit
```

## License

GPLv2 or later.

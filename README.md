# KnowSchema

WordPress Schema Manager with AI Automation and Entity Publishing.

## Description

KnowSchema outputs comprehensive, correct JSON-LD schema across a site using a graph model. It is designed for solo creators, agencies, and teams who need precise control over their entity graph and schema output.

### Key Features (Free)

*   **Schema Graph:** Generates a single, interconnected JSON-LD graph per page.
*   **Entity Management:** Create reusable Organization and Person entities via a dedicated Custom Post Type.
*   **Template System:** Assign schema templates (Article, Product, Review, Event, FAQPage, etc.) per post.
*   **Rich Results Readiness:** Live validation in the editor showing missing required and recommended fields.
*   **Site Audit:** Overview of schema coverage and readiness status across your site.
*   **Wikidata Integration:** Link entities to Wikidata (sameAs) and export "Edit Plans" for manual updates.
*   **Import/Export:** Move settings and entities between sites.

### Pro Features (Planned)

*   **AI Automation:** Draft schema, FAQs, and Entity profiles using AI.
*   **Wikidata Publishing:** Publish entity updates directly to Wikidata via OAuth.
*   **Advanced Audits:** Scheduled scans and historical tracking.

## Installation

1.  Upload the plugin files to the `/wp-content/plugins/knowschema` directory, or install the plugin through the WordPress plugins screen directly.
2.  Activate the plugin through the 'Plugins' screen in WordPress.
3.  Navigate to **Settings -> KnowSchema** to configure your primary entity.
4.  Create your first Entity under **KnowSchema -> Schema Entities**.

## Development

### Requirements

*   PHP 8.0+
*   WordPress 6.2+

### Setup

```bash
composer install
npm install # (If assets require building in future)
```

### Testing

Run the PHPUnit test suite:

```bash
vendor/bin/phpunit
```

## License

GPLv2 or later.
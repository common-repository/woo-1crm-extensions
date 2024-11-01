=== Woo 1CRM Extensions ===
Contributors: visual4
Tags: woocommerce, checkout, 1crm, additional fields, custom fields, cart, warenkorb, benutzerdefinierte felder, bestellung, versand, order, extension, extensions, endpoint, api, webhook, webhooks
Requires at least: 3.1.0
Tested up to: 4.7.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin extends WooCommerce Checkout Process by custom fields, defined in admin backend and adds all fields to Order Webhooks for 1CRM System.
Plugin für WooCommerce das den Bestellprozess um benutzerdefinierte Felder erweitert und diese an die 1CRM Schnittstelle übergibt.

== Description ==

'Woo 1CRM Extensions' Plugin extends WooCommerce Checkout by custom fields.
Admin Users are able to create input fields in WordPress Backend, afterwards shown in different sections in Users Frontend Checkout Process.
Following types of fields are available at the moment:
- text field
- text area
- checkbox
- dropdown

Fields can be created in Billing, Shipping or Order Section.
If customer fills in the blank, contents are shown in order items in WordPress Backend and added to WooCommerce Webhook to 1CRM or equivalent system.
1CRM synchronizes all fields, on condition that 1CRM interface is installed and equivalent custom fields are created in 1CRM System (name of custom field in 1CRM should match "Feld ID im 1CRM" column in plugin settings).

The plugin also provides salutation to billing section and pushes paypal-ID to Webhook body as default.
Optionally, the system sends an email to the administrator of the page if an error occurs at sending Webhooks.
To prevent rounding errors in 1CRM, all rates in order are delivered up to fourth decimal place.

Following Custom-Webhooks are added:
 - customer.updated_fe (when user changes contact details in "My Account"-Section)
 - product_comment.created_updated (when a comment is left to a product)
 - product_category.created_updated (when a category is created or changed. All parent categories are recursively send.)

------
 
Das Woo 1CRM Extensions Plugin erweitert den Bestellprozess von WooCommerce um eigens definierte Felder.
Der Admin Benutzer ist in der Lage, die gewünschten Felder im Backend zu definieren.
Derzeit sind folgende unterschiedliche Feldtypen möglich:
- Textfelder
- Textareas
- Checkboxen
- Dropdowns

Diese können in den Bereichen 'Rechnung', 'Versand' und 'Bestellung allgemein' angelegt werden.
Sie werden dann, entsprechend der definierten Bereiche, im Bestellprozess angezeigt. Füllt der Kunde diese Felder aus, werden die angegebenen Inhalte auch im WordPress Backend bei der Bestellung angezeigt.
Alle definierten Felder werden außerdem zusätzlich im Webhook erweitert, und somit an die 1CRM Schnittstelle übergeben und dort eingefügt.
Dazu ist es nötig, alle zu synchronisierenden Felder zuvor auch in ihrem 1CRM System anzulegen.
Zu beachten ist hierbei, dass die Bezeichnung der 1CRM-Felder identisch zu dem im WordPress-Backend unter "Feld ID im 1CRM" angegebenen Feldnamen erfolgt.

Des Weiteren wird derzeit automatisiert auch die für den Zahlungsprozess notwendige PayPal-ID an das 1CRM übergeben.
Optional erhält der Benutzer die Möglichkeit automatisiert beim Auftreten von Fehlern beim Versenden von Webhooks eine Email an den Administrator der Seite zu versenden.
Alle Preise der Bestellungen werden im Webhook zusätzlich mit allen Dezimalstellen übergeben, somit werden Rundungsprobleme bei der Weiterverarbeitung der Daten im 1CRM verhindert.

Es werden zusätzlich folgende Webhooks zum WooCommerce-Standard hinzugefügt:
 - customer.updated_fe (wenn der Benutzer seine Kontaktdaten unter "My Account" selbst ändert)
 - product_comment.created_updated (wenn ein Komentar zu einem Produkt hinterlassen wird)
 - product_category.created_updated (wenn eine Kategorie angelegt oder geändert wird. Es werden rekursiv alle Eltern Kategorien mit überliefert.)


== Installation ==

1. Upload `woo-1crm-extensions` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on the new menu item "Woo 1CRM Extensions" and create your first Custom Fields!
4. Your custom field group will now appear on the checkout page!
5. Define identical Fields in your 1CRM Interface Plugin
6. Enjoy


== Changelog ==

= 1.0.1 =
Bugfix for integration without WPML 


= 1.0 =
Following Fieldtypes are available and synchronized to 1CRM:
- textfields
- textareas
- checkboxes
- dropdown fields


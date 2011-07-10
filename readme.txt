=== DB Toolkit ===
Contributors: Desertsnowman, HalstonJames
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=PA8U68HRBXTEU&lc=ZA&item_name=dbtoolkit%20development&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags: interface, database, tables, database tables, application development, app engine, database interface toolkit
Requires at least: 2.9.2
Tested up to: 3.2
Stable tag: 0.2.6.5

Database Interface Toolkit creates interfaces (capture/update forms, reports, charts etc.) from database tables.

== Description ==

Adds interfaces to database tables on pages, posts, dashboard and custom menu groups within admin, to manage data.
Build custom databased application for managing data from simple data entries to more complex linking tables.

I'm running a tutorial series to explain the fundermentals on using the system and will be building documentation as i go.
Tutorials will be on up on http://dbtoolkit.digilab.co.za
please note: this is still alpha, but works pretty well.


Features include:

*   Rebuilt Visual forms and reports builder
*   Templates for output
*   Field Specific configuration
*   Application Export and Import
*   Data importing via CSV files
*   Report Exporting in PDF and CSV
*   Multi Interface Layouts using clusters

== Installation ==

1. Upload the plugin folder 'db-toolkit' to your `/wp-content/plugins/` folder
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Select DB Toolkit from the menu group

== Frequently Asked Questions ==

Q: Is there any Documentation?
A: Not fully, but I am putting together a tutorial series and building the documentation as i go along. you can access these at http://dbtoolkit.digilab.co.za/

Q: When will you have an RC release?
A: Well you can technically use it now, however some things are still a little iffy (like cloned linking links) But it should mostly work on single level interfaces.

== Screenshots ==

1. Build Database Management Interfaces and Viewers.
2. Interface and Application Management Screen.
3. An Interface built in DB-Toolkit to manage data.
4. Interface Config Screen (Lots of Options!).
5. Database Table data represented in Chart Mode.
6. Data Importer Dialog.

== Changelog ==

= 0.2.6.5 =
* Corrected a few bugs that prevented images from showing in view/edit mode.
* Other bugs on layout builder corrected

= 0.2.6.4 =
* Fixed a bug in the clusters that echoed out the layout string.

= 0.2.6.2 =
* Fixed a rare bug that made the multi-select filter overlap the page.

= 0.2.6.1 =
* Fixed the View Template and Layout
* Cleaned up the style sheets to better match WP3.2
* Updated jQueryUI style
* Fixed a few bux with editing, dialogs, refresh, toolbars and layout forms.
* Removed the "Filters" title and changed the filter fields titles to be wrapped in h2 tags. (looks much cleaner);
* misc bug fixes.

= 0.2.5.3 =
* Fixed a bug that prevented the saving of form, view and cluster layouts in WP 3.2
* Added AUTH method to the API engine
* Added INSERT, UPDATE, DELETE method to the API engine
* Added Data Sources option to allow for automatictable population from a XML Source. (pretty neet!)

= 0.2.5.2 =
* Fixed a bug where wysiwyg field output with nl2br.

= 0.2.5.1 =
* Updated the timthumb version for image upload field type. Please note that the setup interface has changed.
* added more api changes.

= 0.2.5.0 =
* Fixed a problem with charting on date fields.
* Added html as an API format type

= 0.2.4.8 =
* Fixed a problem with Selected Item Filter field type on a cloned field.

= 0.2.4.8 =
* Optimized javascript and css files so they are not included on every page and only on a page that has an interface. its not perfect yet. just a test to help out with load times. It will be increased to only include required scripts for exact interface configuration.

= 0.2.4.7 =
* Fixed a bug on the Field Group Plugin. might still be dodgy though but i think i got it.

= 0.2.4.6 =
* Fixed a bug that caused an interface to fail if a field has the same name as a mysql function i.e. interval, sum etc..

= 0.2.4.5 =
* Interface changes made to make the editing and creation of interfaces easier as a lot of the features get explained.

= 0.2.4.4 =
* Fixed a bug that prevented the title of an enum fieldtype from displaying correctly.

= 0.2.4.3 =
Added Error Reporting. if a interface has errors, you get an error box asking to submit an error report. This helps me to find query building bugs and fix them.
Data sent in this error report:
- Site name
- Site URL
- Admin Email (Only used for me to email back to ask questions etc.)
- Wordpress Version
- Interface Configuration
- mysql query error
- mysql query generated.

It does not send any data from the table nor the table structure. juist information about the interface and the error.

= 0.2.4.2 =
    Added {{_footer_pagination}}
    Added {{_footer_first}}
    Added {{_footer_last}}
    Added {{_footer_pagecount}}
    Fixed a bug that made the interface load before the widget on text shortcode usage.
    Fixed a bug that prevented jquery from loading.
    Fixed a bug the made duplicate reports on pagination shift.

= 0.2.4.1 =
    Fixed a bug the made duplicate reports on pagination shift.

= 0.2.4.0 =
    Fixed modal style dialogs

= 0.2.3.91 =
    Theme updates and cleanups.

= 0.2.3.8 =
    Added the Gravatar FieldType

= 0.2.3.7 =
    fixed a bug that duplicated the custom source and custom css inserts.

= 0.2.3.6 =
    fixed a bug that prevented "Public" interfaces from showing and reverted them to "read" access.

= 0.2.3.5 =
    minor bug fixes.
    Removed legacy list template panel.
    Bracketed field names if manually changed as not to loose their actual names.
    Indented Cloned Fields.
    Fixed a bug in the single select filter. Now displays as a standard dropdown.
    Changed the FieldTypes Display and Indexing to icon based.
    Added full Roles and capabilities permissions to interface setup.

= 0.2.3.4 =
Fixed a small bug with template mode not allowing custom scripts and css to be loaded for sidebar widgets.

= 0.2.3.3 =
Added support for full roles and capabilities per interface. (a little mess at the moment, but will improve on next release.)

= 0.2.3.2 =
minor bug fixes on template system

= 0.2.3.1 =
bug fixes on stability

== Upgrade Notice ==

Simply overwrite the existing folder with the new one.


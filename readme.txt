=== DB Toolkit ===
Contributors: Desertsnowman, HalstonJames
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=PA8U68HRBXTEU&lc=ZA&item_name=dbtoolkit%20development&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags: interface, database, tables, database tables, application development, app engine, database interface toolkit
Requires at least: 2.9.2
Tested up to: 3.1.3
Stable tag: 0.2.4.7

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

= 0.2.4.7 =
    - Fixed a bug on the Field Group Plugin. might still be dodgy though but i think i got it.

= 0.2.4.6 =
    - Fixed a bug that caused an interface to fail if a field has the same name as a mysql function i.e. interval, sum etc..

= 0.2.4.5 =
    - Interface changes made to make the editing and creation of interfaces easier as a lot of the features get explained.

= 0.2.4.4 =
    - Fixed a bug that prevented the title of an enum fieldtype from displaying correctly.

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

= 0.2.3.0 =
Cleaned up all the code in the core files and better arranged functions library.
Bug fixes!
Added filter= and by= to the shortcode to select a single record when the by is a selected item filter field type.
added Userbase Access Control as part of DB-Toolkit. (enable it in General Settings. Be sure the UAC plugin is disabled.)

= 0.2.2.22 =
Included Userbase Access Control for better page access management.

= 0.2.2.22 =
Mostly fixed bugs and problems that where posted to the forum http://dbtoolkit.digilab.co.za/forum/
Added the option to be able to filter a join result buy a specified where.

= 0.2.2.21 =
Mostly fixed bugs and problems that where posted to the forum http://dbtoolkit.digilab.co.za/forum/
Added clusters to the redirection panel and added application panels within

= 0.2.2.17 =
Fixed a bug with form redirections
Added clusters to the redirection panel and added application panels within
Added view item for calculator field
Additional bug fixes
Fixed a CSS problem with Blockquotes
Added the new forums feed to welcome page.
Made way for the possibility of Premium Support (still undecided but put stuff in)

= 0.2.2.16 =
Bug fixes with layout of forms.
Fixed the field templating to not render empty field templates if value is empty.
Added custom mysql function to mysql field type.

= 0.2.2.15 =
Build in the new list template system.
fixed the email fieldtype to send the new data on update and not the previous.

= 0.2.2.14 =
Fixed a joining problem that created duplicate fields if joining multiple fields to the same table.

= 0.2.2.13 =
Disabled Modal style Dialog due to a zindex javascript error that started in the plugin with Wordpress 3.1 I'll fix it and release an update with it later. for now - dialogs are standard.

= 0.2.2.12 =
left a small bit of debug code in, which is now removed. Sorry.

= 0.2.2.11 =
Added Interface Clusters
Rebuild layout Builders
Made Preparations for V0.3.0.0 Clean Release
Bug Fixes

= 0.2.2.10 =
Wordpress 3.1 Compatibility fix
Add interfaces to the new Admin Bar :)

= 0.2.2.9 =
Even more Visual Tweeks
Fixed a few more bugs
Preperations for Interface Clusters

= 0.2.2.8 =
Visual Tweeks
Fixed a few bugs with grouping field type

= 0.2.2.7 =
Fixed a bug the prevented Charts to show- sorry i didnt pick this up sooner.
PDF Export update to include templates
Import Button to allow importing CSV data into interfaces created
Insert Interface button for wordpress visual editor
Preperations for dynamic table and field creation.
Minor bug fixes.


= 0.2.2.3 =
Added a dashboard menu item which has feeds from support forums, blog, a donate link and a new features box.
Added an import option
Preparations for upcoming features
Fixed the limitation problem with the PDF and CSV Export

= 0.2.2.1 =
Fixed search mode, to only show results once a search is performed.

= 0.2.2.0 =
Fixed the Export Application to include the content of tables so you can pre-populate your applications.
Added API changes to include limiting and paging of data.
Fixed Search Mode to actually show the results
Fixed a bug that prevented the capturing the selected item value in form mode.
wysiwyg editor still a little buggy - but will be fixed soon.

= 0.2.1.8 =
Added the wysiwyg using CKEditor.
Fixed the PDF export.

= 0.2.1.7 =
added a password fieldtype that converts the text to a MD5 hash.

= 0.2.1.6 =
Added the ability to add filters to shortcodes e.g itemid=56

= 0.2.1.5 =
Fixed a form processor bug that prevented the post process from working correctly.
Fixed a bug in multiply columns field type.

= 0.2.1.4 =
Fixed a bug in the UserID FieldType that prevented showing items in list view

= 0.2.1.2 =
Fixed __DIR__ which is only available in php5.3 now works with lower php versions.

= 0.2.1.1 =
added Form Processors to the interface builder. see http://dbtoolkit.digilab.co.za/form-processors/ for more.

= 0.2.1.0 =
Rebuilt the query builder to better allow cloned fields and multiple linking of tables.
Added Application export and import to easily share applications you build (will be exploring this more in later builds)
Cleaned up the PDF Export to prevent overlapping columns.

= 0.2.0.4 = 
Made a massiv amount of changes on the structure + some exrea new features.
see http://dbtoolkit.digilab.co.za/version-0-2-on-the-horizon/ for more.

= 0.1.14 =
Some minor Fixes
= 0.1.13 =
Fixed:
 - Enqueue scripts and styles where not being included within sub menu pages.
 - Date Difference, Percentage field types added.
 - Minor Bug fixes

= 0.1.12 =
Fixed:
 - Enqueue scripts and styles are now only added to interface pages and not all admin pags so they dont interfere with other plugins.

Added:
 - New FieldType: Email Address - Validates as email and options to have result emailed as confirmation.
 - New FieldType: Telephone Number - validates a telephone number.
 - Visibility Shortcode: [visibility private] and [visibility public]
   - private: content nested is only rendered to logged in users
   - public: content nested is only visible to public/not logged in users
 - Cleaned up page styles

= 0.1.11 =
Fixed a vulnerability with a file upload script that could be exploited by allowing the upload of arbatary code as described at johnleitch.net http://www.johnleitch.net/Vulnerabilities/WordPress.Database.Interface.Toolkit.0.1.7.Arbitrary.Upload/61
files are now source validated to confirm they are being uploaded via the interface.
= 0.1.10 =
fixed a bug that prevented the datestap from working coreectly
= 0.1.9 =
View item issue corrcted while trying to view an item with a clone field
fixed the dup naming problem that carried over to the view item as well
= 0.1.8 =
Fixed a small bug where field names with duplicate parts ('nameID' and 'nameTime' where 'name' is the duplicate) gets incorrectly wrapped.
Updated the jqueryUI theme.
minor changes to base
= 0.1.7b =
Fixed a bug for the auto capture of fieldtype userID
= 0.1.7a =
Fixed a bug the prevented the selection of FieldTypes on fields with an underscore (_)
Fixed a bug the caused entries not to be displayed if a field name has a hyphen (-)
= 0.1.7 =
Fixed a bug with the auto id
added a link to the tutorials and documentation website
= 0.1.6 =
Made room for documentation and some minor bug fixes.
Fixed a bug with unserilizing general settings
= 0.1.5 =
fixed a bug that prevented viewing of cloned cloned fields.
= 0.1.4 =
Added support for cloning cloned fields. this allows you to create a chain link or multiple references to fields
= 0.1.0 alpha =
* Initial alpha release. (still heavily in development)

== Upgrade Notice ==

Simply overwrite the existing folder with the new one.


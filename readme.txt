=== DB Toolkit ===
Contributors: Desertsnowman
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=PA8U68HRBXTEU&lc=ZA&item_name=dbtoolkit%20development&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags: interface, database, tables, database tables, application development, app engine, database interface toolkit, DBT0309821
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 0.3.0.130

DB-Toolkit allows you to create content, content managers and viewers

== Description ==

DB-Toolkit allows you to build web applications within Wordpress. From manager interfaces to capture forms to content viewers or almost any type.
DB-Toolkit is not a simple plugin and has a steep learning curve, but the results are very rewarding.

There is a tutorial series on http://dbtoolkit.digilab.co.za to explain the fundamentals on using the system its a few versions old so the interfaces do look a little different.
Please join the support forum on http://dbtoolkit.digilab.co.za/forum/ and I'll do my best to answer any questions and help troubleshoot problems.

please note: this is still alpha.


Some Features:
*   Field-by-field data type handling makes data management very powerful and flexible.
*   Data exporting in PDF and CSV
*   Create API's to connect to your data. This allows you to build mobile apps that feed from your content.
*   Multi Interface Layouts using clusters
*   Build custom content managers, like galleries, contact lists, application forms, employee databases... and so on.
*   Import data from an XML or CSV source
*   Visually build forms that capture data to a database existing or not.

== Installation ==

1. Upload the plugin folder 'db-toolkit' to your `/wp-content/plugins/` folder
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Select DB Toolkit from the menu group

== Frequently Asked Questions ==

Q: Is there any Documentation?
A: Nope. I intended on DB-Toolkit to be a personal project to help my own development, so I didnt write any as i was building it. I have however started writing up some tutorials and am working on new documentation which will be available eventually.

Q: When will you have an RC release?
A: Well you can technically use it now, however some things are still a little iffy (like cloned linking links) But it should mostly work on up to 3rd level interfaces.


== Screenshots ==

1. Build Database Management Interfaces and Viewers.
2. Interface and Application Management Screen.
3. An Interface built in DB-Toolkit to manage data.
4. Interface Config Screen (Lots of Options!).
5. Database Table data represented in Chart Mode.
6. Data Importer Dialog.

== Changelog ==

= 0.3.0.120 =
* minor bug fixes.

= 0.3.0.129 =
* Fixed a bug in the ajax system that disallowed for calling functions from extentions.

= 0.3.0.126 =
* Corrected a bug in the View Processors that prevents a data endpoint.

= 0.3.0.125 =
* Removed the legacy chart setup panel in general settings.

= 0.3.0.124 =
* Added a Rebuild Apps Button on app manager. Use this if you have any interfaces not showing or have just done an upgrade.

= 0.3.0.118 =
* Fixed a bug that prevented the menu links from showing at times.

= 0.3.0.118 =
* Fixed a bug that prevented importing.

= 0.2.6.9 =
* Added a new form processor: Wordpress Login. It can take two fields and use them to login the user before saving the data to the table. (password field gets ******) for security.
* Fixed a bug that prevented Auto Values (IPAddress, UserID, TimeStamp) from running through their value processors.
* Fixed a bug that prevented the wysiwyg fieldtype from loading.

= 0.2.6.8 =
* yet more bug fixes. mainly on the form layout and view layout builder

= 0.2.6.7 =
* fixed a bug that stopped page redirect from working.

= 0.2.6.6 =
* Left some test code in last update - removed it- sorry

= 0.2.6.6 =
* Left some test code in last update - removed it- sorry

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


== Upgrade Notice ==

Simply overwrite the existing folder with the new one.


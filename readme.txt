=== DB Toolkit ===
Contributors: Desertsnowman
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=PA8U68HRBXTEU&lc=ZA&item_name=dbtoolkit%20development&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags: interface, database, tables, database tables, application development, app engine, database interface toolkit, DBT0309821
Requires at least: 3.1
Tested up to: 3.3
Stable tag: 0.3.1.006

DB-Toolkit enables you to build additional Content Management Structures and Database Applications right into your WordPress Site.

== Description ==

DB-Toolkit is a plugin that enables you to build additional Content Management Structures and Database Applications right into your website.

You can build Capture Forms, Reports, your own plugins, Image Galleries, Sliders, Databases, staff management, Hotel Booking systems... Any Data based application.

By defining the kinds of data you are wanting to work with, you can create an almost endless range of content, manageable from its own interfaces and screens. Controlled by both backend (administrators) and frontend (public users) interfaces and forms.

DB-Toolkit is not a simple plugin and has a steep learning curve, but the results are very rewarding.

There is a tutorial series on http://dbtoolkit.co.za to explain the fundamentals on using the system its a few versions old so the interfaces do look a little different.
Please join the support forum on http://dbtoolkit.co.za/forum/ and I'll do my best to answer any questions and help troubleshoot problems.


=Some Features=
* Field-by-field data type handling makes data management very powerful and flexible.
* Data exporting in PDF and CSV.
* Create API's to connect to your data. This allows you to build mobile apps that feed from your content.
* Multi Interface Layouts using clusters.
* Build custom content managers, like galleries, contact lists, application forms, employee databases... and so on.
* Import data from an XML or CSV source
* Visually build forms that capture data to a database existing or not.

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

= 0.3.1.006 =
* Fixed a bug that cause a header error when there are no widgets that contain interfaces.

= 0.3.1.005 =
* Added in the WordPress User Registration form processor. (beta)

= 0.3.1.004 =
* fixed a security problem that could allow a user to edit an entry belonging to someone else.
* Fixed a bug on edit forms that broke international characters.

= 0.3.1.003 =
* fixed a bug with filters not showing on some fields.

= 0.3.1.000 =
* fixed a bug with the close filter toggle dying off in conflict to another plugin.

= 0.3.1.000 =
* Major Update with the fieldtypes that solved a lot of internal problems.
* Internal Style Update to better suit WordPress 3.3
* Lots of bugfixes
* Made changes for something BIG!

= 0.3.0.148 =
* Fixed a bug with the custom shortcode not accepting additional arguments.

= 0.3.0.146 =
* Bug Fixes!

= 0.3.0.146 =
* Changed List template tab to "Templates".
* Moved "Use List Template" to within the list template tab".
* Set Field Templates to always on. So now they will wrap fields in both list view and template view".

= 0.3.0.145 =
* Fixed a bug that prevented dialogs in the wysiwyg from from receiving text inputs.
* Fixed a bug with the SELECT fieldType from working in checkbox mode.

= 0.3.0.143 =
* Fixed a clash with another plugin that made the filters button disappear when clicked.

= 0.3.0.142 =
* Fixed a bug in the .dbt export that didn't include filterlocks in the exported file.

= 0.3.0.141 =
* Added in interface Custom Shortcodes. You can now specify a custom shortcode to an interface
* Wrapper element definition addition to template editor.
* lots of Bug fixes
* Export fixes. Still acting up, but a little better.
* Added in interface Custom Shortcodes. You can now specify a custom shortcode to an interface
* Wrapper element definition addition to template editor.
* lots of Bug fixes
* Added the new Export to .dbt file. Its a clean format and works way better. Still can import from the older .itf
* Removed the export as Wordpress Plugin for a little while, found a bug and need to sort it out before to many people get upset with me :).

= 0.3.0.134 =
* Compatibility for WordPress 3.3 updates.
* Added export to WordPress plugin.
* Corrected the import so that Application Names don't get sanitized.
* Added an Install Application Button to the app builder screen


== Upgrade Notice ==

Simply overwrite the existing folder with the new one.


=== DB Toolkit ===
Contributors: Desertsnowman
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=PA8U68HRBXTEU&lc=ZA&item_name=dbtoolkit%20development&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHosted
Tags: interface, database, tables, database tables, application development, app engine
Requires at least: 2.9.2
Tested up to: 3.0.1
Stable tag: 0.1.8

Create interfaces (forms, reports, graphs etc.) from database tables already existing.

== Description ==

Adds interfaces to database tables on pages, posts, dashboard and custom menu groups within admin, to manage data.
This version is an alpha, proof of concept and there are a lot of bugs still present.
There is no documentation yet so you'll have to guess your way around.
I tried to make it as intuative as possable but due to the complex nature of it somethings may not be so easy to grasp.

Please dont use it in a production site just yet. It is under active development so changes will be frequent.

I'm running a tutorial series to explain the fundermentals on using the system and will be building documentation as i go.
Tutorials will be on up on http://dbtoolkit.digilab.co.za


Features include:

*   Visual forms and reports builder
*   Templates for output
*   Field Specific configuration

Features Currently in Development:

*   Inline Documentation
*   Dynamic Table Creation and Field Manipulation

== Installation ==

1. Upload the plugin folder 'db-toolkit' to your `/wp-content/plugins/` folder
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Select DB Toolkit from the menu group

== Frequently Asked Questions ==

Q: Is there any Dcumentation?
A: Not fully, but I am putting together a tutorial series and building the documentation as i go along. you can access these at http://dbtoolkit.digilab.co.za/

Q: When will you have an RC release?
A: When I am happy that all the major bugs are out. this may be as soon as December.

== Screenshots ==

1. List of already created interfaces and reports
2. report/manager interface created against table
3. Field setup on an interface
4. Easy form building for input/capture forms
5. modal dialog input forms

== Changelog ==

= 0.1.7c =
Fixed a small bug where field names with duplicate parts ('nameID' and 'nameTime' where 'name' is the duplicate) gets incorrectly wrapped.
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


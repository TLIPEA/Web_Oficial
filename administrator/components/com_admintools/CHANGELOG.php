<?php die() ?>
Admin Tools 2.6.2
================================================================================
! PHP File Change Scanner was broken due to missing method declarations
# [MEDIUM] Missing translation string PLG_SYSTEM_ATOOLSUPDATECHECK_MSG
# [MEDIUM] Blank page or 400 error message after updating from 2.6.0 or lower using Live Update

Admin Tools 2.6.1
================================================================================
+ Whitelist domains (useful to prevent blocking of Google Bot, MSN Bot etc)
+ You can specify which security exceptions reasons should not be logged or result in an email being sent
+ gh-12 Added the "Allow PHP Tags" option for XSSShield
+ gh-13 Options for Safe params in XSSShield
~ Now using the built-in Joomla! extensions updater instead of Live Update to deliver updates
~ gh-5 Updated notification plugins to store last timestamp inside the component table, instead of using Joomla params
~ CSRF protection for GeoIP update
~ Only show a reminder to update the GeoIP database if it's older than two weeks
~ Update notification emails do not include an automatic log in URL by default
~ AJAX ordering now shows the ordering in a textbox, to faciliate ordering without much frustration in Joomla! 3
# [LOW] Fixed search in redirections page
# [LOW] PHP Notice in pro.php when an attack is blocked and only under some specific circumstances

Admin Tools 2.6.0
================================================================================
+ HSTS Header support for .htaccess Maker (enhances the privacy of your users if you are using HTTPS for your entire site).
+ You can now use [IP] in the "Show this message to blocked IPs" setting to display the blocked IP to the user being blocked.
+ You can choose between FollowSymlinks and SymLinksIfOwnerMatch in .htaccess Maker
~ Now using the new MaxMind GeoLite2 Country database, via the optional "System - Akeeba GeoIP provider plugin" plugin, available as a separate download from our site.
# [LOW] There is no list limit box in the PHP File Change Scanner results page under Joomla! 3

Admin Tools 2.5.10
================================================================================
! The Joomla! Update notification plugin would send emails constantly due to wrong packaging. Sorry! It's our fault :(

Admin Tools 2.5.9
================================================================================
~ Showing .htaccess Maker to everyone. I underestimated the number of BROKEN servers which report fake information about the server software they are using.
~ Try very hard not to load GeoIP if another extension loads it before us (we can't guarantee that crappy code in other extensions will not cause a site crash; we can only make sure OUR code doesn't cause such a crash)
~ Improved back-end display on Joomla! 3
~ The PHP File Change Scanner is no longer identified as a threat by certain very sensitive security scanning services
# [HIGH] Access Denied messages not blocking requests on some Joomla! 3.1 sites
# [HIGH] Joomla! 3.2 pollutes the global configuration with various crap at runtime, causing the Change Database Prefix feature to brick your site. Basically, Joomla! is doing what it tells extension developers never to do...
# [MEDIUM] All automatic IP bans could be reset by accident before their expiration time
# [MEDIUM] An update email for Joomla! is sent continuously since Joomla! 2.5.16 / 3.2.0. Joomla! now reports the latest version as an available update even when it's already installed. We now check the version number to make sure update emails are NOT sent when you already have the latest version.
# [LOW] The scheme (e.g. HTTPS) of the redirection URL was not kept. Now it's kept as long as Keep URL Parameters is set to No.

Admin Tools 2.5.8
================================================================================
! An annoying –but harmless– fatal error was shown on upgrade from older versions
~ Allow .htaccess Maker on LiteSpeed (it claims to support a large portion of .htaccess commands)

Admin Tools 2.5.7
================================================================================
- Removed the Joomla! Update feature. The core Joomla! Update component is based on Admin Tools and makes no sense having this code in Admin Tools itself. (CORE & PRO)
+ Allow third party applications to log exceptions and ban IPs using the new plugin event onAdminToolsThirdpartyException (PRO)
+ PostgreSQL is now supported *BETA FEATURE* (CORE & PRO)
+ Microsoft SQL Server and Windows Azure SQL Database are now supported *BETA FEATURE* (CORE & PRO)
~ Rewritten Joomla! update notification by email plugin which uses the core Joomla! Update component (PRO)
~ Do not show .htaccess Maker on non-Apache web servers
# [MEDIUM] Viewing a modified file in a PHP File Change Scanner report when you have enabled diffs calculation in Joomla! 2.5 leads to an error (PRO)
# [LOW] The GeoIP helper functions would be loaded even when the GeoIP.dat file was not present (PRO)
# [LOW] Shorthand local IPv6 addresses (e.g. ::1) were not being recognised correctly
# [LOW] CLI CRON scripts not fully compatible with PHP CGI

Admin Tools 2.5.6
================================================================================
+ IPv6 support
+ Allow URL redirections to keep (default) or drop query parameters, useful for redirecting non-SEF URLs
~ Remove time penalty when blocking auto-banned IPs due to resource usage considerations
# [MEDIUM] Combining CSS and Javascript using the plugin delivery method doesn't work on Joomla! 3.1
# [LOW] The .htaccess Maker exception you'd add for files and folders with dashes in their names would not be effective
# [LOW] The .htaccess Maker would assume that Apache version is 2.0 when it's not reported. Many Apache 2.5 servers are configured like that. We now assume version 2.5 in this case.
# [LOW] Pre-installation script would run the wrong SQL files on upgrade (normally not causing a problem, but still it's not the intended behaviour)

Admin Tools 2.5.5
================================================================================
+ Option to hide security exception stats & graphs
+ Added "Robert button" (accept the mandatory information in the post-setup page without ticking each box)
~ Changed the control panel page's layout
# [HIGH] Joomla! 3.1.0 broke several aspects of the component

Admin Tools 2.5.4
================================================================================
~ Additional rules in PHP File Change Scanner
# [HIGH] Fatal error saving the permissions configuration
# [HIGH] 403 errors accessing the component when FOF 2.0.5 or later is installed
# [LOW] Even though the bad behavior integration was removed the interface was not updated

Admin Tools 2.5.3
================================================================================
+ Filters in the PHP File Scanner report page
- Removed Bad Behaviour integration
# [HIGH] Internal server error on ancient versions of PHP 5.3 with broken late static binding implementations
# [HIGH] Master Password not working

Admin Tools 2.5.2
================================================================================
# [HIGH] The Admin Tools plugin would delete the newest, not the oldest, log entries
# [HIGH] The component's menu item was removed if the installation couldn't proceed (too low PHP or Joomla! version)
# [HIGH] Joomla! doesn't run the database upgrade scripts when upgrading from a very old version or when the #__schemas entry is somehow missing
# [HIGH] After a failed installation, even if the subsequent installation is reported successful Joomla! does not install the database tables causing a broken installation
# [MEDIUM] The reason why the installation was aborted is not shown due to a Joomla! bug; worked around

Admin Tools 2.5.1
================================================================================
# [HIGH] Using Master Password would cause inability to access Admin Tools

Admin Tools 2.5.0
================================================================================
+ Notice about updates caching in Admin Tools' Joomla! Update page
- Removed obsolete XML-RPC server option from .htaccess Maker
~ .htaccess Maker now blocks access to the .xml, .txt and other files installed by Joomla! in your site's root
# [HIGH] Custom Admin Tools ACL permissions not taken into account, making it impossible to create custom user groups with narrow Admin Tools access scope
# [LOW] The "Change database collation" feature would always change table fields' collation to utf8_general_ci
# [LOW] Specifying Super Administrator email for update notifications wasn't working
# [LOW] Incompatibility with how some third part Joomla! 3.0.2 components handle JRequest
# [LOW] MySQL 5.6 compatibility
# [LOW] Misleading plugin name in the Joomla! update email confuses users wishing to disable that feature

Admin Tools 2.4.4
================================================================================
+ Allow you to specify only one Super Administrator to receive emails for Admin Tools and Joomla! updates
~ Improved SQLiShield protection
# [LOW] Banning an IP from the log page didn't work due to a typo

Admin Tools 2.4.3
================================================================================
+ You can now select the maximum number of security exception log entries to keep and auto-remove the rest (it's in the System - Admin Tools parameters)
~ Improved post-setup error messages if you have not selected both license & support checkboxes
~ Make sure we are no longer using Joomla!'s integrated extension update (it doesn't support stability and Joomla! version compatibility checks)
# [MEDIUM] PHP File Scanner configuration would get saved in a very wrong way
# [LOW] Admin Tools' MySQL-related features go away when you enable db query caching in sh404SEF
# [LOW] Email sent by the CLI version of PHP File Change Scanner always reads "Unknown site" (thanks Gabriel)
# [LOW] Email sent by the CLI version of PHP File Change Scanner always lists files as Added

Admin Tools 2.4.2
================================================================================
+ You can now select the minimum stability for update notification in the Post-installation Configuration page
+ The users have to accept the license and support policy before using the component
~ Using buttons for add/remove to blacklist and IP lookup in the WAF Security Exceptions Log page
~ Updated signatures for the PHP File Change Scanner
# [LOW] The CSS and JS combination wouldn't "see" some files
# [LOW] JClientFtp::getInstance requires an empty array, not null, as its third parameter
# [LOW] Compatibility with @securejoomla audit service
# [LOW] GeoBlocked IPs were still logged, despite change in version 2.4.1

Admin Tools 2.4.1
================================================================================
~ Do not log GeoBlocked IPs as security exceptions
# [HIGH] Joomla! versions in update notification email are reported wrong, leading to confusion (thank you Steve @ OSTraining for the bug report)
# [LOW] Unpredictable IP-related behaviour when a reverse proxy / load balancer causes the server to set multiple IPs in the server remote_addr variable. Very rare, but annoying.
# [LOW] Missing translation key for two-factor auth security exception
# [LOW] Multidimensional upload arrays sometimes cause a problem with UploadShield
# [LOW] Sometimes the interface renders strangely (e.g. when a plugin sets format="")

Admin Tools 2.4.0
================================================================================
~ On popular request, we now allow you to upgrade from Joomla! 2.5 to 3.0. Please read and bear in mind the big, fat warning before doing so.
# [HIGH] J3 The Admin Tools Update Check plugin would cause a fatal error
# [LOW] J3 URL Redirection feature should use AJAX reordering, not the old method
# [MEDIUM] The minimum stability for Live Update was ignored

Admin Tools 2.4.rc1 (RELEASE CANDIDATE)
================================================================================
+ Compatibility with Joomla! 3.0.0 Stable
+ Two-factor authentication for back-end login, using Google Authenticator and compatible TOTP clients (RFC6238-compliant, except for the last paragraph in Section 4.2)
+ Now using a Quick Icon plugin to show the Joomla! update status instead of the legacy module method
- X-Powered-By feature removed because the Joomla! API no longer allows us to replace HTTP headers.
~ Improved compatibility with reverse proxies when using IP-based white/black-listing and blocking
~ Some fields in WAF configuration were too short
~ Updated .htaccess Maker defaults for com_joomlaupdate and Joomla! 3.0 compatibility. Please regenerate your .htaccess files if you're using .htaccess Maker.
# [LOW] X-Powered-By custom header was being added, not replaced

Admin Tools 2.3.2
================================================================================
~ Improve compatibility with other software using cssmin
# [LOW] Unnecessary parameters were being saved by the Configure WAF page
# [MEDIUM] The template= option in WAF wold always allow installed templates to pass through.
# [HIGH] "Forbid front-end super administrator login" did not work properly since Joomla! 2.5.5 due to a security change in Joomla!
# [HIGH] The .htaccess Maker would detect the wrong version of Apache, creating unoptimised .htaccess files in some cases

Admin Tools 2.3.1
================================================================================
# [HIGH] (regression) Auto-ban of IPs would not work any more
# [LOW] Block messages would not show for back-end access unless the administrator secret parameter was being used

Admin Tools 2.3.0
================================================================================
! THIS VERSION IS ONLY COMPATIBLE WITH JOOMLA! 2.5.1 AND LATER
+ Option to email results of PHP file scanner
~ Default sorting of log entries by date descending (newest entry first)
~ Default sorting of scan results by date descending (newest entry first)
~ Updated Bad Behaviour to version 2.2.7 stable
~ Whitespace cleanup of safe IP lists to avoid CIDR blocks, open IP ranges and netmask notation not working when adding spaces between multiple entries
# [LOW] When marking safe multiple items, the Scan ID is not preserved between requests
# [LOW] The "Calculate diffs when scanning" option was not being saved
# [LOW] Clicking on Changelog for a second time would result in a Javascript error

Admin Tools 2.2.10
================================================================================
# [HIGH] The IP auto-ban did not work as expected
# [HIGH] The generated .htaccess would allow access to some files which should not be web-accessible. Kudos to Paul Franklin, JoomlaFCK Editor team!

Admin Tools 2.2.9
================================================================================
~ Removed the "Remove all instances of Joomla! from the output" feature as it offers no protection and causes a lot of problems
# [LOW] Setting the expiration time for text/html could cause problems in the administration section of Joomla! 2.5.x
# [LOW] Download ID banner would show up in the Core release

Admin Tools 2.2.8
================================================================================
# [HIGH] Blank page on Joomla! 1.5 when the scheduling options were being used

Admin Tools 2.2.7
================================================================================
# [MEDIUM] #280 Rochen shared servers didn't allow you to save WAF configuration due to bad default mod_security settings. Workaround applied (saving the IP lookup service's scheme separately).
# [MEDIUM] Scheduled tasks in the Admin Tools plugin would fire on each page load
# [LOW] Security Exceptions Log would throw an unrecoverable error if you tried using an invalid date in the filter fields (thank you Mona)
# [LOW] Error thrown in the component's control panel when Debug System is enabled
# [LOW] MySQL error in the cpanel view, visible only when Debug Site is enabled

Admin Tools 2.2.6
================================================================================
+ You can change the IP lookup service used by Admin Tools in the Configure WAF page
~ Adding ACL check to the Joomla! update icon module
~ Making sure you can't even run Admin Tools on unsupported versions of Joomla! or PHP
~ Changed timestamp storage for automation tasks so that they don't interfere with WAF settings storage
# The tmp directory under your site's root was always being used to output the scan log file instead of the temp-directory defined in your Global Configuration.
# URL Redirections were using HTTP 303 headers; fixed to use HTTP 301
# The RewriteBase generated by .htaccess Maker for sites in subdirectories may contain double leading slash instead of a single leading slash
# The minimum stability preference wouldn't stick

Admin Tools 2.2.5
================================================================================
# Performance issues in the pro.php plugin file

Admin Tools 2.2.4
================================================================================
+ Making sure you won't forget to enter your Download ID
~ Automatically update the XML update stream in Joomla! 2.5 to make use of the Download ID in the Pro release
# The Change Super Administrator ID function was broken
# Could not filter log entries by GeoBlocking and HTTP:BL

Admin Tools 2.2.3
================================================================================
+ Option to not send out the password in the email notifications of failed logins
+ Making very sure that you will not change your Super Admin ID unless you know very well what you're doing
~ Joomla! 2.5.4 broke the Generator meta tag cloaking
# Wrong path to pro.php mentioned in the IP autoblock email
# The offline.html message under Joomla! 2.5 would be a translation string, not the real text (thanks, akeebafan)
# Logging out a frontend user from the backend would cause the administrator to get logged out when using the Administrator secret URL parameter
# The Joomla! update notification email would display Array instead of the currenlty installed and available Joomla! version numbers
# Putting a site back on-line after using the Emergency Off-Line Mode leaves the the EOMBAK comment on the top of the .htaccess file
# Failed administrator login email didn't work under Joomla! 2.5

Admin Tools 2.2.2
================================================================================
! PHP File Change Scanner is broken (forgot to commit new file)

Admin Tools 2.2.1
================================================================================
! Old update SQL files running twice during Joomla! 1.7 and 2.5 updates
+ The username/password of a failed login is now logged
+ Added permission settings 0660, 0770 and 0757 per popular request (disclaimer: we still consider them VERY BAD permissions choices, but at the end of the day it's your sites and your responsibility)
~ If FOF is not copied on installation the system plugin doesn't execute instead of causing a fatal error and bringing the site down
# Running a scan would remove the obsolete backup records in Akeeba Backup
# JFolder wasn't loaded by FOF, leading to crashes under some rare circumstances
# Some editor pages wouldn't open in Joomla! 1.5
# Disable front-end Super Administrator login would also block Administrator in Joomla! 1.5
# Skipping CSS and JS from being combined didn't work when using a browser on Windows to set up the skips
# The skipped CSS and JS files would appear twice on the page
# Reinstall button not showing when there is no Joomla! update available

Admin Tools 2.2.0
================================================================================
+ You can now update Admin Tools Professional using the Joomla! extensions update (you still have to supply your Download ID to the component)
+ Show 403 messages upon a security exception using customisable HTML templates
+ The Joomla! update now supports updating to the current, the next STS or the next LTS release
# Joomla! 2.5: You can not delete scans
# Joomla! 1.5: Fatal error in the control panel
# File scanner: the Status ordering didn't work (thank you @brianteeman)
# File scanner: would not delete multiple scans (thank you @brianteeman)
# Joomla! 1.7/2.5: We have to always allow template= definitions for com_mailto URLs
# The update notification plugin could fire repeatedly if it wasn't able to update its last run timestamp
# The Joomla! Update icon would not show up on the admin control panel in Joomla! 2.5 unless you changed its position to "icon"
# Notices thrown by pro.php
# Joomla! 2.5: The File Scanner CLI script would crash
# #263 File change scanner doesn't work in IE 7/8/9
# Master password view: clicking on None had no effect under some circumstances

Admin Tools 2.2.a3
================================================================================
+ Admin Tools now picks up the next release and offers to update to it (e.g. 1.7 to 2.5). It won't offer to update to an alpha, beta or RC, though.
+ #219 Upgrade minimum stability level
+ #200 IP Ranges in "Never block these IPs" field
+ You can now force the language Admin Tools security exceptions emails will be sent in
+ You can now force the language Admin Tools Joomla! update emails will be sent in
+ You can now force the language Admin Tools update emails will be sent in
+ #9 Combine and compress JavaScript files
+ #8 Combine, optimise and compress CSS files
~ The tp=1 option no longer shows on Joomla! 1.6+ as it doesn't do anything (that Joomla! feature is no longer present)
- X-Content-Encoded-By was removed as it can no longer work. Joomla! sends the header after any third party has the chance to modify it :(
~ The default sort order for the scan report page is now threat level, descending
~ #222 Improve the new version notification email message
~ Removed GeoIP.dat from the download package. Replaced with instructions to download it.
# Long file paths would cause the print report to be cut off in Google Chrome
# Printing a scan report was impossible in Joomla! 1.5
# The update email comes once every 3 hours, not once every 24 hours as expected
# Clicking on anything after exporting to CSV causes the CSV view to reload
# Publish/unpublish buttons in Anti-spam Bad Words view (thanks Stephen!)
# Publish/unpublish buttons in Security Exceptions Log view (thanks Stephen!)
# Delete and edit buttons in Auto IP Blocking Administration (thanks Stephen!)
# Double Back button in URL Redirections view (thanks Stephen!)
# Force reload of updates didn't work
# Automatic user deletion did not work under Joomla! 1.7/2.5
# The Joomla! update notification icon did not show on Joomla! 2.5.0 due to differences in Joomla's HTML markup
# Could not publish/unpublish redirections from the list view
# You could not change the ordering in Joomla! 2.5.0 due to undocumented JavaScript changes in the Joomla! core
# The Core release would show the Scan icon, causing a 500 error (this feature only exists in Admin Tools Professional)

Admin Tools 2.2.a2
================================================================================
- Removed non-English languages from the main package
~ Do not colourise files or diffs over 60kB big (should keep GeSHi from crashing)
# Some unnecessary files were being copied to the Core release
# Trying to access the WAF control panel would throw an error in Joomla! 1.5
# Installer tried to copy the CLI file scanner script on Joomla! 1.5, causing an error to be printed on installation
# The log model was removed on installation, making the Security Exceptions view inaccessible
# The link to Scheduling (via plugin) was broken
# You could not delete automatically banned IPs
# Publish/unpublish buttons were displayed in IP whitelist/blacklist and autoban IP administration pages, but they were never intended to be included there (there is no such feature)
# Could not ban/unban IPs from the security exceptions log view
# File data would still be saved in the database even when you had produce diffs to No (default setting) leading to database bloat (thanks @brianteeman)
# Ban/unban an IP would not work

Admin Tools 2.2.a1
================================================================================
~ Reimplemented the component using our AkeebaFOF framework extension
~ More sensible defaults for "List of allowed tmpl= keywords" in WAF
+ PHP file scanner

Admin Tools 2.1.14
================================================================================
~ Layout improvements under Joomla! 1.7
~ Small improvements to DFIShield, making sure that the use of numerous ../ instances won't throw it off
~ The Post-Installation wizard is no longer shown in the Core release, as it's not relevant
~ CSS improvements to the Post-Installation wizard
~ Fix Permissions will automatically skip over the contents of the cache (front- and back-end), tmp and log directories
# Joomla! red error text displayed when updating Joomla! w/out Akeeba Backup installed (thanks, Fotis, for the heads up)
# The Advanced mode of CSRFShield would also run the basic mode (referrer filtering), causing troubles on many sites
# Notices thrown in the post-installation wizard (thank you @ot2sen!)
# #166: Array upload fields break UploadShield
# Under Joomla! 1.7, with "Disable editing back-end users" enabled, you could edit a back-end user and remove him from the Manager/Administrator/Super Administrator group
# Use of wrong constant could throw a warning message in the post-installation wizard
# Joomla! 1.7 session storage could overflow when using the Fix Permissions feature, locking you out of your site until you cleared your browser's cache and cookies

Admin Tools 2.1.13
================================================================================
! A few sites would never get past the post-setup view

Admin Tools 2.1.12
================================================================================
+ Admin Tools update notification emails (Admin Tools Professional only)
+ Joomla! update notification emails (Admin Tools Professional only)
+ Post-installation wizard, just like the one of Akeeba Backup
+ Adding force-reload button to the Joomla! update view, in case something's stuck
~ Removing default .htaccess Maker exceptions (they didn't make much sense and were only meant as examples)
~ Adapting to Joomla's new stupid policy of providing upgrade packages only from the .0 and the immediately previous release (e.g. 1.5.0 to 1.5.25 and 1.5.24 to 1.5.25, but NOT 1.5.23 to 1.5.25)
# Downloading a Joomla! update failed on hosts were the tmp directory is unwritable
# Non-standard administrator templates could have a problem with the Joomla! update icon module hiding everything on the page

Admin Tools 2.1.11
================================================================================
+ #148 Failed logins can optionally count as security exceptions, allowing you to use the automatic IP blocking after a number of failed login attempts
+ Adding an Apply (Joomla! 1.5) / Save (Joomla! 1.7) button in the WAF Configuration page
+ Added back the WAF Exceptions feature on popular request
+ IP lookup link in security exception email
+ More options for the inactive user removal feature
~ Making all back-end links absolute instead of relative
# DFIShield would block JCE's image and file managers
# Access from blacklisted IPs would send out an email and trigger a log message. That's pointless.
# The CHANGELOG div caused a horizontal scrollbar to display
# The Yes/No labels in Configure WAF are always shown in English in Joomla! 1.6 and later
# Notices thrown by pro.php
# Only Super Administrators could access Admin Tools on Joomla! 1.6 and later
# Javascript error thrown by the admin module if the Control Panel admin module is disabled
# Joomla! 1.7 or later changed the way the integrated extension update system works, rendering our update feeds invalid (Note: Live Update was still functional, as it's a standalone update system)
! .htaccess Maker would always allow PHP files inside the templates directory to be web-accessible.

Admin Tools 2.1.10
================================================================================
- Rolling back WAF Exceptions; the feature does not work with SEF enabled and the workaround causes routing issues
# Undefined variable in UploadShield

Admin Tools 2.1.9
================================================================================
# Changelog link not working on Firefox
! The "System - Admin Tools" plugin could interfere with routing

Admin Tools 2.1.8
================================================================================
# Joomla! 1.7.0 to 1.7.1 update failed due to a slightly different package format than what was expected.

Admin Tools 2.1.7
================================================================================
+ Ability to partially override the template= switch for multi-template sites
+ Clean-up inactive users (those who registered, but never logged in)
+ The Joomla! update icon now integrates itself into the Quick Icons module in Joomla! 1.6/1.7
+ IPs in the Administrator Whitelist or the "Never block these IPs" lists will not trigger any security exceptions and not be logged at all
+ Display the CHANGELOG inside the component's Control Panel page
# If your server runs the GeoIP PECL module, Admin Tools causes a PHP Fatal Error
# Clean Temporary Directory feature throws notices and warnings when your tmp directory is already empty
# Yes/No values not translated in Master Password view
# Using Apply (Joomla! 1.5) or Save (Joomla! 1.6+) on a new record, didn't display the just saved record, essentialy acting as Save & New
# The Joomal! update status check icon module was installed hidden in Joomla! 1.6/1.7
# #57 WAF Exceptions not active when SEF is enabled
# Database schema updates were not applied from Joomla! 1.7.0 to 1.7.1 updates
# Undefined variables in Permissions Configuration page cause it to throw notices (kudos to user doorknob on our forum for the fix!)

Admin Tools 2.1.6
================================================================================
~ Update of Akeeba Standard Installion Library for Joomla! 1.6/1.7
+ Added permissions setting 0640 to the list
+ You can now allow access to media files inside cache, includes, language, logs and tmp directories without allowing full access to those directories
# Expired auto-banned IP addresses were not cleaned up
# Minimum access level not honoured under Joomla! 1.5
# Joomla! 1.5 ACL feature: would not list Managers
# Joomla! 1.5: If your access level was lower than the minimum, accessing Admin Tools caused an infinite redirection loop

Admin Tools 2.1.5
================================================================================
+ Ability to upgrade from Joomla! 1.6.5+ straight to Joomla! 1.7, including the necessary database changes and removal of obsolete files
~ Project Honeypot HTTP:BL integration is now standalone and does not require the entire Bad Behaviour integration to be activated
~ Watered down Bad Behaviour itnegration to work around known issues with modern browsers
# Fatal error when trying to access a view restricted by the ACL instead of a redirection taking place
# CSRFShield's advanced method not working
# Workaround for Joomla! 1.6+ bug resulting in "Can not build admin menus" and "DB function reports no error" messages when trying to install the component after a failed installation/update
# Site crash if somehow the component is uninstalled but the plugin is not
# Site crash if somehow the component is uninstalled but the module is not
# Wrong loading order, wouldn't load the JSON workaround class on PHP 5.1 hosts

Admin Tools 2.1.4
================================================================================
! Internal Server Error 500 in the back-end due to class file not being properly loaded

Admin Tools 2.1.3
================================================================================
+ IP lookup link in Exceptions Log and Auto IP Ban pages
# The Joomla! Update feature got confused when running inside a Joomla! 1.7 beta release
# File execution order would always be applied, no matter what the user selected
# Must not allow Admin Tools to upgrade from 1.6 to 1.7 due to schema changes

Admin Tools 2.1.2
================================================================================
# PHP notice thrown when logging a security exception
# The tmpl whitelist in WAF was not being taken into account
# Resetting the IP filter in auto-blocked IP administrator page would result in no records being displayed
# The URL Redirection management page had two forms named adminForm, creating Javascript issues
# Automatic IP block email body would be an untranslated key (ATOOLS_LBL_WAF_AUTOIPBLOCKEMAIL_BODY)
# Enabling the scheduling options in Admin Tools plugin would result in a PHP fatal error
# Minor layout issue in GeoBlocking continents list
# Clicking on the scheduling button in the control panel resulted in an error page on Joomla! 1.6/1.7
# Against common sense, on some servers using JModel outside of a controller *does not* load the respective model and returns false. Ugh!

Admin Tools 2.1.1
================================================================================
- Removed "Anti-leech protection" from .htaccess Maker, as modern browsers stopped sending absolute URIs in the HTTP Referer field
# "cpanelModelCpanel not found" message on Joomla! 1.6's and JCE 1.5.7.x's administrator page
# Sometimes Joomla! wouldn't load the JModel class automatically, leading to a PHP fatal error
# Joomla! 1.6 doesn't run the SQL files on update; ugly but working workaround applied
# Joomla! 1.7-Alpha1 would be proposed instead of Joomla! 1.6.4 as a Joomla! core update

Admin Tools 2.1
================================================================================

+ Adding file permissions mode 0440 to the list
+ Local copy of cacerts.pem allows Live Update and other download requests to work with HTTPS URLs on Windows, CentOS and other servers without a system copy of cacert.pem
+ Do not auto-ban IPs in a configurable safe list or the administrator white list
+ #33 Administration of auto-banned IPs
+ #12 Improved email notifications for Super Administrator log-in
+ #11 Email after automatic IP block
+ Bad Behavior reason added to the Security Exceptions Log and the exception email sent to site administrators
+ #101 Allow direct update from 1.6 to 1.7
+ #32 Allow customisation of Admin Tools' 403 message
~ .htaccess Maker: Improved on-the-fly GZip compression rules compatible with more (and newer!) Apache versions
# The referer filtering would block incoming requests from Google AdWords, PayPal etc when they were requesting dynamic pages (generated on the fly by Joomla!) with a .html suffix.
# Leftover tables on uninstallation
# Fatal error about missing JComponentHelper under some circumstances
# #83 .htaccess Maker settings lost when Joomla! component parameter storage overflows
# Opening the plugin editor through the Control Panel button didn't work in Joomla! 1.6 and above
# You would get a warning that the Admin Tools plugin does not exist on Joomla! 1.6 or 1.7
# Loading the GeoIP include file would cause a problem on systems where the GeoIP PECL extension was already loaded
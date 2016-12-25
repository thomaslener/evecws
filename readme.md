# EVE Corporation Wallet Script v0.1
for use with "EVE Online" API by CCP Games.

EVE CWS stores selected Corporation Wallet Journal Data in a MySQL database.
This script is intended to be regularly executed using cronjobs.

EVE CWS makes use of the "old" EVE XML API,
because it's the only way to gather Corp Wallet Data at the time of writing.

Note: this is a very simple, very basic script by a greenhorn, please do not
compare it with the professional scripts out there.
Nevertheless, every second of coding and learning was precious.

# Setup Instructions

[1] Save all this files in the same - non public - folder on your server.

- evecws.php (main script to be executed with cron)
- evecws_daily.php (optional, for daily reports)
- curl.php (required by evecws.php)
- config.ini (the place to save your credentials and choose wallet division)
- install.sql (execute in your database to create tables)

[2] Create tables in your database using install.sql.

[3] Adjust config.ini with database credentials
    and Corp API Key (must have Wallet permissions).
    
[4] Test if everything works as expected by manually executing evecws.php and evecws_daily.php by either typing "php evecws.php" in the     shell or navigating your webbrowser to evecws.php. As soon as the script tells you the data is stored in the database, have a look       at it to check entries. 

[5] Now set up a cronjob with desired frequency to execute 'evecws.php'
    as well as 'evecws_daily.php" for daily reports.

If you get a curl error, please note that PHP open_basedir must be "off" / not set in your php.ini.

# Selecting Transaction Types

Modify evecws.php, row 51

Example:
Standard setup is Brokers Fee, Market Transaction,
Planetary Export Tax and Facility Tax:
$referencetypes = array(46, 2, 97, 56);

For further data types please refer to:
http://eveonline-third-party-documentation.readthedocs.io/en/latest/xmlapi/constants.html#reference-type

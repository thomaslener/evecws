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

- evecws.php (main script to be executed with cron)
- curl.php (required by evecws.php)
- config.ini (the place to save your credentials and choose wallet division)
- install.sql (execute in your database to create table)

[1] Save all of this files in the same - non public - folder on your server.

[2] Create a table in your database using install.sql.

[3] Adjust config.ini with database credentials
    and Corp API Key (must have Wallet permissions).

[4] Set up a cronjob with desired frequency to execute 'evecws.php'

[5] PHP open_basedir must be "off", else curl error occurs.

# Selecting Transaction Types

Modify evecws.php, row 51

Example:
Standard setup is Brokers Fee, Market Transaction,
Planetary Export Tax and Facility Tax:
$referencetypes = array(46, 2, 97, 56);

For further data types please refer to:
http://eveonline-third-party-documentation.readthedocs.io/en/latest/xmlapi/constants.html#reference-type

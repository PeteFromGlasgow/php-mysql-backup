php-mysql-backup
================

Generates gzipped SQL files from a manifest.

## How to Use ##
1. Update the manifest file to include details of your databases.
2. Run the script and watch progress.


## The Manifest File ##
The simplest manifest file possible looks like this:
	[
		{
			"user": "root",
			"databases":
				{
					"std":{}
				} 
		}
	]

The above manifest file would connect to localhost as root with a blank password and attempt to backup the database "std". A more complicated example file is included which shows all the available options in the current version.

## Output ##
By default it will copy files into an sql directory at the same level as the script is run with the name format <DATABASE>.sql.gz.

## Disclaimer ##

This Script is not intended to be a backup solution, in fact it was written as a way to get data from a production database into a testing environment and as such no warranty or support is provided.

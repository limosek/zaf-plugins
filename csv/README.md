# CSV support for Zaf

This plugin tries to automate some basic scenarios using CSV as input files. If you have some data in CSV which ypu want to discover and analyze in Zabbix, this is right place to do so.
```
Plugin 'csv' version 0.1:
 CSV file reader and parser for Zabbix
 It supports reading CSV and getting its data as Zabbix items
 Even more it supports discovery based on CSV

Maintainer: Lukas Macura <lukas@macura.cz>
Url: https://raw.githubusercontent.com/limosek/zaf-plugins/master/csv

Defined items: csv.discovery.fields[] csv.discovery.rows[]

```

## How to use

**Note:** If you need more info about installing zaf: https://github.com/limosek/zaf

**Note**: You can find more examples on [my site](https://macura.cz/search/node?keys=zaf)

Simply instal zaf and after, install csv plugin
```
zaf install csv
```

### Items ###

```
Item csv.discovery.rows

Parameters:
	csv '' ''
	columns '1-100' ''
	rows '1-1000' ''
	header 1 ''
	delimiter ',' ''

```
With given parameters, plugin will return JSON object suitable for autodiscovery. For example, to discover contents of /etc/passwd:
* You can enter range of columns in format 1-3, it means column numbers from 1 to 3.
* Even more, there can be only one number. 
* And you can enter more ranged divided by '_'
* Same for rows
* This example: columns 1,2,3 and rows 5-6, file has no header and fields are separated by ':'
```
zaf run csv.discovery.rows[/etc/passwd,1-2_3,5-6,0,:]
{
   "data" : [
      {
         "{#COLUMN0}" : "FIELD0",
         "{#VALUE0}" : "root",
         "{#VALUE1}" : "x",
         "{#ROW}" : "1",
         "{#COLUMN2}" : "FIELD2",
         "{#COLUMN1}" : "FIELD1",
         "{#VALUE2}" : "0"
      },
      {
         "{#VALUE1}" : "x",
         "{#COLUMN2}" : "FIELD2",
         "{#ROW}" : "2",
         "{#COLUMN1}" : "FIELD1",
         "{#VALUE2}" : "1",
         "{#VALUE0}" : "daemon",
         "{#COLUMN0}" : "FIELD0"
      }
   ]
}
```

```
Item csv.discovery.fields

Parameters:
	csv '' ''
	columns '1-100' ''
	delimiter ',' ''
```
To discovery columns from CSV (first line of it).
Csv:
Name|Surname|Birth
Jon|Doe|2030

```
{
   "data" : [
      {
         "{#NAME}" : "Name",
         "{#COLUMN}" : "0"
      },
      {
         "{#NAME}" : "Surname",
         "{#COLUMN}" : "1"
      },
      {
         "{#NAME}" : "Birth"
         "{#COLUMN}" : "2",
      }
   ]
}
```

### Subcommands ###
Subcommands can be entered by 'zaf csv'.

To send entire CSV file (or its part) by zabbix sender:

```
zaf csv send
Missing arguments!
send file.csv delim mode item1=field1 [item2=field2] ...
mode is stdout or send,
itemx is key for item to send,
fieldx is data to send send,
In itemx and fieldx are replaced this macros:
{COLUMN:x} is replaced by value of column x
{column:x} is replaced by lowercased value of column x
x can be column index (x starts with zero) or header name.
CSV must include header line.

```

To register hosts to Zabbix server based on CSV:
```
zaf csv register
Missing arguments!
register file.csv delim host metadata
host and metadata are strings where:
{COLUMN:x} is replaced by value of column x
{column:x} is replaced by lowercased value of column x
x can be column index (x starts with zero) or header name.
CSV must include header line.
```

### Example ###

There is CSV file with this columns:
Host|CPU|Vulns|Services
coffee-Maker|arm|lotof|hot coffee
usb-Gun|mips|none|calm thunder

If you want to add all of hosts to Zabbix, simple use autodiscovery action based on metadata content. For example, all autodiscovered hosts with metadata ZAF will create host.
Suppose you want to prefix each hostname by 'test_' and you want to lowercase it.
```
zaf csv register 'hosts.csv' ',' 'test_{column:Host}' ZAF
```
This command will autoregister two hosts test_cofee-maker and test_usb-gun.

Next you want to fill data. You created template with right items as zabbix trapper items.
```
zaf csv send 'hosts.csv' ',' 'stdout' {COLUMN:Host} 'vulns={column:Vulns}' 'services={column:Services}'
```
This will show what to send by zabbix_sender. To actualy send it, you can use 
```
zaf csv send 'hosts.csv' ',' 'send' test_{COLUMN:Host} 'vulns={column:Vulns}' 'services={column:Services}'
```



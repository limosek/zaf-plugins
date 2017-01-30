# Booked (phpscheduleit) plugin for Zaf

Booked is opensource reservation system using PHP. More info can be found at http://www.bookedscheduler.com/.
This Zaf plugin supports fetching informations from this system and find if there is some reservation in given time range.
This is usefull for scripting and alerting. 

## How to use

**Note:** If you need more info about installing zaf: https://github.com/limosek/zaf

**Note**: You can find more examples on [my site](https://macura.cz/search/node?keys=zaf)

First, create account in Booked with required access to system and enable API. Next, simply install 
```
zaf install booked
```

It will ask you for url to booked API, username and password. 
After it, import template into Zabbix server: https://raw.githubusercontent.com/limosek/zaf-plugins/master/booked/template.xml 

## Supported items

Items supported now.

### booked.is_first

This item will return 0 or 1 depending on fact, if reservation at given time is first at given timerange.

To test, if reservation after 1 hour is first at this day:
```
booked.is_first[+1hour,day]
```

### booked.num_reservations

This item will return number of reservations in given timerange.

To get number of reservations tomorow:
```
booked.num_reservations[00:00+1day,00:00+2day]
```



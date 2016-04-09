# Plugin fsx

This plugin can autodiscover filesystem objects. Please note that this report can be very time consuming. This is only proof of concept.
Next to this, zabbix user must have permissions to that directories.
General fsx.discovery item has this options:
```
fsx.discovery[/dir,mask,maxdepth,type]
```
You must use % instead od * to get globing for mask. 

## Example1 
This example will discover directories under /var/* (one level) and than it will report their usage.
```
discovery: fsx.discovery[/var,%,1,d]
discovered: 	fsx.pathinfo_du[/var/mail], fsx.pathinfo_du[/var/spool], fsx.pathinfo_du[/var/log], ...
```

## Example2
Find all DEADJOE files in /etc and all subdirs
discovery: fsx.discovery[/etc,DEADJOE,,f]
discovered: 	fsx.pathinfo_du[/etc/DEADJOE], fsx.pathinfo_du[/etc/postfix/DEADJOE], ...







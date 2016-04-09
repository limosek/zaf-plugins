# zaf-plugins
Zabbix Agent Framework plugins repository. It contains working set of default plugins. Feel free to contribute. Or write your public and maintain control file on your site.
Look into plugin directory to see its possibilities and usage.

## fail2ban
Plugin which can autodiscover fail2ban jails and than reports number of banned IPs per jail.

## fsx
Extended filesystem functions. This plugin can autodiscover files or directories somwhere in filesystem. Next to this, it can report disk usage and number of items in that object.

## openssh
Plugin to discover openssh options and their values. Good for security audits. Each option will be discovered and than you can make triggers to check right settings like cleartext authentication or protocol version.

## psx
Extended process list. Plugin will discover runing procesess and userids. Than it reports usage by process and user.

## zaf
Default plugin for zaf. It can autodiscover installed plugins and get their versions.





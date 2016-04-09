# Zaf plugin for detailed process info
This plugin is used to monitor process activity on linux based systems. It supports autodiscovery of processes and next to this it will show CPU and memory usage of each process.
Please note that this plugin is very simple plugin for domonstrating possibilities. It cannot catch all processes because it discovers processes in regular intervals and if process will start and end between this time, it will not be autodiscovered.

## Usage

```

psx.discovery                                 [t|{
 "data":[
 {
  "{#PSNAME}":"daemon" ,
  "{#PSUSER}":"atd" 
 },
 {
  "{#PSNAME}":"hosting" ,
  "{#PSUSER}":"php5-fpm" 
 },
 {
  "{#PSNAME}":"krtek" ,
  "{#PSUSER}":"php5-fpm" 
 },
 {
  "{#PSNAME}":"lipka" ,
  "{#PSUSER}":"php5-fpm" 
 },
 {
  "{#PSNAME}":"macura" ,
  "{#PSUSER}":"php5-fpm" 
 },
 {
  "{#PSNAME}":"memcache" ,
  "{#PSUSER}":"memcached" 
 },
 {
  "{#PSNAME}":"message+" ,
  "{#PSUSER}":"dbus-daemon" 
 },
 {
  "{#PSNAME}":"mysql" ,
  "{#PSUSER}":"mysqld" 
 },
 {
  "{#PSNAME}":"odeli" ,
  "{#PSUSER}":"php5-fpm" 
 },
 {
  "{#PSNAME}":"postfix" ,
  "{#PSUSER}":"qmgr" 
 },
 {
  "{#PSNAME}":"postfix" ,
  "{#PSUSER}":"tlsmgr" 
 },
 {
  "{#PSNAME}":"rdnssd" ,
  "{#PSUSER}":"rdnssd" 
 },
 {
  "{#PSNAME}":"root" ,
  "{#PSUSER}":"acpid" 
 }
 ]
}

```


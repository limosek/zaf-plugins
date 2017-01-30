# Wireless device support for Zaf

This plugin can be used for autodicsovery and query of wireless devices and neighbours.

```
Plugin 'iwx' version 0.5:
 Plugin for wifi mac80211 informations and discovery

Url: https://raw.githubusercontent.com/limosek/zaf-plugins/master/iwx

Defined items: iwx.if_discovery iwx.phy_discovery iwx.channels_discovery[] iwx.clients_discovery[] iwx.neigh_discovery[] iwx.client_signal[] iwx.client_rxrate[] iwx.client_txrate[] iwx.neigh_ssid[] iwx.neigh_signal[] iwx.neigh_channel[] iwx.channel_noise[] iwx.channel_activetime[] iwx.channel_busytime[] iwx.channel_txtime[] iwx.channel_rxtime[]
Test items: iwx.channels_discovery[wlan0] iwx.clients_discovery[wlan0] iwx.neigh_discovery[wlan0]

```

## How to use

**Note:** If you need more info about installing zaf: https://github.com/limosek/zaf

**Note**: You can find more examples on [my site](https://macura.cz/search/node?keys=zaf)

Simply instal zaf and after, install csv plugin
```
zaf install iwx
```

### Items ###

```
Item iwx.if_discovery

Return: json

```

```
Item iwx.phy_discovery

Return: json
```

```
Item iwx.channels_discovery

Cache: 600 

Parameters:
	dev	wlan0 ''

Return: json

Testparameters: wlan0
```



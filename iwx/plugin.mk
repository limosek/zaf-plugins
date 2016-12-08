
NAME=iwx
VERSION=0.5
URL=https://raw.githubusercontent.com/limosek/zaf-plugins/master/iwx
WEB=https://github.com/limosek/zaf-plugins
DEPENDS_OPKG=busybox jshn
DEPENDS_DPKG=dash
DEPENDS_RPM=dash
SUDO=iw
INSTALL_BIN=functions.sh

ITEMS=if_discovery phy_discovery

define iwx/Description
 Plugin for wifi mac80211 informations and discovery
endef

define iwx/Item/if_discovery/Description
 Discovery of wireless interfaces
endef

define iwx/Item/if_discovery/Cmd
 wifi_if_discovery
endef

define iwx/Item/phy_discovery/Description
 Discovery of wireless physical interfaces
endef

define iwx/Item/phy_discovery/Cmd
 wifi_phy_discovery
endef



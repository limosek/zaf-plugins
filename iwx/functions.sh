

wifi_ifphylist_openwrt() {
	local devs phys p ifname ifindex i

	if [ -f /usr/share/libubox/jshn.sh ]; then
		. /usr/share/libubox/jshn.sh

		json_load "$(ubus -S call network.wireless status)"
		json_get_keys phys
		for p in $phys; do
	  		if [ "$1" = "phy" ]; then
	    			echo $p
	    			continue
	  		fi
	  		json_select "$p"
	  		json_select "interfaces"
	  		json_get_keys ifindex
	  		for i in $ifindex; do
	    			json_select $i
	    			json_get_var ifname ifname
	    			echo $ifname
	    			json_select ..
	  		done
	  		json_select ..
	  		json_select ..
		done
	else
		wifi_ifphylist "$1"
	fi
}

wifi_ifphylist() {
	local i

	if [ "$1" = "phy" ]; then
		for i in 0 1 2 3 4 5 6; do
			if zaf_sudo iw phy phy$i info >/dev/null 2>/dev/null; then
				echo phy$i
			fi
		done
	else
		for i in 0 1 2 3 4 5 6; do
			if zaf_sudo iw dev wlan$i info >/dev/null 2>/dev/null; then
				echo wlan$i
			fi
		done
	fi
}

wifi_iflist(){
  zaf_os_specific wifi_ifphylist || wifi_ifphylist
}

wifi_phylist(){
  zaf_os_specific wifi_ifphylist phy || wifi_ifphylist phy
}

wifi_scan_dump(){
    wifi_scan $1 | tr '(' ' ' | \
     		awk '/^BSS / { printf "'$1' %s ",$2} /freq:/ { printf "%s ",$2 } /signal:/ { printf "%s ",$2 } /SSID:/ { printf "%s\n",$2 } ' 
}

wifi_scan() {
    local key="wifi_scan_dump_$1"
    if ! zaf_fromcache "$key"; then
	wifi_real_scan "$1" | zaf_tocache_stdin "$key" 300
    fi
}

wifi_real_scan() {
    zaf_sudo iw dev "$1" scan
}

wifi_channel_dump(){
     wifi_scan "$1" >/dev/null
     zaf_sudo iw dev $1 survey dump | tr -s '\t ' | \
       awk '/frequency:/ { printf "'$1' %i ",$2 } /noise:/ {printf "%i ",$2 } /active time:/ {printf "%i ",$4 } /busy time:/ {printf "%i ",$4 } /receive time:/ {printf "%i ",$4 } /transmit time:/ {printf "%i \n",$4 }'
}

wifi_if_discovery(){                                                                                     
   wifi_iflist | grep '^[a-z].*' | zaf_discovery "{#IF}"
}                                                                                                             
                                                                         
wifi_phy_discovery(){                                                                                       
   wifi_phylist | grep '^[a-z].*' | zaf_discovery "{#DEV}"                                                 
}

wifi_channels_discovery(){
   local devs
   [ -n "$1" ] && devs="$1" || devs="$(wifi_iflist)"
   for dev in $devs; do wifi_channel_dump $dev 2>/dev/null; done | zaf_discovery "{#DEV}" "{#FREQ}" "{#NOISE}" 
}

wifi_neigh_discovery(){
   local devs
   [ -n "$1" ] && devs="$1" || devs="$(wifi_iflist)"
   for dev in $devs; do wifi_scan_dump $dev 2>/dev/null; done | zaf_discovery "{#DEV}" "{#BSS}" "{#FREQ}" "{#SIGNAL}" "{#SSID}"
}

wifi_channel_noise(){
    wifi_channel_dump $1 | grep "^$1 $2" | (read dev freq noise active busy receive transmit; echo $noise)
}

wifi_channel_activetime(){
    wifi_channel_dump $1 | grep "^$1 $2" | (read dev freq noise active busy receive transmit; echo $active)
}

wifi_channel_busytime(){
    wifi_channel_dump $1 | grep "^$1 $2" | (read dev freq noise active busy receive transmit; echo $busy)
}

wifi_channel_receivetime(){
    wifi_channel_dump $1 | grep "^$1 $2" | (read dev freq noise active busy receive transmit; echo $receive)
}

wifi_channel_transmittime(){
    wifi_channel_dump $1 | grep "^$1 $2" | (read dev freq noise active busy receive transmit; echo $transmit)
}

wifi_neigh_channel(){
     wifi_scan_dump $1 | grep "^$1 $2" | (read dev bss freq signal ssid; echo $freq)
}

wifi_neigh_signal(){
     wifi_scan_dump $1 | grep "^$1 $2" | (read dev bss freq signal ssid; echo $signal)
}

wifi_neigh_ssid(){
     wifi_scan_dump $1 | grep "^$1 $2" | (read dev bss freq signal ssid; echo $ssid)
}

wifi_clients(){
    zaf_sudo iw dev $1 station dump | grep ^Station | wc -l
}

wifi_clients_dump(){
    zaf_sudo iw dev $1 station dump | awk '/^Station / { printf "'$1' %s ",$2} /signal:/ { printf "%s ",$2 } /tx bitrate:/ { printf "%s ",$3 } /rx bitrate:/ { printf "%s\n",$3 } '
}

wifi_clients_discovery(){
   local devs
   [ -n "$1" ] && devs="$1" || devs="$(wifi_iflist)"
   for dev in $devs; do wifi_clients_dump $dev 2>/dev/null; done | zaf_discovery "{#DEV}" "{#STATION}" "{#SIGNAL}" "{#TXRATE}" "{#RXRATE}" 
}

wifi_client_signal(){
    wifi_clients_dump $1 | grep "^$1 $2" | (read dev station signal tx rx; echo $signal)
}

wifi_client_rxrate(){
    wifi_clients_dump $1 | grep "^$1 $2" | (read dev station signal tx rx; echo $rx)
}

wifi_client_txrate(){
    wifi_clients_dump $1 | grep "^$1 $2" | (read dev station signal tx rx; echo $tx)
}


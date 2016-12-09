

wifi_ifphylist() {
	if [ "$1" = "phy" ]; then
		iw phy | grep Wiphy | cut -d ' ' -f 2
	else
		iw dev | grep Interface | cut -d ' ' -f 2
	fi
}

wifi_iflist(){
	wifi_ifphylist
}

wifi_phylist(){
	wifi_ifphylist phy
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
		 wifi_scan_dump $1 | grep "^$1 $2" | (
	read dev bss freq signal ssid;
	if [ -n "$freq" ]; then 
		echo $freq
	else
		echo "0"
	fi
	)
}

wifi_neigh_signal(){
		 wifi_scan_dump $1 | grep "^$1 $2" | (
	read dev bss freq signal ssid;
	if [ -n "$signal" ]; then 
		echo $signal
	else
		echo "-200"
	fi
	)
}

wifi_neigh_ssid(){
		 wifi_scan_dump $1 | grep "^$1 $2" | (
	read dev bss freq signal ssid;
	if [ -n "$ssid" ]; then 
		echo $ssid
	else
		echo "N/A"
	fi
	)
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
		wifi_clients_dump $1 | grep "^$1 $2" | (
	read dev station signal tx rx;
	if [ -n "$signal" ]; then 
		echo $signal
	else
		echo "-200"
	fi
	)
}

wifi_client_rxrate(){
		wifi_clients_dump $1 | grep "^$1 $2" | (
	read dev station signal tx rx;
	if [ -n "$rx" ]; then 
		echo $rx
	else
		echo "0"
	fi
	)
}

wifi_client_txrate(){
		wifi_clients_dump $1 | grep "^$1 $2" | (
	read dev station signal tx rx;
	if [ -n "$tx" ]; then 
		echo $tx
	else
		echo "0"
	fi
	)
}


#!/bin/sh

. ${ZAF_LIB_DIR}/zaf.lib.sh
. ${ZAF_LIB_DIR}/os.lib.sh
. ${ZAF_LIB_DIR}/ctrl.lib.sh

if [ "${ZAF_PKG}" = "opkg" ]; then
	AWK='{ print $2" "$5; }'
	PS="w"
else
	AWK='{ print $1" "$11; }'
	PS="--no-headers caux"
fi

ps $PS | awk "$AWK" | sort | uniq | sed -e 's/\//\\\//g' -e '$s/.$//' | zaf_discovery '{#PSNAME}' '{#PSUSER}'


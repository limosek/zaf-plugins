
include lib.mk

ifeq ($(PLUGINS),)
 PLUGINS=$(shell ls -I '*mk' -I Makefile -I README.md)
endif

all: $(PLUGINS)

$(eval $(call Plugins/Rules))



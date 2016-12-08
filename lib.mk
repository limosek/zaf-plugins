
define shvar
V_$(subst .,_,$(subst -,_,$(subst /,_,$(1))))
endef

define shexport
export $(call shvar,$(1))="$$(call $(1))"
endef

define newline


endef

define Plugin/Rule
 ifeq ($(wildcard $(1)/plugin.mk),)
 $(1):
	$(MAKE) PLUGINS=$(1) $(1)/control.zaf
 $(1)/control.zaf:
	@echo -n
 else
include $(1)/plugin.mk
  $(1): $(1)/control.zaf
	$(MAKE) PLUGINS=$(1) $(1)/control.zaf
  $(1)/plugin.mk:
  $(1)/control.zaf: $(1)/plugin.mk
	printf %b '$(subst $(call newline),\n,$(call Plugin/Control,$(1)))'
	
 endif
endef

define Plugins/Rules
 $(foreach p,$(PLUGINS),$(eval $(call Plugin/Rule,$(p))))
endef

define Plugin/Control
Plugin: $(1)
Version: $$(VERSION)
Description::
 $$(call $(1)/Description)
::
endef


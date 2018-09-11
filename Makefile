include $(THEOS)/makefiles/common.mk

TWEAK_NAME = XMPPFrameworkLogger

XMPPFrameworkLogger_FILES = $(wildcard *.xm)

XMPPFrameworkLogger_FRAMEWORKS = UIKit

include $(THEOS_MAKE_PATH)/tweak.mk

after-install::
	install.exec "killall -9 SpringBoard"

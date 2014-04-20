
Name: app-mobile-demo
Epoch: 1
Version: 1.2.5
Release: 1%{dist}
Summary: Mobile Demo
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base

%description
Example mobile application for Android™ devices.

%package core
Summary: Mobile Demo - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: app-base-core >= 1.4.7
Requires: app-network-core

%description core
Example mobile application for Android™ devices.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/mobile_demo
cp -r * %{buildroot}/usr/clearos/apps/mobile_demo/

install -d -m 750 %{buildroot}/var/state/webconfig
install -D -m 0644 packaging/mobile-demo.php %{buildroot}/var/clearos/base/daemon/mobile-demo.php
install -D -m 0644 packaging/mobile_demo.acl %{buildroot}/var/clearos/base/access_control/public/mobile_demo
install -D -m 0755 packaging/mobile-demo.init %{buildroot}/etc/init.d/mobile-demo

%post
logger -p local6.notice -t installer 'app-mobile-demo - installing'

%post core
logger -p local6.notice -t installer 'app-mobile-demo-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/mobile_demo/deploy/install ] && /usr/clearos/apps/mobile_demo/deploy/install
fi

[ -x /usr/clearos/apps/mobile_demo/deploy/upgrade ] && /usr/clearos/apps/mobile_demo/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-mobile-demo - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-mobile-demo-core - uninstalling'
    [ -x /usr/clearos/apps/mobile_demo/deploy/uninstall ] && /usr/clearos/apps/mobile_demo/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/mobile_demo/controllers
/usr/clearos/apps/mobile_demo/htdocs
/usr/clearos/apps/mobile_demo/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/mobile_demo/packaging
%exclude /usr/clearos/apps/mobile_demo/tests
%dir /usr/clearos/apps/mobile_demo
%dir %attr(750,root,webconfig) /var/state/webconfig
/usr/clearos/apps/mobile_demo/deploy
/usr/clearos/apps/mobile_demo/language
/usr/clearos/apps/mobile_demo/libraries
/var/clearos/base/daemon/mobile-demo.php
/var/clearos/base/access_control/public/mobile_demo
%attr(0755,root,root) /etc/init.d/mobile-demo

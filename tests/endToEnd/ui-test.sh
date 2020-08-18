REQUIRED_PKG="mocha"
PKG_OK=$(dpkg-query -W --showformat='${Status}\n' $REQUIRED_PKG|grep "install ok installed")
if [ "" = "$PKG_OK" ]; then
  echo "No $REQUIRED_PKG. Setting up $REQUIRED_PKG."
  sudo apt-get --yes install $REQUIRED_PKG 
fi
SELENIUM_BROWSER=chrome:36:LINUX \
SELENIUM_REMOTE_URL=http://localhost:4444/wd/hub \
mocha --recursive --timeout 600000 ui-test.js

<!DOCTYPE html>
<html>
<head>
    <link id="siteicon" rel="icon" href="./icon.png"/>
    <link rel="stylesheet" href="./panel.css"/>
    <title>Demo Status Window</title>
    <script src="./panel.js" type="text/javascript"></script>
</head>

<body onload="onLoad()">
  <div id="content">
    <h3>This window shows some status information</h3>
    <button onclick="changeSize();">change panel size</button>
    <button onclick="window.close()">close panel</button>
  <ul>These links should open in tabs:
  <li><a href="http://www.mozilla.org" target="_blank">external _blank</a></li>
  <li><a href="http://www.mozilla.org" target="_content">external social</a></li>
  <li><a href="http://www.mozilla.org">external none</a></li>
  <li><a href="http://www.mozilla.org" onclick="window.open(this.getAttribute('href'),'socialtab').focus(); return false;">external onclick</a></li>
  </ul>
    <div>
      <ul id="list" style="margin: 4px"/>
    </div>
  </div>
</body>
</html>
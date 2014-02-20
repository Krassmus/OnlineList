var apiPort;
var ports = [];
var JSUpdater = {
    ports: [],
    fetchData: function () {
        if (!JSUpdater.ports.length) {
            return;
        }
        //fetch data
        var r = new XMLHttpRequest(); 
        r.open(
            "GET", 
            "<?= URLHelper::getURL("dipatch.php/jsupater/get", array('page' => "plugins.php/onlinelist/worker")) ?>", 
            true
        );
        r.onreadystatechange = function () { 
            if (r.readyState != 4 || r.status != 200) return;
            JSUpdater.deliverData(JSON.parse(r.responseText));
        };
        r.send();
    },
    deliverData: function (data) {
        for (var i = 0; i < JSUpdater.ports.length; i++) {
            JSUpdater.ports[i].postMessage({
                'topic': "jsupdater.data",
                'data': data
            });
        }
    }
};
setInterval(JSUpdater.fetchData, 2000);

onconnect = function(e) {
    var port = e.ports[0];
    ports.push(port);
    port.onmessage = function (msgEvent)
    {
        var msg = msgEvent.data;
        if (msg.topic == "social.port-closing") {
            if (port == apiPort) {
                apiPort.close();
                apiPort = null;
            }
            return;
        }
        if (msg.topic == "social.initialize") {
            apiPort = port;
            port.postMessage({
                topic: "social.user-profile", 
                data: {
                iconURL: "<?= Assets::image_path("icons/32/blue/seminar.png") ?>",
                    portrait: "<?= Avatar::getAvatar($GLOBALS['user']->id)->getURL(Avatar::MEDIUM)?>",
                    userName: "<?= htmlReady(get_fullname()) ?>",
                    displayName: "<?= htmlReady(get_fullname()) ?>",
                    profileURL: "<?= URLHelper::getURL("dispatch.php/profile", array('username' => get_username()))?>"
                }
            });
        }
        if (msg.topic === "jsupdater.register") {
            port.postMessage({
                topic: "jsupdater.registered",
                data: {
                    text: "you are registered for updates"
                }
            });
        }
    }
}

// send a message to all provider content
function broadcast(topic, data) {
  for (var i = 0; i < ports.length; i++) {
    ports[i].postMessage({topic: topic, data: data});
  }
}

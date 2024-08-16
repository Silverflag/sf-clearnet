<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Silverflag Anon Chat</title>
<style>
body {
  background-color: #000000;
  color: #e0e0e0;
  font-family: Arial, sans-serif;
  line-height: 1.6;
  margin: 0;
  padding: 0;
}

#container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  color: #ffffff;
  font-weight: bold;
}

#chat-area {
  background-color: #1a1a1a;
  border-radius: 5px;
  padding: 20px;
  margin-bottom: 20px;
}

#chatbox {
  height: 400px;
  overflow-y: auto;
  border: 1px solid #808080;
  padding: 10px;
  margin-bottom: 10px;
}

.input-container {
  display: flex;
  margin-bottom: 10px;
}

#message {
  flex-grow: 1;
  padding: 10px;
  background-color: #2a2a2a;
  border: none;
  color: #e0e0e0;
}

#send-button {
  padding: 10px 20px;
  background-color: #1a2b47;
  border: none;
  color: #ffffff;
  cursor: pointer;
}

#username-popup {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #1a2b47;
  padding: 20px;
  border-radius: 5px;
  z-index: 9999;
}

#username-popup input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: none;
  background-color: #2a2a2a;
  color: #e0e0e0;
}

#username-popup button {
  padding: 10px 20px;
  background-color: #808080;
  border: none;
  color: #ffffff;
  cursor: pointer;
}
</style>
</head>
<body>
<div id="container">
  <h1>Silverflag Anon Chat</h1>
  <div id="chat-area">
    <div id="chatbox"></div>
    <div class="input-container">
      <input type="text" id="message" placeholder="Type your message..." onkeypress="handleKeyPress(event)">
      <input type="button" id="send-button" value="Send" onclick="sendMessage()">
    </div>
    <div>
      <input type="checkbox" id="autoscroll-checkbox" checked>
      <label for="autoscroll-checkbox">Autoscroll</label>
    </div>
  </div>
</div>

<div id="username-popup">
  <p>Please set a username to start chatting!</p>
  <input type="text" id="username" placeholder="Username" oninput="toggleConfirmButton()">
  <button id="confirm-username" onclick="setUsername()" disabled>Confirm</button>
</div>

<script>
// Check if the user has a username set, if not, show the popup
window.onload = function() {
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        if (xhr.responseText.trim() === 'NO_USERNAME') {
          document.getElementById("username-popup").style.display = "block";
        }
      } else {
        console.error('Error checking username: ' + xhr.status);
      }
    }
  };
  xhr.open('GET', 'check_username.php', true);
  xhr.send();
};

// Toggle confirm button based on username input
function toggleConfirmButton() {
  var username = document.getElementById("username").value.trim();
  var confirmButton = document.getElementById("confirm-username");
  confirmButton.disabled = username === '';
}

function sendMessage() {
  var message = document.getElementById("message").value;
  if (message.trim() !== "") {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          document.getElementById("message").value = "";
          if (document.getElementById("autoscroll-checkbox").checked) {
            autoScroll();
          }
        } else {
          alert('Error: ' + xhr.responseText);
        }
      }
    };
    xhr.open('POST', 'send_message.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('message=' + encodeURIComponent(message));
  }
}

function getChat() {
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        document.getElementById("chatbox").innerHTML = xhr.responseText;
        if (document.getElementById("autoscroll-checkbox").checked) {
          autoScroll();
        }
      } else {
        alert('Error: ' + xhr.responseText);
      }
    }
  };
  xhr.open('GET', 'get_chat.php', true);
  xhr.send();
}

function setUsername() {
  var username = document.getElementById("username").value;
  if (username.trim() !== "") {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          if (xhr.responseText === 'OK') {
            document.getElementById("username-popup").style.display = "none";
          } else {
            alert('Error setting username: ' + xhr.responseText);
          }
        }
      }
    };
    xhr.open('POST', 'set_username.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('username=' + encodeURIComponent(username));
  }
}

function handleKeyPress(event) {
  if (event.keyCode === 13) { // Enter key
    sendMessage();
  }
}

function autoScroll() {
  var chatbox = document.getElementById("chatbox");
  chatbox.scrollTop = chatbox.scrollHeight;
}

// Fetch chat initially and then every 900ms
getChat();
setInterval(function() {
  getChat();
}, 900);
</script>

</body>
</html>
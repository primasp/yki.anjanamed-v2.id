let mediaRecorder;
let audioChunks = [];
let stream; // ⬅️ tambahkan ini

document.getElementById("recordBtn").onclick = async () => {
  stream = await navigator.mediaDevices.getUserMedia({ audio: true }); // simpan stream
  //   const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
  mediaRecorder = new MediaRecorder(stream);
  audioChunks = [];

  mediaRecorder.ondataavailable = (e) => {
    audioChunks.push(e.data);
  };

  mediaRecorder.onstop = async () => {
    // const audioBlob = new Blob(audioChunks, { type: "audio/ogg; codecs=opus" });
    const audioBlob = new Blob(audioChunks, { type: "audio/webm" });
    // const audioBlob = new Blob(audioChunks, {
    //   type: "audio/webm; codecs=opus",
    // });

    const formData = new FormData();
    // formData.append("audio", audioBlob);
    formData.append("audio", audioBlob, "rekaman.webm"); // atau rekaman.ogg

    // const response = await fetch("<?= base_url('speech/process') ?>", {
    //   method: "POST",
    //   body: formData,
    // });

    const response = await fetch(BASE_URL + "speech/process", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();
    document.getElementById("outputText").textContent =
      result.transcript || "Gagal transkripsi";
  };

  mediaRecorder.start();
  document.getElementById("stopBtn").disabled = false;
  document.getElementById("recordBtn").disabled = true;
};

document.getElementById("stopBtn").onclick = () => {
  mediaRecorder.stop();
  // Hentikan semua track mikrofon
  stream.getTracks().forEach((track) => track.stop()); // ⬅️ penting!
  document.getElementById("stopBtn").disabled = true;
  document.getElementById("recordBtn").disabled = false;
};

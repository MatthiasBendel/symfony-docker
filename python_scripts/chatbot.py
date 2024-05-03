from vosk import Model, KaldiRecognizer
import os
import pyaudio

# Set the model path
model_path = "../python_data/vosk-model-small-de-0.15"
if not os.path.exists(model_path):
    print("Model not found. Please check the model path.")
    exit(1)

# Load the model
model = Model(model_path)

# Initialize the recognizer with the model, assuming a sample rate of 16000
rec = KaldiRecognizer(model, 16000)

# Set up PyAudio to use the microphone
p = pyaudio.PyAudio()
stream = p.open(format=pyaudio.paInt16, channels=1, rate=16000, input=True, frames_per_buffer=4000)
stream.start_stream()

# Process microphone input
while True:
    data = stream.read(4000, exception_on_overflow=False)
    if rec.AcceptWaveform(data):
        print(rec.Result())
    else:
        print(rec.PartialResult())

# Don't forget to stop and close the stream
stream.stop_stream()
stream.close()
p.terminate()

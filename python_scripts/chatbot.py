from vosk import Model, KaldiRecognizer
import os
import pyaudio
import time
import json
import subprocess

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

# Variables to track time and output
last_word_time = time.time()
current_result = []

# Define actions for music control
def play_music():
    script = 'tell application "Music" to play'
    subprocess.run(["osascript", "-e", script])
    print("Playing music...")

def stop_music():
    script = 'tell application "Music" to pause'
    subprocess.run(["osascript", "-e", script])
    print("Music stopped.")

# Process microphone input
while True:
    data = stream.read(4000, exception_on_overflow=False)
    if rec.AcceptWaveform(data):
        result_json = json.loads(rec.Result())
        if result_json.get('text', '') != '':
            result = result_json['text'].lower()
            current_result.append(result)
            last_word_time = time.time()  # Update the last word time
            result_text = ' '.join(current_result).lower()
            if 'musik an' in result_text:
                play_music()
                current_result = []  # Clear results after action
            elif 'musik aus' in result_text:
                stop_music()
                current_result = []  # Clear results after action

    # Check if it's been more than 1 second since the last word
    current_time = time.time()
    if current_time - last_word_time > 1.0 and current_result:
        print('Heard:', ' '.join(current_result))  # Output what was heard
        current_result = []  # Reset the current result
        last_word_time = current_time  # Reset the last word time

# Don't forget to stop and close the stream
stream.stop_stream()
stream.close()
p.terminate()

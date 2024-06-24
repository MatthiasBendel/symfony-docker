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

# Define actions for music control and system sleep
def play_music():
    script = 'tell application "Music" to play'
    subprocess.run(["osascript", "-e", script])
    print("Playing music...")

def stop_music():
    script = 'tell application "Music" to pause'
    subprocess.run(["osascript", "-e", script])
    print("Music stopped.")

def set_volume(level):
    """
    Set the system volume to the specified level (0-100).
    :param level: int
    """
    if 0 <= level <= 100:
        script = f"set volume output volume {level}"
        subprocess.run(["osascript", "-e", script])
    else:
        raise ValueError("Volume level must be between 0 and 100.")

def increase_volume(increment=20):
    try:
        # Get the current volume level
        result = subprocess.run(["osascript", "-e", "output volume of (get volume settings)"],
                                capture_output=True, text=True)
        current_volume = int(result.stdout.strip())
        # Calculate the new volume
        new_volume = min(current_volume + increment, 100)
        set_volume(new_volume)
        print(f"Changed volume to {new_volume}%")
    except Exception as e:
        print(f"An error occurred: {e}")

def sleep_system():
    # Command to put the system to sleep
    subprocess.run(["pmset", "sleepnow"])
    print("System is going to sleep...")

# Process microphone input
print('listening ...')
while True:
    data = stream.read(4000, exception_on_overflow=False)
    if rec.AcceptWaveform(data):
        result_json = json.loads(rec.Result())
        if result_json.get('text', '') != '':
            result = result_json['text'].lower()
            current_result.append(result)
            last_word_time = time.time()  # Update the last word time
            result_text = ' '.join(current_result).lower()
            print(result_text)
            if 'musik an' in result_text:
                play_music()
                current_result = []  # Clear results after action
            elif 'musik aus' in result_text:
                stop_music()
                current_result = []  # Clear results after action
            elif 'lauter' in result_text:
                increase_volume()
                current_result = []  # Clear results after action
            elif 'leiser' in result_text:
                increase_volume(increment=-20)
                current_result = []  # Clear results after action
            elif 'Ausschalten' in result_text or 'Gute Nacht' in result_text:
                sleep_system()
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

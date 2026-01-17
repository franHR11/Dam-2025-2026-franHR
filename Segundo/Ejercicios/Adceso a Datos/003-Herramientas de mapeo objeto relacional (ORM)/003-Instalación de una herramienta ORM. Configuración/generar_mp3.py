from pydub import AudioSegment
from pydub.generators import Sine
import os

# Create a 3-second sine wave at 440 Hz
tone = Sine(440).to_audio_segment(duration=3000)

# Export as mp3
output_file = "0802.mp3"
tone.export(output_file, format="mp3")
print(f"Created {output_file}")

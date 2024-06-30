import matplotlib.pyplot as plt
import numpy as np
import pandas as pd

# Read the CSV file into a DataFrame
df = pd.read_csv('./saved/data/fer2013/fer2013.csv')

# Define the data
emotions = ['Angry', 'Disgust', 'Fear', 'Happy', 'Sad', 'Surprise', 'Neutral']
counts = df.groupby(['emotion', 'Usage']).size().unstack(fill_value=0)

# Extract counts for each emotion and split type
training = counts['Training'].tolist()
validation = counts['PublicTest'].tolist()  # Assuming 'PublicTest' is for validation
testing = counts['PrivateTest'].tolist()  # Assuming 'PrivateTest' is for testing

# Set the width of the bars
bar_width = 0.6

# Set the positions of the bars on the x-axis
r = np.arange(len(emotions))

plt.figure(figsize=(5, 5))  # Adjust the size as needed

# Create the bars for each emotion
plt.bar(r, training, color='blue', width=bar_width, label='Training')
plt.bar(r, validation, bottom=training, color='orange', width=bar_width, label='Validation')
plt.bar(r, testing, bottom=[i+j for i,j in zip(training, validation)], color='green', width=bar_width, label='Testing')

# Add total count of each bar on top of the bar
for i, (train, valid, test) in enumerate(zip(training, validation, testing)):
    total_count = train + valid + test
    plt.text(i, total_count+50, str(total_count), color='black', ha='center', va='bottom')

    # Add internal counts for each part of the bar
    if i != 1:
        plt.text(i, train/2, str(train), color='white', ha='center', va='center')
        plt.text(i, train + valid/2, str(valid), color='white', ha='center', va='center')
        plt.text(i, train + valid + test/2, str(test), color='white', ha='center', va='center', )
    else:
        plt.text(i, train/2, str(train), color='white', ha='center', va='center')
# Set the y-axis scale starting from 500 and incrementing by 1000
plt.yticks(np.arange(0, max(training) + max(validation) + max(testing) + 1000, 1000))

# Add xticks on the middle of the group bars
plt.xlabel('Emotion',fontweight='bold')
plt.xticks(r, emotions)

# Add ylabel
plt.ylabel('Count', fontweight='bold')

# Show the plot
plt.legend()
plt.tight_layout()
plt.show()

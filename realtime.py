import cv2
import torch
import json
import numpy as np
from torchvision.transforms import transforms
import torch.nn.functional as F

from model import resnet, alexnet

configs = "./configs/fer2013_config.json"
configs = json.load(open(configs))

def get_model(configs):
    
    return resnet.__dict__[configs["arch1"]], alexnet.__dict__[configs["arch2"]]

model1, model2 = get_model(configs)

model1 = model1(
    in_channels=configs["in_channels"],
    num_classes=configs["num_classes"],
)

model2 = model2(
    in_channels=configs["in_channels"],
    num_classes=configs["num_classes"],
)

import os

checkpoint_dir = os.path.join(os.getcwd(), configs["checkpoint_dir"])
checkpoint_path1 = os.path.join(checkpoint_dir, "{}.zip".format(configs["arch1"]))
checkpoint_path2 = os.path.join(checkpoint_dir, "{}.zip".format(configs["arch2"]))

state1 = torch.load(checkpoint_path1, map_location=torch.device('cpu'))
model1.load_state_dict(state1["net"])

state2 = torch.load(checkpoint_path2, map_location=torch.device('cpu'))
model2.load_state_dict(state2["net"])

model1.eval()
model2.eval()

from imgaug import augmenters as iaa

seg = iaa.Sequential(
    [
        iaa.Fliplr(p=0.5),
        iaa.Affine(rotate=(-30, 30)),
    ]
)

transform = transforms.Compose([
    transforms.ToPILImage(),
    transforms.ToTensor(),
])

emotions = {
    0: "Angry",
    1: "Disgust",
    2: "Fear",
    3: "Happy",
    4: "Sad",
    5: "Surprise",
    6: "Neutral",
}

cap = cv2.VideoCapture(0)

face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

outputs = []

def extract_face(frame):

    gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
    
    faces = face_cascade.detectMultiScale(gray, 1.3, 5)
    
    if len(faces) == 0:
        return None, None
    
    largest_face = max(faces, key=lambda face: face[2] * face[3])
    
    x, y, w, h = largest_face
    
    cropped_face = frame[y:y+h, x:x+w]

    resized_face = cv2.resize(cropped_face, (48, 48))
    
    gray_face = cv2.cvtColor(resized_face, cv2.COLOR_BGR2GRAY)
    
    pixel_values = list(gray_face.flatten())
    
    pixel_string = ' '.join(map(str, pixel_values))
    
    return faces, pixel_string

while True:

    ret, frame = cap.read()
    
    faces, fer2013_encoding = extract_face(frame)
    
    if fer2013_encoding:
        
        pixels = fer2013_encoding.split()
        pixels = list(map(int, pixels))
        image = np.array(pixels, dtype=np.uint8).reshape(48, 48)
        image = cv2.resize(image, (224, 224))
        image = image.astype(np.uint8)
        image = np.dstack([image] * 3)
        image = transform(image)
        image = image.unsqueeze(0)

        output1 = model1(image)
        output1 = F.softmax(output1, 1)

        output2 = model2(image)
        output2 = F.softmax(output2, 1)

        _, predict1 = torch.max(output1, 1)
        _, predict2 = torch.max(output2, 1)

        prob1 = output1[0, predict1]
        prob2 = output2[0, predict2]

        if prob1 > prob2:
            predict = predict1
            prob = round(prob1.item() * 100)
        
        else:
            predict = predict1
            prob = round(prob2.item() * 100)

        outputs.append(emotions[predict.item()])

        label_size, base_line = cv2.getTextSize(
            f"{emotions[predict.item()]}: 000", cv2.FONT_HERSHEY_SIMPLEX, 0.8, 2
        )

        for (x, y, w, h) in faces:
            
            cv2.rectangle(frame, (x, y), (x+w, y+h), (0, 255, 0), 2)

            cv2.rectangle(
                frame,
                (x+w, y + 1 - label_size[1]),
                (x+w + label_size[0], y + 1 + base_line),
                (230, 216, 173),
                cv2.FILLED,
            )
            cv2.putText(
                frame,
                f"{emotions[predict.item()]} {prob}",
                (x+w, y + 1),
                cv2.FONT_HERSHEY_SIMPLEX,
                0.8,
                (128, 0, 0),
                2,
            )

    cv2.rectangle(frame, (1, 1), (310, 25), (230, 216, 173), cv2.FILLED)
    
    cv2.putText(
        frame,
        f"Press any key to exit",
        (20, 20),
        cv2.FONT_HERSHEY_SIMPLEX,
        0.8,
        (128, 0, 0),
        2,
    )

    cv2.imshow('Emotion Detector', frame)
        
    if cv2.waitKey(1) & 0xFF != 0xFF:
        break

cap.release()
cv2.destroyAllWindows()

from collections import Counter

counts = Counter(outputs)
most_common = counts.most_common(2)
most_occuring = most_common[0][0] if len(most_common) > 0 else ''

if most_occuring != '':
    print(most_occuring, end='\n')

else:
    print("Error", end='\n')
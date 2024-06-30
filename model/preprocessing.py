import os
import cv2
import numpy as np
import pandas as pd
from torch.utils.data import Dataset
from torchvision.transforms import transforms
from imgaug import augmenters as iaa
import matplotlib.pyplot as plt

seg = iaa.Sequential(
    [
        iaa.Fliplr(p=0.5),
        iaa.Affine(rotate=(-30, 30)),
    ]
)

emotions = {
        0: "Angry",
        1: "Disgust",
        2: "Fear",
        3: "Happy",
        4: "Sad",
        5: "Surprise",
        6: "Neutral",
    }

class FER2013(Dataset):
    
    def __init__(self, stage, size, configs, tta=False, tta_size=48):
        
        self._stage = stage
        self._size = size
        self._configs = configs
        self._tta = tta
        self._tta_size = tta_size

        self._image_size = (configs["image_size"], configs["image_size"])

        self._data = pd.read_csv(
            os.path.join(configs["data_path"], "{}.csv".format(stage))
        )

        self._numrows = self._data.shape[0]
        self._numcols = self._data.shape[1]
        self._cols = self._data.columns.tolist()
        self._emotion_list = self._data["emotion"].tolist()

        print("\nAbout {} Dataset....".format(stage))
        print("\nDimensions of {} Dataset are: ".format(stage), self._numrows, "x", self._numcols)
        print("{} Dataset is {}% of Original Dataset".format(stage, int(round(self._numrows/size, 1)*100)))
        print("Columns in {} Dataset are: ".format(stage), *self._cols)
        self._data = self._data.drop(columns=["Usage"])
        print("Usage column is dropped from {} data".format(stage))

        self._pixels = self._data["pixels"].tolist()
        
        self._emotions = pd.get_dummies(self._data["emotion"])

        self._transform = transforms.Compose(
            [
                transforms.ToPILImage(),
                transforms.ToTensor(),
            ]
        )

    def is_tta(self):
        return self._tta == True

    def __len__(self):
        return len(self._pixels)

    def __getitem__(self, idx):
        
        pixels = self._pixels[idx]
        pixels = list(map(int, pixels.split(" ")))
        image = np.asarray(pixels).reshape(48, 48)
        image = image.astype(np.uint8)

        image = cv2.resize(image, self._image_size)
        image = np.dstack([image] * 3)

        if self._stage == "train":
            image = seg(image=image)

        if self._stage == "test" and self._tta == True:
            
            images = [seg(image=image) for i in range(self._tta_size)]
            images = list(map(self._transform, images))
            target = self._emotions.iloc[idx].idxmax()
            return images, target

        image = self._transform(image)
        target = self._emotions.iloc[idx].idxmax()
        return image, target

def datasets(configs):

    import os
    import pandas as pd

    print("About FER2013 Dataset....")

    link = "https://www.kaggle.com/datasets/nicolejyt/facialexpressionrecognition?resource=download"

    print("\nFER2013 Dataset is downloaded from", link)

    folder_path = "./saved/data/fer2013/"

    dataset_path = os.path.join(folder_path, "fer2013.csv")

    train_file_exists = os.path.exists(os.path.join(folder_path, "train.csv"))
    val_file_exists = os.path.exists(os.path.join(folder_path, "val.csv"))
    test_file_exists = os.path.exists(os.path.join(folder_path, "test.csv"))

    dataset = pd.read_csv(dataset_path)

    if not (train_file_exists and val_file_exists and test_file_exists):
        
        train_dataset = dataset[dataset["Usage"] == "Training"]
        val_dataset = dataset[dataset["Usage"] == "PublicTest"]
        test_dataset = dataset[(dataset["Usage"] != "Training") & (dataset["Usage"] != "PublicTest")]

        train_file_path = os.path.join(folder_path, "train.csv")
        val_file_path = os.path.join(folder_path, "val.csv")
        test_file_path = os.path.join(folder_path, "test.csv")

        train_dataset.to_csv(train_file_path, index=False)
        val_dataset.to_csv(val_file_path, index=False)
        test_dataset.to_csv(test_file_path, index=False)

    print("Dimensions of FER2013 Dataset are: {} x {}".format(dataset.shape[0], dataset.shape[1]))

    emotion = dataset["emotion"].tolist()

    counts = []

    for i in emotions:
        counts.append(emotion.count(i))

    plt.bar([emotions[i] for i in emotions], counts)
    for i, count in enumerate(counts):
        plt.text(i, count + 2, str(count), ha='center')
    plt.xlabel('Emotion')
    plt.ylabel('Count')
    plt.title('FER2013 Emotions')

    checkpoint_dir = os.path.join(
        configs["cwd"], configs["checkpoint_dir"]
    )

    if not os.path.exists(checkpoint_dir):
        os.makedirs(checkpoint_dir, exist_ok=True)
    
    plt.savefig(os.path.join(
        checkpoint_dir,
        'fer2013.png'
    ))

    plt.close()

    return dataset.shape[0]


def print_emotions():

    print("Emotions in FER2013 Datasets are: ")

    for i in emotions:
        print(i, "---->", emotions[i])


def fer2013(stage, size, configs=None, tta=False, tta_size=48):
    return FER2013(stage, size, configs, tta, tta_size)
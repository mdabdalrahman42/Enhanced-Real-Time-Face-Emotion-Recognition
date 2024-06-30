import json
import os
import random
import warnings
import imgaug
import numpy as np
import torch
import statistics as s

warnings.simplefilter(action="ignore")

seed = 1234
random.seed(seed)
imgaug.seed(seed)
torch.manual_seed(seed)
torch.cuda.manual_seed_all(seed)
np.random.seed(seed)
torch.backends.cudnn.deterministic = True
torch.backends.cudnn.benchmark = False

import resnet, alexnet

def main(config_path):
    
    configs = json.load(open(config_path))
    
    configs["cwd"] = os.getcwd()

    model1, model2 = get_model(configs)

    train_set, val_set, test_set = get_dataset(configs)

    from evaluation import FER2013Trainer

    eval = FER2013Trainer(model1, model2, train_set, val_set, test_set, configs)

    train_loss, train_acc, val_loss, val_acc, consume_time = eval.train()

    print("\nTraining Accuracy: {} %".format(round(s.mean(train_acc),2)))

    print("Validation Accuracy: {} %".format(round(s.mean(val_acc),2)))

    test_acc, actual, predicted = eval.test()

    print("Testing Accuracy: {} %".format(round(test_acc,2)))

    from visualization import visualize

    visualize(train_acc, train_loss, val_acc, val_loss, actual, predicted)


def get_model(configs):
    
    return resnet.__dict__[configs["arch1"]], alexnet.__dict__[configs["arch2"]]


def get_dataset(configs):
    
    from preprocessing import datasets, print_emotions, fer2013

    original_size = datasets(configs)

    print_emotions()

    train_set = fer2013("train", original_size, configs)
    val_set = fer2013("val", original_size, configs)
    test_set = fer2013("test", original_size, configs, tta=True, tta_size=10)
    
    return train_set, val_set, test_set


if __name__ == "__main__":
    
    main("./configs/fer2013_config.json")




"""
from google.colab import drive
drive.mount('/content/drive')

%cd /content/drive/MyDrive/Product Recommendation System

!python model/main.py

import os
from IPython.display import display, Image

checkpoint_dir = os.path.join('./checkpoint')

for filename in os.listdir(checkpoint_dir):
  if filename.endswith(".png"):
    print("\n")
    image_path = os.path.join(checkpoint_dir, filename)
    display(Image(filename=image_path))
    print("\n")
"""
import json
import os
import matplotlib.pyplot as plt
from sklearn.metrics import confusion_matrix, precision_score, recall_score, f1_score
import seaborn as sns
import numpy as np
#from IPython.display import display, Image

configs = json.load(open("./configs/fer2013_config.json"))

emotions = {
        0: "Angry",
        1: "Disgust",
        2: "Fear",
        3: "Happy",
        4: "Sad",
        5: "Surprise",
        6: "Neutral",
    }

def visualize(train_acc, train_loss, val_acc, val_loss, actual, predicted):

    precision = precision_score(actual, predicted, average="macro") * 100
    recall = recall_score(actual, predicted, average="macro") * 100
    f1 = f1_score(actual, predicted, average="macro") * 100

    print("Precision: {} %".format(round(precision,2)))
    print("Recall: {} %".format(round(recall,2)))
    print("F1 Score: {} %".format(round(f1,2)))

    print("\nVisualization of Model....")

    checkpoint_dir = os.path.join(
        "./", configs["checkpoint_dir"]
    )

    if not os.path.exists(checkpoint_dir):
        os.makedirs(checkpoint_dir, exist_ok=True)
    
    epochs = [i for i in range(1, configs["max_epoch_num"]+1)]

    plt.plot(epochs, train_acc)
    plt.xlabel('Epoch')
    plt.ylabel('Accuracy')
    plt.title('Training Accuracy over Epochs')
    plt.savefig(os.path.join(
        checkpoint_dir,
        'train_acc.png'
    ))
    plt.close()

    plt.plot(epochs, train_loss, color = "red")
    plt.xlabel('Epoch')
    plt.ylabel('Loss')
    plt.title('Training Loss over Epochs')
    plt.savefig(os.path.join(
        checkpoint_dir,
        'train_loss.png'
    ))
    plt.close()

    plt.plot(epochs, val_acc)
    plt.xlabel('Epoch')
    plt.ylabel('Accuracy')
    plt.title('Validation Accuracy over Epochs')
    plt.savefig(os.path.join(
        checkpoint_dir,
        'val_acc.png'
    ))
    plt.close()

    plt.plot(epochs, val_loss, color = "red")
    plt.xlabel('Epoch')
    plt.ylabel('Loss')
    plt.title('Validation Loss over Epochs')
    plt.savefig(os.path.join(
        checkpoint_dir,
        'val_loss.png'
    ))
    plt.close()

    cm = confusion_matrix(actual, predicted)
    cm_percentage = cm.astype('float') / cm.sum(axis=1)[:, np.newaxis]  * 100

    plt.figure(figsize = (7,6))
    sns.heatmap(cm_percentage, annot=True, cmap='Blues', fmt='.2f', xticklabels=[emotions[i] for i in emotions], yticklabels=[emotions[i] for i in emotions])
    plt.xlabel('Predicted labels')
    plt.ylabel('Actual labels')
    plt.title('Confusion Matrix (Percentages)')
    plt.savefig(os.path.join(
        checkpoint_dir,
        'cm.png'
    ))
    plt.close()

    #for filename in os.listdir(checkpoint_dir):
        #if filename.endswith(".png"):
            #print("\n")
            #image_path = os.path.join(checkpoint_dir, filename)
            #display(Image(filename=image_path))
            #print("\n")
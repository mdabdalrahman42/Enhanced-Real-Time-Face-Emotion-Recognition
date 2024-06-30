import datetime
import os
import numpy as np
import torch
import torch.nn as nn
import torch.nn.functional as F
from torch.optim.lr_scheduler import ReduceLROnPlateau
from torch.utils.data import DataLoader
from tqdm import tqdm
import statistics as s
import contextlib

def make_batch(images):
    
    if not isinstance(images, list):
        images = [images]
    
    return torch.stack(images, 0)

def accuracy(output, target):
    
    with torch.no_grad():
        
        batch_size = target.size(0)
        pred = torch.argmax(output, dim=1)
        correct = pred.eq(target).float().sum(0)
        acc = correct * 100 / batch_size
    
    return [acc]

from radam import RAdam

emotions = {
        0: "Angry",
        1: "Disgust",
        2: "Fear",
        3: "Happy",
        4: "Sad",
        5: "Surprise",
        6: "Neutral",
    }

class Trainer(object):
    def __init__(self):
        pass

class FER2013Trainer(Trainer):

    def __init__(self, model1, model2, train_set, val_set, test_set, configs):
        
        super().__init__()
        
        print("\nModel Training....\n")

        self._configs = configs
        self._lr = self._configs["lr"]
        self._batch_size = self._configs["batch_size"]
        self._weight_decay = self._configs["weight_decay"]
        self._num_workers = self._configs["num_workers"]
        self._device = torch.device(self._configs["device"])
        self._max_epoch_num = self._configs["max_epoch_num"]

        self._train_set = train_set
        self._val_set = val_set
        self._test_set = test_set
        
        self._model1 = model1(
            in_channels=configs["in_channels"],
            num_classes=configs["num_classes"],
        )

        self._model2 = model2(
            in_channels=configs["in_channels"],
            num_classes=configs["num_classes"],
        )

        self._model1 = self._model1.to(self._device)
        self._model2 = self._model2.to(self._device)

        self._train_loader = DataLoader(
            self._train_set,
            batch_size=self._batch_size,
            num_workers=self._num_workers,
            pin_memory=True,
            shuffle=True,
        )
        self._val_loader = DataLoader(
            self._val_set,
            batch_size=self._batch_size,
            num_workers=self._num_workers,
            pin_memory=True,
            shuffle=False,
        )
        self._test_loader = DataLoader(
            self._test_set,
            batch_size=1,
            num_workers=self._num_workers,
            pin_memory=True,
            shuffle=False,
        )

        class_weights = [
            1.02660468,
            9.40661861,
            1.00104606,
            0.56843877,
            0.84912748,
            1.29337298,
            0.82603942,
        ]
        
        class_weights = torch.FloatTensor(np.array(class_weights))

        self._criterion = nn.CrossEntropyLoss().to(self._device)

        self._optimizer1 = RAdam(
            params=self._model1.parameters(),
            lr=self._lr,
            weight_decay=self._weight_decay,
        )

        self._scheduler1 = ReduceLROnPlateau(
            self._optimizer1,
            patience=self._configs["plateau_patience"],
            min_lr=1e-6,
            verbose=True,
        )

        self._optimizer2 = RAdam(
            params=self._model2.parameters(),
            lr=self._lr,
            weight_decay=self._weight_decay,
        )

        self._scheduler2 = ReduceLROnPlateau(
            self._optimizer2,
            patience=self._configs["plateau_patience"],
            min_lr=1e-6,
            verbose=True,
        )

        self._start_time = datetime.datetime.now()
        
        self._start_time = self._start_time.replace(microsecond=0)

        self._train_loss_list = []
        self._train_acc_list = []
        self._val_loss_list = []
        self._val_acc_list = []
        self._test_acc = 0.0
        self._current_epoch_num = 0
        self._consume_times = []

        self._checkpoint_dir = os.path.join(
            self._configs["cwd"], self._configs["checkpoint_dir"]
        )

        if not os.path.exists(self._checkpoint_dir):
            os.makedirs(self._checkpoint_dir, exist_ok=True)

        self._checkpoint_path1 = os.path.join(
            self._checkpoint_dir,
            "{}".format(
                self._configs["arch1"]
            ),
        )

        self._checkpoint_path2 = os.path.join(
            self._checkpoint_dir,
            "{}".format(
                self._configs["arch2"]
            ),
        )

    def _train(self):
        
        self._model1.train()
        self._model2.train()

        train_loss = 0.0
        train_acc = 0.0

        for i, (images, targets) in tqdm(
            enumerate(self._train_loader), total=len(self._train_loader), leave=False
        ):
            images = images.cuda(non_blocking=True)
            targets = targets.cuda(non_blocking=True)

            outputs1 = self._model1(images)

            loss1 = self._criterion(outputs1, targets)
            acc1 = accuracy(outputs1, targets)[0]

            outputs2 = self._model2(images)

            loss2 = self._criterion(outputs2, targets)
            acc2 = accuracy(outputs2, targets)[0]

            train_loss += s.mean([loss1.item(), loss2.item()])
            train_acc += s.mean([acc1.item(), acc2.item()])

            self._optimizer1.zero_grad()
            loss1.backward()
            self._optimizer1.step()

            self._optimizer2.zero_grad()
            loss2.backward()
            self._optimizer2.step()

        i += 1
        
        self._train_loss_list.append(train_loss / i)
        self._train_acc_list.append(train_acc / i)

    def _val(self):
        
        self._model1.eval()
        self._model2.eval()
        
        val_loss = 0.0
        val_acc = 0.0

        with torch.no_grad():
            
            for i, (images, targets) in tqdm(
                enumerate(self._val_loader), total=len(self._val_loader), leave=False
            ):
                images = images.cuda(non_blocking=True)
                targets = targets.cuda(non_blocking=True)

                outputs1 = self._model1(images)

                loss1 = self._criterion(outputs1, targets)
                acc1 = accuracy(outputs1, targets)[0]

                outputs2 = self._model2(images)

                loss2 = self._criterion(outputs2, targets)
                acc2 = accuracy(outputs2, targets)[0]

                val_loss += min(loss1.item(), loss2.item())
                val_acc += max(acc1.item(), acc2.item())

            i += 1
            
            self._val_loss_list.append(val_loss / i)
            self._val_acc_list.append(val_acc / i)

    def _calc_acc_on_private_test_with_tta(self):
        
        self._model1.eval()
        self._model2.eval()
        
        test_acc = 0.0

        actual=[]
        predicted=[]
        
        print("\nModel Testing with Test Time Augmentation....\n")

        with torch.no_grad():
            for i in tqdm(
                range(len(self._test_set)), total=len(self._test_set), leave=False
            ):
                images, targets = self._test_set[i]
                targets = torch.LongTensor([targets])

                images = make_batch(images)
                images = images.cuda(non_blocking=True)
                targets = targets.cuda(non_blocking=True)

                actual.extend(targets.cpu().numpy())

                outputs1 = self._model1(images)
                outputs1 = F.softmax(outputs1, 1)
                outputs1 = torch.sum(outputs1, 0)
                outputs1 = torch.unsqueeze(outputs1, 0)
                acc1 = accuracy(outputs1, targets)[0]

                outputs2 = self._model2(images)
                outputs2 = F.softmax(outputs2, 1)
                outputs2 = torch.sum(outputs2, 0)
                outputs2 = torch.unsqueeze(outputs2, 0)
                acc2 = accuracy(outputs2, targets)[0]


                if acc1 > acc2:
                    test_acc += acc1.item()
                    _, predict = torch.max(outputs1, 1)
                    predicted.extend(predict.cpu().numpy())

                else:
                    test_acc += acc2.item()
                    _, predict = torch.max(outputs2, 1)
                    predicted.extend(predict.cpu().numpy())

            test_acc = test_acc / (i + 1)
        
        return test_acc, actual, predicted

    def train(self):

        while not self._is_stop():
                
            self._increase_epoch_num()
            
            self._train()
            self._val()

            self._save_weights()

            with contextlib.redirect_stdout(None):

                self._scheduler1.step(100 - self._val_acc_list[-1])
                self._scheduler2.step(100 - self._val_acc_list[-1])
            
            self._status()

        return self._train_loss_list, self._train_acc_list, self._val_loss_list, self._val_acc_list, self._consume_times

    def test(self):

        state1 = torch.load(self._checkpoint_path1)
        self._model1.load_state_dict(state1["net"])

        state2 = torch.load(self._checkpoint_path2)
        self._model2.load_state_dict(state2["net"])

        self._test_acc, self._actual, self._predicted = self._calc_acc_on_private_test_with_tta()

        return self._test_acc, self._actual, self._predicted

    def _status(self):
        
        consume_time = str(datetime.datetime.now() - self._start_time)

        self._consume_times.append(consume_time[:-7])

        message = "E{:03d}  {:.3f}/{:.3f} {:.3f}/{:.3f} | Time {}".format(
            self._current_epoch_num,
            self._train_loss_list[-1],
            self._val_loss_list[-1],
            self._train_acc_list[-1],
            self._val_acc_list[-1],
            consume_time[:-7],
        )

        print(message)

    def _is_stop(self):

        return (
            self._current_epoch_num >= self._max_epoch_num
        )

    def _increase_epoch_num(self):
        self._current_epoch_num += 1

    def _save_weights(self):
            
        state_dict1 = self._model1.state_dict()
        state_dict2 = self._model2.state_dict()

        state1 = {
            **self._configs,
            "net": state_dict1,
        }

        torch.save(state1, self._checkpoint_path1)

        state2 = {
            **self._configs,
            "net": state_dict2,
        }

        torch.save(state2, self._checkpoint_path2)
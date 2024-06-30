const dropdownBtnM = document.getElementById("dropdown-btn-1");
const dropdownContentM = document.getElementById("dropdown-content-1");

var btnsM = [
  "dropdown-btn-11",
  "dropdown-btn-12",
  "dropdown-btn-13",
  "dropdown-btn-14",
  "dropdown-btn-15",
  "dropdown-btn-16",
  "dropdown-btn-17",
];
var contentsM = [
  "dropdown-content-11",
  "dropdown-content-12",
  "dropdown-content-13",
  "dropdown-content-14",
  "dropdown-content-15",
  "dropdown-content-16",
  "dropdown-content-17",
];
var togglesM = [
  ".dropdown-toggle-11",
  ".dropdown-toggle-12",
  ".dropdown-toggle-13",
  ".dropdown-toggle-14",
  ".dropdown-toggle-15",
  ".dropdown-toggle-16",
  ".dropdown-toggle-177",
];

dropdownBtnM.addEventListener("click", function () {
  dropdownContentM.classList.toggle("show");

  for (let i = 0; i < 7; i++) {
    const dropdownBtn = document.getElementById(btnsM[i]);
    const dropdownContent = document.getElementById(contentsM[i]);

    dropdownBtn.addEventListener("click", function () {
      dropdownContent.classList.toggle("show");
    });
  }
});

dropdownBtnM.addEventListener("click", function (event) {
  for (let i = 0; i < 7; i++) {
    if (!event.target.matches(".dropdown-toggle-1")) {
      dropdownContentM.classList.remove("show");
    } else if (!event.target.matches(togglesM[i])) {
      dropdownContent = document.getElementById(contentsM[i]);
      dropdownContent.classList.remove("show");
    }
  }
});

const dropdownBtnF = document.getElementById("dropdown-btn-2");
const dropdownContentF = document.getElementById("dropdown-content-2");

var btnsF = [
  "dropdown-btn-21",
  "dropdown-btn-22",
  "dropdown-btn-23",
  "dropdown-btn-24",
  "dropdown-btn-25",
  "dropdown-btn-26",
  "dropdown-btn-27",
];
var contentsF = [
  "dropdown-content-21",
  "dropdown-content-22",
  "dropdown-content-23",
  "dropdown-content-24",
  "dropdown-content-25",
  "dropdown-content-26",
  "dropdown-content-27",
];
var togglesF = [
  ".dropdown-toggle-21",
  ".dropdown-toggle-22",
  ".dropdown-toggle-23",
  ".dropdown-toggle-24",
  ".dropdown-toggle-25",
  ".dropdown-toggle-26",
  ".dropdown-toggle-27",
];

dropdownBtnF.addEventListener("click", function () {
  dropdownContentF.classList.toggle("show");

  for (let i = 0; i < 7; i++) {
    const dropdownBtn = document.getElementById(btnsF[i]);
    const dropdownContent = document.getElementById(contentsF[i]);

    dropdownBtn.addEventListener("click", function () {
      dropdownContent.classList.toggle("show");
    });
  }
});

dropdownBtnF.addEventListener("click", function (event) {
  for (let i = 0; i < 7; i++) {
    if (!event.target.matches(".dropdown-toggle-2")) {
      dropdownContentF.classList.remove("show");
    } else if (!event.target.matches(togglesF[i])) {
      dropdownContent = document.getElementById(contentsF[i]);
      dropdownContent.classList.remove("show");
    }
  }
});

const dropdownBtnA = document.getElementById("dropdown-btn-3");
const dropdownContentA = document.getElementById("dropdown-content-3");

var btnsA = [
  "dropdown-btn-31",
  "dropdown-btn-32",
  "dropdown-btn-33",
  "dropdown-btn-34",
  "dropdown-btn-35",
  "dropdown-btn-36",
  "dropdown-btn-37",
];
var contentsA = [
  "dropdown-content-31",
  "dropdown-content-32",
  "dropdown-content-33",
  "dropdown-content-34",
  "dropdown-content-35",
  "dropdown-content-36",
  "dropdown-content-37",
];
var togglesA = [
  ".dropdown-toggle-31",
  ".dropdown-toggle-32",
  ".dropdown-toggle-33",
  ".dropdown-toggle-34",
  ".dropdown-toggle-35",
  ".dropdown-toggle-36",
  ".dropdown-toggle-37",
];

dropdownBtnA.addEventListener("click", function () {
  dropdownContentA.classList.toggle("show");

  for (let i = 0; i < 7; i++) {
    const dropdownBtn = document.getElementById(btnsA[i]);
    const dropdownContent = document.getElementById(contentsA[i]);

    dropdownBtn.addEventListener("click", function () {
      dropdownContent.classList.toggle("show");
    });
  }
});

dropdownBtnA.addEventListener("click", function (event) {
  for (let i = 0; i < 7; i++) {
    if (!event.target.matches(".dropdown-toggle-3")) {
      dropdownContentA.classList.remove("show");
    } else if (!event.target.matches(togglesA[i])) {
      dropdownContent = document.getElementById(contentsA[i]);
      dropdownContent.classList.remove("show");
    }
  }
});
CREATE TABLE `admin` (
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(30) NOT NULL
);

INSERT INTO `admin` (`username`, `password`) VALUES ('admin', 'admin');



CREATE TABLE Patient (
  `Pid` INT PRIMARY KEY AUTO_INCREMENT,
  `Room_no` INT,
  `Fname` VARCHAR(50) NOT NULL,
  `Lname` VARCHAR(50) NOT NULL,
  `Gender` CHAR(30) NOT NULL,
  `Email` VARCHAR(100) NOT NULL UNIQUE,
  `Contact` VARCHAR(20) NOT NULL,
  `Password` VARCHAR(255) NOT NULL,
  CONSTRAINT unique_patient_info UNIQUE (Fname, Lname, Contact),
  CONSTRAINT unique_patient UNIQUE (Email, Password)
);

CREATE TABLE Doctor (
  `Username` VARCHAR(50) NOT NULL,
  `Password` VARCHAR(255) NOT NULL,
  `Email` VARCHAR(100) PRIMARY KEY,
  `Specialisation` VARCHAR(50) NOT NULL,
  `Fees` INT NOT NULL,
  CONSTRAINT unique_doc UNIQUE (Username,Password)
);

CREATE TABLE Appointment (
  `Pid` INT NOT NULL,
  `AppID` INT PRIMARY KEY AUTO_INCREMENT,
  `Email` VARCHAR(100) NOT NULL,
  `Appdate` DATE NOT NULL,
  `Apptime` TIME NOT NULL,
  `Disease` VARCHAR(255) NOT NULL,
  `Prescription` TEXT,
  `Mode` VARCHAR(20) NOT NULL,
  `Status` VARCHAR(20) NOT NULL,
  FOREIGN KEY (Pid) REFERENCES Patient(Pid),
  FOREIGN KEY (Email) REFERENCES Doctor(Email)
);

CREATE TABLE Review (
  `Pid` INT,
  `Email` VARCHAR(100) NOT NULL,
  `Ratings` INT NOT NULL,
  `Remarks` TEXT,
  CONSTRAINT pk_rev PRIMARY KEY (Pid,Email),
  FOREIGN KEY (Pid) REFERENCES Patient(Pid),
  FOREIGN KEY (Email) REFERENCES Doctor(Email)
);

CREATE TABLE Staff (
  `Sid` INT PRIMARY KEY AUTO_INCREMENT,
  `Name` VARCHAR(50) NOT NULL
);

CREATE TABLE Rooms (
  `Room_no` INT PRIMARY KEY,
  `Pid` int,
  `Sid` int,
  FOREIGN KEY (`Pid`) REFERENCES Patient(`Pid`),
  FOREIGN KEY (`Sid`) REFERENCES Staff(`Sid`)
);
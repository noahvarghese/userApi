
/**
 * Author:  Noah Varghese
 * Created: Jan 19, 2019
 */

DROP DATABASE IF EXISTS `assignments`;

CREATE DATABASE IF NOT EXISTS `assignments`; 
USE `assignments`;

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user`(
    ID INT NOT NULL AUTO_INCREMENT,
    FIRSTNAME VARCHAR(255) NOT NULL,
    LASTNAME VARCHAR(255) NOT NULL,
    EMAIL VARCHAR(255) NOT NULL,
    PASSWORD VARCHAR(255) NOT NULL,
    CREATED TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    MODIFIED TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (ID)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course`(
    ID INT NOT NULL AUTO_INCREMENT,
    USERID INT NOT NULL,
    COURSENAME VARCHAR(255) NOT NULL,
    COURSECODE VARCHAR(255), -- Can leave blank and add later
    SCHOOLYEAR INT NOT NULL, -- In PHP script default to current year
    TERM ENUM('winter', 'spring', 'fall') NOT NULL, -- In PHP script if blank use current term (Jan - April, May - August, Sept - Dec)
    PRIMARY KEY (ID),
    FOREIGN KEY (USERID) REFERENCES user(ID)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `assignment`;
CREATE TABLE IF NOT EXISTS `assignment`(
    ID INT NOT NULL AUTO_INCREMENT,
    COURSEID INT NOT NULL,
    ASSIGNMENTNAME VARCHAR(255) NOT NULL,
    DUEDATE DATE NOT NULL, -- Can leave blank, update later
    WEIGHT DECIMAL(3,1), -- Can leave blank, update later
    GRADE DECIMAL(3,1),
    COMPLETE BOOLEAN NOT NULL, -- default to not completed, user not required to check this box when entering information
    PRIMARY KEY (ID),
    FOREIGN KEY (COURSEID) REFERENCES course(ID)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `user` (`ID`, `FIRSTNAME`, `LASTNAME`, `EMAIL`, `PASSWORD`, `CREATED`, `MODIFIED`) VALUES
(1, 'Noah', 'Varghese', 'varghese.noah@gmail.com', '$2y$10$OKeZfxXq2HyGRJCSE.MFn.ktIZa3aC890Ck2Q/iYraloINL1eXXIi', '2019-01-28 20:12:24', '2019-01-28 22:01:52');

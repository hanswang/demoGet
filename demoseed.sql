create database demo character set utf8;
create user 'mysqldemo'@'localhost' identified by 'mysqlpass';

grant all privileges on demo.* to 'mysqldemo'@'localhost';

use demo;
CREATE TABLE `continents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8;

CREATE TABLE `countries` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `continent_id` INT(11) NOT NULL,
    `date_format` VARCHAR(100) NOT NULL,
    `currency_character` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8;

CREATE TABLE `states` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    `country_id` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB DEFAULT CHARSET=UTF8;

select * from continents;

insert into continents (name) values ('Asian'), ('Pacific'), ('Africa'), ('America'), ('Europe');
insert into countries (name, continent_id, date_format, currency_character) values
('China', 1, 'GMT', 'RMB'), ('Russia', 1, 'GMT', 'RUB'), ('Australia', 2, 'GMT', 'AUD');
insert into states (name, code, country_id)
values ('shanxi', 'sx', 1), ('xiamen', 'xm', 1), ('beijing', 'bj', 1);
create table users
(
	uid int auto_increment
		primary key,
	name varchar(15) not null,
	passwd char(32) not null,
	reg_date timestamp default current_timestamp() null,
	email varchar(30) not null,
	tel varchar(13) not null,
	constraint users_name_uindex
		unique (name)
);


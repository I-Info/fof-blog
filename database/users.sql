create table users
(
    id          int auto_increment
        primary key,
    name        varchar(15)                           not null,
    passwd      char(32)                              not null,
    email       varchar(30)                           not null,
    tel         varchar(13)                           not null,
    followers   int       default 0                   not null,
    create_time timestamp default current_timestamp() not null,
    update_time timestamp default current_timestamp() not null on update current_timestamp(),
    constraint users_name_uindex
        unique (name)
);


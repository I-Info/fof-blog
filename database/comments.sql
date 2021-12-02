create table comments
(
    id          int                                   not null
        primary key,
    content     varchar(250)                          not null,
    uid         int                                   not null,
    create_time timestamp default current_timestamp() not null,
    update_time timestamp default current_timestamp() not null on update current_timestamp()
);


create table blogs
(
    id          int auto_increment
        primary key,
    content     text                                                                not null,
    uid         int                                                                 not null,
    likes       int       default 0                                                 not null,
    create_time timestamp default current_timestamp()                               not null,
    update_time timestamp default current_timestamp() ON UPDATE current_timestamp() not null
);

create index blogs_uid_index
    on blogs (uid);


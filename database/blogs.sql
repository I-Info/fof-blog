create table blogs
(
    id          int auto_increment
        primary key,
    content     text                                  not null,
    uid         int                                   not null,
    likes       int       default 0                   not null,
    create_time timestamp default current_timestamp() not null,
    update_time timestamp default current_timestamp() not null on update current_timestamp(),
    constraint blogs_users_id_fk
        foreign key (uid) references users (id)
            on update cascade on delete cascade
);

create index blogs_uid_index
    on blogs (uid);


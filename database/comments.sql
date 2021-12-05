create table comments
(
    id          int auto_increment
        primary key,
    content     varchar(250)                          not null,
    uid         int                                   not null,
    blog_id     int                                   not null,
    likes       int       default 0                   not null,
    create_time timestamp default current_timestamp() not null,
    update_time timestamp default current_timestamp() not null on update current_timestamp(),
    constraint comments_blogs_id_fk
        foreign key (blog_id) references blogs (id)
            on update cascade on delete cascade,
    constraint comments_users_id_fk
        foreign key (uid) references users (id)
            on update cascade on delete cascade
);

create index comments_blog_id_index
    on comments (blog_id);

create index comments_uid_index
    on comments (uid);


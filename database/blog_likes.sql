create table blog_likes
(
    uid     int not null,
    blog_id int not null,
    constraint likes_uid_blog_id_uindex
        unique (uid, blog_id),
    constraint likes_blogs_id_fk
        foreign key (blog_id) references blogs (id),
    constraint likes_users_id_fk
        foreign key (uid) references users (id)
            on update cascade on delete cascade
);


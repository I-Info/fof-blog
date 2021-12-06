create table comment_likes
(
    uid        int not null,
    comment_id int not null,
    constraint comment_likes_uid_comment_id_uindex
        unique (uid, comment_id),
    constraint comment_likes_comments_id_fk
        foreign key (comment_id) references comments (id),
    constraint comment_likes_users_id_fk
        foreign key (uid) references users (id)
            on update cascade on delete cascade
);

create index comment_likes_comment_id_index
    on comment_likes (comment_id);


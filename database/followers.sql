create table followers
(
    uid          int not null,
    follower_uid int not null,
    constraint followers_uid_follower_uid_uindex
        unique (uid, follower_uid),
    constraint followers_users_id_fk
        foreign key (uid) references users (id)
            on update cascade on delete cascade,
    constraint followers_users_id_fk_2
        foreign key (follower_uid) references users (id)
);

create index followers_follower_uid_index
    on followers (follower_uid);


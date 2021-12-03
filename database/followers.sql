create table followers
(
    uid          int not null,
    follower_uid int not null,
    constraint followers_uid_follower_uid_uindex
        unique (uid, follower_uid)
);

create index followers_follower_uid_index
    on followers (follower_uid);


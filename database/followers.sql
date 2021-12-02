create table followers
(
    uid          int not null,
    follower_uid int not null
);

create index followers_follower_uid_index
    on followers (follower_uid);

create index followers_uid_index
    on followers (uid);


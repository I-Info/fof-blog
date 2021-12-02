create table likes
(
	uid int null,
	blog_id int not null,
	constraint likes_uid_blog_id_uindex
		unique (uid, blog_id)
);


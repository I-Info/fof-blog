create table blog_comment
(
	blog_id int not null,
	comment_id int not null,
	constraint blog_comment_comment_id_uindex
		unique (comment_id)
);

create index blog_comment_blog_id_index
	on blog_comment (blog_id);


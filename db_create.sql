CREATE TABLE tweet (
	tweet_id BIGINT NOT NULL,
	text VARCHAR(255),
	time TIMESTAMP,
	PRIMARY KEY (tweet_id)
);
CREATE TABLE image (
	image_id BIGINT NOT NULL,
	tweet_id BIGINT NOT NULL,
	filename VARCHAR(255),
	PRIMARY KEY (image_id),
	FOREIGN KEY (tweet_id)
		REFERENCES tweet(tweet_id)
		ON DELETE CASCADE
);

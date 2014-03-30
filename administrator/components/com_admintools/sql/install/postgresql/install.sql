CREATE TABLE IF NOT EXISTS "#__admintools_acl" (
	"user_id" bigint NOT NULL,
	"permissions" text,
	PRIMARY KEY ("user_id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_adminiplist" (
	"id" serial NOT NULL,
	"ip" character varying(255) DEFAULT NULL,
	"description" character varying(255) DEFAULT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "#__admintools_adminiplist_ip" UNIQUE ("ip")
);

CREATE TABLE  IF NOT EXISTS "#__admintools_badwords" (
	"id" serial NOT NULL,
	"word" character varying(255) DEFAULT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "#__admintools_badwords_word" UNIQUE ("word")
);

CREATE TABLE  IF NOT EXISTS "#__admintools_customperms" (
	"id" serial NOT NULL,
	"path" character varying(255) NOT NULL,
	"perms" character varying(4) DEFAULT '0644',
	PRIMARY KEY ("id")
);

CREATE INDEX "#__admintools_customperms_path" ON "#__admintools_customperms" ("path");

CREATE TABLE IF NOT EXISTS "#__admintools_filescache" (
	"admintools_filescache_id" serial NOT NULL,
	"path" character varying(2048) NOT NULL,
	"filedate" int NOT NULL DEFAULT '0',
	"filesize" int NOT NULL DEFAULT '0',
	"data" bytea,
	"checksum" character varying(32) NOT NULL DEFAULT '',
	PRIMARY KEY ("admintools_filescache_id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_ipautoban" (
	"ip" character varying(255) NOT NULL,
	"reason" character varying(255) DEFAULT 'other',
	"until" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	PRIMARY KEY ("ip")
);

CREATE TABLE IF NOT EXISTS "#__admintools_ipblock" (
	"id" serial NOT NULL,
	"ip" character varying(255) DEFAULT NULL,
	"description" character varying(255) DEFAULT NULL,
	PRIMARY KEY ("id"),
	CONSTRAINT "#__admintools_ipblock_ip" UNIQUE ("ip")
);

CREATE TABLE  IF NOT EXISTS "#__admintools_log" (
	"id" serial NOT NULL,
	"logdate" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	"ip" character varying(40) DEFAULT NULL,
	"url" character varying(255) DEFAULT NULL,
	"reason" character varying(255) DEFAULT 'other',
	"extradata" text,
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_redirects" (
	"id" serial NOT NULL,
	"source" character varying(255) DEFAULT NULL,
	"dest" character varying(255) DEFAULT NULL,
	"ordering" bigint NOT NULL DEFAULT '0',
	"published" smallint NOT NULL DEFAULT '1',
	"keepurlparams" smallint NOT NULL DEFAULT '1',
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_scanalerts" (
	"admintools_scanalert_id" serial NOT NULL,
	"path" character varying(2048) NOT NULL,
	"scan_id" bigint NOT NULL DEFAULT '0',
	"diff" text,
	"threat_score" int NOT NULL DEFAULT '0',
	"acknowledged" smallint NOT NULL DEFAULT '0',
	PRIMARY KEY ("admintools_scanalert_id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_scans" (
	"id" serial NOT NULL,
	"description" character varying(255) NOT NULL,
	"comment" text,
	"backupstart" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	"backupend" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
	"status" character varying(10) NOT NULL DEFAULT 'run',
	"origin" character varying(30) NOT NULL DEFAULT 'backend',
	"type" character varying(30) NOT NULL DEFAULT 'full',
	"profile_id" bigint NOT NULL DEFAULT '1',
	"archivename" text,
	"absolute_path" text,
	"multipart" int NOT NULL DEFAULT '0',
	"tag" character varying(255) DEFAULT NULL,
	"filesexist" smallint NOT NULL DEFAULT '1',
	"remote_filename" character varying(1000) DEFAULT NULL,
	"total_size" bigint NOT NULL DEFAULT '0',
	PRIMARY KEY ("id")
);

CREATE INDEX "#__admintools_scans_idx_fullstatus" ON "#__admintools_scans" ("filesexist", "status");
CREATE INDEX "#__admintools_scans_idx_stale" ON "#__admintools_scans" ("status", "origin");

CREATE TABLE IF NOT EXISTS "#__admintools_storage" (
	"key" character varying(255) NOT NULL,
	"value" text NOT NULL,
	PRIMARY KEY ("key")
);

CREATE TABLE IF NOT EXISTS "#__admintools_wafexceptions" (
	"id" serial NOT NULL,
	"option" character varying(255) DEFAULT NULL,
	"view" character varying(255) DEFAULT NULL,
	"query" character varying(255) DEFAULT NULL,
	PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__admintools_profiles" (
	"id" serial NOT NULL,
	"description" character varying(255) NOT NULL,
	"configuration" text,
	"filters" text,
	PRIMARY KEY ("id")
);

/* This rule simulates the INSERT IGNORE in MySQL */
CREATE RULE "#__admintools_profiles_on_duplicate_ignore" AS ON INSERT TO "#__admintools_profiles"
	WHERE EXISTS(SELECT 1 FROM "#__admintools_profiles"
		WHERE ("id")=(NEW."id"))
	DO INSTEAD NOTHING;

/* This is the actual insert */
INSERT INTO "#__admintools_profiles"
("id","description", "configuration", "filters") VALUES
(1,'Default PHP Change Scanner Profile','','');

/* We have to drop the rule simulating the INSERT IGNORE in MySQL */
DROP RULE "#__admintools_profiles_on_duplicate_ignore" ON "#__admintools_profiles";

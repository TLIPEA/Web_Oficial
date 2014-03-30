SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_acl]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_acl] (
	[user_id] [INT] IDENTITY(1,1) NOT NULL,
	[permissions] [TEXT],
	CONSTRAINT [PK_#__admintools_acl] PRIMARY KEY CLUSTERED
	(
		[user_id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_adminiplist]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_adminiplist] (
	[id] [INT] IDENTITY(1,1) NOT NULL,
	[ip] [NVARCHAR](255) DEFAULT NULL,
	[description] [NVARCHAR](255) DEFAULT NULL,
	CONSTRAINT [PK_#__admintools_adminiplist] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_badwords]') AND type in (N'U'))
BEGIN
CREATE TABLE  [#__admintools_badwords] (
	[id] [INT] IDENTITY(1,1) NOT NULL,
	[word] [NVARCHAR](255) DEFAULT NULL,
	CONSTRAINT [PK_#__admintools_badwords] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_customperms]') AND type in (N'U'))
BEGIN
CREATE TABLE  [#__admintools_customperms] (
	[id] [INT] IDENTITY(1,1) NOT NULL,
	[path] [NVARCHAR](255) NOT NULL,
	[perms] [NVARCHAR](4) DEFAULT '0644',
	CONSTRAINT [PK_#__admintools_customperms] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__admintools_customperms]') AND name = N'idx_path')
BEGIN
CREATE NONCLUSTERED INDEX [idx_path] ON [#__admintools_customperms]
(
	[path] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;


IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_filescache]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_filescache] (
	[admintools_filescache_id] [INT] IDENTITY(1,1) NOT NULL,
	[path] [NVARCHAR](2048) NOT NULL,
	[filedate] [INT] NOT NULL DEFAULT '0',
	[filesize] [INT] NOT NULL DEFAULT '0',
	[data] TEXT,
	[checksum] [NVARCHAR](32) NOT NULL DEFAULT '',
	CONSTRAINT [PK_#__admintools_filescache] PRIMARY KEY CLUSTERED
	(
		[admintools_filescache_id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_ipautoban]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_ipautoban] (
	[ip] [NVARCHAR](255) NOT NULL UNIQUE,
	[reason] [NVARCHAR](255) DEFAULT 'other',
	[until] [DATETIME] NOT NULL DEFAULT ('1900-01-01 00:00:00')
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_ipblock]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_ipblock] (
	[id] [INT] IDENTITY(1,1) NOT NULL,
	[ip] [NVARCHAR](255) DEFAULT NULL,
	[description] [NVARCHAR](255) DEFAULT NULL,
	CONSTRAINT [PK_#__admintools_ipblock] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_log]') AND type in (N'U'))
BEGIN
CREATE TABLE  [#__admintools_log] (
	[id] [INT] IDENTITY(1,1) NOT NULL,
	[logdate] [DATETIME] NOT NULL DEFAULT ('1900-01-01 00:00:00'),
	[ip] [NVARCHAR](40) DEFAULT NULL,
	[url] [NVARCHAR](255) DEFAULT NULL,
	[reason] [NVARCHAR](255) DEFAULT 'other',
	[extradata] [TEXT],
	CONSTRAINT [PK_#__admintools_log] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_redirects]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_redirects] (
	[id] [INT] IDENTITY(1,1) NOT NULL,
	[source] [NVARCHAR](255) DEFAULT NULL,
	[dest] [NVARCHAR](255) DEFAULT NULL,
	[ordering] [BIGINT] NOT NULL DEFAULT '0',
	[published] [TINYINT] NOT NULL DEFAULT '1',
	[keepurlparams] [TINYINT] NOT NULL DEFAULT '1',
	CONSTRAINT [PK_#__admintools_redirects] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_scanalerts]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_scanalerts] (
	[admintools_scanalert_id] [INT] IDENTITY(1,1) NOT NULL,
	[path] [NVARCHAR](2048) NOT NULL,
	[scan_id] [BIGINT] NOT NULL DEFAULT '0',
	[diff] [TEXT],
	[threat_score] [INT] NOT NULL DEFAULT '0',
	[acknowledged] [TINYINT] NOT NULL DEFAULT '0',
	CONSTRAINT [PK_#__admintools_scanalerts] PRIMARY KEY CLUSTERED
	(
		[admintools_scanalert_id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_storage]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_storage] (
	[key] [NVARCHAR](255) NOT NULL,
	[value] [TEXT] NOT NULL,
	CONSTRAINT [PK_#__admintools_storage] PRIMARY KEY CLUSTERED
	(
		[key] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_wafexceptions]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_wafexceptions] (
	[id] [INT] IDENTITY(1,1) NOT NULL,
	[option] [NVARCHAR](255) DEFAULT NULL,
	[view] [NVARCHAR](255) DEFAULT NULL,
	[query] [NVARCHAR](255) DEFAULT NULL,
	CONSTRAINT [PK_#__admintools_wafexceptions] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_scans]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_scans] (
	[id] [BIGINT] IDENTITY(1,1) NOT NULL,
	[description] [NVARCHAR](255) NOT NULL,
	[comment] [NVARCHAR](4000) NULL,
	[backupstart] [DATETIME] NOT NULL DEFAULT ('1900-01-01 00:00:00'),
	[backupend] [DATETIME] NOT NULL DEFAULT ('1900-01-01 00:00:00'),
	[status] [NVARCHAR](8) NOT NULL DEFAULT ('run'),
	[origin] [NVARCHAR](30) NOT NULL DEFAULT ('backend'),
	[type] [NVARCHAR](30) NOT NULL DEFAULT ('full'),
	[profile_id] [BIGINT] NOT NULL DEFAULT ('1'),
	[archivename] [NVARCHAR](4000),
	[absolute_path] [NVARCHAR](4000),
	[multipart] [INT] NOT NULL DEFAULT ('0'),
	[tag] [NVARCHAR](255) NULL,
	[filesexist] [TINYINT] NOT NULL DEFAULT ('1'),
	[remote_filename] [NVARCHAR](1000) NULL,
	[total_size] [BIGINT] NOT NULL DEFAULT ('0'),
	CONSTRAINT [PK_#__admintools_scans] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__admintools_scans]') AND name = N'idx_fullstatus')
BEGIN
CREATE NONCLUSTERED INDEX [idx_fullstatus] ON [#__admintools_scans]
(
	[filesexist] ASC,
	[status] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.indexes WHERE object_id = OBJECT_ID(N'[#__admintools_scans]') AND name = N'idx_stale')
BEGIN
CREATE NONCLUSTERED INDEX [idx_stale] ON [#__admintools_scans]
(
	[status] ASC,
	[origin] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
END;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_profiles]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__admintools_profiles] (
	[id] [INT] IDENTITY(1,1) NOT NULL,
	[description] [NVARCHAR](255) NOT NULL,
	[configuration] TEXT NULL,
	[filters] TEXT NULL,
	CONSTRAINT [PK_#__admintools_profiles] PRIMARY KEY CLUSTERED
	(
		[id] ASC
	) WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;

SET IDENTITY_INSERT #__admintools_profiles ON;
IF NOT EXISTS (SELECT * FROM #__admintools_profiles WHERE id = 1)
BEGIN
INSERT INTO #__admintools_profiles (id, description, configuration, filters)
SELECT 1, 'Default PHP Change Scanner Profile', '', ''
END;
SET IDENTITY_INSERT #__admintools_profiles  OFF;
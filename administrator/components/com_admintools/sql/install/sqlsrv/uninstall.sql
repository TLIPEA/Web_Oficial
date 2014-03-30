SET QUOTED_IDENTIFIER ON;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_ipblock]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_ipblock]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_adminiplist]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_adminiplist]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_redirects]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_redirects]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_log]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_log]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_badwords]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_badwords]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_customperms]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_customperms]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_ipautoban]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_ipautoban]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_acl]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_acl]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_wafexceptions]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_wafexceptions]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_storage]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_storage]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_filescache]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_filescache]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_scanalerts]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_scanalerts]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_scans]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_scans]
END;

IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__admintools_profiles]') AND type in (N'U'))
BEGIN
	DROP TABLE [#__admintools_profiles]
END;
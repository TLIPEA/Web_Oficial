DROP TABLE IF EXISTS #__rokcandy;
DELETE FROM #__assets WHERE id IN (SELECT asset_id as id from #__categories where extension = 'com_rokcandy');
DELETE FROM #__categories WHERE extension = 'com_rokcandy';
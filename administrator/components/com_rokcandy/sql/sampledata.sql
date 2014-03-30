INSERT INTO #__rokcandy (macro, html, published, ordering) VALUES
('[h1]{text}[/h1]', '<h1>{text}</h1>', 1, 1),
('[h2]{text}[/h2]', '<h2>{text}</h2>', 1, 2),
('[h3]{text}[/h3]', '<h3>{text}</h3>', 1, 3),
('[h4]{text}[/h4]', '<h4>{text}</h4>', 1, 4),
('[h5]{text}[/h5]', '<h5>{text}</h5>', 1, 5),
('[b]{text}[/b]', '<strong>{text}</strong>', 1, 6),
('[i]{text}[/i]', '<em>{text}</em>', 1, 7),
('[code]{text}[/code]', '<code>{text}</code>', 1, 8);

UPDATE #__extensions
SET params='{\"forcecache\":\"0\",\"adminenabled\":\"0\",\"editenabled\":\"0\",\"contentPlugin\":\"0\",\"disabled\":\"\"}'
WHERE type= 'component' AND element='com_rokcandy';

UPDATE #__rokcandy, #__categories
SET #__rokcandy.catid = #__categories.id
WHERE #__categories.extension = 'com_rokcandy'
AND #__categories.title = 'Basic';
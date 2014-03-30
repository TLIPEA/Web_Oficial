<?php
	$base = urldecode($_GET['bp']);
	$asset = $_GET['asset'];
	$author = $_GET['author'];
	$editor = $_GET['textarea'];
	$mootols_core = $base . 'media/system/js/mootools-core.js';
	$mootols_more = $base . 'media/system/js/mootools-more.js';
	$modal_js = $base . 'media/system/js/modal.js';
	$modal_css = $base . 'media/system/css/modal.css';

	$mediamanager = $base . 'administrator/index.php?option=com_media&view=images&tmpl=component&asset='.$asset.'&author='.$author.'&e_name=';
	$rokgallery = $base . 'administrator/index.php?option=com_rokgallery&view=gallerypicker&tmpl=component&textarea=';

	$mediamanager_rel = 'rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';
	$rokgallery_rel = 'rel="{handler: \'iframe\', size: {x: 695, y: 400}}"';

	function rokgalleryExists(){
		global $base;
		$rokgalleryExists = @get_headers($base . 'plugins/editors-xtd/rokgallery/rokgallery.xml');
		if ($rokgalleryExists[0] == 'HTTP/1.1 404 Not Found') $rokgalleryExists = false;
		else $rokgalleryExists = true;

		return false;//$rokgalleryExists;
	}

	$rokgalleryExists = rokgalleryExists();
	function renderPicker($text){
		global $rokgallery, $mediamanager, $mediamanager_rel, $rokgallery_rel, $rokgalleryExists;
		$picker = array();
		if ($rokgalleryExists){
			$picker[] = '<div class="picker">';
			$picker[] = '	<select data-mediatype>';
			$picker[] = '		<option '.$mediamanager_rel.' value="'.$rokgallery.$text.'" selected>RokGallery</option>';
			$picker[] = '		<option '.$rokgallery_rel.' value="'.$mediamanager.$text.'">MediaManager</option>';
			$picker[] = '	</select>';
			$picker[] = '	<a data-picker class="modal-button '.$text.'" href="#"><span>Pick</span></a>';
			$picker[] = '</div>';
		} else {
			$picker[] = '<div class="picker '.$text.'">';
			$picker[] = '	<a data-picker class="modal-button '.$text.'" '.$mediamanager_rel.' href="'.$mediamanager.$text.'" tabindex="-1"><span>Pick</span></a>';
			$picker[] = '</div>';
		}

		return implode("\n", $picker);
	}
?>
<!doctype html>
<html>
<head>
	<title>RokBox Snippets Generator</title>
	<link rel="stylesheet" href="../assets/css/rokbox.css"></link>
	<link rel="stylesheet" href="<?php echo $modal_css; ?>"></link>
	<script src="<?php echo $mootols_core; ?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo $mootols_more; ?>" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo $modal_js; ?>" type="text/javascript" charset="utf-8"></script>
	<script src="../assets/js/rokbox.js" type="text/javascript" charset="utf-8"></script>
</head>
<body>
	<div class="container">
		<input type="hidden" name="editor_id" value="<?php echo $editor; ?>" />
		<div class="row">
			<span class="label">Link<span class="required-input">*</span></span>
			<input id="link" name="link" data-required type="text" placeholder="ie, images/powered_by.png"></input>
			<?php echo renderPicker('link'); ?>
		</div>
		<div class="row">
			<span class="label">DOM Element</span>
			<input id="element" name="element" type="text" placeholder="ie, body form#form-login // div.some-class-name"></input>
			<div class="notice">Specify a CSS rule that matches the element in the page you want to render in the popup.</div>
		</div>
		<div class="row">
			<span class="label">Album</span>
			<input name="album" type="text" placeholder="RokBox, Gallery, Personal, etc..."></input>
		</div>
		<div class="row">
			<span class="label">Caption</span>
			<input name="caption" type="text" placeholder=""></input>
		</div>
		<div class="row">
			<span class="label">Content</span>
			<label for="text" class="radio">
				<input id="text" data-switcher name="content" type="radio" value="text" checked></input>
				Text
			</label>
			<label for="thumbnail" class="radio">
				<input id="thumbnail" data-switcher name="content" type="radio" value="thumbnail"></input>
				Thumbnail
			</label>
			<div class="sub-row">
				<input class="text_text" id="text_text" name="text" type="text" placeholder="ie, My RokBox"></input>
				<div class="notice text_text">Leave the field blank to wrap your current selection in the Editor</div>

				<input class="thumbnail_text" id="thumbnail_text" name="thumbnail" type="text" placeholder="ie, images/powered_by.png"></input><?php echo renderPicker('thumbnail_text'); ?>
				<div class="notice thumbnail_text">Leave the field blank to auto-generate thumbnails if the Link is a local image</div>
			</div>
		</div>

		<div class="footer">
			<ul>
				<li><a href="#" id="button-insert-new">Insert and New</a></li>
				<li><a href="#" id="button-insert-close">Insert and Close</a></li>
				<li><a href="#" id="button-cancel">Cancel</a></li>
			</ul>
		</div>
	</div>
</body>
</html>

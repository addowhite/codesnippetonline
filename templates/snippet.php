<div class="card snippet-container">
	<table class="snippet-title-table">
		<tr>
			<td><p class="title snippet-title"><?=$title?></p></td>
			<td><p class="snippet-language"><?=$language?></p></td>
		</tr>
	</table>
	<div class="snippet-content">
		<div class="snippet-editor" id="editor_<?=$snippet_id?>"><?=$content?></div>
	</div>
	<div class="button-container">
		<?=button("like_snippet_button_$snippet_id"   , "<i class=\"fa fa-thumbs-o-up\"   aria-hidden=\"true\"></i><p id=\"like_snippet_count_$snippet_id\" class=\"count\">$like_count</p>", "processes/like_snippet.php?id=$snippet_id"   , "left")?>
		<?=button("dislike_snippet_button_$snippet_id", "<i class=\"fa fa-thumbs-o-down\" aria-hidden=\"true\"></i><p id=\"dislike_snippet_count_$snippet_id\" class=\"count\">$dislike_count</p>", "processes/dislike_snippet.php?id=$snippet_id", "left")?>
		<?=button("view_snippet_button_$snippet_id"   , '<i class="fa fa-expand"          aria-hidden="true"></i>', "pages/snippet/snippet.php?id=$snippet_id"    , "left")?>
		<p class="snippet-author">by <?=$author?></p>
	</div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
		var editor = ace.edit("editor_<?=$snippet_id?>");
	  editor.setTheme("ace/theme/monokai");
	  editor.getSession().setMode("ace/mode/<?=Infra::get_ace_language_name($language)?>");
		editor.setReadOnly(true);
		editor.renderer.setShowGutter(false);
		editor.setShowPrintMargin(false);
		editor.setOption("maxLines", 20);
		addLikeButtonCallback("like"   , "<?=$snippet_id?>");
		addLikeButtonCallback("dislike", "<?=$snippet_id?>");
	});
</script>
